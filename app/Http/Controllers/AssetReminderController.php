<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetReminder;

class AssetReminderController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetReminder::query();

        if ($request->asset_type) {
            $query->where('asset_type', $request->asset_type);
        }

        $reminders = $query
            ->with(['assetQr.subKategori', 'assetNoQr'])
            ->orderBy('reminder_date', 'asc')
            ->get()
            ->map(function ($reminder) {
                $assetName = '-';
                if ($reminder->asset_type === 'QR') {
                    $assetName = $reminder->assetQr->cnama ?? optional(optional($reminder->assetQr)->subKategori)->cnama ?? '-';
                } elseif ($reminder->asset_type === 'NOQR') {
                    $assetName = optional($reminder->assetNoQr)->cnama ?? '-';
                }

                $reminder->asset_name = $assetName;
                return $reminder;
            });

        return response()->json([
            'success' => true,
            'data' => $reminders
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_type'      => 'required|in:QR,NOQR',
            'reminder_date'   => 'required|date',
            'note'            => 'nullable|string',

            'asset_qr_id'     => 'nullable|integer',
            'asset_noqr_code' => 'nullable|string|max:10',
        ]);

        // Validasi manual
        if (
            $request->asset_type == 'QR'
            && empty($request->asset_qr_id)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Asset QR wajib dipilih'
            ], 422);
        }

        if (
            $request->asset_type == 'NOQR'
            && empty($request->asset_noqr_code)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Asset NOQR wajib dipilih'
            ], 422);
        }

        $reminder = AssetReminder::create([
            'asset_type'      => $request->asset_type,
            'asset_qr_id'     => $request->asset_qr_id,
            'asset_noqr_code' => $request->asset_noqr_code,
            'reminder_date'   => $request->reminder_date,
            'note'            => $request->note,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reminder berhasil disimpan',
            'data'    => $reminder
        ]);
    }

    public function show($id)
    {
        $reminder = AssetReminder::find($id);

        if (!$reminder) {
            return response()->json([
                'success' => false,
                'message' => 'Reminder tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $reminder
        ]);
    }

    public function destroy($id)
    {
        $reminder = AssetReminder::find($id);

        if (!$reminder) {
            return response()->json([
                'success' => false,
                'message' => 'Reminder tidak ditemukan'
            ], 404);
        }

        $reminder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reminder berhasil dihapus'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'asset_type'      => 'required|in:QR,NOQR',
            'reminder_date'   => 'required|date',
            'note'            => 'nullable|string',

            'asset_qr_id'     => 'nullable|integer',
            'asset_noqr_code' => 'nullable|string|max:10',
        ]);

        $reminder = AssetReminder::find($id);

        if (!$reminder) {
            return response()->json([
                'success' => false,
                'message' => 'Reminder tidak ditemukan'
            ], 404);
        }

        if (
            $request->asset_type == 'QR'
            && empty($request->asset_qr_id)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Asset QR wajib dipilih'
            ], 422);
        }

        if (
            $request->asset_type == 'NOQR'
            && empty($request->asset_noqr_code)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Asset NOQR wajib dipilih'
            ], 422);
        }

        $reminder->update([
            'asset_type'      => $request->asset_type,
            'asset_qr_id'     => $request->asset_qr_id,
            'asset_noqr_code' => $request->asset_noqr_code,
            'reminder_date'   => $request->reminder_date,
            'note'            => $request->note,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reminder berhasil diupdate',
            'data'    => $reminder
        ]);
    }
}
