<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSchedule extends Model
{
    protected $table = 'mschedule';
    protected $primaryKey = 'nid';
    public $timestamps = false;

    protected $fillable = ['cname', 'dstart', 'dend', 'dstart2', 'dend2', 'dcreated'];

    public function userSchedules()
    {
        return $this->hasMany(UserSchedule::class, 'nidsched');
    }
}
