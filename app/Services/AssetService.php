<?php

namespace App\Services;

use App\Models\MassetSubKat;
use App\Models\MassetQr;
use App\Models\MassetNoQr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AssetService
{
    /**
     * Simpan asset berdasarkan sub kategori (QR / Non QR)
     */
    public static function store(array $data)
    {
        return DB::transaction(function () use ($data) {

            $subkat = MassetSubKat::findOrFail($data['nidsubkat']);

            /**
             * =========================
             * ASSET QR (UNIT)
             * =========================
             */
            if ($subkat->isQr()) {

                $lastUrut = MassetQr::where('nidsubkat', $subkat->nid)
                    ->max('nurut');

                $nurut = ($lastUrut ?? 0) + 1;

                $qrCode = 'QR-' . $subkat->nid . '-' . $nurut;

                return MassetQr::create([
                    'nidsubkat' => $subkat->nid,
                    'niddept'   => $data['niddept'],
                    'nurut'     => $nurut,
                    'cqr'       => $qrCode,
                    'dbeli'     => $data['dbeli'] ?? null,
                    'nbeli'     => $data['nbeli'] ?? null,
                    'cstatus'   => $data['cstatus'] ?? 'AKTIF',
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

            // VALIDASI WAJIB
            if (empty($data['msatuan_id'])) {
                throw new \Exception('Satuan wajib diisi untuk asset Non QR');
            }

            if (empty($data['nqty'])) {
                throw new \Exception('Qty wajib diisi untuk asset Non QR');
            }

            $existing = MassetNoQr::where('nidsubkat', $subkat->nid)
                ->where('niddept', $data['niddept'])
                ->lockForUpdate()
                ->first();

            if ($existing) {

                MassetNoQr::where('nidsubkat', $subkat->nid)
                    ->where('niddept', $data['niddept'])
                    ->update([
                        'nqty'       => DB::raw('nqty + ' . (int) $data['nqty']),
                        'nminstok'   => $data['nminstok'] ?? $existing->nminstok, // ✅ FIX
                        'msatuan_id' => $data['msatuan_id'],
                        'ccatatan'   => $data['ccatatan'] ?? $existing->ccatatan,
                        'dtrans'     => now(),
                    ]);

                return $existing->refresh();
            }


            return MassetNoQr::create([
                'nidsubkat'   => $subkat->nid,
                'niddept'    => $data['niddept'],
                'nqty'       => (int) $data['nqty'],
                'nminstok'   => $data['nminstok'] ?? 0,
                'msatuan_id' => $data['msatuan_id'], // ✅
                'dtrans'     => now(),
                'ccatatan'   => $data['ccatatan'] ?? null,
            ]);
        });
    }

}
