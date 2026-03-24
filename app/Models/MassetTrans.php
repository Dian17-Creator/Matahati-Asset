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
        'ngrpid',
        'cjnstrans',     // Add | MoveIn | MoveOut | ServiceOut | ServiceIn | Dispose
        'dtrans',
        'cnotrans',
        'ckode',
        'cnama',
        'nlokasi',

        'dbeli',
        'cmerk',
        'dgaransi',

        'nqty',
        'nqtyselesai',
        'nhrgbeli',

        'ccatatan',
        'dreffoto',
        'creftrans',
    ];
    protected $casts = [
        'dtrans'       => 'date',
        'dbeli'        => 'date',
        'dgaransi'     => 'date',
        'nqty'         => 'integer',
        'nqtyselesai'  => 'integer',
        'nhrgbeli'     => 'decimal:2',
    ];
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
