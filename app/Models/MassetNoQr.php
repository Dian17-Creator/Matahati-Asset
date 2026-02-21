<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MassetNoQr extends Model
{
    use HasFactory;

    protected $table = 'masset_noqr';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'nidsubkat',
        'niddept',
        'ckode',
        'cnama',
        'nqty',
        'nminstok',
        'msatuan_id',
        'dtrans',
        'ccatatan',
    ];

    protected $casts = [
        'nqty'      => 'integer',
        'nminstok'  => 'integer',
        'dtrans'    => 'datetime',
    ];

    public function subKategori()
    {
        return $this->belongsTo(MassetSubKat::class, 'nidsubkat', 'nid');
    }

    public function department()
    {
        return $this->belongsTo(Mdepartment::class, 'niddept', 'nid');
    }

    public function satuan()
    {
        return $this->belongsTo(Msatuan::class, 'msatuan_id');
    }
}
