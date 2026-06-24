<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MassetAudit extends Model
{
    protected $table = 'masset_audit';
    protected $primaryKey = 'nid';
    public $timestamps = false;

    protected $fillable = [
        'ngrpid',
        'nlokasi',
        'ckode',
        'cnama',
        'cstatus',
        'nqty',
        'nqtyreal',
        'ccatatan',
        'dreffoto',
    ];

    protected $casts = [
        'dtrans' => 'date',
        'nqty' => 'integer',
        'nqtyreal' => 'integer',
    ];

    /**
     * Relasi ke tabel mdepartment (lokasi)
     */
    public function department()
    {
        return $this->belongsTo(\App\Models\Mdepartment::class, 'nlokasi', 'nid');
    }
}
