<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MassetAudit;
use App\Models\Mdepartment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    /**
     * 🔹 LIST DATA (VIEW ONLY)
     */
    public function index(Request $request)
    {
        $query = MassetAudit::with('department');

        // 🔍 Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where("ckode", "like", "%" . $request->search . "%")
                  ->orWhere("cnama", "like", "%" . $request->search . "%");
            });
        }

        // 🔍 Filter Status
        if ($request->status) {
            $query->where('cstatus', $request->status);
        }

        // 🔍 Filter Lokasi
        if ($request->lokasi) {
            $query->where('nlokasi', $request->lokasi);
        }

        $data = $query->latest("nid")->paginate(10);

        $lokasiList = Mdepartment::select('nid', 'cname')->orderBy('cname')->get();

        return view("audit.index", compact("data", "lokasiList"));
    }

    /**
     * 🔹 FORM CREATE
     */
    public function create()
    {
        return view("audit.create");
    }

    /**
     * 🔹 STORE DATA
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "ngrpid" => "required|integer",
            "nlokasi" => "required|integer",
            "dtrans" => "nullable|date",
            "ckode" => "required|string|max:50",
            "cnama" => "nullable|string|max:100",
            "cstatus" => "nullable|in:BAIK/SESUAI,MASALAH/TDK.SESUAI",
            "nqty" => "nullable|integer",
            "nqtyreal" => "nullable|integer",
            "ccatatan" => "nullable|string|max:100",
            "dreffoto" => "nullable|string|max:255",
        ]);

        MassetAudit::create($validated);

        return redirect()
            ->route("audit.index")
            ->with("success", "Data audit berhasil ditambahkan");
    }

    /**
     * 🔹 FORM EDIT
     */
    public function edit($id)
    {
        $data = MassetAudit::findOrFail($id);

        return view("audit.edit", compact("data"));
    }

    /**
     * 🔹 UPDATE DATA
     */
    public function update(Request $request, $id)
    {
        $data = MassetAudit::findOrFail($id);

        $validated = $request->validate([
            "ngrpid" => "required|integer",
            "nlokasi" => "required|integer",
            "dtrans" => "nullable|date",
            "ckode" => "required|string|max:50",
            "cnama" => "nullable|string|max:100",
            "cstatus" => "nullable|in:BAIK/SESUAI,MASALAH/TDK.SESUAI",
            "nqty" => "nullable|integer",
            "nqtyreal" => "nullable|integer",
            "ccatatan" => "nullable|string|max:100",
            "dreffoto" => "nullable|string|max:255",
        ]);

        $data->update($validated);

        return redirect()
            ->route("audit.index")
            ->with("success", "Data audit berhasil diupdate");
    }

    /**
     * 🔹 DELETE
     */
    public function destroy($id)
    {
        $data = MassetAudit::findOrFail($id);
        $data->delete();

        return redirect()
            ->route("audit.index")
            ->with("success", "Data audit berhasil dihapus");
    }

    // Mobile Endpoint Api

    public function apiStore(Request $request)
    {
        try {
            // 🔍 VALIDASI
            $validator = Validator::make($request->all(), [
                "ngrpid" => "required|integer",
                "nlokasi" => "required|integer",
                "dtrans" => "nullable|date",
                "ckode" => "required|string|max:50",
                "cnama" => "nullable|string|max:100",
                "cstatus" => "nullable|in:BAIK/SESUAI,MASALAH/TDK.SESUAI",
                "nqty" => "nullable|integer",
                "nqtyreal" => "nullable|integer",
                "ccatatan" => "nullable|string|max:100",
                "dreffoto" => "nullable|string|max:255",
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Validasi gagal",
                        "errors" => $validator->errors(),
                    ],
                    422,
                );
            }

            // 💾 SIMPAN DATA
            $data = MassetAudit::create($validator->validated());

            return response()->json(
                [
                    "success" => true,
                    "message" => "Data berhasil disimpan",
                    "data" => $data,
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Terjadi kesalahan",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function apiStoreNonQr(Request $request)
    {
        try {
            // 🔍 VALIDASI
            $validator = Validator::make($request->all(), [
                "ngrpid" => "required|integer",
                "nlokasi" => "required|integer",
                "ckode" => "required|string|max:50",
                "cnama" => "nullable|string|max:100",
                "nqty" => "nullable|integer",

                // 🔥 kondisi utama
                "kondisi" => "required|in:sesuai,tidak_sesuai",

                // 🔥 wajib kalau tidak sesuai
                "nqtyreal" => "required_if:kondisi,tidak_sesuai|integer",
                "ccatatan" => "required_if:kondisi,tidak_sesuai|string|max:255",
                "foto" => "required_if:kondisi,tidak_sesuai|image|max:2048",
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Validasi gagal",
                        "errors" => $validator->errors(),
                    ],
                    422,
                );
            }

            $data = $validator->validated();

            // Untuk Qty ambil dari master asset non QR
            $asset = DB::table("masset_noqr")
                ->where("ckode", $request->ckode)
                ->first();

            if (!$asset) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Asset tidak ditemukan",
                    ],
                    404,
                );
            }

            $data["nqty"] = $asset->nqty;

            // 🧠 Mapping kondisi → cstatus
            if ($data["kondisi"] === "sesuai") {
                $data["cstatus"] = "BAIK/SESUAI";
                $data["nqtyreal"] = null;
                $data["ccatatan"] = null;
                $data["dreffoto"] = null;
            } else {
                $data["cstatus"] = "MASALAH/TDK.SESUAI";
            }

            // 📷 Upload foto (kalau ada)
            if (
                $request->hasFile("foto") &&
                $request->file("foto")->isValid()
            ) {
                $file = $request->file("foto");
                $kodeAsset = preg_replace(
                    "/[^A-Za-z0-9_\-]/",
                    "_",
                    $request->ckode,
                );
                $datetime = now()->format("Ymd_His");

                $ext = $file->getClientOriginalExtension();
                $filename =
                    $kodeAsset .
                    "_" .
                    $datetime .
                    "_" .
                    rand(10, 99) .
                    "." .
                    $ext;

                $folder = "assets/audit";
                $destinationPath = public_path($folder);

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true);
                }

                $file->move($destinationPath, $filename);
                $data["dreffoto"] = $filename;
            }

            // ⏱️ Auto tanggal
            $data["dtrans"] = now();

            // 🧹 Bersihkan field tambahan
            unset($data["kondisi"]);
            unset($data["foto"]);

            // 💾 Simpan
            $save = MassetAudit::create($data);

            return response()->json(
                [
                    "success" => true,
                    "message" => "Audit Non QR berhasil disimpan",
                    "data" => $save,
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Terjadi kesalahan",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function apiStoreQr(Request $request)
    {
        try {
            // 🔍 VALIDASI
            $validator = Validator::make($request->all(), [
                "ngrpid" => "required|integer",
                "nlokasi" => "required|integer",
                "cqr" => "required|string|max:50",
                "cnama" => "nullable|string|max:100",

                // 🔥 kondisi utama
                "kondisi" => "required|in:normal,masalah",

                // 🔥 wajib kalau masalah
                "ccatatan" => "required_if:kondisi,masalah|string|max:255",
                "foto" => "required_if:kondisi,masalah|image|max:2048",
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Validasi gagal",
                        "errors" => $validator->errors(),
                    ],
                    422,
                );
            }

            $data = $validator->validated();

            // Cari asset di master QR
            $asset = DB::table("masset_qr")
                ->where("cqr", $request->cqr)
                ->first();

            if (!$asset) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Asset QR tidak ditemukan",
                    ],
                    404,
                );
            }

            $data["ckode"] = $asset->cqr;
            $data["cnama"] = $data["cnama"] ?? $asset->cnama;
            $data["nqty"] = 1; // Aset QR selalu berjumlah 1
            $data["nqtyreal"] = 1; // Aset QR selalu berjumlah 1 secara fisik

            // 🧠 Mapping kondisi → cstatus
            if ($data["kondisi"] === "normal") {
                $data["cstatus"] = "BAIK/SESUAI";
                $data["ccatatan"] = null;
                $data["dreffoto"] = null;
            } else {
                $data["cstatus"] = "MASALAH/TDK.SESUAI";
            }

            // 📷 Upload foto (kalau ada)
            if (
                $request->hasFile("foto") &&
                $request->file("foto")->isValid()
            ) {
                $file = $request->file("foto");
                $kodeAsset = preg_replace(
                    "/[^A-Za-z0-9_\-]/",
                    "_",
                    $request->cqr,
                );
                $datetime = now()->format("Ymd_His");

                $ext = $file->getClientOriginalExtension();
                $filename =
                    $kodeAsset .
                    "_" .
                    $datetime .
                    "_" .
                    rand(10, 99) .
                    "." .
                    $ext;

                $folder = "assets/audit";
                $destinationPath = public_path($folder);

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0775, true);
                }

                $file->move($destinationPath, $filename);
                $data["dreffoto"] = $filename;
            }

            // ⏱️ Auto tanggal
            $data["dtrans"] = now();

            // 🧹 Bersihkan field tambahan
            unset($data["kondisi"]);
            unset($data["foto"]);
            unset($data["cqr"]);

            // 💾 Simpan
            $save = MassetAudit::create($data);

            return response()->json(
                [
                    "success" => true,
                    "message" => "Audit QR berhasil disimpan",
                    "data" => $save,
                ],
                201,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Terjadi kesalahan",
                    "error" => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function apiCabang()
    {
        $data = DB::table("mdepartment")->select("nid", "cname")->get();

        return response()->json($data);
    }

    public function apiAssetNonQr(Request $request)
    {
        $query = DB::table("masset_noqr")->select("ckode", "cnama", "nqty");

        if ($request->niddept) {
            $query->where("niddept", $request->niddept); // ✅ FIX
        }

        return response()->json($query->get());
    }

    public function apiAssetQr(Request $request)
    {
        $query = DB::table("masset_qr")->select("cqr", "cnama");

        if ($request->niddept) {
            $query->where("niddept", $request->niddept);
        }

        return response()->json($query->get());
    }
}

