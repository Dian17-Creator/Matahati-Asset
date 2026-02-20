<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use illuminate\Database\Eloquent\Factories\HasFactory;

class MassetTrans extends Model
{
    use HasFactory;

    protected $table = 'masset_trans';
    protected $primaryKey = 'nid';
    public $timestamps = false;

    protected $fillable = [
        'nid',
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
}
