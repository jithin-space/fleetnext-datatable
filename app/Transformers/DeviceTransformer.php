<?php

namespace App\Transformers;

use App\Device;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;

// use Illuminate\Support\Facades\Log;

class DeviceTransformer extends TransformerAbstract
{
    /**
     * @param \App\Device $device
     * @return array
     */

    public function transform($device)
    {

        Log::error(json_encode($device));

        $attributes = json_decode($device->device_attributes, true);
        $event_attributes = json_decode($device->event_attributes, true);
        $knot_speed = ($event_attributes && $event_attributes['speed']) ? $event_attributes['speed'] : 0;

        return [
            'id' => $device->id,
            'name' => $device->name,
            'uniqueid' => $device->uniqueid,
            'chasisnum' => array_key_exists('chasis_number', $attributes) ?
            $attributes['chasis_number'] : 'N/A',
            'simnum' => array_key_exists('device_sim_no', $attributes) ?
            strval($attributes['device_sim_no']) : 'N/A',
            'event_attributes' => $device->event_attributes,
            'lastupdate' => ($device->speedcount > 0) ? $device->servertime : 'N/A',
            'recordedspeed' => ($device->speedcount > 0) ?
            round($knot_speed * 1.852, 4)
            : 'N/A',
            'speedcount' => $device->speedcount,
            'details_url' => route('api.device_single_details', $device->id),
        ];
    }

}
