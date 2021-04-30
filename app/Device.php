<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Event;

class Device extends Model
{
    //
    protected $connection = 'mysql2';
    protected $table = 'tc_devices';

    public function events() {
        return $this->hasMany(Event::class, 'deviceid', 'id');
    }

    public function positions() {
        return $this->hasMany(Position::class, 'deviceid', 'id');
    }
    
    public function speed_events() {
        return $this->hasMany(Event::class, 'deviceid', 'id')
            ->where('tc_events.type', 'deviceOverspeed');
    }
}
