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
        'ckode',
        'cnama',
        'nlokasi',
        'dbeli',
        'cmerk',
        'dgaransi',
        'nhrgbeli',
        'ccatatan',
        'dreffoto',
    ];

    // 🔗 Sub Kategori
    public function subKategori()
    {
        return $this->belongsTo(MassetSubKat::class, 'ngrpid', 'nid');
    }

    // 🔗 Department / Lokasi
    public function department()
    {
        return $this->belongsTo(Mdepartment::class, 'nlokasi', 'nid');
    }
}
