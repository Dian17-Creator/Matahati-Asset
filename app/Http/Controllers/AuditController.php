<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MassetAudit;
use Illuminate\Support\Facades\Validator;

class AuditController extends Controller
{
    /**
     * 🔹 LIST DATA
     */
    public function index(Request $request)
    {
        $query = MassetAudit::query();

        // 🔍 Search (opsional)
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('ckode', 'like', '%' . $request->search . '%')
                  ->orWhere('cnama', 'like', '%' . $request->search . '%');
            });
        }

        $data = $query->latest('nid')->paginate(10);

        return view('audit.index', compact('data'));
    }

    /**
     * 🔹 FORM CREATE
     */
    public function create()
    {
        return view('audit.create');
    }

    /**
     * 🔹 STORE DATA
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ngrpid'     => 'required|integer',
            'nlokasi'    => 'required|integer',
            'dtrans'     => 'nullable|date',
            'ckode'      => 'required|string|max:50',
            'cnama'      => 'nullable|string|max:100',
            'cstatus'    => 'nullable|in:BAIK/SESUAI,MASALAH/TDK.SESUAI',
            'nqty'       => 'nullable|integer',
            'nqtyreal'   => 'nullable|integer',
            'ccatatan'   => 'nullable|string|max:100',
            'dreffoto'   => 'nullable|string|max:255',
        ]);

        MassetAudit::create($validated);

        return redirect()->route('audit.index')
            ->with('success', 'Data audit berhasil ditambahkan');
    }

    /**
     * 🔹 FORM EDIT
     */
    public function edit($id)
    {
        $data = MassetAudit::findOrFail($id);

        return view('audit.edit', compact('data'));
    }

    /**
     * 🔹 UPDATE DATA
     */
    public function update(Request $request, $id)
    {
        $data = MassetAudit::findOrFail($id);

        $validated = $request->validate([
            'ngrpid'     => 'required|integer',
            'nlokasi'    => 'required|integer',
            'dtrans'     => 'nullable|date',
            'ckode'      => 'required|string|max:50',
            'cnama'      => 'nullable|string|max:100',
            'cstatus'    => 'nullable|in:BAIK/SESUAI,MASALAH/TDK.SESUAI',
            'nqty'       => 'nullable|integer',
            'nqtyreal'   => 'nullable|integer',
            'ccatatan'   => 'nullable|string|max:100',
            'dreffoto'   => 'nullable|string|max:255',
        ]);

        $data->update($validated);

        return redirect()->route('audit.index')
            ->with('success', 'Data audit berhasil diupdate');
    }

    /**
     * 🔹 DELETE
     */
    public function destroy($id)
    {
        $data = MassetAudit::findOrFail($id);
        $data->delete();

        return redirect()->route('audit.index')
            ->with('success', 'Data audit berhasil dihapus');
    }

    // Mobile Endpoint Api

    public function apiStore(Request $request)
    {
        try {
            // 🔍 VALIDASI
            $validator = Validator::make($request->all(), [
                'ngrpid'     => 'required|integer',
                'nlokasi'    => 'required|integer',
                'dtrans'     => 'nullable|date',
                'ckode'      => 'required|string|max:50',
                'cnama'      => 'nullable|string|max:100',
                'cstatus'    => 'nullable|in:BAIK/SESUAI,MASALAH/TDK.SESUAI',
                'nqty'       => 'nullable|integer',
                'nqtyreal'   => 'nullable|integer',
                'ccatatan'   => 'nullable|string|max:100',
                'dreffoto'   => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // 💾 SIMPAN DATA
            $data = MassetAudit::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data'    => $data
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
