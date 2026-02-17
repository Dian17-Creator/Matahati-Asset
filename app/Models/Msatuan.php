<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Msatuan extends Model
{
    protected $table = 'msatuan';

    protected $fillable = [
        'nama',
    ];

    public $timestamps = false;

    public function assetNonQr()
    {
        return $this->hasMany(\App\Models\MassetNoQr::class, 'msatuan_id');
    }

}
