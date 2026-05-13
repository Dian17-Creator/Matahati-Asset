<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetReminder extends Model
{
    protected $table = 'asset_reminder';

    protected $fillable = [
        'asset_type',
        'asset_qr_id',
        'asset_noqr_code',
        'reminder_date',
        'note',
    ];

    protected $casts = [
        'reminder_date' => 'datetime',
    ];

    // Relasi Ke Masset Qr
    public function assetQr()
    {
        return $this->belongsTo(MassetQr::class, 'asset_qr_id', 'nid');
    }

    // Relasi Ke Masset No Qr
    public function assetNoQr()
    {
        return $this->belongsTo(MassetNoQr::class, 'asset_noqr_code', 'ckode');
    }

    public function getAssetAttribute()
    {
        if ($this->asset_type == 'QR') {
            return $this->assetQr;
        }

        return $this->assetNoQr;
    }
}
