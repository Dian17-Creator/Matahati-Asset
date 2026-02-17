<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\AssetService;
// MODELS
use App\Models\MassetKat;
use App\Models\MassetSubKat;
use App\Models\MassetQr;
use App\Models\MassetNoQr;
use App\Models\Mdepartment;
use App\Models\Msatuan;

class AssetController extends Controller
{
    /**
     * Halaman Master Asset
     */
    public function index()
    {
        return view('Asset.index', [
            'kategori'    => MassetKat::all(),
            'subkategori' => MassetSubKat::with('kategori')->get(),
            'departments' => Mdepartment::all(),
            'satuan'      => Msatuan::orderBy('nama')->get(), // ✅ TAMBAHAN
            'assetQr'     => MassetQr::with('subKategori.kategori', 'department')->get(),
            'assetNoQr'   => MassetNoQr::with(
                'subKategori.kategori',
                'department',
                'satuan' // ✅ eager load
            )->get(),
        ]);
    }

    /**
     * Simpan asset (QR / Non QR)
     * Penentuan tabel berdasarkan sub kategori (fqr)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nidsubkat'  => 'required|exists:masset_subkat,nid',
            'niddept'    => 'required|exists:mdepartment,nid',

            // NON QR
            'nqty'       => 'nullable|integer|min:1',
            'nminstok'   => 'nullable|integer|min:0',
            'msatuan_id' => 'nullable|exists:msatuan,id',

            // QR
            'dbeli'      => 'nullable|date',
            'nbeli'      => 'nullable|integer|min:0',

            'ccatatan'   => 'nullable|string',
        ]);

        AssetService::store($validated);

        return redirect()->route('asset.index')
            ->with('success', 'Asset berhasil disimpan');
    }

    /**
     * Simpan Kategori
     */
    public function storeKategori(Request $request)
    {
        $request->validate([
            'ckode' => 'required|string|max:50|unique:masset_kat,ckode',
            'cnama' => 'required|string|max:100',
        ]);

        MassetKat::create([
            'ckode'    => $request->ckode,
            'cnama'    => $request->cnama,
            'dcreated' => now(),
        ]);

        return redirect()->route('asset.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Simpan Sub Kategori
     */
    public function storeSubKategori(Request $request)
    {
        $request->validate([
            'nidkat' => 'required|exists:masset_kat,nid',
            'ckode'  => 'required|string|max:50',
            'cnama'  => 'required|string|max:100',
            'fqr'    => 'required|boolean',
        ]);

        MassetSubKat::create([
            'nidkat'   => $request->nidkat,
            'ckode'    => $request->ckode,
            'cnama'    => $request->cnama,
            'fqr'      => $request->fqr,
            'dcreated' => now(),
        ]);

        return redirect()->route('asset.index')
            ->with('success', 'Sub Kategori berhasil ditambahkan');
    }

    public function updateKategori(Request $request, $id)
    {
        $request->validate([
            'ckode' => 'required|string|max:50|unique:masset_kat,ckode,' . $id . ',nid',
            'cnama' => 'required|string|max:100',
        ]);

        MassetKat::where('nid', $id)->update([
            'ckode' => $request->ckode,
            'cnama' => $request->cnama,
        ]);

        return redirect()->route('asset.index')
            ->with('success', 'Kategori berhasil diupdate');
    }

    public function deleteKategori($id)
    {
        // optional safety check
        if (MassetSubKat::where('nidkat', $id)->exists()) {
            return back()->with('error', 'Kategori masih memiliki Sub Kategori');
        }

        MassetKat::where('nid', $id)->delete();

        return redirect()->route('asset.index')
            ->with('success', 'Kategori berhasil dihapus');
    }

    public function updateSubKategori(Request $request, $id)
    {
        $request->validate([
            'nidkat' => 'required|exists:masset_kat,nid',
            'ckode'  => 'required|string|max:50',
            'cnama'  => 'required|string|max:100',
            'fqr'    => 'required|boolean',
        ]);

        MassetSubKat::where('nid', $id)->update([
            'nidkat' => $request->nidkat,
            'ckode'  => $request->ckode,
            'cnama'  => $request->cnama,
            'fqr'    => $request->fqr,
        ]);

        return redirect()->route('asset.index')
            ->with('success', 'Sub Kategori berhasil diupdate');
    }

    public function deleteSubKategori($id)
    {
        // optional safety check
        if (
            MassetQr::where('nidsubkat', $id)->exists() ||
            MassetNoQr::where('nidsubkat', $id)->exists()
        ) {
            return back()->with('error', 'Sub kategori masih digunakan asset');
        }

        MassetSubKat::where('nid', $id)->delete();

        return redirect()->route('asset.index')
            ->with('success', 'Sub Kategori berhasil dihapus');
    }

}
