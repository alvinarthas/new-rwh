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

<form class="form-horizontal" role="form" action="{{ route('exportSO') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <div class="form-group text-right m-b-0">
                    <button class="btn btn-success btn-trans btn-rounded waves-effect waves-light w-xs m-b-5">
                        <span class="mdi mdi-file-excel">
                            Cetak Excel
                        </span>
                    </button>
                    <input type="hidden" id="start" name="start" value="{{$transaksi['start']}}">
                    <input type="hidden" id="end" name="end" value="{{$transaksi['end']}}">
                    <input type="hidden" id="trx_method" name="trx_method" value="{{$method}}">
                    <input type="hidden" id="param" name="param" value="{{$param}}">
                </div>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Transaction ID</th>
                        <th>Transaction Date</th>
                        <th>Customer</th>
                        <th>Creator</th>
                        <th>Total</th>
                        <th>Option</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</form>

<div class="card-box">
    <h4 class="m-t-0 header-title">Transaksi Detail</h4>
    <div class="col-12">
        <div class="p-20">
            <div class="form-group row">
                <label class="col-2 col-form-label">Start Date</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="{{$transaksi['start']}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">End Date</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="{{$transaksi['end']}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Transaksi</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="{{$transaksi['ttl_trx']}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Pendapatan</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($transaksi['ttl_pemasukan'],2,",",".")}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total BV</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($transaksi['ttl_total'],2,",",".")}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Qty</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="{{$transaksi['ttl_count']}}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" id="modalLarge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" id="do-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Sales Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
            </div>
            <div class="modal-body" id="modalView">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    $(document).ready(function () {
        var column = [{data : 'no', name : 'no', searchable : false},
            {data : 'trx_id', name : 'trx_id'},
            {data : "trx_date",name : "trx_date"},
            {data : "customer", name : "customer"},
            {data : "creator", name : "creator"},
            {data : "total", name : "total", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' )},
            {data : "option", name : "option", orderable : false, searchable : false}];
        $('#responsive-datatable').DataTable({
            "processing" : true,
            "serverSide" : true,
            "order": [[0, "desc"]],
            "ajax" : {
                "url" : "{{ route('salesData') }}",
                "type" : "POST",
                "data" : {
                    "start_date" : $("#start").val(),
                    "end_date" : $("#end").val(),
                    "param" : $("#param").val(),
                    "trx_method" : $("#trx_method").val(),
                    "_token" : $("meta[name='csrf-token']").attr("content"),
                }
            },"columns" : column,"columnDefs" : [
                {
                    targets: '_all',
                    type: 'natural'
                }
            ],oLanguage : {sProcessing: "<div id='loader'></div>"},
        });
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
                url: "sales/"+id,
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

    function getDetail(id){
        $.ajax({
            url : "{{route('sales.show',['id'=>1])}}",
            type : "get",
            dataType: 'json',
            data:{
                id:id,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modalLarge').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function previewInvoice(id){
        $.ajax({
            url : "{{route('invoicePrint')}}",
            type : "get",
            dataType: 'json',
            data:{
                trx_id:id,
                jenis:"Show"
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modalLarge').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function printPdf(id){
        windowUrl = $('#route'+id).val();
        console.log(windowUrl)
        windowName = "Invoice";
        var printWindow = window.open(windowUrl, windowName, 'left=50000,top=50000,width=0,height=0');
        printWindow.focus();
        printWindow.print();
    }
</script>
