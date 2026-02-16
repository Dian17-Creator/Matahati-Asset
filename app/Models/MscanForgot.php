<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MscanForgot extends Model
{
    protected $table = 'mscan_forgot';
    protected $primaryKey = 'nid';

    // karena tabel tidak pakai created_at & updated_at
    public $timestamps = false;

    protected $fillable = [
        'nuserId',
        'dscanned',
        'dtime',

        'nlat',
        'nlng',
        'cplacename',

        'creason',
        'cphoto_path',

        'cstatus',
        'dapproved',
        'nadminid',

        'csuperstat',
        'dsuper',
        'nsuperid',

        'chrdstat',
        'dhrd',
        'nhrdid',
    ];

    protected $casts = [
        'dscanned'  => 'date',
        'dtime'     => 'datetime:H:i:s',

        'dapproved' => 'datetime',
        'dsuper'    => 'datetime',
        'dhrd'      => 'datetime',
    ];

    /* =========================
     | RELATIONSHIP (OPSIONAL)
     ========================= */

    public function user()
    {
        return $this->belongsTo(Muser::class, 'nuserId', 'nid');
    }

    public function admin()
    {
        return $this->belongsTo(Muser::class, 'nadminid', 'nid');
    }

    public function supervisor()
    {
        return $this->belongsTo(Muser::class, 'nsuperid', 'nid');
    }

    public function hrd()
    {
        return $this->belongsTo(Muser::class, 'nhrdid', 'nid');
    }
}
