<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    //
    protected $connection = 'mysql2';
    protected $table = 'tc_positions';

    public function device() {
        return $this->belongsTo(Device::class, 'deviceid');
    }

    public function getCreatedAtAttribute() {
        return $this->servertime;
    }
}
