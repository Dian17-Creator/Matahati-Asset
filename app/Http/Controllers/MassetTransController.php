<?php

namespace App\Http\Controllers;

use App\Models\MassetTrans;
use App\Models\MassetNoQr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AssetService;

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
            ])->orderByDesc('dtrans')->paginate(10),
        ]);
    }

    /**
     * =========================
     * STORE TRANSAKSI (ADD)
     * =========================
     */
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

            /**
             * =================================================
             * TRANSAKSI QR (UNIT BARU)
             * =================================================
             */
            if ($request->jenis_asset === 'QR') {

                $validated = $request->validate([
                    'sub_kategori_id' => 'required|exists:masset_subkat,nid',
                    'nlokasi'         => 'required|exists:mdepartment,nid',
                    'nama_asset'      => 'nullable|string|max:255',
                    'cmerk'           => 'nullable|string|max:255',
                    'dbeli'           => 'nullable|date',
                    'dgaransi'        => 'nullable|date',
                    'nhrgbeli'        => 'nullable|numeric',
                    'ccatatan'        => 'nullable|string',
                    'foto'            => 'nullable|image|max:2048',
                ]);

                /**
                 * 1️⃣ SIMPAN ASSET QR (UNIT)
                 */
                $qr = AssetService::storeQr([
                    'nidsubkat' => $validated['sub_kategori_id'],
                    'niddept'   => $validated['nlokasi'],
                    'cnama'     => $validated['nama_asset'] ?? null,
                    'cmerk'     => $validated['cmerk'] ?? null,
                    'dbeli'     => $validated['dbeli'] ?? null,
                    'dgaransi'  => $validated['dgaransi'] ?? null,
                    'nbeli'     => $validated['nhrgbeli'] ?? 0,
                    'ccatatan'  => $validated['ccatatan'] ?? null,
                ]);

                /**
                 * 2️⃣ GENERATE NO TRANSAKSI
                 */
                $periode = now()->format('ym');
                $urut = MassetTrans::where('cjnstrans', 'Add')
                    ->whereRaw("DATE_FORMAT(dtrans,'%y%m') = ?", [$periode])
                    ->lockForUpdate()
                    ->count() + 1;

                $cnotrans = 'AD/'.$periode.'-'.str_pad($urut, 4, '0', STR_PAD_LEFT);

                /**
                 * 3️⃣ UPLOAD FOTO (OPSIONAL)
                 */
                $namaFoto = null;
                if ($request->hasFile('foto')) {
                    $file = $request->file('foto');
                    $namaFoto = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                    $file->move(public_path('uploads/asset'), $namaFoto);
                }

                /**
                 * 4️⃣ LOG TRANSAKSI QR
                 */
                MassetTrans::create([
                    'ngrpid'        => $qr->nidsubkat,
                    'cjnstrans'     => 'Add',
                    'dtrans'        => now(),
                    'cnotrans'      => $cnotrans,

                    'ckode'         => $qr->cqr,
                    'cnama'         => $qr->cnama,
                    'cmerk'         => $validated['cmerk'] ?? null,     // ✅ AMBIL DARI REQUEST
                    'nlokasi'       => $qr->niddept,

                    'dbeli'         => $qr->dbeli,
                    'dgaransi'      => $validated['dgaransi'] ?? null,  // ✅ REQUEST
                    'nhrgbeli'      => $qr->nbeli ?? 0,
                    'nqty'          => 1,

                    'nqtyselesai'   => 0,
                    'creftrans'     => $cnotrans,

                    'ccatatan'      => $validated['ccatatan'] ?? null,
                    'dreffoto'      => $namaFoto,
                ]);
            }

            /**
             * =================================================
             * TRANSAKSI NON QR (STOK)
             * =================================================
             */
            if ($request->jenis_asset === 'NON_QR') {

                $validated = $request->validate([
                    'ckode_asset_nonqr' => 'required|exists:masset_noqr,ckode',
                    'nqty'              => 'required|integer|min:1',
                    'dbeli'             => 'nullable|date',
                    'nhrgbeli'          => 'nullable|numeric',
                    'ccatatan'          => 'nullable|string',
                ]);

                $nonQr = MassetNoQr::where('ckode', $validated['ckode_asset_nonqr'])
                    ->lockForUpdate()
                    ->firstOrFail();

                /**
                 * NO TRANSAKSI
                 */
                $periode = now()->format('ym');
                $urut = MassetTrans::where('cjnstrans', 'Add')
                    ->whereRaw("DATE_FORMAT(dtrans,'%y%m') = ?", [$periode])
                    ->lockForUpdate()
                    ->count() + 1;

                $cnotrans = 'AD/'.$periode.'-'.str_pad($urut, 4, '0', STR_PAD_LEFT);

                /**
                 * LOG TRANSAKSI NON QR
                 */
                MassetTrans::create([
                    'ngrpid'        => $nonQr->nidsubkat,
                    'cjnstrans'     => 'Add',
                    'dtrans'        => now(),
                    'cnotrans'      => $cnotrans,

                    'ckode'         => $nonQr->ckode,
                    'cnama'         => $nonQr->cnama,
                    'cmerk'         => $nonQr->cmerk ?? null,
                    'nlokasi'       => $nonQr->niddept,

                    'nqty'          => $validated['nqty'],
                    'dbeli'         => $validated['dbeli'] ?? null,
                    'nhrgbeli'      => $validated['nhrgbeli'] ?? 0,

                    'nqtyselesai'   => 0,
                    'creftrans'     => $cnotrans,

                    'ccatatan'      => $validated['ccatatan'] ?? null,
                ]);

                /**
                 * UPDATE STOK
                 */
                $nonQr->update([
                    'nqty'   => $nonQr->nqty + $validated['nqty'],
                    'dtrans' => now(),
                ]);
            }
        });

        return back()->with('success', 'Transaksi asset berhasil disimpan');
    }

    public function transaksiAjax(Request $request)
    {
        $query = MassetTrans::with(['subKategori.kategori','department']);

        if ($request->jenis) {
            $map = [
                'Penambahan' => 'Add',
                'Mutasi' => 'Move',
                'Perbaikan Masuk' => 'ServiceIn',
                'Perbaikan Selesai' => 'ServiceOut',
                'Pemusnahan' => 'Dispose',
            ];
            $query->where('cjnstrans', $map[$request->jenis] ?? null);
        }

        if ($request->kategori) {
            $query->whereHas(
                'subKategori.kategori',
                fn ($q) =>
                $q->where('cnama', $request->kategori)
            );
        }

        if ($request->subkategori) {
            $query->whereHas(
                'subKategori',
                fn ($q) =>
                $q->where('cnama', $request->subkategori)
            );
        }

        $transaksi = $query
            ->orderByDesc('dtrans')
            ->paginate(5)
            ->withQueryString();

        return view('Asset.components.partials.transaksi_table', compact('transaksi'));
    }
}
