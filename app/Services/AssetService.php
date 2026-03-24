<?php

namespace App\Services;

use App\Models\MassetSubKat;
use App\Models\MassetQr;
use App\Models\MassetNoQr;
use App\Models\MassetTrans;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetService
{
    public static function store(array $data)
    {
        $subkat = MassetSubKat::findOrFail($data['nidsubkat']);

        if ($subkat->isQr()) {
            return self::storeQr($data);
        }

        return self::storeNonQr($data);
    }
    public static function storeQr(array $data): MassetQr
    {
        return DB::transaction(function () use ($data) {

            $subkat = MassetSubKat::with('kategori')
                ->findOrFail($data['nidsubkat']);

            if (! $subkat->isQr()) {
                throw new \Exception('Sub kategori bukan QR');
            }

            $lastUrut = MassetQr::where('nidsubkat', $subkat->nid)->max('nurut');
            $nurut = ($lastUrut ?? 0) + 1;

            $qrCode =
                $subkat->kategori->ckode . '-' .
                $subkat->ckode . '-' .
                str_pad($nurut, 4, '0', STR_PAD_LEFT);

            return MassetQr::create([
                'nidsubkat' => $subkat->nid,
                'niddept'   => $data['niddept'],
                'nurut'     => $nurut,
                'cqr'       => $qrCode,

                // 🔥 DATA UNIT (WAJIB KONSISTEN)
                'cnama'     => $data['cnama']     ?? null,
                'cmerk'     => $data['cmerk']     ?? null,
                'dbeli'     => $data['dbeli']     ?? null,
                'dgaransi'  => $data['dgaransi']  ?? null,
                'nbeli'     => $data['nbeli']     ?? 0,

                'cstatus'   => 'Aktif',
                'ccatatan'  => $data['ccatatan']  ?? null,
                'dtrans'    => now(),
                'dcreated'  => now(),
            ]);
        });
    }
    public static function storeNonQr(array $data): MassetNoQr
    {
        return DB::transaction(function () use ($data) {

            $subkat = MassetSubKat::with('kategori')
                ->findOrFail($data['nidsubkat']);

            if ($subkat->isQr()) {
                throw new \Exception('Sub kategori QR tidak boleh masuk Non QR');
            }

            foreach (['msatuan_id','kode_urut','cnama','niddept'] as $field) {
                if (empty($data[$field])) {
                    throw new \Exception("Field {$field} wajib diisi");
                }
            }

            $nqty = isset($data['nqty']) ? (int) $data['nqty'] : 0;

            $ckode =
                $subkat->kategori->ckode . '-' .
                $subkat->ckode . '-' .
                $data['kode_urut'];

            $existing = MassetNoQr::where('ckode', $ckode)
                ->where('niddept', $data['niddept'])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $existing->update([
                    'nqty' => $existing->nqty + $nqty,
                    'msatuan_id' => $data['msatuan_id'],
                    'nminstok'   => $data['nminstok'] ?? $existing->nminstok,
                    'ccatatan'   => $data['ccatatan'] ?? $existing->ccatatan,
                    'dtrans'     => now(),
                ]);

                return $existing->refresh();
            }

            return MassetNoQr::create([
                'nidsubkat'  => $subkat->nid,
                'niddept'    => $data['niddept'],
                'ckode'      => $ckode,
                'cnama'      => $data['cnama'],
                'nqty'       => $nqty,
                'msatuan_id' => $data['msatuan_id'],
                'nminstok'   => $data['nminstok'] ?? 0,
                'ccatatan'   => $data['ccatatan'] ?? null,
                'dtrans'     => now(),
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
                // 'fdone'     => 1,
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
                // 'fdone'     => 1,
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
                // 'fdone'     => 1,
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
    public static function mutasiQr(array $data): void
    {
        DB::transaction(function () use ($data) {

            $asset = MassetQr::findOrFail($data['nid']);

            $deptAsal   = $asset->niddept;
            $deptTujuan = $data['niddept_tujuan'];

            $cnotrans = self::generateNoTransMove();

            // =========================
            // MOVE OUT
            // =========================
            MassetTrans::create([
                'cjnstrans' => 'MoveOut',
                'cnotrans'  => $cnotrans,
                'nidqr'     => $asset->nid,
                'ckode'     => $asset->cqr,
                'ngrpid'    => $asset->nidsubkat,
                'cnama'     => $asset->cnama,
                'nlokasi'   => $deptAsal,
                'dbeli'     => $asset->dbeli,
                'nhrgbeli'  => $asset->nbeli ?? 0,
                'nqty'      => -1,
                'ccatatan'  => $data['ccatatan'],
                'dtrans'    => now(),
            ]);

            // =========================
            // MOVE IN
            // =========================
            MassetTrans::create([
                'cjnstrans' => 'MoveIn',
                'cnotrans'  => $cnotrans,
                'nidqr'     => $asset->nid,
                'ckode'     => $asset->cqr,        // ✅ FIX
                'ngrpid'    => $asset->nidsubkat,  // ✅ FIX
                'cnama'     => $asset->cnama,
                'nlokasi'   => $deptTujuan,
                'dbeli'     => $asset->dbeli,
                'nhrgbeli'  => $asset->nbeli ?? 0,
                'nqty'      => 1,
                'ccatatan'  => $data['ccatatan'],
                'dtrans'    => now(),
            ]);

            // =========================
            // UPDATE LOKASI
            // =========================
            $asset->update([
                'niddept' => $deptTujuan
            ]);
        });
    }
    public static function mutasiNonQr(array $data): void
    {
        DB::transaction(function () use ($data) {

            // 🚫 VALIDASI LOKASI
            if ($data['niddept_asal'] == $data['niddept_tujuan']) {
                throw new \Exception('Lokasi asal dan tujuan tidak boleh sama');
            }

            // =========================
            // LOCK DATA ASAL
            // =========================
            $assetAsal = DB::table('masset_noqr')
                ->where('ckode', $data['ckode'])
                ->where('niddept', $data['niddept_asal'])
                ->lockForUpdate()
                ->first();

            if (!$assetAsal) {
                throw new \Exception('Asset tidak ditemukan di lokasi asal');
            }

            // =========================
            // VALIDASI STOK
            // =========================
            if ($assetAsal->nqty < $data['qty']) {
                throw new \Exception('Stok tidak mencukupi untuk mutasi');
            }

            // =========================
            // GENERATE NO TRANSAKSI
            // =========================
            $cnotrans = self::generateNoTransMove();

            // =========================
            // MOVE OUT
            // =========================
            MassetTrans::create([
                'cjnstrans' => 'MoveOut',
                'cnotrans'  => $cnotrans,
                'ckode'     => $assetAsal->ckode,
                'ngrpid'    => $assetAsal->nidsubkat,
                'cnama'     => $assetAsal->cnama,
                'nlokasi'   => $assetAsal->niddept,
                'nqty'      => -$data['qty'],
                'ccatatan'  => $data['ccatatan'],
                'dtrans'    => now(),
            ]);

            // =========================
            // KURANGI STOK ASAL
            // =========================
            DB::table('masset_noqr')
                ->where('ckode', $assetAsal->ckode)
                ->where('niddept', $assetAsal->niddept)
                ->update([
                    'nqty' => DB::raw('nqty - '.$data['qty'])
                ]);

            // =========================
            // LOCK DATA TUJUAN
            // =========================
            $assetTujuan = DB::table('masset_noqr')
                ->where('ckode', $data['ckode'])
                ->where('niddept', $data['niddept_tujuan'])
                ->lockForUpdate()
                ->first();

            if ($assetTujuan) {
                // update
                DB::table('masset_noqr')
                    ->where('ckode', $data['ckode'])
                    ->where('niddept', $data['niddept_tujuan'])
                    ->update([
                        'nqty' => DB::raw('nqty + '.$data['qty'])
                    ]);
            } else {
                // insert baru
                DB::table('masset_noqr')->insert([
                    'ckode'     => $assetAsal->ckode,
                    'cnama'     => $assetAsal->cnama,
                    'nidsubkat' => $assetAsal->nidsubkat,
                    'niddept'   => $data['niddept_tujuan'],
                    'nqty'      => $data['qty'],
                    'msatuan_id' => $assetAsal->msatuan_id,
                    'nminstok'  => $assetAsal->nminstok ?? 0,
                    'ccatatan'  => $data['ccatatan'],
                    'dtrans'    => now(),
                ]);
            }

            // =========================
            // MOVE IN
            // =========================
            MassetTrans::create([
                'cjnstrans' => 'MoveIn',
                'cnotrans'  => $cnotrans,
                'ckode'     => $assetAsal->ckode,
                'ngrpid'    => $assetAsal->nidsubkat,
                'cnama'     => $assetAsal->cnama,
                'nlokasi'   => $data['niddept_tujuan'],
                'nqty'      => $data['qty'],
                'ccatatan'  => $data['ccatatan'],
                'dtrans'    => now(),
            ]);
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
            ->whereIn('cjnstrans', ['MoveIn','MoveOut'])
            ->whereRaw("DATE_FORMAT(dtrans,'%y%m') = ?", [$periode])
            ->distinct('cnotrans') // 🔥 INI KUNCI
            ->count('cnotrans') + 1;

        return 'MV/'.$periode.'-'.str_pad($urut, 4, '0', STR_PAD_LEFT);
    }
}
