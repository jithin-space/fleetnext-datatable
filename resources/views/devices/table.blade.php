@extends('layouts.app')

@section('content')
    <div class="panel-heading">Simple table</div>
    <div class="panel-body">
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>UniqueID</th>
                <th>Category</th>
                <th>Last Update</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($devices as $device)
                <tr>
                    <td>{{ $device->id }}</td>
                    <td>{{ $device->name }}</td>
                    <td>{{ $device->uniqueid }}</td>
                    <td>{{ $device->category }}</td>
                    <td>{{ $device->lastupdate }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
