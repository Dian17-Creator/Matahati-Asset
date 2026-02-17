<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MassetKat extends Model
{
    use HasFactory;

    protected $table = 'masset_kat';
    protected $primaryKey = 'nid';
    public $timestamps = false;

    protected $fillable = [
        'ckode',
        'cnama',
        'dcreated',
    ];

    protected $casts = [
        'dcreated' => 'datetime:Y-m-d H:i:s',
    ];

    public function subKategori()
    {
        return $this->hasMany(MassetSubKat::class, 'nidkat', 'nid');
    }
}
