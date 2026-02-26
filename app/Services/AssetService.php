<?php

namespace App\Services;

use App\Models\MassetSubKat;
use App\Models\MassetQr;
use App\Models\MassetNoQr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetService
{
    public static function store(array $data)
    {
        return DB::transaction(function () use ($data) {

            $subkat = MassetSubKat::with('kategori')
                ->findOrFail($data['nidsubkat']);

            /**
             * =========================
             * ASSET QR (UNIT)
             * =========================
             */
            if ($subkat->isQr()) {

                $lastUrut = MassetQr::where('nidsubkat', $subkat->nid)
                    ->max('nurut');

                $nurut = ($lastUrut ?? 0) + 1;

                // ✅ FORMAT QR BARU
                $counterFormat = str_pad($nurut, 4, '0', STR_PAD_LEFT);
                $qrCode = $subkat->kategori->ckode
                    . '-' . $subkat->ckode
                    . '-' . $counterFormat;

                return MassetQr::create([
                    'nidsubkat' => $subkat->nid,
                    'niddept'   => $data['niddept'],
                    'nurut'     => $nurut,
                    'cqr'       => $qrCode,
                    'cnama'     => $data['cnama'] ?? null,
                    'dbeli'     => $data['dbeli'] ?? null,
                    'nbeli'     => $data['nbeli'] ?? null,
                    'cstatus'   => $data['cstatus'] ?? 'Aktif',
                    'dtrans'    => now(),
                    'ccatatan'  => $data['ccatatan'] ?? null,
                    'dcreated'  => now(),
                ]);
            }

            /**
             * =========================
             * ASSET NON QR (STOK)
             * =========================
             */
            if (empty($data['msatuan_id'])) {
                throw new \Exception('Satuan wajib diisi untuk asset Non QR');
            }

            if (empty($data['nqty'])) {
                throw new \Exception('Qty wajib diisi untuk asset Non QR');
            }

            if (empty($data['kode_urut'])) {
                throw new \Exception('Kode urut wajib diisi untuk asset Non QR');
            }

            if (empty($data['cnama'])) {
                throw new \Exception('Nama asset wajib diisi untuk asset Non QR');
            }

            // ✅ BENTUK KODE FINAL
            $ckode = $subkat->kategori->ckode
                   . '-' . $subkat->ckode
                   . '-' . $data['kode_urut'];

            // 🔒 CARI BERDASARKAN CKODE (BUKAN SUBKAT!)
            $existing = MassetNoQr::where('ckode', $ckode)
                ->lockForUpdate()
                ->first();

            if ($existing) {

                // ✅ UPDATE 1 BARIS SAJA
                $existing->update([
                    'nqty'       => $existing->nqty + (int) $data['nqty'],
                    'nminstok'   => $data['nminstok'] ?? $existing->nminstok,
                    'msatuan_id' => $data['msatuan_id'],
                    'ccatatan'   => $data['ccatatan'] ?? $existing->ccatatan,
                    'dtrans'     => now(),
                ]);

                return $existing->refresh();
            }

            // ✅ JIKA BELUM ADA → CREATE BARU
            return MassetNoQr::create([
                'nidsubkat'   => $subkat->nid,
                'niddept'     => $data['niddept'],
                'ckode'       => $ckode,
                'cnama'       => $data['cnama'],
                'nqty'        => (int) $data['nqty'],
                'nminstok'    => $data['nminstok'] ?? 0,
                'msatuan_id'  => $data['msatuan_id'],
                'dtrans'      => now(),
                'ccatatan'    => $data['ccatatan'] ?? null,
            ]);
        });
    }

    public static function pemusnahanQr(array $data)
    {
        return DB::transaction(function () use ($data) {

            $row = DB::table('masset_qr')
                ->where('nid', $data['nid'])
                ->lockForUpdate()
                ->first();

            if (! $row) {
                throw new \Exception('Asset QR tidak ditemukan');
            }

            if ($row->cstatus !== 'Aktif') {
                throw new \Exception('Asset QR sudah Non Aktif');
            }

            // ======================
            // INSERT TRANSAKSI (DISPOSE)
            // ======================
            DB::table('masset_trans')->insert([
                'ngrpid'    => $row->nidsubkat,
                'cjnstrans' => 'Dispose',
                'dtrans'    => now(),
                'cnotrans'  => self::generateNoTransDispose(),

                'ckode'     => $row->cqr,
                'cnama'     => $row->cnama,
                'nlokasi'   => $row->niddept,

                // 🔥 SNAPSHOT DATA BELI
                'dbeli'     => $row->dbeli,
                'nhrgbeli'  => $row->nbeli ?? 0,

                'nqty'      => -1,
                'ccatatan'  => $data['ccatatan'] ?? $row->ccatatan,
                'fdone'     => 1,
            ]);

            // ======================
            // UPDATE ASSET QR
            // ======================
            DB::table('masset_qr')
                ->where('nid', $data['nid'])
                ->update([
                    'cstatus'  => 'Non Aktif',
                    'ccatatan' => $data['ccatatan'] ?? $row->ccatatan,
                    'dtrans'   => now(),
                ]);

            return true;
        });
    }

    public static function pemusnahanNonQr(array $data)
    {
        return DB::transaction(function () use ($data) {

            $row = DB::table('masset_noqr')
                ->where('ckode', $data['ckode'])
                ->lockForUpdate()
                ->first();

            if (! $row) {
                throw new \Exception('Asset Non QR tidak ditemukan');
            }

            if ($row->nqty < $data['qty']) {
                throw new \Exception('Qty melebihi stok');
            }

            // ======================
            // INSERT TRANSAKSI (DISPOSE)
            // ======================
            DB::table('masset_trans')->insert([
                'ngrpid'    => $row->nidsubkat,
                'cjnstrans' => 'Dispose',
                'dtrans'    => now(),
                'cnotrans'  => self::generateNoTransDispose(),

                'ckode'     => $row->ckode,
                'cnama'     => $row->cnama,
                'nlokasi'   => $row->niddept,

                'nqty'      => -1 * (int) $data['qty'],
                'ccatatan'  => $data['ccatatan'] ?? $row->ccatatan,
                'fdone'     => 1,
            ]);

            // ======================
            // UPDATE STOK NON QR
            // ======================
            DB::table('masset_noqr')
                ->where('ckode', $data['ckode'])
                ->update([
                    'nqty'     => $row->nqty - $data['qty'],
                    'ccatatan' => $data['ccatatan'] ?? $row->ccatatan,
                    'dtrans'   => now(),
                ]);

            return true;
        });
    }

    public static function perbaikanQr(array $data)
    {
        return DB::transaction(function () use ($data) {

            $row = DB::table('masset_qr')
                ->where('nid', $data['nid'])
                ->lockForUpdate()
                ->first();

            if (! $row) {
                throw new \Exception('Asset QR tidak ditemukan');
            }

            if ($row->cstatus === 'Non Aktif') {
                throw new \Exception('Asset QR Non Aktif tidak dapat diperbaiki');
            }

            // =========================
            // TENTUKAN JENIS TRANSAKSI
            // =========================
            if ($row->cstatus === 'Aktif' && $data['cstatus'] === 'Perbaikan') {
                $jenis  = 'ServiceIn';   // masuk perbaikan
                $prefix = 'SI';
            } elseif ($row->cstatus === 'Perbaikan' && $data['cstatus'] === 'Aktif') {
                $jenis  = 'ServiceOut';  // selesai perbaikan
                $prefix = 'SO';
            } else {
                // tidak ada perubahan status
                throw new \Exception('Perubahan status tidak valid');
            }

            // =========================
            // GENERATE NOMOR TRANSAKSI
            // =========================
            $periode = now()->format('ym');

            $urut = DB::table('masset_trans')
                ->where('cjnstrans', $jenis)
                ->whereRaw("DATE_FORMAT(dtrans,'%y%m') = ?", [$periode])
                ->count() + 1;

            $cnotrans = $prefix.'/'.$periode.'-'.str_pad($urut, 4, '0', STR_PAD_LEFT);

            // =========================
            // INSERT TRANSAKSI
            // =========================
            DB::table('masset_trans')->insert([
                'ngrpid'    => $row->nidsubkat,
                'cjnstrans' => $jenis,
                'dtrans'    => $data['dtrans'],
                'cnotrans'  => $cnotrans,
                'ckode'     => $row->cqr,
                'cnama'     => $row->cnama,
                'nlokasi'   => $row->niddept,
                'dbeli'     => $row->dbeli,
                'nhrgbeli'  => $row->nbeli ?? 0,
                'nqty'      => 1,
                'ccatatan'  => $data['ccatatan'] ?? $row->ccatatan,
                'fdone'     => 1,
            ]);

            // =========================
            // UPDATE STATUS ASSET QR
            // =========================
            DB::table('masset_qr')
                ->where('nid', $data['nid'])
                ->update([
                    'cstatus'  => $data['cstatus'],
                    'dtrans'   => $data['dtrans'],
                    'ccatatan' => $data['ccatatan'] ?? $row->ccatatan,
                ]);

            return true;
        });
    }

    public static function mutasiQr(array $data)
    {
        return DB::transaction(function () use ($data) {

            $row = DB::table('masset_qr')
                ->where('nid', $data['nid'])
                ->lockForUpdate()
                ->first();

            if (! $row) {
                throw new \Exception('Asset QR tidak ditemukan');
            }

            if ($row->cstatus !== 'Aktif') {
                throw new \Exception('Hanya asset QR Aktif yang dapat dimutasi');
            }

            if ($row->niddept == $data['niddept_tujuan']) {
                throw new \Exception('Lokasi tujuan tidak boleh sama dengan lokasi asal');
            }

            // ======================
            // GENERATE NO TRANS
            // ======================
            $cnotrans = self::generateNoTransMove();

            // ======================
            // INSERT TRANSAKSI
            // ======================
            DB::table('masset_trans')->insert([
                'ngrpid'    => $row->nidsubkat,
                'cjnstrans' => 'Move',
                'dtrans'    => now(),
                'cnotrans'  => $cnotrans,

                'ckode'     => $row->cqr,
                'cnama'     => $row->cnama,
                'nlokasi'   => $data['niddept_tujuan'], // lokasi baru

                'dbeli'     => $row->dbeli,
                'nhrgbeli'  => $row->nbeli ?? 0,

                'nqty'      => 0, // QR tidak mengubah qty
                'ccatatan'  => $data['ccatatan'] ?? 'Mutasi lokasi',
                'fdone'     => 1,
            ]);

            // ======================
            // UPDATE ASSET
            // ======================
            DB::table('masset_qr')
                ->where('nid', $data['nid'])
                ->update([
                    'niddept'  => $data['niddept_tujuan'],
                    'dtrans'   => now(),
                    'ccatatan' => $data['ccatatan'] ?? $row->ccatatan,
                ]);

            return true;
        });
    }

    public static function mutasiNonQr(array $data)
    {
        return DB::transaction(function () use ($data) {

            $asal = DB::table('masset_noqr')
                ->where('ckode', $data['ckode'])
                ->where('niddept', $data['niddept_asal'])
                ->lockForUpdate()
                ->first();

            if (! $asal) {
                throw new \Exception('Asset Non QR tidak ditemukan di lokasi asal');
            }

            if ($data['qty'] > $asal->nqty) {
                throw new \Exception('Qty mutasi melebihi stok asal');
            }

            if ($asal->niddept == $data['niddept_tujuan']) {
                throw new \Exception('Lokasi tujuan tidak boleh sama dengan lokasi asal');
            }

            // ======================
            // GENERATE NO TRANS
            // ======================
            $cnotrans = self::generateNoTransMove();

            // ======================
            // TRANSAKSI KELUAR (ASAL)
            // ======================
            DB::table('masset_trans')->insert([
                'ngrpid'    => $asal->nidsubkat,
                'cjnstrans' => 'Move',
                'dtrans'    => now(),
                'cnotrans'  => $cnotrans,

                'ckode'     => $asal->ckode,
                'cnama'     => $asal->cnama,
                'nlokasi'   => $asal->niddept,

                'nqty'      => -1 * (int) $data['qty'], // 🔥 KELUAR
                'ccatatan'  => $data['ccatatan'] ?? 'Mutasi keluar',
                'fdone'     => 1,
            ]);

            // ======================
            // TRANSAKSI MASUK (TUJUAN)
            // ======================
            DB::table('masset_trans')->insert([
                'ngrpid'    => $asal->nidsubkat,
                'cjnstrans' => 'Move',
                'dtrans'    => now(),
                'cnotrans'  => $cnotrans,

                'ckode'     => $asal->ckode,
                'cnama'     => $asal->cnama,
                'nlokasi'   => $data['niddept_tujuan'],

                'nqty'      => +1 * (int) $data['qty'], // 🔥 MASUK
                'ccatatan'  => $data['ccatatan'] ?? 'Mutasi masuk',
                'fdone'     => 1,
            ]);

            // ======================
            // UPDATE STOK ASAL
            // ======================
            DB::table('masset_noqr')
                ->where('ckode', $asal->ckode)
                ->where('niddept', $asal->niddept)
                ->update([
                    'nqty'   => $asal->nqty - $data['qty'],
                    'dtrans' => now(),
                ]);

            // ======================
            // UPDATE / INSERT TUJUAN
            // ======================
            $tujuan = DB::table('masset_noqr')
                ->where('ckode', $asal->ckode)
                ->where('niddept', $data['niddept_tujuan'])
                ->lockForUpdate()
                ->first();

            if ($tujuan) {

                DB::table('masset_noqr')
                    ->where('ckode', $asal->ckode)
                    ->where('niddept', $data['niddept_tujuan'])
                    ->update([
                        'nqty'     => $tujuan->nqty + $data['qty'],
                        'dtrans'   => now(),
                        'ccatatan' => $data['ccatatan'] ?? $tujuan->ccatatan,
                    ]);

            } else {

                DB::table('masset_noqr')->insert([
                    'nidsubkat'  => $asal->nidsubkat,
                    'niddept'    => $data['niddept_tujuan'],
                    'ckode'      => $asal->ckode,
                    'cnama'      => $asal->cnama,
                    'nqty'       => $data['qty'],
                    'nminstok'   => $asal->nminstok,
                    'msatuan_id' => $asal->msatuan_id,
                    'dtrans'     => now(),
                    'ccatatan'   => $data['ccatatan'] ?? $asal->ccatatan,
                ]);
            }

            return true;
        });
    }

    private static function generateNoTransDispose(): string
    {
        $periode = now()->format('ym');

        $urut = DB::table('masset_trans')
            ->where('cjnstrans', 'Dispose')
            ->whereRaw("DATE_FORMAT(dtrans,'%y%m') = ?", [$periode])
            ->count() + 1;

        return 'DP/'.$periode.'-'.str_pad($urut, 4, '0', STR_PAD_LEFT);
    }

    private static function generateNoTransMove(): string
    {
        $periode = now()->format('ym');

        $urut = DB::table('masset_trans')
            ->where('cjnstrans', 'Move')
            ->whereRaw("DATE_FORMAT(dtrans,'%y%m') = ?", [$periode])
            ->lockForUpdate() // 🔒 biar aman kalau concurrent
            ->count() + 1;

        return 'MV/'.$periode.'-'.str_pad($urut, 4, '0', STR_PAD_LEFT);
    }
}
