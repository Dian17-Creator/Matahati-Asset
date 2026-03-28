<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MassetReports extends Model
{
    protected $table = 'masset_reports';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'periode_start',
        'periode_end',
        'satuan',
        'min_stok',
        'stok_awal',
        'masuk',
        'keluar',
        'stok_akhir',
    ];

    protected $casts = [
        'periode_start' => 'date',
        'periode_end'   => 'date',
    ];
}
