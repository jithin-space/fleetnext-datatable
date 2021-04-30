<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Device;

class Event extends Model
{
    //
    protected $connection = 'mysql2';
    protected $table = 'tc_events';

    public function device() {
        return $this->belongsTo(Device::class, 'deviceid');
    }

    public function getCreatedAtAttribute() {
        return $this->servertime;
    }
}
