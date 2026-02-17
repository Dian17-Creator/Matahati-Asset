<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Msatuan;

class MsatuanController extends Controller
{
    /**
     * List satuan
     */
    public function index()
    {
        return view('msatuan.index', [
            'satuan' => Msatuan::orderBy('nama')->get()
        ]);
    }

    /**
     * Simpan satuan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:msatuan,nama',
        ]);

        Msatuan::create($validated);

        return redirect()->back()->with('success', 'Satuan berhasil ditambahkan');
    }

    /**
     * Update satuan
     */
    public function update(Request $request, $id)
    {
        $satuan = Msatuan::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:msatuan,nama,' . $satuan->id,
        ]);

        $satuan->update($validated);

        return redirect()->back()->with('success', 'Satuan berhasil diupdate');
    }

    /**
     * Hapus satuan
     */
    public function destroy($id)
    {
        $satuan = Msatuan::findOrFail($id);

        // OPTIONAL safety check
        if ($satuan->assetNonQr()->exists()) {
            return redirect()->back()
                ->with('error', 'Satuan sedang digunakan dan tidak bisa dihapus');
        }

        $satuan->delete();

        return redirect()->back()->with('success', 'Satuan berhasil dihapus');
    }
}
