<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\KartuStokMail;

class KartuStokService
{
    public function run()
    {
        $start = now()->toDateString();
        $end   = now()->toDateString();

        Log::info("START KartuStok {$start}");

        // 🔥 MASTER QUERY
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

        // 🔥 QUERY DATA
        $data = DB::table($master)
            ->leftJoin('masset_trans as t', 'master.kode', '=', 't.ckode')
            ->selectRaw("
                master.kode as kode_produk,
                master.cnama as nama_produk,
                master.satuan,
                master.min_stok,

                COALESCE(SUM(
                    CASE WHEN t.dtrans < ? THEN COALESCE(t.nqty,0) ELSE 0 END
                ),0) as awal,

                COALESCE(SUM(
                    CASE WHEN t.dtrans BETWEEN ? AND ? AND t.nqty > 0 THEN t.nqty ELSE 0 END
                ),0) as masuk,

                COALESCE(SUM(
                    CASE WHEN t.dtrans BETWEEN ? AND ? AND t.nqty < 0 THEN ABS(t.nqty) ELSE 0 END
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

        // 🔥 KIRIM EMAIL
        Mail::to('lordkuro07@gmail.com')
            ->send(new KartuStokMail($data, $start));

        Log::info("SUCCESS KartuStok {$start}");

        return true;
    }
}
