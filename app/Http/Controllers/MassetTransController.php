<?php

namespace App\Http\Controllers;

use App\Models\MassetTrans;
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
            ])
            ->orderByDesc('nid')
            ->get(),
        ]);
    }

    /**
     * =========================
     * STORE TRANSAKSI
     * =========================
     */
    public function store(Request $request)
    {
        $request->validate([
            'ngrpid'       => 'required|exists:masset_subkat,nid',
            'ckode'        => 'required|string|max:50',
            'cnama'        => 'nullable|string|max:100',
            'nlokasi'      => 'required|exists:mdepartment,nid',
            'dbeli'        => 'required|date',
            'cmerk'        => 'nullable|string|max:50',
            'dgaransi'     => 'nullable|date',
            'nhrgbeli'     => 'nullable|numeric',
            'ccatatan'     => 'nullable|string|max:100',

            // FOTO
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request) {

            /** ======================
             * HANDLE UPLOAD FOTO
             * ====================== */
            $namaFoto = null;

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');

                $namaFoto = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // SIMPAN KE: public/uploads/asset
                $file->move(public_path('uploads/asset'), $namaFoto);
            }

            /** ======================
             * SIMPAN TRANSAKSI
             * ====================== */
            MassetTrans::create([
                'ngrpid'      => $request->ngrpid,
                'ckode'       => $request->ckode,
                'cnama'       => $request->cnama,
                'nlokasi'     => $request->nlokasi,
                'dbeli'       => $request->dbeli,
                'cmerk'       => $request->cmerk,
                'dgaransi'    => $request->dgaransi,
                'nhrgbeli'    => $request->nhrgbeli,
                'ccatatan'    => $request->ccatatan,
                'dreffoto'    => $namaFoto,
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Transaksi asset berhasil disimpan');
    }

    private function shortCode($text)
    {
        return strtoupper(substr(preg_replace('/[^A-Z]/i', '', $text), 0, 3));
    }

    public function generateQrCode(Request $request)
    {
        $subkat = \App\Models\MassetSubKat::with('kategori')
            ->where('nid', $request->ngrpid)
            ->firstOrFail();

        $katCode  = $this->shortCode($subkat->kategori->cnama);
        $subCode  = $this->shortCode($subkat->cnama);

        // cari kode terakhir
        $last = MassetTrans::where('ngrpid', $subkat->nid)
            ->where('ckode', 'LIKE', "$katCode-$subCode-%")
            ->orderByDesc('ckode')
            ->first();

        $nextNumber = 1;

        if ($last) {
            $lastNum = intval(substr($last->ckode, -3));
            $nextNumber = $lastNum + 1;
        }

        $kode = sprintf('%s-%s-%03d', $katCode, $subCode, $nextNumber);

        return response()->json([
            'kode' => $kode
        ]);
    }
}
