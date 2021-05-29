@extends('layouts.app')

@section('content')
<h3 class="primary">PowerCut Events(Last 72hrs)</h3>
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
                <th>Events In 72hr</th>
                <th>Last Reported</th>
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
            <h4 class="modal-title">All Reported Powercuts For {{ name }} </h4>
        </div>
        <div class="modal-body">
        <table class="table details-table" id="purchases-{{id}}">
            <thead>
            <tr>
                <th>Id</th>
                <th>Time</th>
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
    "lengthMenu": [
        [10, 100, 500, -1],
        [10, 100, 500, "All"]
    ],
    dom: 'lrBtip',
    buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
    ],
    processing: true,
    'language': {
        'processing': '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
    },
    serverSide: false,
    ajax: '{{ route("api.power_cut",["email"=>Auth::user()->email]) }}',
    columns: [{
            "className": 'details-control',
            "orderable": false,
            "searchable": false,
            "data": null,
            "defaultContent": '<img src="./images/details_open.png">'
        },
        {
            searchable: false,
            orderable: false,
            data: null,
            defaultContent: '',
            targets: 1,
        },
        {
            data: 'name',
            name: 'name'
        },
        {
            data: 'uniqueid',
            name: 'uniqueid'
        },
        {
            data: 'chasisnum',
            name: 'chasisnum',
            orderable: false
        },
        {
            data: 'simnum',
            name: 'simnum',
            orderable: false
        },
        {
            data: 'count',
            name: 'count',
            orderable: true
        },
        {
            data: 'lastupdate',
            name: 'lastupdate',
            orderable: true
        },
    ],
    order: [
        [2, 'asc']
    ],
    "createdRow": function(row, data, dataIndex) {
        // if (data['speedcount'] != 0) {
        //     $(row).addClass('overspeed');
        // }
    },
    initComplete: function() {

        $('#customers-table tfoot tr').clone(true).appendTo('#customers-table thead');

        $('#customers-table thead tr:eq(1) td').each(function(i) {
            if (!this.classList.contains('non_searchable')) {
                var title = $(this).text();
                $(this).html('<input type="text" placeholder="Search ' + title + '" />');
                $(this).removeClass('sorting');
                $('input', this).on('keyup change', function() {
                    if (table.column(i).search() !== this.value) {
                        table
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            }
        });
    }
});

table.on('draw.dt', function() {
    var PageInfo = $('#customers-table').DataTable().page.info();
    table.column(1, {
        page: 'current'
    }).nodes().each(function(cell, i) {
        cell.innerHTML = i + 1 + PageInfo.start;
    });
});

$('#customers-table tbody').on('click', 'td.details-control', function() {
    var tr = $(this).closest('tr');
    var that = this;
    var row = table.row(tr);
    var tableId = 'purchases-' + row.data().id;

    if ($('#myModal').hasClass('in')) {
        // This row is already open - close it
        $(this)[0].firstChild.src = './images/details_open.png';
    } else {
        $('.modal-dialog').html(template(row.data()));
        initTable(tableId, row.data());
        $('#myModal').modal();
        $(that)[0].firstChild.src = './images/details_close.png';
    }

    $('#myModal').on('hidden.bs.modal', function() {
        $(that)[0].firstChild.src = './images/details_open.png';
    });
});



function initTable(tableId, data) {
    $('#' + tableId).DataTable({
        "lengthMenu": [
            [10, 100, 500, -1],
            [10, 100, 500, "All"]
        ],
        dom: 'lrfBtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        processing: true,
        searching: false,
        serverSide: true,
        ajax: data.details_url,
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'servertime',
                name: 'servertime'
            },
        ]
    })
}

$('#customers-table_filter').hide();
</script>
@endsection
