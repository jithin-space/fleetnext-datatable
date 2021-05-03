@extends('layouts.app')

@section('content')
    <h3 class="primary">Speed Report</h3>
    <div class="panel-body">
        <table class="table table-bordered" id="customers-table">
            <thead>
                <tr>
                    <th ></th>
                    <th >Id</th>
                    <th>Name</th>
                    <th>Unique ID</th>
                    <th>Chasis No</th>
                    <th>SIM No</th>
                    <th>Overspeeds</th>
                    <th>Reported AT</th>
                    <th >Speed(kmph)</th>
                </tr>
            </thead>
             <tfoot>
                <tr>
                    <td class="non_searchable"></td>
                    <td class="non_searchable"></>
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

    <div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
    </div>
    </div>
@endsection

@section('javascript')

    <script id="details-template" type="text/x-handlebars-template">
        @verbatim

        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Device {{ name }}'s Positions</h4>
        </div>
        <div class="modal-body">
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
        </div>
        </div>
        @endverbatim
    </script>

    <script>
          var template = Handlebars.compile($("#details-template").html());
          var table = $('#customers-table').DataTable({
            "lengthMenu": [[10, 100, 500, -1], [10, 100, 500, "All"]],
            dom: 'lrBtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            processing: true,
            'language': {
            'processing': '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            serverSide: false,
            ajax: '{{ route('api.row_details') }}',
            columns: [
              {
                "className":      'details-control',
                "orderable":      false,
                "searchable":     false,
                "data":           null,
                "defaultContent": '<img src="./images/details_open.png">'
              },
              {
                  searchable: false,
                  orderable: false,
                  data: null,
                  defaultContent: '',
                  targets: 1,
              },
              { data: 'name', name: 'name' },
              { data: 'uniqueid', name: 'uniqueid' },
              { data: 'chasisnum', name: 'chasisnum', orderable: false },
              { data: 'simnum', name: 'simnum', orderable: false },
              { data: 'speedcount',  name: 'speedcount', orderable: true },
              { data: 'lastupdate', name: 'lastupdate', orderable: false },
              { data: 'recordedspeed', name: 'recordedspeed', orderable: true },
            ],
            order: [[2, 'asc']],
	    "createdRow": function( row, data, dataIndex ) {
                if ( data['speedcount'] != 0 ) {
                  $(row).addClass( 'overspeed' );
                }
            },
            initComplete: function () {

                $('#customers-table tfoot tr').clone(true).appendTo( '#customers-table thead' );

                $('#customers-table thead tr:eq(1) td').each( function (i) {
                    if(!this.classList.contains('non_searchable')) {
                    var title = $(this).text();
                    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
                    $(this).removeClass('sorting');
                    $( 'input', this ).on( 'keyup change', function () {
                        if ( table.column(i).search() !== this.value ) {
                            table
                                .column(i)
                                .search( this.value )
                                .draw();
                        }
                    } );
                }
                } );
            }
          });

          table.on( 'draw.dt', function () {
            var PageInfo = $('#customers-table').DataTable().page.info();
                table.column(1, { page: 'current' }).nodes().each( function (cell, i) {
                    cell.innerHTML = i + 1 + PageInfo.start;
                } );
            } );

          $('#customers-table tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var that = this;
            var row = table.row( tr );
            var tableId = 'purchases-' + row.data().id;

            if ($('#myModal').hasClass('in')) {
              // This row is already open - close it
              $(this)[0].firstChild.src='./images/details_open.png';
            }
            else {
              $('.modal-dialog').html(template(row.data()));
              initTable(tableId, row.data());
              $('#myModal').modal();
                $(that)[0].firstChild.src='./images/details_close.png';
            }

              $('#myModal').on('hidden.bs.modal',function(){
                $(that)[0].firstChild.src='./images/details_open.png';
              });
          });



          function initTable(tableId, data) {
            $('#' + tableId).DataTable({
            "lengthMenu": [[10, 100, 500, -1], [10, 100, 500, "All"]],
            dom: 'lrfBtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            processing: true,
	    searching: false,
            serverSide: true,
            ajax: data.details_url,
            columns: [
                { data: 'id', name: 'id' },
                { data: 'servertime', name: 'servertime' },
	        { data: null,
	          name: 'latitude',
                  render: function(data, type, row, meta){
                     if(type === 'display'){
                        //data = '<a  target="_blank" href="https://maps.google.com/maps?q=' + row.latitude + ',' + row.longitude +  '"> ' + row.latitude + ' </a>';
                        data = '<a  target="popup" onclick="window.open(\'https://maps.google.com/maps?q=' + row.latitude + ',' + row.longitude +  '\',\'popup\',\'width=600,height=600,resizable=no\'); return false;"> ' + row.latitude + ' </a>';
                     }
                     return data;
                  }
	        },
	        { data: null,
	          name: 'longitude',
                  render: function(data, type, row, meta){
                     if(type === 'display'){
                        data = '<a  target="popup" onclick="window.open(\'https://maps.google.com/maps?q=' + row.latitude + ',' + row.longitude +  '\',\'popup\',\'width=600,height=600,resizable=no\'); return false;"> ' + row.longitude + ' </a>';
                     }
                     return data;
                  }
	        },
                { data: 'speed', name: 'speed', render: (data) => (data*1.852).toFixed(2) },
            ]
            })
        }

        $('#customers-table_filter').hide();
    </script>
@endsection
