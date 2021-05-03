<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Device;

// use Illuminate\Support\Facades\Log;


class DeviceTransformer extends TransformerAbstract
{
    /**
     * @param \App\Device $device
     * @return array
     */

   
    public function transform(Device $device)
    {

        $attributes = json_decode($device->attributes,true);
        $speed_count = count($device->speed_events);
        $lastUpdateObj = ($speed_count > 0) ? $device->speed_events()
                            ->orderBy('servertime')->first() : [];

        
        return [
            'id'   => $device->id,
            'name' => $device->name,
            'uniqueid' =>  $device->uniqueid,
            'chasisnum' => array_key_exists('chasis_number', $attributes) ?
                            $attributes['chasis_number'] : 'N/A',
            'simnum' => array_key_exists('device_sim_no', $attributes)?
                            strval($attributes['device_sim_no']): 'N/A',
            'lastupdate' => ($speed_count > 0) ? $lastUpdateObj['servertime']: 'N/A',
            'recordedspeed' => ($speed_count > 0 ) ? 
                                round(json_decode($lastUpdateObj['attributes'],true)['speed']*1.852,4)
                                   : 'N/A',
            'speedcount' => $speed_count,
            'details_url' => route('api.device_single_details', $device->id),
        ];
    }

   
}