<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class StockCardController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end   = $request->end_date ?? now()->endOfMonth()->toDateString();

        // 🔥 MASTER (ANTI DUPLIKAT)
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

        $data = DB::table($master)
            ->leftJoin('masset_trans as t', 'master.kode', '=', 't.ckode')

            ->selectRaw("
                master.kode as kode_produk,
                master.cnama as nama_produk,
                master.satuan,
                master.min_stok,

                -- STOK AWAL
                COALESCE(SUM(
                    CASE
                        WHEN t.dtrans < ? THEN COALESCE(t.nqty,0)
                        ELSE 0
                    END
                ),0) as awal,

                -- MASUK
                COALESCE(SUM(
                    CASE
                        WHEN t.dtrans BETWEEN ? AND ?
                        AND t.nqty > 0
                        THEN t.nqty ELSE 0
                    END
                ),0) as masuk,

                -- KELUAR
                COALESCE(SUM(
                    CASE
                        WHEN t.dtrans BETWEEN ? AND ?
                        AND t.nqty < 0
                        THEN ABS(t.nqty) ELSE 0
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

        // 🔥 HITUNG AKHIR + FIX QR
        $data->map(function ($item) {

            if ($item->awal == 0 && $item->masuk == 0 && $item->keluar == 0) {
                if ($item->satuan === 'Unit') {
                    $item->awal = 1;
                }
            }

            $item->akhir = $item->awal + $item->masuk - $item->keluar;

            return $item;
        });

        return view('kartustok.index', compact('data', 'start', 'end'));
    }
}
