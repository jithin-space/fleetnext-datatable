<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Device;

class DeviceController extends Controller
{
    //

    public function index()
    {
        $devices = Device::all();
        return view('devices.table', compact('devices'));
    }

    public function getSpeedReport()
    {
        return view('devices.speed_report');
    }

    public function getPowerCut()
    {
        return view('devices.power_cut');
    }
    // public function getMasterDetails()
    // {
    //     return view('customers.master_details');
    // }

    // public function getColumnSearch()
    // {
    //     return view('customers.column_search');
    // }

    // public function getRowAttributes()
    // {
    //     return view('customers.row_attributes');
    // }

    // public function getCarbon()
    // {
    //     return view('customers.carbon');
    // }
}
