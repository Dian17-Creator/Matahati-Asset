<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Masset extends Model
{
    use HasFactory;

    protected $table = 'masset';
    protected $primaryKey = 'nid';
    public $timestamps = false;

    protected $fillable = [
        'ngrpid',
        'kcode',
        'cnama',
        'nlokasi',
        'dbeli',
        'cmerk',
        'dgaransi',
        'nhrgbeli',
        'ccatatan',
        'dcref',
    ];

    protected $casts = [
        'dbeli'    => 'date',
        'dgaransi' => 'date',
        'dcref'    => 'datetime',
        'nhrgbeli' => 'integer',
    ];
}
