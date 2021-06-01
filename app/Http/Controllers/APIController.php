<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Device;
use Illuminate\Http\Request;
use App\Transformers\DeviceTransformer;
use App\Transformers\PositionTransformer;

// use Yajra\Datatables\Datatables;
use DB;
use Yajra\DataTables\Facades\DataTables;

class APIController extends Controller
{
    public function getDeviceIds($email){

        if($email) {
            $ids = DB::connection('mysql2')->select(DB::raw('select id from tc_users where email="'.$email.'"'));
            if($ids&& count($ids)>0 && $ids[0]->id){
                $d_ids = DB::connection('mysql2')->select(DB::raw('select deviceid from tc_user_device where userid='.$ids[0]->id));
                return $d_ids;
            }

            return [];
        }
    }
    public function getSpeedReport(Request $request)
    {
        // $customers = Customer::select(['id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at']);
        // $devices = Device::with('speed_events');

        $device_ids = [];
        $out=[];
        if($request->has('email')){


            $device_ids = $this->getDeviceIds($request->get('email') === 'admin@revitsone.com' ? 'admin': $request->get('email'));

            $out =implode(',',array_map(function($i){
                return $i->deviceid;                
            },$device_ids));

        }

        if(count($device_ids) > 0 ){

            $sql = 'select id, name, uniqueid, servertime, d.attributes as device_attributes, sub.attributes as event_attributes, sub.speedcount
                from tc_devices d left join (select count(*) as speedcount,max(servertime) as servertime, deviceid, attributes from tc_events
                where type = "deviceOverspeed" and servertime >= now() - interval 3 day group by deviceid)sub on d.id = sub.deviceid where d.id in ('.$out.')';


            // Log::error(DB::connection('mysql2')->select(DB::raw($sql))->toSql());

            $devices = DB::connection('mysql2')->select(DB::raw($sql));

            // return Datatables::of($feedbacks)->toJson();
            $result = DataTables::of($devices)
                ->setTransformer(new DeviceTransformer)
                ->addColumn('details_url', function ($device) {
                    return route('api.speed_single_details', $device->id);
                })
                ->rawColumns(['speedcount'])
                ->make(true);

            return $result;
        }

    
        return DataTables::of([])->toJson();


    }

    public function getPowerCut(Request $request)
    {
        // $customers = Customer::select(['id', 'first_name', 'last_name', 'email', 'created_at', 'updated_at']);
        // $devices = Device::with('speed_events');

        $device_ids = [];
        $out=[];
        if($request->has('email')){


            $device_ids = $this->getDeviceIds($request->get('email') === 'admin@revitsone.com' ? 'admin': $request->get('email'));

            $out =implode(',',array_map(function($i){
                return $i->deviceid;                
            },$device_ids));

        }

        if(count($device_ids) > 0 ){

            $sql = 'select t1.id,t1.uniqueid,t1.name,t1.attributes,t2.servertime,t2.count from tc_devices as t1 inner join(select max(servertime)as servertime,count(servertime) as count,deviceid  from tc_events where type = "alarm" and attributes->"$.alarm"="powerCut" and servertime >= now() - interval 3 day group by deviceid) as t2 on t1.id=t2.deviceid';
            // where t1.id in ('.$out.')';
            
            // Log::error(DB::connection('mysql2')->select(DB::raw($sql))->toSql());

            $devices = DB::connection('mysql2')->select(DB::raw($sql));

            // return Datatables::of($feedbacks)->toJson();
            $result = DataTables::of($devices)
                ->setTransformer(new PositionTransformer)
                ->addColumn('details_url', function ($device) {
                    return route('api.power_single_details', $device->id);
                })
                ->make(true);
            return $result;
        }

    
        return DataTables::of([])->toJson();


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
    public function getSpeedReportSingleData($id)
    {
        $positions = Device::findOrFail($id)->positions()
            ->whereRaw('servertime > now() - interval 3 day')->get();

        return Datatables::of($positions)->make(true);
    }

    public function getPowerReportSingleData($id)
    {
        $sql='select id,servertime from tc_events where type="alarm" and attributes->"$.alarm"="powerCut" and deviceid='.$id;
        $events = DB::connection('mysql2')->select(DB::raw($sql));
        return Datatables::of($events)->make(true);
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
                return '<a href="#edit-' . $customer->id . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
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
