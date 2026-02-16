<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Csalary;
use App\Models\Rsalary;
use App\Models\muser;
use App\Models\Mtunjangan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\PayrollExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExportBCA;
use App\Exports\PayrollExportBri;
use App\Exports\PayrollExportMandiriExcel;
use App\Exports\MultiPayrollMandiriExport;
use App\Models\Mdepartment;
use App\Models\Mrekening;

class GajiController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        $selYear  = $request->year ?? now()->year;
        $selMonth = $request->month ?? now()->month;

        // master data
        $users = muser::orderBy('cname')->get();
        $departments = Mdepartment::orderBy('cname')->get();
        $mrekening = Mrekening::orderBy('bank')->get();
        $mtunjangan = Mtunjangan::with('user')->orderByDesc('tanggal_berlaku')->get();

        // optional department filter from query string
        $depIdRaw = $request->input('department_id', null);
        $depId = null;
        if (!is_null($depIdRaw) && trim((string)$depIdRaw) !== '') {
            $depId = intval($depIdRaw) > 0 ? intval($depIdRaw) : null;
        }

        // Ambil payroll (apply department filter jika ada)
        $query = Csalary::with('user')
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->orderBy('user_id');

        if ($depId !== null) {
            // <-- IMPORTANT: muser primary key is `nid`, not `id` -> pluck('nid')
            $userIds = muser::where('niddept', $depId)->pluck('nid')->toArray();

            if (count($userIds) === 0) {
                // tidak ada user di departemen itu -> kembalikan result kosong
                $rows = collect([]);
            } else {
                $query->whereIn('user_id', $userIds);
                $rows = $query->get();
            }
        } else {
            $rows = $query->get();
        }

        // Format untuk view (tambahkan department_id agar blade bisa pakai data-department-id)
        $data = $rows->map(function ($model) {
            $user = $model->user;

            // Tentukan jabatan
            $jabatan = 'Crew';
            if ($user) {
                if (!empty($user->fhrd)) {
                    $jabatan = 'HRD';
                } elseif (!empty($user->fadmin)) {
                    $jabatan = 'Captain';
                } elseif (!empty($user->fsuper)) {
                    $jabatan = 'Supervisor';
                } elseif (!empty($user->jabatan)) {
                    $jabatan = $user->jabatan;
                }
            }

            $gaji_harian   = (float) $model->gaji_harian;
            $gaji_pokok    = (float) $model->gaji_pokok;
            $jumlah_masuk  = (int)   $model->jumlah_masuk;

            $jenisGaji = strtolower($model->jenis_gaji ?? 'pokok');

            if ($jenisGaji === 'harian') {
                $displayGaji      = $gaji_harian;
                $displayGajiPokok = $gaji_pokok;
            } else {
                $displayGaji      = $gaji_pokok;
                $displayGajiPokok = $gaji_pokok;
            }

            $fmt = fn ($n) => 'Rp ' . number_format((float)$n, 2, ',', '.');

            return [
                'id' => $model->id,
                'user_id' => $model->user_id,
                // use nid from user relation (or null)
                'department_id' => $user->niddept ?? null,
                'user_name' => $user->cname ?? '-',
                'jabatan' => $jabatan,
                'jumlah_masuk' => $jumlah_masuk,

                'gaji' => $fmt($displayGaji),
                'gaji_harian' => $displayGaji,
                'gaji_pokok' => $fmt($displayGajiPokok),

                'tunjangan_makan' => $fmt($model->tunjangan_makan),
                'tunjangan_jabatan' => $fmt($model->tunjangan_jabatan),
                'tunjangan_transport' => $fmt($model->tunjangan_transport),
                'tunjangan_luar_kota' => $fmt($model->tunjangan_luar_kota),
                'tunjangan_masa_kerja' => $fmt($model->tunjangan_masa_kerja),

                'gaji_lembur' => $fmt($model->gaji_lembur),
                'tabungan_diambil' => $fmt($model->tabungan_diambil),
                'potongan_lain' => $fmt($model->potongan_lain),
                'potongan_tabungan' => $fmt($model->potongan_tabungan),
                'potongan_keterlambatan' => $fmt($model->potongan_keterlambatan),

                'total_gaji' => $fmt($model->total_gaji),
                'note' => $model->note,
                'keterangan_absensi' => $model->keterangan_absensi,
                'reasonedit' => $model->reasonedit,
                'status' => $model->status,
            ];
        })->toArray();

        return view('penggajian.index', [
            'data'  => $data,
            'year'  => $year,
            'month' => $month,
            'mtunjangan' => $mtunjangan,
            'users' => $users,
            'selYear' => $selYear,
            'selMonth' => $selMonth,
            'departments' => $departments,
            'mrekening' => $mrekening,
        ]);
    }
    public function update(Request $request, $id)
    {
        $row = Csalary::findOrFail($id);

        $oldJumlahMasuk    = (int) ($row->jumlah_masuk ?? 0);
        $oldTunjanganMakan = (float) ($row->tunjangan_makan ?? 0);


        $validated = $request->validate([
            'jumlah_masuk'        => 'nullable|integer|min:0',
            'gaji_harian'         => 'nullable|regex:/^[0-9\.\,\sRp]+$/',
            'gaji_pokok'          => 'nullable|regex:/^[0-9\.\,\sRp]+$/',
            'tunjangan_makan'     => 'nullable|regex:/^[0-9\.\,\sRp]+$/',
            'tunjangan_jabatan'   => 'nullable|regex:/^[0-9\.\,\sRp]+$/',
            'tunjangan_transport' => 'nullable|regex:/^[0-9\.\,\sRp]+$/',
            'tunjangan_luar_kota' => 'nullable|regex:/^[0-9\.\,\sRp]+$/',
            'tunjangan_masa_kerja' => 'nullable|regex:/^[0-9\.\,\sRp]+$/',
            'gaji_lembur'         => 'nullable|regex:/^[0-9\.\,\sRp]+$/',
            'tabungan_diambil'    => 'nullable|regex:/^[0-9\.\,\sRp]+$/',
            'potongan_lain'       => 'nullable|regex:/^[0-9\.\,\sRp]+$/',
            'potongan_tabungan'   => 'nullable|regex:/^[0-9\.\,\sRp]+$/',
            'potongan_keterlambatan'   => 'nullable|regex:/^[0-9\.\,\sRp]+$/',
            'note'                => 'nullable|string|max:1000',
            'reasonedit'          => 'nullable|string|max:1000',
        ]);

        $clean = function ($v) {
            if ($v === null || $v === '') {
                return 0;
            }

            // ✅ JIKA SUDAH NUMERIC, JANGAN DISENTUH
            if (is_numeric($v)) {
                return (float) $v;
            }

            // ❗ HANYA UNTUK FORMAT LOKAL
            $v = str_replace(['Rp', ' '], '', $v);

            // jika format ID (ada koma)
            if (str_contains($v, ',')) {
                $v = str_replace('.', '', $v);
                $v = str_replace(',', '.', $v);
            }

            return is_numeric($v) ? (float) $v : 0;
        };

        $newJumlahMasuk        = $validated['jumlah_masuk'] ?? $row->jumlah_masuk ?? 0;
        $newGajiHarian         = $clean($validated['gaji_harian']         ?? $request->input('gaji_harian'));
        $newGajiPokokInput     = $clean($validated['gaji_pokok']          ?? $request->input('gaji_pokok'));
        $newTunjanganJabatan   = $clean($validated['tunjangan_jabatan']   ?? $request->input('tunjangan_jabatan'));
        $newTunjanganTransport = $clean($validated['tunjangan_transport'] ?? $request->input('tunjangan_transport'));
        $newTunjanganLuarKota  = $clean($validated['tunjangan_luar_kota'] ?? $request->input('tunjangan_luar_kota'));
        $newTunjanganMasaKerja = $clean($validated['tunjangan_masa_kerja'] ?? $request->input('tunjangan_masa_kerja'));
        $newGajiLembur         = $clean($validated['gaji_lembur']         ?? $request->input('gaji_lembur'));
        $newTabunganDiambil    = $clean($validated['tabungan_diambil']    ?? $request->input('tabungan_diambil'));
        $newPotonganLain       = $clean($validated['potongan_lain']       ?? $request->input('potongan_lain'));
        $newPotonganTabungan   = $clean($validated['potongan_tabungan']   ?? $request->input('potongan_tabungan'));
        $newPotonganKeterlambatan = $clean($validated['potongan_keterlambatan']   ?? $request->input('potongan_keterlambatan'));
        $newNote               = $validated['note'] ?? $request->input('note');
        $newReasonEdit         = $validated['reasonedit'] ?? $request->input('reasonedit');

        // Disini yh
        if (
            $oldJumlahMasuk == 0 &&
            $newJumlahMasuk > 0
        ) {
            $latestTunjangan = \App\Models\Mtunjangan::where('nid', $row->user_id)
                ->whereDate('tanggal_berlaku', '<=', now())
                ->orderByDesc('tanggal_berlaku')
                ->orderByDesc('id')
                ->first();

            if ($latestTunjangan) {

                // === GAJI ===
                if (strtolower($latestTunjangan->jenis_gaji) === 'harian') {
                    $newGajiHarian = (float) $latestTunjangan->nominal_gaji;
                    $newGajiPokok  = $newGajiHarian * $newJumlahMasuk;
                } else {
                    $newGajiPokok  = (float) $latestTunjangan->nominal_gaji;
                    $newGajiHarian = $newJumlahMasuk > 0
                        ? ($newGajiPokok / $newJumlahMasuk)
                        : 0;
                }

                // === TUNJANGAN ===
                if ($latestTunjangan->tunjangan_makan > 0) {
                    $newTunjanganMakan = $latestTunjangan->tunjangan_makan * $newJumlahMasuk;
                }

                $newTunjanganJabatan   = (float) $latestTunjangan->tunjangan_jabatan;
                $newTunjanganTransport = (float) $latestTunjangan->tunjangan_transport;
                $newTunjanganLuarKota  = (float) $latestTunjangan->tunjangan_luar_kota;
                $newTunjanganMasaKerja = (float) $latestTunjangan->tunjangan_masa_kerja;
            }
        }


        $jenis = strtolower($row->jenis_gaji ?? 'pokok');
        if ($jenis === 'harian') {
            $newGajiPokok = ($newGajiHarian ?? 0) * ($newJumlahMasuk ?? 0);
        } else {
            $newGajiPokok = $newGajiPokokInput;
            if ($newGajiPokok <= 0 && $newGajiHarian > 0) {
                $newGajiPokok = ($newGajiHarian ?? 0) * ($newJumlahMasuk ?? 0);
            }
        }

        $tunjanganPerHari = $oldJumlahMasuk > 0
            ? $oldTunjanganMakan / $oldJumlahMasuk
            : 0;

        if (!($oldJumlahMasuk == 0 && $newJumlahMasuk > 0)) {
            $newTunjanganMakan = $clean(
                $validated['tunjangan_makan'] ?? $request->input('tunjangan_makan')
            );
        }

        if (
            $oldJumlahMasuk > 0 &&
            $newJumlahMasuk != $oldJumlahMasuk &&
            $tunjanganPerHari > 0
        ) {
            $newTunjanganMakan = $tunjanganPerHari * $newJumlahMasuk;
        }

        // ================= GUARD ALASAN EDIT =================
        $watchedFields = [
            'jumlah_masuk'        => [$row->jumlah_masuk,        $newJumlahMasuk],
            'gaji_harian'         => [$row->gaji_harian,         $newGajiHarian],
            'gaji_pokok'          => [$row->gaji_pokok,          $newGajiPokok],
            'tunjangan_makan'     => [$row->tunjangan_makan,     $newTunjanganMakan],
            'tunjangan_jabatan'   => [$row->tunjangan_jabatan,   $newTunjanganJabatan],
            'tunjangan_transport' => [$row->tunjangan_transport, $newTunjanganTransport],
            'tunjangan_luar_kota' => [$row->tunjangan_luar_kota, $newTunjanganLuarKota],
            'tunjangan_masa_kerja' => [$row->tunjangan_masa_kerja,$newTunjanganMasaKerja],
            'gaji_lembur'         => [$row->gaji_lembur,         $newGajiLembur],
            'tabungan_diambil'    => [$row->tabungan_diambil,    $newTabunganDiambil],
            'potongan_lain'       => [$row->potongan_lain,       $newPotonganLain],
            'potongan_tabungan'   => [$row->potongan_tabungan,   $newPotonganTabungan],
            'potongan_keterlambatan' => [$row->potongan_keterlambatan, $newPotonganKeterlambatan],
        ];

        $isChanged = false;

        foreach ($watchedFields as $field => [$old, $new]) {
            if ((float)$old !== (float)$new) {
                $isChanged = true;
                break;
            }
        }

        if ($isChanged && empty(trim($newReasonEdit))) {
            return redirect()->back()
                ->withErrors(['reasonedit' => 'Alasan edit wajib diisi jika ada perubahan data.'])
                ->withInput();
        }

        $row->jumlah_masuk        = $newJumlahMasuk;
        $row->gaji_harian         = $newGajiHarian;
        $row->gaji_pokok          = $newGajiPokok;
        $row->tunjangan_makan     = $newTunjanganMakan;
        $row->tunjangan_jabatan   = $newTunjanganJabatan;
        $row->tunjangan_transport = $newTunjanganTransport;
        $row->tunjangan_luar_kota = $newTunjanganLuarKota;
        $row->tunjangan_masa_kerja = $newTunjanganMasaKerja;
        $row->gaji_lembur         = $newGajiLembur;
        $row->tabungan_diambil    = $newTabunganDiambil;
        $row->potongan_lain       = $newPotonganLain;
        $row->potongan_tabungan   = $newPotonganTabungan;
        $row->potongan_keterlambatan   = $newPotonganKeterlambatan;
        $row->note                = $newNote;
        $row->reasonedit        = $newReasonEdit;

        // total tunjangan
        $totalTunjangan =
            ($row->tunjangan_makan      ?? 0)
            + ($row->tunjangan_jabatan    ?? 0)
            + ($row->tunjangan_transport  ?? 0)
            + ($row->tunjangan_luar_kota  ?? 0)
            + ($row->tunjangan_masa_kerja ?? 0);

        // total potongan
        $totalPotongan =
            ($row->potongan_lain       ?? 0)
            + ($row->potongan_tabungan   ?? 0)
            + ($row->tabungan_diambil    ?? 0)
            + ($row->potongan_keterlambatan   ?? 0);

        // total gaji
        $row->total_gaji = round(
            ($row->gaji_pokok ?? 0)
            + $totalTunjangan
            + ($row->gaji_lembur ?? 0)
            - $totalPotongan,
            2
        );

        $row->save();
        return redirect()->back()->with('success', 'Payroll berhasil diperbarui.');
    }

    public function getLatestTunjangan($nid)
    {
        $row = Mtunjangan::where('nid', $nid)
            ->orderByDesc('tanggal_berlaku')
            ->orderByDesc('id')
            ->first();

        if (!$row) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'jenis_gaji' => $row->jenis_gaji,
            'nominal_gaji' => $row->nominal_gaji,
            't_makan' => $row->tunjangan_makan,
            't_jabatan' => $row->tunjangan_jabatan,
            't_transport' => $row->tunjangan_transport,
            't_luarkota' => $row->tunjangan_luar_kota,
            't_masakerja' => $row->tunjangan_masa_kerja,
        ]);
    }

    public function tunjanganIndex()
    {
        $data = Mtunjangan::with('user')
            ->orderByDesc('tanggal_berlaku')
            ->get();

        return view('penggajian.tunjangan_index', [
            'data' => $data
        ]);
    }

    public function tunjanganStore(Request $request)
    {
        $validated = $request->validate([
            'nid' => 'nullable|exists:muser,nid',
            'tanggal_berlaku' => 'required|date',
            'jenis_gaji' => 'required|in:pokok,harian',

            'nominal_gaji' => 'nullable',
            'tunjangan_makan' => 'nullable',
            'tunjangan_jabatan' => 'nullable',
            'tunjangan_transport' => 'nullable',
            'tunjangan_luar_kota' => 'nullable',
            'tunjangan_masa_kerja' => 'nullable',
        ]);

        // 🔑 NORMALISASI ANGKA (FORMAT ID → FLOAT)
        $clean = function ($v) {
            if ($v === null || $v === '') {
                return 0;
            }

            // ✅ JIKA SUDAH NUMERIC, JANGAN DISENTUH
            if (is_numeric($v)) {
                return (float) $v;
            }

            // ❗ HANYA UNTUK FORMAT LOKAL
            $v = str_replace(['Rp', ' '], '', $v);

            // jika format ID (ada koma)
            if (str_contains($v, ',')) {
                $v = str_replace('.', '', $v);
                $v = str_replace(',', '.', $v);
            }

            return is_numeric($v) ? (float) $v : 0;
        };

        $validated['nominal_gaji']          = $clean($request->nominal_gaji);
        $validated['tunjangan_makan']       = $clean($request->tunjangan_makan);
        $validated['tunjangan_jabatan']     = $clean($request->tunjangan_jabatan);
        $validated['tunjangan_transport']   = $clean($request->tunjangan_transport);
        $validated['tunjangan_luar_kota']   = $clean($request->tunjangan_luar_kota);
        $validated['tunjangan_masa_kerja']  = $clean($request->tunjangan_masa_kerja);

        Mtunjangan::create($validated);

        return back()->with('success', 'Tunjangan berhasil ditambahkan.');
    }

    public function tunjanganDelete($id)
    {
        $row = Mtunjangan::findOrFail($id);
        $row->delete();

        return back()->with('success', 'Data tunjangan berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        // Accept either: bulan=YYYY-MM (from modal_report_gaji) OR month & year (old behavior)
        $bulanInput = $request->input('bulan', null);
        if ($bulanInput && preg_match('/^\d{4}-\d{2}$/', $bulanInput)) {
            [$year, $month] = explode('-', $bulanInput);
            $year = (int) $year;
            $month = (int) $month;
        } else {
            $month = (int) $request->input('month', now()->month);
            $year  = (int) $request->input('year', now()->year);
        }

        // source table (csalary / rsalary)
        $source = strtolower((string) $request->input('source_table', 'csalary'));
        $modelClass = $source === 'rsalary' ? Rsalary::class : Csalary::class;
        $source = $source === 'rsalary' ? 'rsalary' : 'csalary';

        // parse selected_ids (CSV, JSON array, or array)
        $selectedRaw = $request->input('selected_ids', null);
        $selectedIds = [];
        if ($selectedRaw !== null && trim((string)$selectedRaw) !== '') {
            if (is_string($selectedRaw)) {
                $decoded = json_decode($selectedRaw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $selectedIds = array_values(array_filter($decoded, function ($v) {
                        return is_numeric($v) && intval($v) > 0;
                    }));
                } else {
                    $selectedIds = array_values(array_filter(array_map('trim', explode(',', $selectedRaw)), function ($v) {
                        return is_numeric($v) && intval($v) > 0;
                    }));
                }
            } elseif (is_array($selectedRaw)) {
                $selectedIds = array_values(array_filter($selectedRaw, function ($v) {
                    return is_numeric($v) && intval($v) > 0;
                }));
            }
        }

        // base query for chosen model
        $query = $modelClass::with('user')
            ->where('period_year', $year)
            ->where('period_month', $month);

        // if explicit selected ids -> restrict by those csalary/rsalary ids
        if (count($selectedIds) > 0) {
            $query->whereIn('id', $selectedIds);
        } else {
            // department filter if provided
            $departmentInput = $request->input('department_id', null);
            if (!is_null($departmentInput) && trim((string)$departmentInput) !== '') {
                $userIds = [];

                if (is_numeric($departmentInput)) {
                    // numeric -> treat as Mdepartment.nid (or id)
                    $depVal = intval($departmentInput);
                    $dep = Mdepartment::where('nid', $depVal)->orWhere('id', $depVal)->first();
                    if ($dep) {
                        // muser.niddept likely stores dep->nid (or dep->id depending on your db)
                        // try both possibilities: nid then id
                        $userIds = muser::where('niddept', $dep->nid ?? $dep->id)->pluck('nid')->toArray();
                    } else {
                        // fallback: assume muser.niddept equals given numeric
                        $userIds = muser::where('niddept', $depVal)->pluck('nid')->toArray();
                    }
                } else {
                    // string -> may be cname, code, or directly the value stored in muser.niddept
                    $dep = Mdepartment::where('cname', $departmentInput)
                        ->orWhere('code', $departmentInput)
                        ->first();

                    if ($dep) {
                        $userIds = muser::where('niddept', $dep->nid ?? $dep->id)->pluck('nid')->toArray();
                    } else {
                        // fallback: treat departmentInput as direct muser.niddept value (e.g. 'CK')
                        $userIds = muser::where('niddept', $departmentInput)->pluck('nid')->toArray();
                    }
                }

                if (empty($userIds)) {
                    return back()->with('error', 'Tidak ada karyawan pada departemen yang dipilih.');
                }

                $query->whereIn('user_id', $userIds);
            }
        }

        // get rows
        $data = $query->get();

        // debug
        \Log::info('exportExcel called', [
            'source' => $source,
            'year' => $year, 'month' => $month,
            'selected_count' => count($selectedIds),
            'rows' => $data->count(),
            'department' => $request->input('department_id', null),
        ]);

        if ($data->count() === 0) {
            return back()->with('error', 'Tidak ada data untuk periode / filter yang dipilih.');
        }

        // produce filename and download using existing PayrollExport
        $fileName = "ReportGaji-{$month}-{$year}.xlsx";
        return Excel::download(new PayrollExport($data, $month, $year), $fileName);
    }


    public function getSlipInfo($id)
    {
        $row = Csalary::find($id);

        if (!$row) {
            return response()->json([
                'success' => false,
                'bulan' => 'Bulan ini',
                'wa_footer' => env(
                    'WA_FOOTER'
                )
            ]);
        }

        // Hitung bulan
        try {
            $periodMonth = $row->period_month ?? $row->month ?? null;
            $periodYear  = $row->period_year ?? $row->year ?? null;

            if ($periodMonth && $periodYear) {
                $bulan = Carbon::createFromDate(
                    (int)$periodYear,
                    (int)$periodMonth,
                    1
                )->translatedFormat('F Y');
            } else {
                $bulan = Carbon::parse($row->created_at)->translatedFormat('F Y');
            }
        } catch (\Exception $e) {
            $bulan = Carbon::now()->translatedFormat('F Y');
        }

        return response()->json([
            'success'   => true,
            'bulan'    => $bulan,
            'wa_footer' => env(
                'WA_FOOTER'
            ),
        ]);
    }


    public function exportBank(Request $req)
    {
        // validasi dasar (department_id & selected_ids bersifat optional/tolerant)
        $req->validate([
            'bulan'        => 'required',
            'bank'         => 'required|string',
            'payroll_date' => 'nullable|date',
            'mrekening_id' => 'nullable|exists:mrekening,id',
            'department_id' => 'nullable',
            'selected_ids'  => 'nullable',
        ]);

        // parse periode YYYY-MM
        $bulan = $req->input('bulan');
        if (!preg_match('/^\d{4}-\d{2}$/', $bulan)) {
            return back()->with('error', 'Format periode tidak valid. Gunakan YYYY-MM.');
        }
        [$year, $month] = explode('-', $bulan);
        $year = (int) $year;
        $month = (int) $month;

        // payroll_date (default ke tanggal 5)
        $defaultDay = 5;
        if ($req->filled('payroll_date')) {
            try {
                $payrollDate = Carbon::parse($req->input('payroll_date'));
            } catch (\Exception $e) {
                $payrollDate = Carbon::create($year, $month, $defaultDay);
            }
        } else {
            $payrollDate = Carbon::create($year, $month, $defaultDay);
        }

        // normalize bank & mandiri requirement
        $bankInput = (string) $req->input('bank', '');
        $bankLower = strtolower(trim($bankInput));
        $mrekeningId = $req->input('mrekening_id') ? intval($req->input('mrekening_id')) : null;
        if ($bankLower === 'mandiri' && !$mrekeningId) {
            return back()->with('error', 'Silakan pilih Rekening Sumber untuk Mandiri.');
        }

        // base query: csalary for period
        $query = Csalary::with(['user', 'user.rekening'])
            ->where('period_year', $year)
            ->where('period_month', $month);

        // --- support explicit selected_ids (client may send CSV or JSON array) ---
        $selectedRaw = $req->input('selected_ids', null);
        $selectedIds = [];
        if ($selectedRaw !== null && trim((string)$selectedRaw) !== '') {
            if (is_string($selectedRaw)) {
                $decoded = json_decode($selectedRaw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $selectedIds = array_values(array_filter($decoded, function ($v) {
                        return is_numeric($v) && intval($v) > 0;
                    }));
                } else {
                    $selectedIds = array_values(array_filter(array_map('trim', explode(',', $selectedRaw)), function ($v) {
                        return is_numeric($v) && intval($v) > 0;
                    }));
                }
            } elseif (is_array($selectedRaw)) {
                $selectedIds = array_values(array_filter($selectedRaw, function ($v) {
                    return is_numeric($v) && intval($v) > 0;
                }));
            }
        }

        if (count($selectedIds) > 0) {
            // jika ada selected ids, batasi hasil berdasarkan id csalary
            $query->whereIn('id', $selectedIds);
        } else {
            // no explicit selected_ids → gunakan department filter jika dikirim
            $departmentInput = $req->input('department_id', null);

            if (!is_null($departmentInput) && trim((string)$departmentInput) !== '') {

                $userIds = [];

                if (is_numeric($departmentInput)) {
                    // department_id numeric → itu adalah Mdepartment.nid
                    $depVal = intval($departmentInput);

                    // pastikan departemen ada
                    $dep = Mdepartment::where('nid', $depVal)->first();

                    if ($dep) {
                        // ambil semua user yg niddept = nid dep
                        $userIds = muser::where('niddept', $dep->nid)->pluck('nid')->toArray();
                    } else {
                        // fallback: tetap saja treat sebagai niddept = angka
                        $userIds = muser::where('niddept', $depVal)->pluck('nid')->toArray();
                    }
                } else {
                    // department string → bisa cname atau code atau string code di user.niddept
                    $dep = Mdepartment::where('cname', $departmentInput)
                        ->orWhere('code', $departmentInput)
                        ->first();

                    if ($dep) {
                        $userIds = muser::where('niddept', $dep->nid)->pluck('nid')->toArray();
                    } else {
                        // fallback: treat sebagai nilai langsung user.niddept
                        $userIds = muser::where('niddept', $departmentInput)->pluck('nid')->toArray();
                    }
                }

                if (empty($userIds)) {
                    return back()->with('error', 'Tidak ada karyawan pada departemen yang dipilih.');
                }

                $query->whereIn('user_id', $userIds);
            }

        }

        if ($bankLower === 'mandiri') {
            $query->whereHas('user.rekening', function ($q) use ($mrekeningId) {
                $q->where('id', $mrekeningId);
            });
        } else {
            $query->where(function ($qb) use ($bankLower) {
                $qb->whereHas('user', function ($qUser) use ($bankLower) {
                    // use whereRaw inside whereHas so 'muser' is referenced in the subquery (safe)
                    $qUser->whereRaw('LOWER(bank) LIKE ?', ["%{$bankLower}%"]);
                });

                $qb->orWhereHas('user.rekening', function ($qRek) use ($bankLower) {
                    $qRek->whereRaw('LOWER(bank) LIKE ?', ["%{$bankLower}%"]);
                });
            });
        }

        // ambil data
        $data = $query->get();

        if ($data->count() === 0) {
            return back()->with('error', 'Tidak ada data payroll yang sesuai kriteria (periode / bank / rekening / departemen).');
        }

        // ---------- export sesuai bank ----------
        if ($bankLower === 'bca') {
            $fileName = "payroll_bca_{$month}-{$year}.xlsx";
            return Excel::download(
                new PayrollExportBCA($data, $month, $year, $payrollDate),
                $fileName
            );
        }

        if ($bankLower === 'mandiri') {
            $companyAccount = '';
            $companyAlias = '';
            $reference = '';

            if ($mrekeningId) {
                $rek = Mrekening::find($mrekeningId);
                if ($rek) {
                    $companyAlias   = ($rek->atas_nama ? strtoupper($rek->bank . ' - ' . $rek->atas_nama) : '');
                    $companyAccount = $rek->nomor_rekening ?: ($rek->nomor ?? '');
                }
            }

            // build bankList — (ambil dari implementasi lama / helper)
            $bankLines = <<<'TXT'
            BANK INDONESIA|BI|INDOIDJA|0010016
            PT. BANK RAKYAT INDONESIA (PERSERO)|BRI|BRINIDJA|0020307
            PT. BANK MANDIRI (PERSERO) TBK|BANK MANDIRI|BMRIIDJA|0080017
            PT. BANK NEGARA INDONESIA (PERSERO)|BANK BNI|BNINIDJA|0090010
            PT. BANK DANAMON INDONESIA Tbk.|BANK DANAMON|BDINIDJA|0110042
            PT. BANK DANAMON INDONESIA UNIT USAHA SYARIAH|BANK DANAMON UUS|SYBDIDJ1|0119920
            PT. BANK PERMATA,TBK|BANK PERMATA|BBBAIDJA|0130475
            PT. BANK PERMATA,TBK UNIT USAHA SYARIAH|BANK PERMATA UUS|SYBBIDJ1|0139926
            PT. BANK CENTRAL ASIA Tbk.|BCA|CENAIDJA|0140397
            PT. BANK MAYBANK INDONESIA Tbk.|BANK MAYBANK|IBBKIDJA|0160131
            PT. BANK MAYBANK INDONESIA Tbk. UNIT USAHA SYARIAH|BANK MAYBANK UUS|SYBKIDJ1|0169925
            PT. PANIN BANK Tbk.|PANIN BANK|PINBIDJA|0190017
            PT. BANK CIMB NIAGA TBK|BANK CIMB|BNIAIDJA|0220026
            PT. BANK CIMB NIAGA TBK - UNIT USAHA SYARIAH|BANK CIMB NIAGA UUS|SYNAIDJ1|0229920
            PT. BANK UOB INDONESIA|UOB INDONESIA|BBIJIDJA|0230016
            PT. BANK OCBC NISP, Tbk.|BANK OCBC NISP|NISPIDJA|0280024
            PT.BANK OCBC NISP TBK - UNIT USAHA SYARIAH|BANK OCBC NISP - UUS|SYONIDJ1|0289928
            CITIBANK, NA|CITIBANK|CITIIDJX|0310305
            KC JPMORGAN CHASE BANK, N.A|JPMORGAN BANK|CHASIDJX|0320308
            BANK OF AMERICA NA|BOA|BOFAID2X|0330301
            PT. BANK WINDU KENTJANA INTERNASIONAL, TBK|BANK WINDU KENTJANA|MCORIDJA|0360300
            PT. BANK ARTHA GRAHA INTERNASIONAL, TBK|BAG INTERNASIONAL|ARTGIDJA|0370028
            PT. BANK SUMITOMO MITSUI INDONESIA|SUMITOMO|SUNIIDJA|0450304
            PT. BANK DBS INDONESIA|DBS|DBSBIDJA|0460307
            PT. BANK RESONA PERDANIA|BANK RESONA|BPIAIDJA|0470300
            PT. BANK MIZUHO INDONESIA|BANK MIZUHO|MHCCIDJA|0480303
            STANDARD CHARTERED BANK|STANDCHARD|SCBLIDJX|0500306
            PT. BANK BTPN|BTPN|TAPEIDJ1|2130101
            PT. BANK VICTORIA SYARIAH|BANK VICTORIASYARIAH|SWAGIDJ1|4050072
            PT. BANK SYARIAH BRI|SYARIAH BRI|DJARIDJ1|4220051
            PT. BANK MEGA Tbk.|BANK MEGA|MEGAIDJA|4260121
            PT BANK BNI SYARIAH|BNI SYARIAH|SYNIIDJ1|4270027
            PT. BANK BUKOPIN Tbk.|BUKOPIN|BBUKIDJA|4410010
            PT. BANK SYARIAH MANDIRI Tbk.|BSM|BSMDIDJA|4510017
            PT. BANK BISNIS INTERNASIONAL|BANK BISNIS|BUSTIDJ1|4590011
            PT. BANK ANDARA|BANK ANDARA|RIPAIDJ1|4660019
            PT. BANK JASA JAKARTA|BANK JASA JAKARTA|JSABIDJ1|4720014
            PT. BANK KEB HANA INDONESIA|BANK KEB HANA|HNBNIDJA|4840017
            PT. BANK MNC INTERNASIONAL, TBK|MNC BANK|BUMIIDJA|4850010
            PT. BANK YUDHA BHAKTI|BANK YUDHA BHAKTI|YUDBIDJ1|4900012
            PT. BANK MITRANIAGA|BANK MITRANIAGA|MGABIDJ1|4910015
            PT. BANK RAKYAT INDONESIA AGRONIAGA, TBK|AGRONIAGA|AGTBIDJA|4940014
            PT. BANK SBI INDONESIA|BANK SBI|IDMOIDJ1|4980016
            PT. BANK ROYAL INDONESIA|BANK ROYAL|ROYBIDJ1|5010011
            PT. BANK NATIONALNOBU|BANK NATIONALNOBU|LFIBIDJ1|5030017
            PT. BANK MEGA SYARIAH|BANK MEGA SYARIAH|BUTGIDJ1|5060016
            PT. BANK INA PERDANA|BANK INA|INPBIDJ1|5130014
            PT. BANK PANIN SYARIAH|BANK PANIN SYARIAH|ARFAIDJ1|5170016
            PT. PRIMA MASTER BANK|PRIMA MASTER|PMASIDJ1|5200012
            PT. BANK SYARIAH BUKOPIN|BANK SYARIAH BUKOPIN|SDOBIDJ1|5210031
            PT. BANK SAHABAT SAMPOERNA|BANK SAMPOERNA|BDIPIDJ1|5230011
            PT. BANK DINAR INDONESIA|BANK DINAR|LMANIDJ1|5260010
            PT. BANK AMAR INDONESIA|BANK AMAR|LOMAIDJA|5310012
            PT. BANK KESEJAHTERAAN EKONOMI|BANK KESEJAHTERAAN|KSEBIDJ1|5350014
            PT. BANK BCA SYARIAH|BANK BCA SYARIAH|SYCAIDJ1|5360017
            PT. BANK ARTOS INDONESIA|BANK ARTOS|ATOSIDJA|5420012
            PT. BANK TABUNGAN PENSIUNAN NASIONAL SYARIAH|BANK BTPN SYARIAH|PUBAIDJ1|5470046
            PT. BANK MULTI ARTA SENTOSA|Bank MAS|MASBIDJ1|5480010
            PT. BANK MAYORA|BANK MAYORA|MAYOIDJA|5530012
            PT. BANK PUNDI INDONESIA, TBK|BANK PUNDI|EKSTIDJ1|5580017
            PT. CENTRATAMA NASIONAL BANK|BANK CNB|CNBAIDJ1|5590036
            PT. BANK FAMA INTERNATIONAL|BANK FAMA|FAMAIDJA|5620016
            PT. BANK MANDIRI TASPEN POS|BANK MANDIRI TASPEN POS|SIHBIDJ1|5640012
            PT. BANK VICTORIA INTERNATIONAL|BANK VICTORIA|VICTIDJ1|5660018
            PT. BANK HARDA INTERNATIONAL|BANK HARDA|HRDAIDJ1|5670011
            PT. BANK AGRIS|BANK AGRIS|AGSSIDJA|9450305
            PT. BANK MAYBANK SYARIAH INDONESIA|MAYBANK SYARIAH|MBBEIDJA|9470302
            PT. BANK CTBC INDONESIA|CTBC INDONESIA|CTCBIDJA|9490307
            PT. BANK COMMONWEALTH|BANK COMMONWEALTH|BICNIDJA|9500307
            TXT;

            $bankList = [];
            foreach (preg_split('/\r\n|\n|\r/', trim($bankLines)) as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $parts = array_map('trim', explode('|', $line));
                if (count($parts) < 4) {
                    continue;
                }
                $bankList[] = [
                    'nama' => $parts[0],
                    'singkat' => $parts[1],
                    'bic' => $parts[2],
                    'kode' => $parts[3],
                ];
            }

            $export = new \App\Exports\MultiPayrollMandiriExport(
                $data,
                $bankList,
                $companyAccount,
                $companyAlias,
                $reference,
                $payrollDate->format('Ymd'),
                route('payroll.mandiri.csv', [
                    'period' => sprintf('%04d-%02d', $year, $month)
                ]),
                (int)$payrollDate->format('d'),
                $year,
                $month
            );

            $fileName = "payroll_mandiri_{$month}-{$year}.xlsx";
            return Excel::download($export, $fileName);
        }

        if ($bankLower === 'bri') {
            $fileName = "payroll_bri_{$month}-{$year}.xlsx";
            return Excel::download(
                new PayrollExportBri($data, $fileName),
                $fileName
            );
        }

        // fallback
        return back()->with('error', 'Format bank tidak dikenali.');
    }


    public function filterByDepartment(Request $req)
    {
        $year  = (int) ($req->input('year', now()->year));
        $month = (int) ($req->input('month', now()->month));
        $depIdRaw = $req->input('department_id', null);

        \Log::info('filterByDepartment called', ['year' => $year, 'month' => $month, 'department_id_raw' => $depIdRaw]);

        $depId = null;
        if ($depIdRaw !== null && trim((string)$depIdRaw) !== '') {
            $depId = is_numeric($depIdRaw) ? intval($depIdRaw) : null;
        }

        try {
            $query = Csalary::with('user')
                ->where('period_year', $year)
                ->where('period_month', $month)
                ->orderBy('user_id');

            $userIds = [];
            if ($depId !== null) {
                $userIds = muser::where('niddept', $depId)->pluck('nid')->toArray();
                if (count($userIds) === 0) {
                    $rows = collect([]);
                } else {
                    $query->whereIn('user_id', $userIds);
                    $rows = $query->get();
                }
            } else {
                // if depIdRaw is non-numeric string (e.g. 'CK'), try to match it directly against muser.niddept
                if ($depIdRaw !== null && trim((string)$depIdRaw) !== '') {
                    // treat depIdRaw as string code/cname fallback
                    $userIds = muser::where('niddept', $depIdRaw)->pluck('nid')->toArray();

                    if (count($userIds) === 0) {
                        // try finding by department cname
                        $dep = Mdepartment::where('cname', $depIdRaw)->orWhere('code', $depIdRaw)->first();
                        if ($dep) {
                            $userIds = muser::where('niddept', $dep->id)->pluck('nid')->toArray();
                        }
                    }

                    if (count($userIds) === 0) {
                        $rows = collect([]);
                    } else {
                        $query->whereIn('user_id', $userIds);
                        $rows = $query->get();
                    }
                } else {
                    $rows = $query->get();
                }
            }

            $data = $rows->map(function ($model) {
                $user = $model->user;
                $jabatan = 'Crew';
                if ($user) {
                    if (!empty($user->fhrd)) {
                        $jabatan = 'HRD';
                    } elseif (!empty($user->fadmin)) {
                        $jabatan = 'Captain';
                    } elseif (!empty($user->fsuper)) {
                        $jabatan = 'Supervisor';
                    } elseif (!empty($user->jabatan)) {
                        $jabatan = $user->jabatan;
                    }
                }

                $gaji_harian   = (float) $model->gaji_harian;
                $gaji_pokok    = (float) $model->gaji_pokok;
                $jumlah_masuk  = (int)   $model->jumlah_masuk;
                $jenisGaji = strtolower($model->jenis_gaji ?? 'pokok');
                $displayGaji = $jenisGaji === 'harian' ? $gaji_harian : $gaji_pokok;

                $fmt = fn ($n) => 'Rp ' . number_format((float)$n, 2, ',', '.');

                return [
                    'id' => $model->id,
                    'user_id' => $model->user_id,
                    'department_id' => $user ? (string) ($user->niddept ?? '') : '',
                    'user_name' => $user ? ($user->cname ?? '-') : ('(no-user-' . $model->user_id . ')'),
                    'jabatan' => $jabatan,
                    'jumlah_masuk' => $jumlah_masuk,
                    'gaji' => $fmt($displayGaji),
                    'gaji_harian' => $displayGaji,
                    'gaji_pokok' => $fmt($gaji_pokok),
                    'tunjangan_makan' => $fmt($model->tunjangan_makan),
                    'tunjangan_jabatan' => $fmt($model->tunjangan_jabatan),
                    'tunjangan_transport' => $fmt($model->tunjangan_transport),
                    'tunjangan_luar_kota' => $fmt($model->tunjangan_luar_kota),
                    'tunjangan_masa_kerja' => $fmt($model->tunjangan_masa_kerja),
                    'gaji_lembur' => $fmt($model->gaji_lembur),
                    'tabungan_diambil' => $fmt($model->tabungan_diambil),
                    'potongan_lain' => $fmt($model->potongan_lain),
                    'potongan_tabungan' => $fmt($model->potongan_tabungan),
                    'potongan_keterlambatan' => $fmt($model->potongan_keterlambatan),
                    'total_gaji' => $fmt($model->total_gaji),
                    'note' => $model->note,
                    'keterangan_absensi' => $model->keterangan_absensi,
                    'reasonedit' => $model->reasonedit,
                    'status' => $model->status,
                ];
            })->toArray();

            // render partial rows (blade expects rows only)
            $html = view('penggajian.components.table_payroll_rows', ['data' => $data])->render();

            // prepare 'users' array grouped by department for client logging / counting
            $usersGrouped = [];
            foreach ($data as $row) {
                $did = (string) ($row['department_id'] ?? '(no-dept)');
                $usersGrouped[$did] = $usersGrouped[$did] ?? [];
                $usersGrouped[$did][] = $row['user_name'] ?? '(no-name)';
            }

            // respond with html, users_by_dept and user_ids (flat)
            $flatUserIds = array_values(array_unique(array_map(function ($r) {
                return (string) ($r['user_id'] ?? '');
            }, $data)));

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => count($data),
                'users_by_dept' => $usersGrouped,
                'user_ids' => $flatUserIds,
            ], 200);

        } catch (\Throwable $e) {
            \Log::error('filterByDepartment failed', [
                'err' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'depIdRaw' => $depIdRaw
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Server error saat memproses filter. Cek log server.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function exportReport(Request $request)
    {
        $request->validate([
            'bulan' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'department_id' => ['nullable'],
        ]);

        [$year, $month] = explode('-', $request->input('bulan'));
        $year = (int) $year;
        $month = (int) $month;

        $departmentInput = $request->input('department_id', null);
        $departmentName = null; // what we will pass to PayrollExport
        $userIds = null;

        if (!is_null($departmentInput) && trim((string)$departmentInput) !== '') {
            // try numeric => treat as Mdepartment.nid (your schema uses nid)
            if (is_numeric($departmentInput)) {
                $dep = Mdepartment::where('nid', intval($departmentInput))->first();
                if ($dep) {
                    $departmentNameRaw = trim((string)$dep->cname);
                    // get users where niddept == dep.nid
                    $userIds = muser::where('niddept', $dep->nid)->pluck('nid')->toArray();
                } else {
                    // fallback: maybe muser.niddept stores that numeric value directly
                    $userIds = muser::where('niddept', intval($departmentInput))->pluck('nid')->toArray();
                    $departmentNameRaw = (string)$departmentInput;
                }
            } else {
                // departmentInput is string: try match Mdepartment cname or code
                $dep = Mdepartment::where('cname', $departmentInput)
                                  ->orWhere('code', $departmentInput)
                                  ->first();
                if ($dep) {
                    $departmentNameRaw = trim((string)$dep->cname);
                    $userIds = muser::where('niddept', $dep->nid)->pluck('nid')->toArray();
                } else {
                    // fallback: maybe muser.niddept stores a code like "CK" or "BACKOFFICE"
                    $departmentNameRaw = trim((string)$departmentInput);
                    $userIds = muser::where('niddept', $departmentInput)->pluck('nid')->toArray();
                }
            }

            if (empty($userIds)) {
                return back()->with('error', 'Tidak ada karyawan pada departemen yang dipilih.');
            }

            // normalize department title:
            // if code/name equals "CK" (case-insensitive) -> use "CENTRAL KITCHEN"
            $rawUpper = strtoupper(trim((string)($departmentNameRaw ?? '')));

            // CK → CENTRAL KITCHEN MATAHATI CAFE
            if ($rawUpper === 'CK' || $rawUpper === 'CENTRAL KITCHEN') {
                $departmentName = 'CENTRAL KITCHEN MATAHATI CAFE';
            } else {
                // Departemen lain → {DEPT} MATAHATI CAFE (uppercase)
                $departmentName = $rawUpper !== ''
                    ? ($rawUpper . ' MATAHATI CAFE')
                    : null;
            }
        }

        // build query for csalary period
        $query = Csalary::with('user')
            ->where('period_year', $year)
            ->where('period_month', $month)
            ->orderBy('user_id');

        if (is_array($userIds) && count($userIds) > 0) {
            $query->whereIn('user_id', $userIds);
        }

        $data = $query->get();

        if ($data->count() === 0) {
            return back()->with('error', 'Tidak ada data payroll untuk periode & departemen tersebut.');
        }

        $fileName = sprintf('Report-Gaji-%04d-%02d.xlsx', $year, $month);

        // pass departmentName (nullable) as 4th parameter — PayrollExport sudah men-supportnya
        return Excel::download(new \App\Exports\PayrollExport($data, $month, $year, $departmentName), $fileName);
    }
}
