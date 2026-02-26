<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryTransactionController extends Controller
{
    public function index(Request $request)
    {
        // ambil parameter bulan (format: YYYY-MM)
        $bulan = $request->query('bulan');

        $rows = collect(DB::select("
            (
                SELECT
                    'PENAMBAHAN'        AS jenis_transaksi,
                    mt.dbeli            AS tanggal,
                    mt.ckode            AS kode_barang,
                    mt.cnama            AS nama_barang,
                    1                   AS qty,
                    mt.ccatatan         AS catatan
                FROM masset_trans mt
            )

            UNION ALL

            (
                SELECT
                    CASE
                        WHEN mq.cstatus = 'Perbaikan' THEN 'PERBAIKAN'
                        WHEN mq.cstatus = 'Non Aktif' THEN 'PEMUSNAHAN'
                        WHEN mq.cstatus = 'Aktif'
                             AND UPPER(mq.ccatatan) LIKE '%MUTASI%' THEN 'MUTASI'
                        ELSE 'PENAMBAHAN'
                    END                 AS jenis_transaksi,
                    mq.dtrans           AS tanggal,
                    mq.cqr              AS kode_barang,
                    mq.cnama            AS nama_barang,
                    1                   AS qty,
                    mq.ccatatan         AS catatan
                FROM masset_qr mq
            )

            UNION ALL

            (
                SELECT
                    CASE
                        WHEN UPPER(mn.ccatatan) LIKE '%MUTASI%' THEN 'MUTASI'
                        WHEN UPPER(mn.ccatatan) LIKE '%PECAH%'
                          OR UPPER(mn.ccatatan) LIKE '%RUSAK%'
                          OR UPPER(mn.ccatatan) LIKE '%HILANG%' THEN 'PEMUSNAHAN'
                        ELSE 'PENAMBAHAN'
                    END                 AS jenis_transaksi,
                    mn.dtrans           AS tanggal,
                    mn.ckode            AS kode_barang,
                    mn.cnama            AS nama_barang,
                    mn.nqty             AS qty,
                    mn.ccatatan         AS catatan
                FROM masset_noqr mn
            )

            ORDER BY tanggal DESC
        "));

        /**
         * =========================
         * FILTER BULAN (JIKA ADA)
         * =========================
         */
        if ($bulan) {
            $rows = $rows->filter(function ($row) use ($bulan) {
                return date('Y-m', strtotime($row->tanggal)) === $bulan;
            });
        }

        /**
         * =========================
         * GROUP BY BULAN (Y-m)
         * =========================
         */
        $historyByMonth = $rows
            ->filter(fn ($r) => !empty($r->tanggal))
            ->groupBy(fn ($r) => date('Y-m', strtotime($r->tanggal)));

        return view('Asset.history', compact('historyByMonth', 'bulan'));
    }
}
