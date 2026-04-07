<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StockCardController extends Controller
{
    public function index(Request $request)
    {
        $start  = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end    = $request->end_date ?? now()->endOfMonth()->toDateString();
        $kode   = $request->kode;
        $lokasi = $request->lokasi; // 🔥 NEW

        // 🔥 MASTER
        $master = DB::raw("
        (
            SELECT
                kode,
                MAX(cnama) as cnama,
                MAX(satuan) as satuan,
                MAX(min_stok) as min_stok
            FROM (
                SELECT
                    cqr as kode,
                    cnama,
                    'Unit' as satuan,
                    1 as min_stok
                FROM masset_qr

                UNION ALL

                SELECT
                    ckode as kode,
                    cnama,
                    csatuan as satuan,
                    COALESCE(nminstok,0) as min_stok
                FROM masset_noqr
            ) x
            GROUP BY kode
        ) as master
    ");

        // =====================================
        // 🔥 SUMMARY
        // =====================================
        $query = DB::table($master)
            ->leftJoin('masset_trans as t', 'master.kode', '=', 't.ckode');

        // 🔥 FILTER LOKASI
        if ($lokasi) {
            $query->where('t.nlokasi', $lokasi);
        }

        $data = $query
            ->selectRaw("
                master.kode as kode_produk,
                master.cnama as nama_produk,
                master.satuan,
                master.min_stok,

                COALESCE(SUM(
                    CASE
                        WHEN t.dtrans < ?
                        " . ($lokasi ? "AND t.nlokasi = {$lokasi}" : "") . "
                        THEN COALESCE(t.nqty,0)
                        ELSE 0
                    END
                ),0) as awal,

                COALESCE(SUM(
                    CASE
                        WHEN t.dtrans BETWEEN ? AND ?
                        AND t.nqty > 0
                        " . ($lokasi ? "AND t.nlokasi = {$lokasi}" : "") . "
                        THEN t.nqty
                        ELSE 0
                    END
                ),0) as masuk,

                COALESCE(SUM(
                    CASE
                        WHEN t.dtrans BETWEEN ? AND ?
                        AND t.nqty < 0
                        " . ($lokasi ? "AND t.nlokasi = {$lokasi}" : "") . "
                        THEN ABS(t.nqty)
                        ELSE 0
                    END
                ),0) as keluar
            ", [$start, $start, $end, $start, $end])
            ->groupBy(
                'master.kode',
                'master.cnama',
                'master.satuan',
                'master.min_stok'
            )
            ->orderBy('master.kode')
            ->get();

        // 🔥 HITUNG AKHIR
        $data->map(function ($item) {
            if ($item->awal == 0 && $item->masuk == 0 && $item->keluar == 0) {
                if ($item->satuan === 'Unit') {
                    $item->awal = 1;
                }
            }

            $item->akhir = $item->awal + $item->masuk - $item->keluar;
            return $item;
        });

        // =====================================
        // 🔥 DETAIL
        // =====================================
        $trans = [];
        $stok_awal = 0;
        $nama_barang = null;

        if ($kode) {

            $barang = collect($data)->firstWhere('kode_produk', $kode);
            $nama_barang = $barang->nama_produk ?? '-';

            // 🔥 stok awal (pakai lokasi juga)
            $stokAwalQuery = DB::table('masset_trans')
                ->where('ckode', $kode)
                ->whereDate('dtrans', '<', $start);

            if ($lokasi) {
                $stokAwalQuery->where('nlokasi', $lokasi);
            }

            $stok_awal = $stokAwalQuery->sum('nqty') ?? 0;

            // 🔥 transaksi detail
            $transQuery = DB::table('masset_trans')
                ->where('ckode', $kode)
                ->whereBetween('dtrans', [$start, $end]);

            if ($lokasi) {
                $transQuery->where('nlokasi', $lokasi);
            }

            $trans = $transQuery
                ->orderBy('dtrans')
                ->get();

            // 🔥 running saldo
            $saldo = $stok_awal;

            foreach ($trans as $t) {

                if ($t->nqty > 0) {
                    $t->masuk = $t->nqty;
                    $t->keluar = 0;
                    $saldo += $t->nqty;
                } else {
                    $t->masuk = 0;
                    $t->keluar = abs($t->nqty);
                    $saldo -= abs($t->nqty);
                }

                $t->saldo = $saldo;

                // 🔥 mapping jenis
                $mapJenis = [
                    'Add'        => 'Penambahan',
                    'MoveIn'     => 'Mutasi Masuk',
                    'MoveOut'    => 'Mutasi Keluar',
                    'ServiceIn'  => 'Perbaikan Selesai',
                    'ServiceOut' => 'Perbaikan Masuk',
                    'Dispose'    => 'Pemusnahan',
                ];

                $jenisRaw = $t->cjnstrans ?? '';
                $jenis = $mapJenis[$jenisRaw] ?? $jenisRaw;

                $t->no_trans = $t->cnotrans ?? '-';

                $ket = $t->ccatatan ?? '';
                $t->keterangan = $ket
                    ? $jenis . ' - ' . $ket
                    : $jenis;
            }
        }

        // 🔥 ambil list lokasi buat dropdown
        $lokasiList = DB::table('mdepartment')->get();

        return view('kartustok.index', compact(
            'data',
            'start',
            'end',
            'kode',
            'trans',
            'stok_awal',
            'nama_barang',
            'lokasiList', // 🔥 NEW
            'lokasi'      // 🔥 NEW
        ));
    }

    public function detail(Request $request)
    {
        $kode   = $request->kode;
        $start  = $request->start_date;
        $end    = $request->end_date;
        $lokasi = $request->lokasi;

        // stok awal
        $stok_awal = DB::table('masset_trans')
            ->where('ckode', $kode)
            ->whereDate('dtrans', '<', $start)
            ->when($lokasi, fn ($q) => $q->where('nlokasi', $lokasi))
            ->sum('nqty');

        // transaksi
        $trans = DB::table('masset_trans')
            ->where('ckode', $kode)
            ->whereBetween('dtrans', [$start, $end])
            ->when($lokasi, fn ($q) => $q->where('nlokasi', $lokasi))
            ->orderBy('dtrans')
            ->get();

        $saldo = $stok_awal;

        foreach ($trans as $t) {
            if ($t->nqty > 0) {
                $t->masuk = $t->nqty;
                $t->keluar = 0;
                $saldo += $t->nqty;
            } else {
                $t->masuk = 0;
                $t->keluar = abs($t->nqty);
                $saldo -= abs($t->nqty);
            }

            $t->saldo = $saldo;
        }

        return response()->json([
            'stok_awal' => $stok_awal,
            'trans' => $trans
        ]);
    }
}
