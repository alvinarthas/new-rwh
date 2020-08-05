<style>
    #loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 50px;
        height: 50px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
        margin-left:10px;
        margin-right:10px;
        margin-top:10px;
    }
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<div class="card-box table-responsive">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a href="#sales" data-toggle="tab" aria-expanded="false" class="nav-link active">
                Per Purchase Order
            </a>
        </li>
        <li class="nav-item">
            <a href="#delivery" data-toggle="tab" aria-expanded="true" class="nav-link">
                Per Receive Item
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="sales">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <th>No</th>
                                <th>Purchase ID</th>
                                <th>Tanggal PO</th>
                                <th>Supplier</th>
                                <th>Total Harga Modal</th>
                                <th>Total Harga Distributor</th>
                                <th>Status Terima</th>
                                <th>Option</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="delivery">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <table id="responsive-datatable2" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <th>No</th>
                                <th>Transaction ID</th>
                                <th>RP ID</th>
                                <th>Supplier</th>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Qty Receive</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="start_date" value="{{$start_date}}">
<input type="hidden" id="end_date" value="{{$end_date}}">

<script>
// Responsive Datatable
$('#responsive-datatable').DataTable({
    "processing" : true,
    "serverSide" : true,
    "ajax" : {
        "url" : "{{ route('receiveProdAjx') }}",
        "type" : "POST",
        "data" : {
            "start_date" : $("#start_date").val(),
            "end_date" : $("#end_date").val(),
            "jenis" : "purchase",
            "_token" : $("meta[name='csrf-token']").attr("content"),
        }
    },"columns" : [{data : "no", name : "no", searchable : false},
            {data : "trx_id", name : "trx_id"},
            {data : "tgl", name : "tgl"},
            {data : "supplier", name : "supplier"},
            {data : "total_harga", name : "total_harga", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' )},
            {data : "total_harga_dist", name : "total_harga_dist", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' )},
            {data : "status", name : "status", orderable : false, searchable :false},
            {data : "option", name : "option", orderable : false, searchable : false}
    ],"columnDefs" : [
        {
            targets: '_all',
            type: 'natural'
        }
    ],oLanguage : {sProcessing: "<div id='loader'></div>"},
});
$('#responsive-datatable2').DataTable({
    "processing" : true,
    "serverSide" : true,
    "ajax" : {
        "url" : "{{ route('receiveProdAjx') }}",
        "type" : "POST",
        "data" : {
            "start_date" : $("#start_date").val(),
            "end_date" : $("#end_date").val(),
            "jenis" : "receive",
            "_token" : $("meta[name='csrf-token']").attr("content"),
        }
    },"columns" : [{data : "no", name : "no", searchable : false},
            {data : "po_id", name : "po_id"},
            {data : "rp_id",name : "rp_id"},
            {data : "supplier", name : "supplier"},
            {data : "prod_id", name : "prod_id"},
            {data : "prod_name", name : "prod_name"},
            {data : "qty", name : "qty"},
            {data : "unit", name : "unit"},
            {data : "qtyrp", name : "qtyrp", searchable: false, orderable:false},
    ],"columnDefs": [
        {
            "targets": [ 2 ],
            "visible": false,
        },
    ],oLanguage : {sProcessing: "<div id='loader'></div>"},
});

function deletePurchase(id){
    var token = $("meta[name='csrf-token']").attr("content");

    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger m-l-10',
        buttonsStyling: false
    }).then(function () {
        $.ajax({
            url: "purchase/"+id,
            type: 'DELETE',
            data: {
                "id": id,
                "_token": token,
            },
        }).done(function (data) {
            swal(
                'Deleted!',
                'Your file has been deleted.',
                'success'
            )
            location.reload();
        }).fail(function (msg) {
            swal(
                'Failed',
                'Your imaginary file is safe :)',
                'error'
            )
        });

    }, function (dismiss) {
        // dismiss can be 'cancel', 'overlay',
        // 'close', and 'timer'
        if (dismiss === 'cancel') {
            console.log("eh ga kehapus");
            swal(
                'Cancelled',
                'Your imaginary file is safe :)',
                'error'
            )
        }
    })
}
</script>
