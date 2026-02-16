<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\mrequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class mrequestController extends Controller
{
    /**
     * 🔹 Ambil semua data request
     * - HRD bisa lihat semua
     * - Captain bisa lihat pending di levelnya
     * - User hanya lihat miliknya sendiri
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'hrd') {
            $requests = mrequest::with(['user'])
                ->orderBy('dcreated', 'desc')
                ->get();
        } elseif ($user->role === 'captain') {
            $requests = mrequest::with(['user'])
                ->where('cstatus', 'pending')
                ->orderBy('dcreated', 'desc')
                ->get();
        } else {
            // pegawai biasa
            $requests = mrequest::with(['user'])
                ->where('nuserId', $user->nid)
                ->orderBy('dcreated', 'desc')
                ->get();
        }

        return response()->json($requests);
    }

    /**
     * 🔹 Captain Approve / Reject
     */
    public function approveCaptain(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:approved,rejected',
            ]);

            $req = mrequest::find($id);
            if (!$req) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
            }

            // 🔍 Tambahkan log debug
            Log::info('Approve Captain Debug', [
                'auth_id' => Auth::id(),
                'guard' => Auth::getDefaultDriver(),
                'user_class' => Auth::user() ? get_class(Auth::user()) : null,
                'user_data' => Auth::user(),
                'target_request' => $req->nid,
            ]);

            $req->update([
                'cstatus' => $request->status,
                'nadminid' => Auth::id(),
                'dupdated' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Captain telah ' . $request->status . ' permintaan ini',
            ]);
        } catch (\Throwable $e) {
            Log::error('Approve Captain Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 🔹 HRD Approve / Reject (langsung, tanpa menunggu Captain)
     */
    public function approveHrd(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:approved,rejected',
            ]);

            $req = mrequest::find($id);
            if (!$req) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
            }

            // 🔍 Tambahkan log debug
            Log::info('Approve HRD Debug', [
                'auth_id' => Auth::id(),
                'guard' => Auth::getDefaultDriver(),
                'user_class' => Auth::user() ? get_class(Auth::user()) : null,
                'user_data' => Auth::user(),
                'target_request' => $req->nid,
            ]);

            $req->update([
                'chrdstat' => $request->status,
                'nhrdid' => Auth::id(),
                'duphrd' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'HRD telah ' . $request->status . ' permintaan ini',
            ]);
        } catch (\Throwable $e) {
            Log::error('Approve HRD Error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        // Cari data izin berdasarkan ID request
        $izin = \DB::table('mrequest')->where('nid', $id)->first();

        if (!$izin) {
            abort(404, 'Data izin tidak ditemukan.');
        }

        // Gunakan NID user (pegawai) untuk redirect
        $userId = $izin->nuserid ?? null;

        if (!$userId) {
            abort(404, 'User ID tidak ditemukan pada request ini.');
        }

        // 🔁 Redirect ke halaman request milik user tersebut
        return redirect()->to(url("/backoffice/requests/" . $userId));
    }

}
