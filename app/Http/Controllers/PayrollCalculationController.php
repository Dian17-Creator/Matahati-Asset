<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Csalary;
use App\Models\Tusercontract;
use App\Models\muser;
use App\Models\Mtunjangan;
use App\Models\Mrekening;
use App\Jobs\CalculatePayrollJob;
use Illuminate\Http\Request;
use App\Mail\SlipKirimGaji;
use Carbon\Carbon;
use DB;

class PayrollCalculationController extends Controller
{
    public function recalcAll(Request $request)
    {
        $request->validate([
            'year' => 'nullable|integer',
            'month' => 'nullable|integer|min:1|max:12',
            'split_by_change' => 'nullable|in:0,1',
        ]);

        $year  = (int)($request->year ?? now()->year);
        $month = (int)($request->month ?? now()->month);
        $recalculate = $request->has('recalculate');
        $split = $request->input('split_by_change') == '1';

        $selYear  = $year;
        $selMonth = $month;

        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd   = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        /**
         * ==================================================
         * 1️⃣ TAMPILKAN RSALARY JIKA SUDAH ADA & TIDAK RECALC
         * ==================================================
         */
        $rsExists = DB::table('rsalary')
            ->join('muser', 'muser.nid', '=', 'rsalary.user_id')
            ->where('muser.factive', 1)
            ->where('rsalary.period_year', $year)
            ->where('rsalary.period_month', $month)
            ->exists();

        if ($rsExists && !$recalculate) {
            // ⭐ FIX: filter user aktif
            $rows = DB::table('rsalary')
                ->join('muser', 'muser.nid', '=', 'rsalary.user_id')
                ->where('muser.factive', 1)
                ->where('rsalary.period_year', $year)
                ->where('rsalary.period_month', $month)
                ->orderBy('rsalary.user_id')
                ->select('rsalary.*')
                ->get();

            $data = $rows->map(function ($r) {
                $fmt = fn ($n) => 'Rp ' . number_format((float)$n, 0, ',', '.');

                $user = DB::table('muser')->where('nid', $r->user_id)->first();

                return [
                    'user_id' => $r->user_id,
                    'user_name' => $user->cname ?? '-',
                    'jabatan' => $r->jabatan ?? ($user->jabatan ?? 'Crew'),
                    'jumlah_masuk' => (int) $r->jumlah_masuk,
                    'gaji_pokok' => $fmt($r->gaji_pokok ?? 0),
                    'tunjangan_makan' => $fmt($r->tunjangan_makan ?? 0),
                    'total_gaji' => $fmt($r->total_gaji ?? 0),
                    'keterangan_absensi' => $r->keterangan_absensi,
                    'status' => $r->status,
                ];
            })->toArray();

            return view('penggajian.index', [
                'data' => $data,
                'year' => $year,
                'month' => $month,
                'mtunjangan' => Mtunjangan::with('user')->orderByDesc('tanggal_berlaku')->get(),
                'users' => muser::orderBy('cname')->get(),
                'mrekening' => Mrekening::orderBy('bank')->get(),
                'selYear' => $selYear,
                'selMonth' => $selMonth,
                'note_info' => 'Menampilkan payroll dari rsalary (karyawan aktif saja)',
            ]);
        }

        /**
         * ==================================================
         * 2️⃣ AMBIL USER AKTIF SAJA (TANPA UBAH DB)
         * ==================================================
         */
        $userIds = DB::table('mtunjangan')
            ->join('muser', 'muser.nid', '=', 'mtunjangan.nid')
            ->where('muser.factive', 1) // ⭐ FIX UTAMA
            ->whereDate('mtunjangan.tanggal_berlaku', '<=', $periodEnd)
            ->select('mtunjangan.nid')
            ->distinct()
            ->pluck('mtunjangan.nid')
            ->toArray();

        Log::info('PAYROLL RECALC START', compact('year', 'month', 'userIds'));

        /**
         * ==================================================
         * 3️⃣ LOOP HITUNG PAYROLL
         * ==================================================
         */
        foreach ($userIds as $uid) {

            // 🔒 GUARD KERAS
            $isActive = DB::table('muser')
                ->where('nid', $uid)
                ->where('factive', 1)
                ->exists();

            if (!$isActive) {
                Log::warning("SKIP payroll (inactive user): {$uid}");
                continue; // ⛔ STOP TOTAL
            }

            // Jalankan Job (job sudah aman cek factive)
            (new CalculatePayrollJob(
                (int)$uid,
                $year,
                $month,
                $recalculate,
                $split
            ))->handle();

            $cs = Csalary::where('user_id', $uid)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->first();

            if (!$cs) {
                continue;
            }

            // Insert rsalary hanya sekali
            $exists = DB::table('rsalary')
                ->where('user_id', $uid)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->exists();

            if (!$exists) {
                $payload = [
                    'user_id' => $cs->user_id,
                    'period_year' => $cs->period_year,
                    'period_month' => $cs->period_month,
                    'jabatan' => $cs->jabatan,
                    'jumlah_masuk' => $cs->jumlah_masuk,
                    'gaji_pokok' => $cs->gaji_pokok,
                    'tunjangan_makan' => $cs->tunjangan_makan,
                    'total_gaji' => $cs->total_gaji,
                    'gaji_lembur' => $cs->gaji_lembur,
                    'keterangan_absensi' => $cs->keterangan_absensi,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $columns = Schema::getColumnListing('rsalary');
                DB::table('rsalary')->insert(
                    array_intersect_key($payload, array_flip($columns))
                );
            }
        }

        return redirect()
            ->route('penggajian.index', compact('year', 'month', 'selYear', 'selMonth'))
            ->with('success', 'Perhitungan payroll selesai (hanya karyawan aktif).');
    }

    public function recalcAjax(Request $req)
    {
        $req->validate([
            'user_ids'     => 'required|array',
            'user_ids.*'   => 'integer',
            'period_month' => 'required|numeric|min:1|max:12',
            'period_year'  => 'required|integer',
        ]);

        $userIds = $req->input('user_ids');
        $month   = (int) $req->input('period_month');
        $year    = (int) $req->input('period_year');

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end   = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        // jika bulan berjalan, batasi sampai hari ini
        $now = Carbon::now();
        $loopEnd = ($now->year === $year && $now->month === $month)
            ? $now->endOfDay()
            : $end;

        \Log::info('recalcAjax START', [
            'users'   => $userIds,
            'period'  => "$year-$month",
            'start'   => $start->toDateString(),
            'loopEnd' => $loopEnd->toDateString(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Ambil SEMUA SCAN (mscan + manual + face)
        |--------------------------------------------------------------------------
        */
        $scans = collect()
            ->merge(
                DB::table('mscan')
                    ->selectRaw("nuserid, DATE(dscanned) as ddate")
                    ->whereIn('nuserid', $userIds)
                    ->whereBetween(DB::raw('DATE(dscanned)'), [$start, $loopEnd])
                    ->groupBy('nuserid', DB::raw('DATE(dscanned)'))
                    ->get()
            )
            ->merge(
                DB::table('mscan_manual')
                    ->selectRaw("nuserId as nuserid, DATE(dscanned) as ddate")
                    ->whereIn('nuserId', $userIds)
                    ->whereBetween(DB::raw('DATE(dscanned)'), [$start, $loopEnd])
                    ->groupBy('nuserId', DB::raw('DATE(dscanned)'))
                    ->get()
            )
            ->merge(
                DB::table('mface_scan')
                    ->selectRaw("nuserId as nuserid, DATE(dscanned) as ddate")
                    ->whereIn('nuserId', $userIds)
                    ->whereBetween(DB::raw('DATE(dscanned)'), [$start, $loopEnd])
                    ->groupBy('nuserId', DB::raw('DATE(dscanned)'))
                    ->get()
            );

        // presentMap[user_id][date] = true
        $presentMap = [];
        foreach ($scans as $row) {
            $uid = (int) $row->nuserid;
            $ds  = $row->ddate;
            $presentMap[$uid][$ds] = true;
        }


        // Ambil REQUEST (izin / sakit) yang approved
        $requests = DB::table('mrequest')
            ->selectRaw("nuserid, DATE(drequest) as ddate, category")
            ->whereIn('nuserid', $userIds)
            ->whereBetween(DB::raw('DATE(drequest)'), [$start, $loopEnd])
            ->where(function ($q) {
                $q->whereRaw("LOWER(IFNULL(cstatus,''))='approved'")
                  ->orWhereRaw("LOWER(IFNULL(chrdstat,''))='approved'")
                  ->orWhereRaw("LOWER(IFNULL(csuperstat,''))='approved'");
            })
            ->get();

        $requestMap = [];
        foreach ($requests as $r) {
            $cat = strtolower($r->category ?? 'izin');
            if (str_contains($cat, 'sakit')) {
                $requestMap[$r->nuserid][$r->ddate] = 'sakit';
            } else {
                $requestMap[$r->nuserid][$r->ddate] = 'izin';
            }
        }

        // LOOP PER USER
        $results = [];

        foreach ($userIds as $uid) {
            $A = $I = $S = 0;

            $d = $start->copy();
            while ($d->lte($loopEnd)) {
                if ($d->dayOfWeek === Carbon::SUNDAY) {
                    $d->addDay();
                    continue;
                }

                $ds = $d->toDateString();

                if (!empty($presentMap[$uid][$ds])) {
                    // hadir
                } elseif (!empty($requestMap[$uid][$ds])) {
                    $requestMap[$uid][$ds] === 'sakit' ? $S++ : $I++;
                } else {
                    $A++;
                }

                $d->addDay();
            }

            // 🔴 FIX UTAMA: jumlah masuk dihitung ulang
            $jumlahMasuk = count($presentMap[$uid] ?? []);

            $ket = "A = $A, I = $I, S = $S";

            DB::table('csalary')
                ->where('user_id', $uid)
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->update([
                    'jumlah_masuk'       => $jumlahMasuk, // ✅ FIX
                    'keterangan_absensi' => $ket,
                    'updated_at'         => now(),
                ]);

            $results[] = [
                'user_id'      => $uid,
                'ket'          => $ket,
                'jumlah_masuk' => $jumlahMasuk,
                'counts'       => compact('A', 'I', 'S'),
            ];
        }

        \Log::info('recalcAjax DONE', ['count' => count($results)]);

        return response()->json(['results' => $results]);
    }
}
