<?php

namespace App\Http\Controllers;

use App\Models\MassetTrans;
use App\Models\MassetQr;
use App\Models\MassetNoQr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $validated = $request->validate([
            'ckode_asset' => 'required|string',
            'nlokasi'     => 'required|integer|exists:mdepartment,nid',
            'nqty'        => 'required|integer|min:1',

            'dbeli'       => 'nullable|date',
            'cmerk'       => 'nullable|string|max:50',
            'dgaransi'    => 'nullable|date',
            'nhrgbeli'    => 'nullable|numeric',
            'ccatatan'    => 'nullable|string|max:100',

            'foto'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request) {

            /*
            ======================
            CARI ASSET (QR / NON QR)
            ======================
            */
            $qr = MassetQr::with('subKategori')
                ->where('cqr', $validated['ckode_asset'])
                ->first();

            $nonQr = null;
            if (! $qr) {
                $nonQr = MassetNoQr::with('subKategori')
                    ->where('ckode', $validated['ckode_asset'])
                    ->lockForUpdate() // 🔒 penting
                    ->first();
            }

            if (! $qr && ! $nonQr) {
                throw new \Exception('Kode asset tidak valid');
            }

            $asset = $qr ?? $nonQr;

            /*
            ======================
            GENERATE NOMOR TRANSAKSI
            FORMAT: AD/YYMM-XXXX
            ======================
            */
            $jenis   = 'add';
            $prefix  = 'AD';
            $periode = now()->format('ym');

            $urut = MassetTrans::where('cjnstrans', $jenis)
                ->whereRaw("DATE_FORMAT(dtrans,'%y%m') = ?", [$periode])
                ->lockForUpdate()
                ->count() + 1;

            $cnotrans = $prefix.'/'.$periode.'-'.str_pad($urut, 4, '0', STR_PAD_LEFT);

            /*
            ======================
            HANDLE UPLOAD FOTO
            ======================
            */
            $namaFoto = null;
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $namaFoto = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/asset'), $namaFoto);
            }

            /*
            ======================
            SIMPAN TRANSAKSI ADD
            ======================
            */
            MassetTrans::create([
                'ngrpid'     => $asset->nidsubkat,
                'cjnstrans'  => $jenis,
                'dtrans'     => Carbon::now(),
                'cnotrans'   => $cnotrans,

                'ckode'      => $validated['ckode_asset'],
                'cnama'      => $asset->cnama ?? $asset->subKategori->cnama,
                'nlokasi'    => $validated['nlokasi'],

                'nqty'       => $validated['nqty'],
                'dbeli'      => $validated['dbeli'],
                'cmerk'      => $validated['cmerk'],
                'dgaransi'   => $validated['dgaransi'],
                'nhrgbeli'   => $validated['nhrgbeli'] ?? 0,
                'ccatatan'   => $validated['ccatatan'],
                'dreffoto'   => $namaFoto,
                'fdone'      => 0,
            ]);

            // ======================
            // UPDATE STOK NON QR (FIX BUG)
            // ======================
            if ($nonQr) {

                MassetNoQr::where('ckode', $validated['ckode_asset'])
                    ->where('niddept', $validated['nlokasi']) // 🔥 WAJIB
                    ->update([
                        'nqty'     => DB::raw('nqty + '.(int) $validated['nqty']),
                        'ccatatan' => $validated['ccatatan']
                                        ? $validated['ccatatan']
                                        : $nonQr->ccatatan,
                        'dtrans'   => now(),
                    ]);
            }
        });

        return back()->with('success', 'Transaksi penambahan asset berhasil disimpan');
    }
}
