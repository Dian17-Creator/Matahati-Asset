<?php

namespace App\Http\Controllers;

use App\Models\MassetTrans;
use App\Models\MassetNoQr;
use App\Models\MassetKat;
use App\Models\MassetSubKat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AssetService;
use Illuminate\Support\Facades\Log;

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

            // 🔥 TAMBAH INI
            'kategoriFilter' => MassetKat::orderBy('cnama')->get(),
            'subKategoriFilter' => MassetSubKat::orderBy('cnama')->get(),
        ]);
    }

    /**
     * =========================
     * STORE TRANSAKSI (ADD)
     * =========================
     */
    public function store(Request $request)
    {
        try {

            Log::info('=== START STORE TRANSAKSI ===', [
                'request' => $request->all()
            ]);

            DB::transaction(function () use ($request) {

                Log::info('--- MASUK TRANSACTION ---');

                /**
                 * ================= QR =================
                 */
                if ($request->jenis_asset === 'QR') {

                    Log::info('PROSES QR');

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

                    Log::info('VALIDATED QR', $validated);

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

                    Log::info('QR CREATED', (array) $qr);

                    $periode = now()->format('ym');
                    $urut = MassetTrans::where('cjnstrans', 'Add')
                        ->whereRaw("DATE_FORMAT(dtrans,'%y%m') = ?", [$periode])
                        ->lockForUpdate()
                        ->count() + 1;

                    $cnotrans = 'AD/'.$periode.'-'.str_pad($urut, 4, '0', STR_PAD_LEFT);

                    Log::info('NO TRANSAKSI', ['cnotrans' => $cnotrans]);

                    $namaFoto = null;
                    if ($request->hasFile('foto')) {
                        $file = $request->file('foto');
                        $namaFoto = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                        $file->move(public_path('uploads/asset'), $namaFoto);

                        Log::info('UPLOAD FOTO QR', ['file' => $namaFoto]);
                    }

                    MassetTrans::create([
                        'ngrpid'        => $qr->nidsubkat,
                        'cjnstrans'     => 'Add',
                        'dtrans'        => now(),
                        'cnotrans'      => $cnotrans,
                        'ckode'         => $qr->cqr,
                        'cnama'         => $qr->cnama,
                        'cmerk'         => $validated['cmerk'] ?? null,
                        'nlokasi'       => $qr->niddept,
                        'dbeli'         => $qr->dbeli,
                        'dgaransi'      => $validated['dgaransi'] ?? null,
                        'nhrgbeli'      => $qr->nbeli ?? 0,
                        'nqty'          => 1,
                        'nqtyselesai'   => 0,
                        'creftrans'     => $cnotrans,
                        'ccatatan'      => $validated['ccatatan'] ?? null,
                        'dreffoto'      => $namaFoto,
                    ]);

                    Log::info('INSERT TRANSAKSI QR SUCCESS');
                }

                /**
 * ================= NON QR =================
 */
                if ($request->jenis_asset === 'NON_QR') {

                    Log::info('PROSES NON QR');

                    $validated = $request->validate([
                        'ckode_asset_nonqr' => 'required|exists:masset_noqr,ckode',
                        'nqty'              => 'required|integer|min:1',
                        'dbeli'             => 'nullable|date',
                        'nhrgbeli'          => 'nullable|numeric',
                        'ccatatan'          => 'nullable|string',
                        'niddept'           => 'required|integer', // 🔥 WAJIB DARI FORM
                    ]);

                    Log::info('VALIDATED NON QR', $validated);

                    // 🔥 PASTIKAN LOKASI DARI FORM (BUKAN DARI AUTH)
                    $niddept = $validated['niddept'];

                    Log::info('NIDDEPT FINAL', ['niddept' => $niddept]);

                    // 🔒 AMBIL DATA SPESIFIK (kode + lokasi)
                    $nonQr = MassetNoQr::where('ckode', $validated['ckode_asset_nonqr'])
                        ->where('niddept', $niddept)
                        ->lockForUpdate()
                        ->first();

                    if (!$nonQr) {
                        Log::error('NON QR NOT FOUND', [
                            'ckode'   => $validated['ckode_asset_nonqr'],
                            'niddept' => $niddept
                        ]);
                        throw new \Exception('Asset Non QR tidak ditemukan di lokasi tersebut');
                    }

                    Log::info('DATA NON QR', (array) $nonQr);

                    /**
                     * NO TRANSAKSI
                     */
                    $periode = now()->format('ym');
                    $urut = MassetTrans::where('cjnstrans', 'Add')
                        ->whereRaw("DATE_FORMAT(dtrans,'%y%m') = ?", [$periode])
                        ->lockForUpdate()
                        ->count() + 1;

                    $cnotrans = 'AD/'.$periode.'-'.str_pad($urut, 4, '0', STR_PAD_LEFT);

                    Log::info('NO TRANSAKSI', ['cnotrans' => $cnotrans]);

                    /**
                     * UPLOAD FOTO
                     */
                    $namaFoto = null;
                    if ($request->hasFile('foto')) {
                        $file = $request->file('foto');
                        $namaFoto = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                        $file->move(public_path('uploads/transaksi'), $namaFoto);

                        Log::info('UPLOAD FOTO NON QR', ['file' => $namaFoto]);
                    }

                    /**
                     * INSERT TRANSAKSI
                     */
                    MassetTrans::create([
                        'ngrpid'      => $nonQr->nidsubkat,
                        'cjnstrans'   => 'Add',
                        'dtrans'      => now(),
                        'cnotrans'    => $cnotrans,

                        'ckode'       => $nonQr->ckode,
                        'cnama'       => $nonQr->cnama,
                        'cmerk'       => $nonQr->cmerk ?? null,
                        'nlokasi'     => $nonQr->niddept,

                        'nqty'        => $validated['nqty'],
                        'dbeli'       => $validated['dbeli'] ?? null,
                        'nhrgbeli'    => $validated['nhrgbeli'] ?? 0,

                        'nqtyselesai' => 0,
                        'creftrans'   => $cnotrans,
                        'ccatatan'    => $validated['ccatatan'] ?? null,
                        'dreffoto'    => $namaFoto,
                    ]);

                    Log::info('INSERT TRANSAKSI NON QR SUCCESS');

                    /**
                     * UPDATE STOK (SUPER AMAN 🔥)
                     */
                    $affected = DB::table('masset_noqr')
                        ->where('ckode', $nonQr->ckode)
                        ->where('niddept', $nonQr->niddept)
                        ->where('nidsubkat', $nonQr->nidsubkat) // 🔥 extra safety
                        ->update([
                            'nqty'   => DB::raw('nqty + '.(int) $validated['nqty']),
                            'dtrans' => now(),
                        ]);

                    if ($affected === 0) {
                        Log::error('UPDATE STOK GAGAL');
                        throw new \Exception('Gagal update stok');
                    }

                    Log::info('UPDATE STOK SUCCESS', [
                        'qty_tambah' => $validated['nqty']
                    ]);
                }

                Log::info('--- END TRANSACTION ---');
            });

            Log::info('=== SUCCESS STORE ===');

            return redirect()->route('asset.index')
                ->with('success', 'Transaksi asset berhasil disimpan');

        } catch (\Throwable $e) {

            Log::error('=== ERROR STORE TRANSAKSI ===', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    public function transaksiAjax(Request $request)
    {
        $sort = $request->get('sort', 'tanggal');
        $direction = $request->get('direction', 'desc');

        // mapping FE → DB
        $sortable = [
            'lokasi' => 'nlokasi',
            'tanggal' => 'dtrans',
            'jenis' => 'cjnstrans',
            'kode' => 'ckode',
            'nama' => 'cnama',
        ];

        $query = MassetTrans::with(['subKategori.kategori','department']);

        // ================= FILTER =================
        if ($request->jenis) {

            if (in_array($request->jenis, ['MoveIn','MoveOut'])) {
                // 🔥 langsung karena FE sudah sesuai DB
                $query->where('cjnstrans', $request->jenis);

            } else {

                $map = [
                    'Penambahan'        => 'Add',
                    'Perbaikan Masuk'   => 'ServiceIn',
                    'Perbaikan Selesai' => 'ServiceOut',
                    'Pemusnahan'        => 'Dispose',
                ];

                if (isset($map[$request->jenis])) {
                    $query->where('cjnstrans', $map[$request->jenis]);
                }
            }
        }

        if ($request->kategori) {
            $query->whereHas(
                'subKategori.kategori',
                fn ($q) => $q->where('cnama', $request->kategori)
            );
        }

        if ($request->subkategori) {
            $query->whereHas(
                'subKategori',
                fn ($q) => $q->where('cnama', $request->subkategori)
            );
        }

        // ================= SORTING (INI YANG KURANG) =================
        if (isset($sortable[$sort])) {
            $query->orderBy($sortable[$sort], $direction);
        } else {
            $query->orderByDesc('dtrans');
        }

        $transaksi = $query
            ->paginate(5)
            ->withQueryString();

        return view('Asset.components.partials.transaksi_table', compact('transaksi'));
    }
}
