<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Device;
use DB;

// use Yajra\Datatables\Datatables;
use App\Transformers\DeviceTransformer;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class APIController extends Controller
{
    public function getRowDetailsData()
    {
        // $customers = Customer::select(['id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at']);
        $devices = Device::with('speed_events');


        // $sql = 'select id, name, uniqueid, servertime, d.attributes as device_attributes, sub.attributes as event_attributes
        //     from tc_devices d left join (select max(servertime) as servertime, deviceid, attributes from tc_events 
        //     where type = "deviceOverspeed" group by deviceid)sub on d.id = sub.deviceid';

        // DB::statement("SET SQL_MODE=''");

        // $devices  = DB::connection('mysql2')->select(DB::raw($sql));

        // return Datatables::of($feedbacks)->toJson();
        $result = DataTables::of($devices)
        ->setTransformer(new DeviceTransformer)
        // ->filterColumn('chasisnum', function($query, $keyword){
        //     $sql = "json_extract(attributes, '$.chasis_number') like ? ";
        //     $query->whereRaw($sql, ["%{$keyword}%"]);
        // })
        // ->filterColumn('simnum', function($query, $keyword){
        //     $sql = "json_extract(attributes, '$.device_sim_no') like ? ";
        //     $query->whereRaw($sql, ["%{$keyword}%"]);
        // })
        ->addColumn('speedcount', function(Device $device) {
            return count($device->speed_events);
        })
        ->addColumn('details_url', function($device) {
                return route('api.device_single_details', $device->id);
        })
        ->rawColumns(['speedcount'])
        ->make(true);

        return $result;

    }

    // public function getMasterDetailsData()
    // {
    //     $customers = Customer::select();

    //     return Datatables::of($customers)
    //         ->addColumn('details_url', function($customer) {
    //             return route('api.master_single_details', $customer->id);
    //         })->make(true);
    // }

   
    // public function getMasterDetailsSingleData($id)
    // {
    //     $purchases = Customer::findOrFail($id)->purchases;

    //     return Datatables::of($purchases)->make(true);
    // }
    public function getMasterDetailsSingleData($id)
    {
        $positions = Device::findOrFail($id)->positions;

        return Datatables::of($positions)->make(true);
    }


    public function getColumnSearchData()
    {
        $customers = Customer::select();

        return Datatables::of($customers)->make(true);
    }

    public function getRowAttributesData()
    {
        $customers = Customer::select(['id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at']);

        return Datatables::of($customers)
            ->addColumn('action', function ($customer) {
                return '<a href="#edit-'. $customer->id.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
            })
            ->editColumn('id', '{{$id}}')
            ->removeColumn('updated_at')
            ->setRowId('id')
            ->setRowClass(function ($user) {
                return $user->id % 2 == 0 ? 'alert-success' : 'alert-warning';
            })
            ->setRowData([
                'id' => 'test',
            ])
            ->setRowAttr([
                'color' => 'red',
            ])
            ->make(true);
    }

    public function getCarbonData()
    {
        $customers = Customer::select(['id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at']);

        return Datatables::of($customers)
            ->editColumn('created_at', '{!! $created_at !!}')
            ->editColumn('updated_at', function ($customer) {
                return $customer->updated_at->format('Y/m/d');
            })
            ->filterColumn('updated_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(updated_at,'%Y/%m/%d') like ?", ["%$keyword%"]);
            })
            ->make(true);
    }
}
