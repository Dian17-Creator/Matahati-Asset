<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Mail\NotifikasiEmail;

class NotifikasiController extends Controller
{
    // 🔹 1. TAMPILAN WEB — hanya tampilkan notifikasi, tidak kirim email
    public function index()
    {
        $user = auth()->user();
        $isHrd = $user->fhrd == 1;
        $userDept = $user->niddept;

        $today = \Carbon\Carbon::today();
        $next30Days = \Carbon\Carbon::today()->addDays(30);

        // Ambil izin pending
        $izinPending = \DB::table('mrequest')
            ->join('muser', 'mrequest.nuserid', '=', 'muser.nid')
            ->where('mrequest.chrdstat', 'pending')
            ->where('mrequest.cstatus', 'pending')
            ->when(!$isHrd, fn ($q) => $q->where('muser.niddept', $userDept))
            ->select('mrequest.nid', 'muser.cname as nama', 'mrequest.nuserid as nuserid', 'mrequest.dcreated as tanggal')
            ->get();

        // Ambil absen manual pending
        $absenPending = \DB::table('mscan_manual')
            ->join('muser', 'mscan_manual.nuserId', '=', 'muser.nid')
            ->where('mscan_manual.chrdstat', 'pending')
            ->where('mscan_manual.cstatus', 'pending')
            ->when(!$isHrd, fn ($q) => $q->where('muser.niddept', $userDept))
            ->select('mscan_manual.nid', 'muser.cname as nama', 'muser.nid as nuserid', 'mscan_manual.dscanned as tanggal')
            ->get();

        //Lupa Absen Pending
        $forgotPending = \DB::table('mscan_forgot')
            ->join('muser', 'mscan_forgot.nuserId', '=', 'muser.nid')
            ->where('mscan_forgot.chrdstat', 'pending')
            ->where('mscan_forgot.cstatus', 'pending')
            ->when(!$isHrd, fn ($q) => $q->where('muser.niddept', $userDept))
            ->select(
                'mscan_forgot.nid',
                'muser.cname as nama',
                'muser.nid as nuserid',
                'mscan_forgot.dscanned as tanggal'
            )
            ->get();


        // Ambil kontrak habis 30 hari lagi
        $contractsExpiring = \DB::table('tusercontract')
            ->join('muser', 'tusercontract.nuserid', '=', 'muser.nid')
            ->whereBetween('tusercontract.dend', [$today, $next30Days])
            ->where('tusercontract.cstatus', 'active')
            ->when(!$isHrd, fn ($q) => $q->where('muser.niddept', $userDept))
            ->select('tusercontract.nid', 'muser.cname as nama', 'tusercontract.nuserid as nuserid', 'tusercontract.dend as tanggal_akhir')
            ->get();

        // Ambil registrasi wajah yang belum aktif (belum ada di mface_scan)
        // $facePending = DB::table('tuserfaces')
        //     ->join('muser', 'tuserfaces.nuserid', '=', 'muser.nid')
        //     ->leftJoin('mface_scan', 'tuserfaces.nuserid', '=', 'mface_scan.nuserid')
        //     ->whereNull('mface_scan.nuserid') // BELUM APPROVED
        //     ->when(!$isHrd, fn ($q) => $q->where('muser.niddept', $userDept))
        //     ->select(
        //         'tuserfaces.nid',
        //         'muser.cname as nama',
        //         'muser.nid as nuserid',
        //         'tuserfaces.dcreated as tanggal'
        //     )
        //     ->get();

        // Gabungkan semua notifikasi
        $notifications = collect();

        foreach ($izinPending as $izin) {
            $notifications->push([
                'message' => '📝 Izin dari ' . $izin->nama . ' menunggu approval',
                'time' => $izin->tanggal,
                'type' => 'izin',
                'url' => url('/backoffice/requestcard/' . $izin->nuserid)
            ]);
        }

        foreach ($absenPending as $absen) {
            $notifications->push([
                'message' => '📋 Absen manual dari ' . $absen->nama . ' menunggu approval',
                'time' => $absen->tanggal,
                'type' => 'absen',
                'url' => url('/backoffice/logs/' . $absen->nuserid. '?source=manual&status=pending')
            ]);
        }

        foreach ($forgotPending as $forgot) {
            $notifications->push([
                'message' => '🕒 Lupa absen dari ' . $forgot->nama . ' menunggu approval HRD',
                'time'    => $forgot->tanggal,
                'type'    => 'forgot',
                'url' => url('/backoffice/logs/' . $forgot->nuserid . '?source=forgot&status=pending')
            ]);
        }

        foreach ($contractsExpiring as $kontrak) {
            $remainingDays = \Carbon\Carbon::parse($kontrak->tanggal_akhir)->diffInDays($today);
            $notifications->push([
                'message' => '⏳ Kontrak kerja ' . $kontrak->nama . ' akan berakhir dalam ' . $remainingDays . ' hari (' .
                    \Carbon\Carbon::parse($kontrak->tanggal_akhir)->format('d M Y') . ')',
                'time' => $kontrak->tanggal_akhir,
                'type' => 'contract',
                'url' => url('/schedule') // atau bisa ke halaman detail kontrak kalau kamu punya route-nya
            ]);
        }

        // foreach ($facePending as $face) {
        //     $notifications->push([
        //         'message' => '📷 Registrasi wajah dari ' . $face->nama . ' menunggu approval',
        //         'time'    => $face->tanggal,
        //         'type'    => 'face',
        //         'url'     => url('/faces') // atau /faces/approval
        //     ]);
        // }

        $notifications = $notifications->sortByDesc('time')->values();

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count()
        ]);
    }
    // 🔹 2. UNTUK CRON — kirim email HRD jika ada notifikasi
    public function sendEmails(Request $request)
    {
        // 🔒 Cek token dari .env
        if ($request->input('token') !== env('CRON_TOKEN')) {
            abort(403, 'Forbidden');
        }

        $today = Carbon::today();
        $next30Days = Carbon::today()->addDays(30);

        // ambil semua pending & expiring seperti di index()
        $izin = DB::table('mrequest')
            ->join('muser', 'mrequest.nuserid', '=', 'muser.nid')
            ->where('mrequest.chrdstat', 'pending')
            ->where('mrequest.cstatus', 'pending')
            ->select('muser.cname as nama', 'mrequest.creason as alasan', 'mrequest.dcreated as tanggal')
            ->get();

        $absen = DB::table('mscan_manual')
            ->join('muser', 'mscan_manual.nuserId', '=', 'muser.nid')
            ->where('mscan_manual.chrdstat', 'pending')
            ->where('mscan_manual.cstatus', 'pending')
            ->select('muser.cname as nama', 'mscan_manual.creason as alasan', 'mscan_manual.dscanned as tanggal')
            ->get();

        $forgot = DB::table('mscan_forgot')
            ->join('muser', 'mscan_forgot.nuserId', '=', 'muser.nid')
            ->where('mscan_forgot.chrdstat', 'pending')
            ->where('mscan_forgot.cstatus', 'pending')
            ->select(
                'muser.cname as nama',
                'mscan_forgot.creason as alasan',
                'mscan_forgot.dscanned as tanggal'
            )
            ->get();


        $contracts = DB::table('tusercontract')
            ->join('muser', 'tusercontract.nuserid', '=', 'muser.nid')
            ->whereBetween('tusercontract.dend', [$today, $next30Days])
            ->where('tusercontract.cstatus', 'active')
            ->select('muser.cname as nama', 'tusercontract.dend as tanggal_akhir')
            ->get();


        // $facePending = DB::table('tuserfaces')
        // ->join('muser', 'tuserfaces.nuserid', '=', 'muser.nid')
        // ->leftJoin('mface_scan', 'tuserfaces.nuserid', '=', 'mface_scan.nuserid')
        // ->whereNull('mface_scan.nuserid')
        // ->select('muser.cname as nama')
        // ->distinct()
        // ->get();

        $notifications = collect();

        foreach ($izin as $i) {
            $notifications->push("📝 Izin dari {$i->nama} — alasan: {$i->alasan}");
        }
        foreach ($absen as $a) {
            $notifications->push("📋 Absen manual dari {$a->nama} — alasan: {$a->alasan}");
        }
        foreach ($contracts as $c) {
            $remaining = Carbon::parse($c->tanggal_akhir)->diffInDays($today);
            $notifications->push("⏳ Kontrak {$c->nama} berakhir dalam {$remaining} hari (" .
                Carbon::parse($c->tanggal_akhir)->format('d M Y') . ")");
        }
        // foreach ($facePending as $face) {
        //     $notifications->push("📷 Registrasi wajah dari {$face->nama} menunggu approval");
        // }

        // Jika tidak ada notif, skip
        if ($notifications->isEmpty()) {
            \Log::info('⏸️ Tidak ada notifikasi baru untuk dikirim.');
            return response()->json(['status' => 'empty']);
        }

        // Kirim email
        $hrdEmail = env('HRD_EMAIL', 'matahati.hrd@gmail.com');

        try {
            Mail::to($hrdEmail)->send(new NotifikasiEmail($notifications));
            \Log::info('✅ Notifikasi email dikirim ke ' . $hrdEmail . ' (count: ' . $notifications->count() . ')');
        } catch (\Exception $e) {
            \Log::error('❌ Gagal kirim email: ' . $e->getMessage());
        }

        return response()->json(['status' => 'sent', 'count' => $notifications->count()]);
    }
}
