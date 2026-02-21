<?php

namespace App\Http\Controllers;

use App\Models\MassetTrans;
use App\Models\MassetQr;
use App\Models\MassetNoQr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MassetTransController extends Controller
{
    /**
     * =========================
     * VIEW TRANSAKSI
     * =========================
     */
    public function index()
    {
        return view('Asset.components.master_asset_transaksi', [
            'transaksi' => MassetTrans::with([
                'subKategori.kategori',
                'department',
            ])->orderByDesc('nid')->get(),
        ]);
    }

    /**
     * =========================
     * STORE TRANSAKSI
     * =========================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ckode_asset' => 'required|string',   // dari dropdown
            'dbeli'       => 'required|date',
            'cmerk'       => 'nullable|string|max:50',
            'dgaransi'    => 'nullable|date',
            'nhrgbeli'    => 'nullable|numeric',
            'ccatatan'    => 'nullable|string|max:100',

            // FOTO
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request) {

            /**
             * ======================
             * CARI ASSET (QR / NON QR)
             * ======================
             */
            $qr = MassetQr::with(['subKategori', 'department'])
                ->where('cqr', $validated['ckode_asset'])
                ->first();

            $nonQr = null;

            if (! $qr) {
                $nonQr = MassetNoQr::with(['subKategori', 'department'])
                    ->where('ckode', $validated['ckode_asset'])
                    ->first();
            }

            if (! $qr && ! $nonQr) {
                throw new \Exception('Kode asset tidak valid');
            }

            $asset = $qr ?? $nonQr;

            /**
             * ======================
             * HANDLE UPLOAD FOTO
             * ======================
             */
            $namaFoto = null;

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $namaFoto = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/asset'), $namaFoto);
            }

            /**
             * ======================
             * SIMPAN TRANSAKSI
             * ======================
             */
            MassetTrans::create([
                'ngrpid'   => $asset->nidsubkat,
                'ckode'    => $validated['ckode_asset'],
                'cnama'    => $asset->cnama ?? $asset->subKategori->cnama,
                'nlokasi'  => $asset->niddept,
                'dbeli'    => $validated['dbeli'],
                'cmerk'    => $validated['cmerk'] ?? null,
                'dgaransi' => $validated['dgaransi'] ?? null,
                'nhrgbeli' => $validated['nhrgbeli'] ?? null,
                'ccatatan' => $validated['ccatatan'] ?? null,
                'dreffoto' => $namaFoto,
                'fqr'      => $qr ? 1 : 0, // penanda QR / Non QR
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Transaksi asset berhasil disimpan');
    }
}
