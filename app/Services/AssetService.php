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

            // Ambil sub kategori
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
                    'dtrans'    => Carbon::now(),
                    'ccatatan'  => $data['ccatatan'] ?? null,
                    'dcreated'  => Carbon::now(),
                ]);
            }

            /**
             * =========================
             * ASSET NON QR (STOK)
             * =========================
             */

            // cek apakah stok sudah ada
            $existing = MassetNoQr::where('nidsubkat', $subkat->nid)
            ->where('niddept', $data['niddept'])
            ->lockForUpdate()
            ->first();

            if ($existing) {

                MassetNoQr::where('nidsubkat', $subkat->nid)
                    ->where('niddept', $data['niddept'])
                    ->update([
                        'nqty'     => DB::raw('nqty + ' . ($data['nqty'] ?? 1)),
                        'ccatatan' => $data['ccatatan'] ?? $existing->ccatatan,
                        'dtrans'   => now(),
                    ]);

                return $existing->refresh();
            }

            // jika belum ada → insert
            return MassetNoQr::create([
                'nidsubkat' => $subkat->nid,
                'niddept'   => $data['niddept'],
                'nqty'      => $data['nqty'] ?? 1,
                'nminstok'  => $data['nminstok'] ?? 0,
                'csatuan'   => $data['csatuan'] ?? null,
                'dtrans'    => now(),
                'ccatatan'  => $data['ccatatan'] ?? null,
            ]);
        });
    }
}
