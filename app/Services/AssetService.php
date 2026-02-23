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
                $qrCode = $subkat->kategori->ckode
                        . '-' . $subkat->ckode
                        . '-' . $nurut;

                return MassetQr::create([
                    'nidsubkat' => $subkat->nid,
                    'niddept'   => $data['niddept'],
                    'nurut'     => $nurut,
                    'cqr'       => $qrCode,
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
}
