<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MassetTrans extends Model
{
    use HasFactory;

    protected $table = 'masset_trans';
    protected $primaryKey = 'nid';
    public $timestamps = false;

    protected $fillable = [
        'ngrpid',        // sub kategori
        'cjnstrans',     // Add | Move | ServiceOut | ServiceIn | Destroy
        'dtrans',        // tanggal transaksi
        'cnotrans',       // Nomor transaksi
        'ckode',         // kode asset
        'cnama',         // nama asset
        'nlokasi',       // lokasi / department
        'dbeli',         // tanggal beli
        'cmerk',         // merk
        'dgaransi',      // tanggal garansi
        'nqty',          // qty (NON QR)
        'nhrgbeli',      // harga beli
        'ccatatan',      // catatan
        'dreffoto',      // foto
        'fdone',         // flag selesai (0/1)
    ];

    protected $casts = [
        'dtrans'    => 'date',
        'dbeli'     => 'date',
        'dgaransi'  => 'date',
        'nqty'      => 'integer',
        'nhrgbeli'  => 'decimal:2',
        'fdone'     => 'boolean',
    ];

    /**
     * =========================
     * RELATIONS
     * =========================
     */

    // 🔗 Sub Kategori
    public function subKategori()
    {
        return $this->belongsTo(
            MassetSubKat::class,
            'ngrpid',
            'nid'
        );
    }

    // 🔗 Department / Lokasi
    public function department()
    {
        return $this->belongsTo(
            Mdepartment::class,
            'nlokasi',
            'nid'
        );
    }
}
