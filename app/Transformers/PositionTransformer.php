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

    public function transform($position)
    {


        $event_attributes = json_decode($postion->pos_attributes, true);
        $dev_attributes = json_decode($postion->dev_attributes, true);

        return [
            'id' => $position->id,
            'name' => $position->name,
            'uniqueid' => $position->uniqueid,
            'chasisnum' => array_key_exists('chasis_number', $dev_attributes) ?
            $dev_attributes['chasis_number'] : 'N/A',
            'simnum' => array_key_exists('device_sim_no', $dev_attributes) ?
            strval($dev_attributes['device_sim_no']) : 'N/A',
            'event_attributes' => $event_attributes,
            'lastupdate' => $position->servertime,
        ];
    }

}
