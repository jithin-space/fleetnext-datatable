@extends('layouts.app')

@section('content')
    <div class="panel-heading">Row details</div>
    <div class="panel-body">
        <table class="table table-bordered" id="customers-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Unique ID</th>
                    <th>Chasis No</th>
                    <th>SIM No</th>
                    <th>Overspeeds</th>
                    <th>Reported AT</th>
                    <th>Speed(kmph)</th>
                </tr>
            </thead>
             <tfoot>
                <tr>
                    <td class="non_searchable"></td>
                    <td class="non_searchable"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="non_searchable"></td>
                    <td></td>
                    <td class="non_searchable"></td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@section('javascript')

    <script id="details-template" type="text/x-handlebars-template">
        @verbatim
        <div class="label label-info">Device {{ name }}'s Positions</div>
        <table class="table details-table" id="purchases-{{id}}">
            <thead>
            <tr>
                <th>Id</th>
                <th>Time</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Speed(kmph)</th>
            </tr>
            </thead>
        </table>
        @endverbatim
    </script>

    <script>
          var template = Handlebars.compile($("#details-template").html());
          var table = $('#customers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('api.row_details') }}',
            columns: [
              {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     false,
                "data":           null,
                "defaultContent": ''
              },
              { data: 'id', name: 'id' },
              { data: 'name', name: 'name' },
              { data: 'uniqueid', name: 'uniqueid' },
              { data: 'chasisnum', name: 'chasisnum', orderable: false },
              { data: 'simnum', name: 'simnum', orderable: false },
              { data: 'speedcount',  name: 'speedcount', orderable: false },
              { data: 'lastupdate', name: 'lastupdate', orderable: false },
              { data: 'recordedspeed', name: 'recordedspeed', orderable: false },
            ],
            order: [[1, 'asc']],
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;

                    // example for removing search field
                    if (!column.footer().classList.contains('non_searchable')) {
                    var input = document.createElement("input");
                    $(input).appendTo($(column.footer()).empty())
                    .keyup(function () {
                        column.search($(this).val(), false, false, true).draw();
                    });
                    }
                });
            }
          });

          $('#customers-table tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );
            var tableId = 'purchases-' + row.data().id;

            if ( row.child.isShown() ) {
              // This row is already open - close it
              row.child.hide();
              tr.removeClass('shown');
            }
            else {
              // Open this row
              row.child(template(row.data())).show();
                initTable(tableId, row.data());
                console.log(row.data());
                tr.addClass('shown');
                tr.next().find('td').addClass('no-padding bg-gray');
            }
          });

          function initTable(tableId, data) {
            $('#' + tableId).DataTable({
            processing: true,
            serverSide: true,
            ajax: data.details_url,
            columns: [
                { data: 'id', name: 'id' },
                { data: 'servertime', name: 'servertime' },
                { data: 'latitude', name: 'latitude' },
                { data: 'longitude', name: 'longitude' },
                { data: 'speed', name: 'speed', render: (data) => (data*1.852).toFixed(2) },
            ]
            })
        }
    </script>
@endsection