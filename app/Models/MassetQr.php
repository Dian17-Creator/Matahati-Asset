<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MassetQr extends Model
{
    use HasFactory;

    protected $table = 'masset_qr';
    protected $primaryKey = 'nid';
    public $timestamps = false;

    protected $fillable = [
        'nidsubkat',
        'niddept',
        'nurut',
        'cqr',
        'dbeli',
        'nbeli',
        'cstatus',
        'dtrans',
        'ccatatan',
        'dcreated',
    ];

    protected $casts = [
        'nurut'   => 'integer',
        'nbeli'    => 'integer',
        'dbeli'    => 'date',
        'dtrans'   => 'datetime',
        'dcreated' => 'datetime',
    ];

    public function subKategori()
    {
        return $this->belongsTo(MassetSubKat::class, 'nidsubkat', 'nid');
    }

    public function department()
    {
        return $this->belongsTo(Mdepartment::class, 'niddept', 'nid');
    }

}
