<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MassetSubKat extends Model
{
    use HasFactory;

    protected $table = 'masset_subkat';
    protected $primaryKey = 'nid';
    public $timestamps = false;

    protected $fillable = [
        'nidkat',
        'ckode',
        'cnama',
        'fqr',
        'dcreated',
    ];

    protected $casts = [
        'fqr'      => 'boolean',
        'dcreated' => 'datetime:Y-m-d H:i:s',
    ];

    public function kategori()
    {
        return $this->belongsTo(MassetKat::class, 'nidkat', 'nid');
    }

    /** helper biar controller bersih */
    public function isQr()
    {
        return $this->fqr === true;
    }
}
