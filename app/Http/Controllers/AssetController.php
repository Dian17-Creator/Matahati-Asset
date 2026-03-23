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

        $assetNonQrPemusnahan = MassetNoQr::where('nqty', '>', 0)
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
        if ($request->jenis_asset === 'QR') {
            $request->validate([
                'kode_asset_qr' => 'required|integer|exists:masset_qr,nid',
                'ccatatan' => 'nullable|string|max:100',
            ]);

            AssetService::pemusnahanQr([
                'nid' => $request->kode_asset_qr,
                'ccatatan' => $request->ccatatan,
            ]);
        }

        if ($request->jenis_asset === 'NON_QR') {
            $request->validate([
                'kode_asset_nonqr' => 'required|string|exists:masset_noqr,ckode',
                'qty' => 'required|integer|min:1',
                'ccatatan' => 'nullable|string|max:100',
            ]);

            AssetService::pemusnahanNonQr([
                'ckode' => $request->kode_asset_nonqr,
                'qty' => $request->qty,
                'ccatatan' => $request->ccatatan,
            ]);
        }

        return back()->with('success', 'Asset berhasil dimusnahkan');
    }

    public function perbaikanQr(Request $request)
    {
        $jenis  = $request->jenis_asset;
        $status = $request->cstatus;

        Log::info('=== START PERBAIKAN ===');
        Log::info('REQUEST DATA', $request->all());

        try {

            DB::transaction(function () use ($request, $jenis, $status) {

                $cjnstrans = $status === 'Perbaikan' ? 'ServiceIn' : 'ServiceOut';

                Log::info('Jenis Asset: ' . $jenis);
                Log::info('Status: ' . $status);
                Log::info('Jenis Transaksi: ' . $cjnstrans);

                /**
                 * =========================
                 * QR (UNIT)
                 * =========================
                 */
                if ($jenis === 'QR') {

                    Log::info('MASUK QR FLOW');

                    $validated = $request->validate([
                        'kode_asset_qr' => 'required|integer|exists:masset_qr,nid',
                        'ccatatan'      => 'nullable|string|max:255',
                    ]);

                    Log::info('VALIDATED QR', $validated);

                    $asset = MassetQr::where('nid', $validated['kode_asset_qr'])
                        ->lockForUpdate()
                        ->first();

                    if (!$asset) {
                        Log::error('QR ASSET NOT FOUND');
                        throw new \Exception('Asset QR tidak ditemukan');
                    }

                    Log::info('ASSET QR', $asset->toArray());

                    $asset->update([
                        'cstatus'  => $status,
                        'dtrans'   => now(),
                        'ccatatan' => $validated['ccatatan'] ?? null,
                    ]);

                    Log::info('QR UPDATED');

                    MassetTrans::create([
                        'ngrpid'      => $asset->nidsubkat,
                        'cjnstrans'   => $cjnstrans,
                        'dtrans'      => now(),
                        'cnotrans'    => 'SV/' . now()->format('ym') . '-' . rand(1000, 9999),

                        'ckode'       => $asset->cqr,
                        'cnama'       => $asset->cnama,
                        'nlokasi'     => $asset->niddept,
                        'nhrgbeli'    => $qr->nbeli ?? 0,

                        'nqty'        => 1,
                        'nqtyselesai' => $cjnstrans === 'ServiceOut' ? 1 : 0,
                        'ccatatan'    => $validated['ccatatan'] ?? null,
                    ]);

                    Log::info('TRANSAKSI QR BERHASIL');
                }

                /**
                 * =========================
                 * NON QR (STOK)
                 * =========================
                 */
                if ($jenis === 'NON_QR') {

                    Log::info('MASUK NON QR FLOW');

                    $validated = $request->validate([
                        'kode_asset_qr' => 'required|string',
                        'nidsubkat'     => 'required|integer',
                        'niddept'       => 'required|integer',
                        'qty'           => 'required|integer|min:1',
                        'ccatatan'      => 'nullable|string|max:255',
                    ]);

                    Log::info('VALIDATED NON QR', $validated);

                    /**
                     * 🔥 UPDATE STATUS (SPESIFIK 1 ROW)
                     */
                    DB::table('masset_noqr')
                        ->where('ckode', $validated['kode_asset_qr'])
                        ->where('nidsubkat', $validated['nidsubkat'])
                        ->where('niddept', $validated['niddept'])
                        ->update([
                            'cstatus'  => $status,
                            'dtrans'   => now(),
                            'ccatatan' => $validated['ccatatan'] ?? null,
                        ]);

                    Log::info('NON QR STATUS UPDATED');

                    /**
                     * 🔥 AMBIL DATA SETELAH UPDATE
                     */
                    $asset = DB::table('masset_noqr')
                        ->where('ckode', $validated['kode_asset_qr'])
                        ->where('nidsubkat', $validated['nidsubkat'])
                        ->where('niddept', $validated['niddept'])
                        ->first();

                    if (!$asset) {
                        Log::error('NON QR ASSET NOT FOUND AFTER UPDATE');
                        throw new \Exception('Asset tidak ditemukan');
                    }

                    /**
                     * 🔥 TRANSAKSI
                     */
                    MassetTrans::create([
                        'ngrpid'      => $asset->nidsubkat,
                        'cjnstrans'   => $cjnstrans,
                        'dtrans'      => now(),
                        'cnotrans'    => 'SV/' . now()->format('ym') . '-' . rand(1000, 9999),

                        'ckode'       => $asset->ckode,
                        'cnama'       => $asset->cnama,
                        'nlokasi'     => $asset->niddept,

                        'nqty'        => $validated['qty'],
                        'nqtyselesai' => $cjnstrans === 'ServiceOut' ? $validated['qty'] : 0,
                        'ccatatan'    => $validated['ccatatan'] ?? null,
                    ]);

                    Log::info('TRANSAKSI NON QR BERHASIL');
                }
            });

            Log::info('=== END SUCCESS ===');

            return back()->with('success', 'Transaksi perbaikan berhasil disimpan');

        } catch (\Throwable $e) {

            Log::error('=== ERROR PERBAIKAN ===');
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());

            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function mutasi(Request $request)
    {
        // =========================
        // MUTASI QR
        // =========================
        if ($request->jenis_asset === 'QR') {

            $validated = $request->validate([
                'kode_asset_qr' => 'required|integer|exists:masset_qr,nid',
                'niddept_tujuan' => 'required|exists:mdepartment,nid',
                'ccatatan'      => 'nullable|string|max:255',
            ]);

            AssetService::mutasiQr([
                'nid'           => $validated['kode_asset_qr'],
                'niddept_tujuan' => $validated['niddept_tujuan'],
                'ccatatan'      => $validated['ccatatan'] ?? null,
            ]);
        }

        // =========================
        // MUTASI NON QR
        // =========================
        if ($request->jenis_asset === 'NON_QR') {

            $validated = $request->validate([
                'kode_asset_nonqr' => 'required|string|exists:masset_noqr,ckode',
                'niddept_asal'     => 'required|exists:mdepartment,nid',
                'niddept_tujuan'   => 'required|exists:mdepartment,nid',
                'qty'              => 'required|integer|min:1',
                'ccatatan'         => 'nullable|string|max:255',
            ]);

            AssetService::mutasiNonQr([
                'ckode'          => $validated['kode_asset_nonqr'],
                'niddept_asal'   => $validated['niddept_asal'],   // 🔑 WAJIB
                'niddept_tujuan' => $validated['niddept_tujuan'],
                'qty'            => (int) $validated['qty'],
                'ccatatan'       => $validated['ccatatan'] ?? null,
            ]);
        }

        return back()->with('success', 'Mutasi asset berhasil disimpan');
    }

    public function getAssetByStatus(Request $request): JsonResponse
    {
        $status = $request->status;

        if ($status === 'Perbaikan') {
            $qr = MassetQr::with('subKategori')
                ->where('cstatus', 'Aktif')
                ->get();

            $nonqr = MassetNoQr::with('subKategori')
                ->where('cstatus', 'Aktif')
                ->get();
        } else {
            $qr = MassetQr::with('subKategori')
                ->where('cstatus', 'Perbaikan')
                ->get();

            $nonqr = MassetNoQr::with('subKategori')
                ->where('cstatus', 'Perbaikan')
                ->get();
        }

        $result = collect();

        // QR
        foreach ($qr as $item) {
            $result->push([
                'id'      => $item->nid,
                'kode'    => $item->cqr,
                'nama'    => $item->cnama ?? $item->subKategori->cnama,
                'status'  => $item->cstatus,
                'jenis'   => 'QR',
            ]);
        }

        // NON QR
        foreach ($nonqr as $item) {
            $result->push([
                'id'      => $item->ckode,
                'kode'    => $item->ckode,
                'nama'    => $item->cnama,
                'status'  => $item->cstatus,
                'jenis'   => 'NON_QR',
                'nidsubkat' => $item->nidsubkat,
                'niddept'   => $item->niddept,
            ]);
        }

        return response()->json($result);
    }
}
