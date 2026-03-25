<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\AssetService;
use App\Models\MassetKat;
use App\Models\MassetSubKat;
use App\Models\MassetQr;
use App\Models\MassetNoQr;
use App\Models\Mdepartment;
use App\Models\Msatuan;
use App\Models\MassetTrans;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        // =========================
        // MASTER KECIL (PAGINATION)
        // =========================
        $satuan = Msatuan::orderBy('nama')
            ->paginate(5, ['*'], 'satuan_page');

        $kategori = MassetKat::orderBy('ckode')
            ->paginate(5, ['*'], 'kategori_page');

        $subkategori = MassetSubKat::with('kategori')
            ->orderBy('ckode', 'asc')
            ->paginate(5, ['*'], 'subkategori_page');

        $assetQr = MassetQr::with(['subKategori.kategori', 'department'])
            ->orderBy('cqr', 'asc')
            ->paginate(5, ['*'], 'asset_qr_page');

        $assetNoQr = MassetNoQr::with(['subKategori.kategori', 'department', 'satuan'])
            ->orderBy('ckode', 'asc')
            ->paginate(5, ['*'], 'asset_nonqr_page');

        $transaksi = MassetTrans::with([
                'subKategori.kategori',
                'department',
            ])
            ->orderByDesc('ckode')
            ->paginate(5, ['*'], 'transaksi_page')
            ->withQueryString(); // 🔥 WAJIB

        // ✅ FULL DATA UNTUK DROPDOWN
        $kategoriAll = MassetKat::orderBy('ckode')->get();
        $SatuanAll = Msatuan::orderBy('nama')->get();
        $subkategoriAll = MassetSubKat::with('kategori')->orderBy('ckode')->get();
        $AssetQrAll = MassetQr::with(['subKategori.kategori', 'department'])->orderBy('cqr')->get();
        $AssetNoQrAll = MassetNoQr::with(['subKategori.kategori', 'department', 'satuan'])->orderBy('ckode')->get();
        $kategoriFilter = \App\Models\MassetKat::orderBy('cnama')->get();
        $subKategoriFilter = \App\Models\MassetSubKat::orderBy('cnama')->get();

        // =========================
        // HANDLE AJAX REQUEST
        // =========================
        if ($request->ajax()) {

            if ($request->has('satuan_page')) {
                return view(
                    'Asset.components.partials.satuan_table',
                    compact('satuan')
                )->render();
            }

            if ($request->has('kategori_page')) {
                return view(
                    'Asset.components.partials.kategori_table',
                    compact('kategori')
                )->render();
            }

            if ($request->has('subkategori_page')) {
                return view(
                    'Asset.components.partials.subkategori_table',
                    compact('subkategori')
                )->render();
            }

            if ($request->has('asset_qr_page')) {
                return view(
                    'Asset.components.partials.asset_qr_table',
                    compact('assetQr')
                )->render();
            }

            if ($request->has('asset_nonqr_page')) {
                return view(
                    'Asset.components.partials.asset_nonqr_table',
                    compact('assetNoQr')
                )->render();
            }

            if ($request->has('transaksi_page')) {
                return view(
                    'Asset.components.partials.transaksi_table',
                    compact('transaksi')
                )->render();
            }
        }

        // =========================
        // DROPDOWN KODE ASSET (QR + NON QR)
        // =========================
        $assetQrList = MassetQr::with(['subKategori', 'department'])
            ->get()
            ->map(function ($qr) {
                return [
                    'kode'   => $qr->cqr,
                    'nama'   => $qr->cnama ?? $qr->subKategori->cnama,
                    'lokasi' => $qr->department->cname ?? '-',
                    'qty'    => 1,              // QR = 1 unit
                    'jenis'  => 'QR',
                    'niddept' => $qr->niddept,
                ];
            });

        $assetNonQrList = MassetNoQr::with(['subKategori', 'department'])
            ->get()
            ->map(function ($nqr) {
                return [
                    'kode'   => $nqr->ckode,
                    'nama'   => $nqr->cnama,
                    'lokasi' => $nqr->department->cname ?? '-',
                    'qty'    => $nqr->nqty,
                    'jenis'  => 'NON_QR',
                    'niddept' => $nqr->niddept,
                ];
            });

        //Data Pemusnahan Asset
        $assetQrAktif = MassetQr::with('subKategori')
            ->where('cstatus', 'Aktif')
            ->orderBy('cqr')
            ->get();

        $assetQrPerbaikan = MassetQr::with('subKategori')
            ->whereIn('cstatus', ['Aktif', 'Perbaikan'])
            ->orderBy('cqr')
            ->get();

        $assetNonQrPemusnahan = MassetNoQr::with('department')
            ->where('nqty', '>', 0)
            ->orderBy('ckode')
            ->get();

        $assetDropdown = collect($assetQrList)
            ->merge($assetNonQrList)
            ->sortBy('kode')
            ->values();

        // LOAD NORMAL (FIRST LOAD)
        return view('Asset.index', [
            'satuan'       => $satuan,
            'kategori'     => $kategori,
            'kategoriAll'  => $kategoriAll,

            'SatuanAll'    => $SatuanAll,
            'subkategoriAll' => $subkategoriAll,
            'assetQrAll'   => $AssetQrAll,

            'assetNoQrAll' => $AssetNoQrAll,
            'subkategori'  => $subkategori,
            'departments'  => Mdepartment::all(),

            'assetQr'      => $assetQr,
            'assetNoQr'    => $assetNoQr,
            'transaksi'    => $transaksi,

            'assetDropdown' => $assetDropdown,
            'assetQrAktif' => $assetQrAktif,
            'assetQrPerbaikan' => $assetQrPerbaikan,
            'assetNonQrPemusnahan' => $assetNonQrPemusnahan,

            'kategoriFilter' => $kategoriFilter,
            'subKategoriFilter' => $subKategoriFilter,
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
            'cnama'      => 'nullable|string|max:255',
            'kode_urut'  => 'nullable|string|max:10',
            'nqty'       => 'nullable|integer|min:0',
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
    public function pemusnahan(Request $request)
    {
        Log::info('=== START PEMUSNAHAN CONTROLLER ===');
        Log::info('REQUEST MASUK:', $request->all());

        if ($request->jenis_asset === 'QR') {

            Log::info('JENIS: QR');

            $request->validate([
                'kode_asset_qr' => 'required|integer|exists:masset_qr,nid',
                'ccatatan' => 'nullable|string|max:100',
            ]);

            Log::info('DATA QR VALID:', [
                'nid' => $request->kode_asset_qr
            ]);

            AssetService::pemusnahanQr([
                'nid' => $request->kode_asset_qr,
                'ccatatan' => $request->ccatatan,
            ]);
        }

        if ($request->jenis_asset === 'NON_QR') {

            Log::info('JENIS: NON QR');

            $request->validate([
                'kode_asset_nonqr' => 'required',
                'qty' => 'required|integer|min:1',
                'ccatatan' => 'nullable|string|max:100',
            ]);

            Log::info('VALUE DROPDOWN:', [
                'raw' => $request->kode_asset_nonqr
            ]);

            // 🔥 PARSE
            [$ckode, $niddept] = explode('|', $request->kode_asset_nonqr);

            Log::info('HASIL PARSE:', [
                'ckode' => $ckode,
                'niddept' => $niddept,
                'qty' => $request->qty
            ]);

            AssetService::pemusnahanNonQr([
                'ckode'   => $ckode,
                'niddept' => $niddept,
                'qty'     => $request->qty,
                'ccatatan' => $request->ccatatan,
            ]);
        }

        Log::info('=== END PEMUSNAHAN CONTROLLER ===');

        return back()->with('success', 'Asset berhasil dimusnahkan');
    }
    public function perbaikanQr(Request $request)
    {
        $status = $request->cstatus;

        try {

            DB::transaction(function () use ($request, $status) {

                // 🔥 PARSE VALUE (QR|id atau NON_QR|kode)
                [$jenis, $kodeFix] = explode('|', $request->kode_asset);

                $request->merge([
                    'kode_asset' => $kodeFix
                ]);

                $cjnstrans = $status === 'Perbaikan' ? 'ServiceIn' : 'ServiceOut';

                // ================= VALIDASI =================
                if ($jenis === 'QR') {
                    $validated = $request->validate([
                        'kode_asset' => 'required',
                        'ccatatan'   => 'nullable|string|max:255',
                    ]);
                } else {
                    $validated = $request->validate([
                        'kode_asset' => 'required',
                        'nidsubkat'  => 'required|integer',
                        'niddept'    => 'required|integer',
                        'qty'        => 'required|integer|min:1',
                        'ccatatan'   => 'nullable|string|max:255',
                    ]);
                }

                Log::info('=== START PERBAIKAN ===');
                Log::info('PARSED', ['jenis' => $jenis, 'kode' => $kodeFix]);

                // ================= QR =================
                if ($jenis === 'QR') {

                    $asset = MassetQr::where('nid', $validated['kode_asset'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    $asset->update([
                        'cstatus'  => $status,
                        'dtrans'   => now(),
                        'ccatatan' => $validated['ccatatan'] ?? null,
                    ]);

                    MassetTrans::create([
                        'ngrpid'      => $asset->nidsubkat,
                        'cjnstrans'   => $cjnstrans,
                        'dtrans'      => now(),
                        'cnotrans'    => 'SV/' . now()->format('ym') . '-' . rand(1000, 9999),

                        'ckode'       => $asset->cqr,
                        'cnama'       => $asset->cnama,
                        'nlokasi'     => $asset->niddept,

                        'nqty'        => 1,
                        'nqtyselesai' => $cjnstrans === 'ServiceOut' ? 1 : 0,
                        'ccatatan'    => $validated['ccatatan'] ?? null,

                        // 🔥 KUNCI
                        'jenis_asset' => 'QR',
                    ]);
                }

                // ================= NON QR =================
                if ($jenis === 'NON_QR') {

                    if ($cjnstrans === 'ServiceIn') {

                        $asset = DB::table('masset_noqr')
                            ->where('ckode', $validated['kode_asset'])
                            ->where('nidsubkat', $validated['nidsubkat'])
                            ->where('niddept', $validated['niddept'])
                            ->first();

                        if (!$asset) {
                            throw new \Exception('Asset tidak ditemukan');
                        }

                        DB::table('masset_noqr')
                            ->where('ckode', $validated['kode_asset'])
                            ->where('nidsubkat', $validated['nidsubkat'])
                            ->where('niddept', $validated['niddept'])
                            ->update([
                                'cstatus' => 'Perbaikan',
                                'dtrans'  => now(),
                            ]);

                        MassetTrans::create([
                            'ngrpid'      => $validated['nidsubkat'],
                            'cjnstrans'   => 'ServiceIn',
                            'dtrans'      => now(),
                            'cnotrans'    => 'SV/' . now()->format('ym') . '-' . rand(1000, 9999),

                            'ckode'       => $validated['kode_asset'],
                            'cnama'       => $asset->cnama,
                            'nlokasi'     => $validated['niddept'],

                            'nqty'        => $validated['qty'],
                            'nqtyselesai' => 0,
                            'ccatatan'    => $validated['ccatatan'] ?? null,

                            // 🔥 KUNCI
                            'jenis_asset' => 'NON_QR',
                        ]);
                    }

                    if ($cjnstrans === 'ServiceOut') {

                        $transIn = MassetTrans::where('ckode', $validated['kode_asset'])
                            ->where('cjnstrans', 'ServiceIn')
                            ->where('nlokasi', $validated['niddept'])
                            // ->where('jenis_asset', 'NON_QR') // 🔥 PENTING
                            ->orderByDesc('dtrans')
                            ->lockForUpdate()
                            ->first();

                        if (!$transIn) {
                            throw new \Exception('Data ServiceIn tidak ditemukan');
                        }

                        $sisa = $transIn->nqty - ($transIn->nqtyselesai ?? 0);

                        if ($validated['qty'] > $sisa) {
                            throw new \Exception("Qty melebihi sisa ({$sisa})");
                        }

                        $transIn->update([
                            'nqtyselesai' => ($transIn->nqtyselesai ?? 0) + $validated['qty']
                        ]);

                        DB::table('masset_noqr')
                            ->where('ckode', $validated['kode_asset'])
                            ->where('nidsubkat', $validated['nidsubkat'])
                            ->where('niddept', $validated['niddept'])
                            ->update([
                                'cstatus' => ($sisa - $validated['qty']) <= 0 ? 'Aktif' : 'Perbaikan',
                                'dtrans'  => now(),
                            ]);

                        MassetTrans::create([
                            'ngrpid'      => $validated['nidsubkat'],
                            'cjnstrans'   => 'ServiceOut',
                            'dtrans'      => now(),
                            'cnotrans'    => 'SV/' . now()->format('ym') . '-' . rand(1000, 9999),

                            'ckode'       => $validated['kode_asset'],
                            'cnama'       => $transIn->cnama,
                            'nlokasi'     => $validated['niddept'],

                            'nqty'        => $validated['qty'],
                            'nqtyselesai' => $validated['qty'],
                            'ccatatan'    => $validated['ccatatan'] ?? null,

                            // 🔥 KUNCI
                            'jenis_asset' => 'NON_QR',
                        ]);
                    }
                }

                Log::info('=== END PERBAIKAN ===');
            });

            return back()->with('success', 'Transaksi perbaikan berhasil disimpan');

        } catch (\Throwable $e) {
            Log::error('ERROR PERBAIKAN: ' . $e->getMessage());
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
    public function mutasi(Request $request)
    {
        try {

            // =========================
            // MUTASI QR
            // =========================
            if ($request->jenis_asset === 'QR') {

                $validated = $request->validate([
                    'kode_asset_qr'   => 'required|integer|exists:masset_qr,nid',
                    'niddept_tujuan'  => 'required|exists:mdepartment,nid',
                    'ccatatan'        => 'nullable|string|max:255',
                ]);

                AssetService::mutasiQr([
                    'nid'             => $validated['kode_asset_qr'],
                    'niddept_tujuan'  => $validated['niddept_tujuan'],
                    'ccatatan'        => $validated['ccatatan'] ?? null,
                ]);

                // =========================
                // MUTASI NON QR
                // =========================
            } elseif ($request->jenis_asset === 'NON_QR') {

                $validated = $request->validate([
                    'kode_asset_nonqr' => 'required|string|exists:masset_noqr,ckode',
                    'niddept_asal'     => 'required|exists:mdepartment,nid',
                    'niddept_tujuan'   => 'required|exists:mdepartment,nid',
                    'qty'              => 'required|integer|min:1',
                    'ccatatan'         => 'nullable|string|max:255',
                ]);

                // 🚫 prevent mutasi ke dept yang sama
                if ($validated['niddept_asal'] == $validated['niddept_tujuan']) {
                    return back()->withErrors('Departemen asal dan tujuan tidak boleh sama');
                }

                AssetService::mutasiNonQr([
                    'ckode'          => $validated['kode_asset_nonqr'],
                    'niddept_asal'   => $validated['niddept_asal'],
                    'niddept_tujuan' => $validated['niddept_tujuan'],
                    'qty'            => (int) $validated['qty'],
                    'ccatatan'       => $validated['ccatatan'] ?? null,
                ]);

            } else {
                return back()->withErrors('Jenis asset tidak valid');
            }

            return back()->with('success', 'Mutasi asset berhasil disimpan');

        } catch (\Throwable $e) {

            // 🔍 optional: log error biar gampang tracking
            \Log::error('Error mutasi asset', [
                'message' => $e->getMessage(),
            ]);

            return back()->withErrors($e->getMessage());
        }
    }
    public function getAssetByStatus(Request $request): JsonResponse
    {
        $status = $request->status;

        $result = collect();

        /**
         * =========================
         * PERBAIKAN MASUK
         * =========================
         */
        if ($status === 'Perbaikan') {

            // QR dari aktif
            $qr = MassetQr::with(['subKategori', 'department'])
                ->where('cstatus', 'Aktif')
                ->get();

            // ambil semua kode QR
            $qrCodes = $qr->pluck('cqr')->toArray();

            // NON QR dari stok aktif
            $nonqr = MassetNoQr::with('subKategori')
                ->where('cstatus', 'Aktif')
                ->where('nqty', '>', 0)
                ->get();

            // ================= QR =================
            foreach ($qr as $item) {
                $result->push([
                    'id'      => $item->nid,
                    'kode'    => $item->cqr,
                    'nama'    => $item->cnama ?? optional($item->subKategori)->cnama,
                    'jenis'   => 'QR',
                    'lokasi' => optional($item->department)->cname ?? $item->niddept,
                ]);
            }

            // ================= NON QR =================
            foreach ($nonqr as $item) {

                // 🔥 FILTER DUPLIKAT (kalau sudah ada QR)
                if (in_array($item->ckode, $qrCodes)) {
                    continue;
                }

                $result->push([
                    'id'        => $item->ckode,
                    'kode'      => $item->ckode,
                    'nama'      => $item->cnama,
                    'jenis'     => 'NON_QR',
                    'nidsubkat' => $item->nidsubkat,
                    'niddept'   => $item->niddept,
                    'qty'       => $item->nqty,
                    'lokasi'    => optional($item->department)->cname ?? $item->niddept, // 🔥
                ]);
            }
        }

        /**
         * =========================
         * PERBAIKAN SELESAI
         * =========================
         */ else {

            // QR dari status perbaikan
            $qr = MassetQr::with(['subKategori', 'department'])
                 ->where('cstatus', 'Perbaikan')
                 ->get();

            // ambil kode QR
            $qrCodes = $qr->pluck('cqr')->toArray();

            // NON QR dari transaksi (FIX FINAL)
            $nonqr = MassetTrans::where('cjnstrans', 'ServiceIn')
                ->whereRaw('(nqty - nqtyselesai) > 0')
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                    ->from('masset_qr')
                    ->whereColumn('masset_qr.cqr', 'masset_trans.ckode');
                })
                ->get();

            // ================= QR =================
            foreach ($qr as $item) {
                $result->push([
                    'id'      => $item->nid,
                    'kode'    => $item->cqr,
                    'nama'    => $item->cnama ?? optional($item->subKategori)->cnama,
                    'jenis'   => 'QR',
                    'lokasi'  => optional($item->department)->cname ?? $item->niddept, // 🔥 TAMBAH
                ]);
            }

            // ================= NON QR =================
            foreach ($nonqr as $item) {

                // 🔥 FILTER DUPLIKAT (kalau sudah ada QR)
                if (in_array($item->ckode, $qrCodes)) {
                    continue;
                }

                $result->push([
                    'id'        => $item->ckode,
                    'kode'      => $item->ckode,
                    'nama'      => $item->cnama,
                    'jenis'     => 'NON_QR',
                    'sisa'      => $item->nqty - $item->nqtyselesai,
                    'nidsubkat' => $item->ngrpid,
                    'niddept'   => $item->nlokasi,
                    'lokasi' => DB::table('mdepartment')
                        ->where('nid', $item->nlokasi)
                        ->value('cname') ?? $item->nlokasi
                ]);
            }
        }

        return response()->json($result);
    }
}
