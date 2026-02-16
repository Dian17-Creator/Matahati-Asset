<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\muser;
use App\Models\Tusercontract;
use App\Models\MasterSchedule;
use App\Models\UserSchedule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Jobs\CalculatePayrollJob;
use DatePeriod;
use DateInterval;
use DateTime;

class ScheduleController extends Controller
{
    /**
     * 🔹 Halaman utama: Daftar shift, jadwal, dan kontrak kerja
     */
    public function index()
    {
        $authUser = auth()->user();

        // Hanya superadmin bisa lihat semua shift
        $masters = [];
        if ($authUser->fsuper == 1) {
            $masters = MasterSchedule::orderBy('cname')->get();
        }

        // Filter user berdasarkan departemen
        $query = muser::with('department')->orderBy('cname');

        // ✅ HR lihat semua
        // ✅ Super & Captain hanya departemen sendiri
        if (!$authUser->fhrd) {
            $query->where('niddept', $authUser->niddept);
        }

        $users = $query->get();

        // Ambil semua kontrak kerja
        $contracts = Tusercontract::with('user')
            ->orderBy('dstart', 'desc')
            ->get();

        return view('schedule.index', compact('masters', 'users', 'authUser', 'contracts'));
    }

    /* ==========================================================
       🔸 CRUD SHIFT MASTER
    ========================================================== */

    public function store(Request $request)
    {
        $request->validate([
            'cname'   => 'required|string|max:255',
            'dstart'  => 'required|date_format:H:i',
            'dend'    => 'required|date_format:H:i|after:dstart',
            'dstart2' => 'nullable|date_format:H:i',
            'dend2'   => 'nullable|date_format:H:i|after:dstart2'
        ]);

        MasterSchedule::create([
            'cname'    => $request->cname,
            'dstart'   => $request->dstart,
            'dend'     => $request->dend,
            'dstart2'  => $request->dstart2,
            'dend2'    => $request->dend2,
            'dcreated' => now()
        ]);

        return back()->with('success', '✅ Shift berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'cname'   => 'required|string|max:255',
            'dstart'  => 'required|date_format:H:i',
            'dend'    => 'required|date_format:H:i|after:dstart',
            'dstart2' => 'nullable|date_format:H:i',
            'dend2'   => 'nullable|date_format:H:i'
        ]);

        // ================= VALIDASI SPLIT =================
        if ($request->filled('dstart2') || $request->filled('dend2')) {

            // wajib lengkap
            if (!$request->filled('dstart2') || !$request->filled('dend2')) {
                return back()
                    ->withErrors(['split' => 'Jam split harus diisi lengkap'])
                    ->with('edit_shift_id', $id)
                    ->withInput();
            }

            $s1Start = strtotime($request->dstart);
            $s1End   = strtotime($request->dend);
            $s2Start = strtotime($request->dstart2);
            $s2End   = strtotime($request->dend2);

            // ❗ 1️⃣ dend2 harus > dstart2
            if ($s2End <= $s2Start) {
                return back()
                    ->withErrors([
                        'split' => 'Jam selesai split harus lebih besar dari jam mulai split'
                    ])
                    ->with('edit_shift_id', $id)
                    ->withInput();
            }

            // ❗ 2️⃣ tidak boleh overlap shift utama
            if ($s2Start < $s1End && $s2End > $s1Start) {
                return back()
                    ->withErrors([
                        'split' => 'Jam split tidak boleh overlap dengan shift utama'
                    ])
                    ->with('edit_shift_id', $id)
                    ->withInput();
            }
        }

        $shift = MasterSchedule::findOrFail($id);
        $shift->update([
            'cname'   => $request->cname,
            'dstart'  => $request->dstart,
            'dend'    => $request->dend,
            'dstart2' => $request->dstart2,
            'dend2'   => $request->dend2
        ]);

        return back()->with('success', '✅ Shift berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $sched = MasterSchedule::findOrFail($id);
        $sched->delete();

        return redirect()->back()->with('success', '🗑 Shift berhasil dihapus.');
    }

    /* ==========================================================
       🔸 ASSIGN JADWAL PEGAWAI
    ========================================================== */

    public function showAssignForm(Request $request)
    {
        $request->validate([
            'nuserid' => 'required|exists:muser,nid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $nuserid = $request->nuserid;
        $start = new DateTime($request->start_date);
        $end = new DateTime($request->end_date);
        $end->modify('+1 day');

        $period = new DatePeriod($start, new DateInterval('P1D'), $end);
        $masters = MasterSchedule::orderBy('cname')->get();
        $user = muser::find($nuserid);

        $existingSchedules = UserSchedule::where('nuserid', $nuserid)
            ->whereBetween('dwork', [$request->start_date, $request->end_date])
            ->pluck('nidsched', 'dwork')
            ->toArray();

        return view('schedule.assign', compact('user', 'masters', 'period', 'existingSchedules'));
    }

    public function assignSchedule(Request $request)
    {
        $request->validate([
            'nuserid' => 'required|exists:muser,nid',
            'dates'   => 'required|array'
        ]);

        foreach ($request->dates as $day => $schedId) {

            // Jika kosong → hapus
            if (empty($schedId)) {
                UserSchedule::where('nuserid', $request->nuserid)
                    ->where('dwork', $day)
                    ->delete();
                continue;
            }

            $master = MasterSchedule::find($schedId);
            if (!$master) {
                continue;
            }

            // ✅ SIMPAN 1 BARIS SAJA (ISI dstart2 & dend2)
            UserSchedule::updateOrCreate(
                [
                    'nuserid' => $request->nuserid,
                    'dwork'   => $day
                ],
                [
                    'dstart'     => $master->dstart,
                    'dend'       => $master->dend,
                    'dstart2'    => $master->dstart2, // 🔥 INI
                    'dend2'      => $master->dend2,   // 🔥 INI
                    'nidsched'   => $schedId,
                    'cschedname' => $master->cname
                ]
            );
        }

        return redirect()
            ->route('schedule.index')
            ->with('success', '✅ Jadwal berhasil disimpan.');
    }

    /* ==========================================================
       🔸 API UNTUK MOBILE
    ========================================================== */

    public function apiUserSchedule($userId)
    {
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID required'
            ], 400);
        }

        $rows = UserSchedule::where('nuserid', $userId)
            ->orderBy('dwork', 'asc')
            ->orderBy('dstart', 'asc')
            ->get();

        if ($rows->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        // 🔹 GROUP PER TANGGAL
        $grouped = $rows->groupBy('dwork');

        $data = $grouped->map(function ($items, $date) {

            $sessions = [];

            foreach ($items as $row) {

                // Session 1 (WAJIB)
                if ($row->dstart && $row->dend) {
                    $sessions[] = [
                        'start' => substr($row->dstart, 0, 5),
                        'end'   => substr($row->dend, 0, 5),
                    ];
                }

                // Session 2 (OPSIONAL – SPLIT)
                if ($row->dstart2 && $row->dend2) {
                    $sessions[] = [
                        'start' => substr($row->dstart2, 0, 5),
                        'end'   => substr($row->dend2, 0, 5),
                    ];
                }
            }

            return [
                'date'      => $date,
                'shiftName' => $items->first()->cschedname,
                'sessions'  => $sessions
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function apiTodayShift($userId)
    {
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User ID required'
            ], 400);
        }

        // Pakai timezone lokal
        $today = \Carbon\Carbon::now('Asia/Jakarta')->toDateString();

        // Ambil SATU baris (karena sekarang 1 row = 1 hari)
        $row = UserSchedule::where('nuserid', $userId)
            ->where('dwork', $today)
            ->first();

        if (!$row) {
            return response()->json([
                'success' => true,
                'data' => null
            ]);
        }

        $sessions = [];

        // ✅ Session 1 (WAJIB)
        if ($row->dstart && $row->dend) {
            $sessions[] = [
                'start' => substr($row->dstart, 0, 5),
                'end'   => substr($row->dend, 0, 5),
            ];
        }

        // ✅ Session 2 (OPSIONAL – SPLIT)
        if ($row->dstart2 && $row->dend2) {
            $sessions[] = [
                'start' => substr($row->dstart2, 0, 5),
                'end'   => substr($row->dend2, 0, 5),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'date'      => $today,
                'shiftName' => $row->cschedname,
                'sessions'  => $sessions
            ]
        ]);
    }

    public function storeContract(Request $request)
    {
        $request->validate([
            'nuserid'       => 'required|exists:muser,nid',
            'dstart'        => 'required|date',
            'dend'          => 'required|date|after:dstart',
            'dstart2'       => 'nullable|date',
            'dend2'         => 'nullable|date|after:dstart2',
            'nterm'         => 'required|in:3,6,12',
            'ctermtype'     => 'required|in:probation,promotion,evaluation',
            'cnotes'        => 'nullable|string|max:255',
        ]);

        // Clean nominal
        $nominal = preg_replace('/[^0-9]/', '', $request->nominal_gaji) ?: 0;
        $nominal = (float)$nominal;

        // Nonaktifkan kontrak aktif sebelumnya
        \DB::table('tusercontract')
            ->where('nuserid', $request->nuserid)
            ->where('cstatus', 'active')
            ->update(['cstatus' => 'terminated']);

        // Insert kontrak baru
        $contract = Tusercontract::create([
            'nuserid'      => $request->nuserid,
            'dstart'       => $request->dstart,
            'dend'         => $request->dend,
            'nterm'        => $request->nterm,
            'ctermtype'    => $request->ctermtype,
            'cnotes'       => $request->cnotes,
        ]);

        $year  = now()->year;
        $month = now()->month;

        // Hitung payroll SEKETIKA (tanpa queue)
        (new CalculatePayrollJob(
            $contract->nuserid,
            $year,
            $month,
            true // force overwrite
        ))->handle();

        return redirect()
            ->route('schedule.index')
            ->with('success', 'Kontrak kerja berhasil ditambahkan dan payroll tersinkron!');
    }

    // Update kontrak
    public function updateContract(Request $request, $id)
    {
        $contract = Tusercontract::findOrFail($id);

        $request->validate([
            'nuserid'      => 'required|exists:muser,nid',
            'dstart'       => 'required|date',
            'dend'         => 'required|date|after:dstart',
            'nterm'        => 'required|in:3,6,12',
            'ctermtype'    => 'required|in:probation,promotion,evaluation',
            'cnotes'       => 'nullable|string|max:255',
        ]);

        // Clean nominal
        $nominal = preg_replace('/[^0-9]/', '', $request->nominal_gaji) ?: 0;
        $nominal = (float)$nominal;

        // Update kontrak
        $contract->update([
            'nuserid'      => $request->nuserid,
            'dstart'       => $request->dstart,
            'dend'         => $request->dend,
            'nterm'        => $request->nterm,
            'ctermtype'    => $request->ctermtype,
            'cnotes'       => $request->cnotes,
        ]);

        $year  = now()->year;
        $month = now()->month;

        // Hitung payroll langsung
        (new CalculatePayrollJob(
            $contract->nuserid,
            $year,
            $month,
            true
        ))->handle();

        return redirect()
            ->route('schedule.index')
            ->with('success', 'Kontrak berhasil diperbarui & payroll tersinkron!');
    }
    // Hapus kontrak
    public function destroyContract($id)
    {
        $contract = Tusercontract::findOrFail($id);
        $contract->delete();

        return redirect()->route('schedule.index')->with('success', 'Kontrak kerja berhasil dihapus!');
    }
    // helper Overlap
    private function timeOverlap($startA, $endA, $startB, $endB)
    {
        return $startA < $endB && $endA > $startB;
    }

}
