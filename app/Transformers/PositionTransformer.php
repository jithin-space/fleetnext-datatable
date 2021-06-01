<?php

namespace App\Transformers;

use App\Device;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;

// use Illuminate\Support\Facades\Log;

class PositionTransformer extends TransformerAbstract
{
    /**
     * @param \App\Device $device
     * @return array
     */

    public function transform($device)
    {


        $dev_attributes = json_decode($device->attributes, true);

        return [
            'id' => $device->id,
            'name' => $device->name,
            'uniqueid' => $device->uniqueid,
            'chasisnum' => array_key_exists('chasis_number', $dev_attributes) ?
            $dev_attributes['chasis_number'] : 'N/A',
            'simnum' => array_key_exists('device_sim_no', $dev_attributes) ?
            strval($dev_attributes['device_sim_no']) : 'N/A',
            'lastupdate' => $device->servertime,
            'count' => $device->count,
        ];
    }

}
