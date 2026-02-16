<?php

namespace App\Http\Controllers;

use App\Models\MscanForgot;
use Illuminate\Http\Request;

class MscanForgotController extends Controller
{
    /**
     * 📄 List lupa absen
     */
    public function index(Request $request)
    {
        $status = $request->get('status'); // pending / approved / rejected

        $query = MscanForgot::with('user');

        if ($status) {
            $query->where('cstatus', $status);
        }

        $logs = $query
            ->orderByDesc('nid')
            ->paginate(10);

        return view('backoffice.forgot.index', compact('logs', 'status'));
    }

    /**
     * 🔍 Detail lupa absen
     */
    public function show($id)
    {
        $log = MscanForgot::with('user')->findOrFail($id);

        return view('backoffice.forgot.show', compact('log'));
    }

    /**
     * ✅❌ Approve / Reject (HRD ONLY)
     */
    public function approve(Request $request, $id)
    {
        // 🔒 HRD ONLY
        if (auth()->user()->fhrd != 1) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'reason' => 'nullable|string|max:255',
        ]);

        $log = MscanForgot::findOrFail($id);

        $log->update([
            'cstatus'   => $request->status,
            'nadminid'  => auth()->id(),
            'dapproved' => now(),
            // optional: simpan alasan reject terpisah
            'creject_reason' => $request->status === 'rejected'
                ? $request->reason
                : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->status === 'approved'
                ? 'Lupa absen disetujui oleh HRD'
                : 'Lupa absen ditolak oleh HRD',
        ]);
    }
}
