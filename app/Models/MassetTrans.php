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
        'cjnstrans',     // Add | Move | ServiceOut | ServiceIn | Dispose
        'dtrans',        // tanggal transaksi
        'cnotrans',      // nomor transaksi
        'ckode',         // kode asset
        'cnama',         // nama asset
        'nlokasi',       // lokasi / department

        'dbeli',         // tanggal beli
        'cmerk',         // merk
        'dgaransi',      // tanggal garansi

        'nqty',          // qty transaksi
        'nqtyselesai',   // qty selesai (service / dispose)
        'nhrgbeli',      // harga beli

        'ccatatan',      // catatan
        'dreffoto',      // foto
        'creftrans',     // referensi transaksi (parent)
    ];

    protected $casts = [
        'dtrans'       => 'date',
        'dbeli'        => 'date',
        'dgaransi'     => 'date',
        'nqty'         => 'integer',
        'nqtyselesai'  => 'integer',
        'nhrgbeli'     => 'decimal:2',
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

    /**
     * =========================
     * HELPERS (OPTIONAL TAPI KUAT)
     * =========================
     */

    // apakah transaksi sudah selesai
    public function isSelesai(): bool
    {
        if ($this->nqtyselesai === null) {
            return false;
        }

        return abs($this->nqtyselesai) >= abs($this->nqty);
    }

    // relasi ke transaksi referensi (self join)
    public function refTrans()
    {
        return $this->belongsTo(
            self::class,
            'creftrans',
            'cnotrans'
        );
    }
}
