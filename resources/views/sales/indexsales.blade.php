@php
    use App\TempSales;
    use App\TempSalesDet;
@endphp
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <th>No</th>
                    <th>Transaction ID</th>
                    <th>Transaction Date</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Option</th>
                </thead>
                <tbody>
                    @csrf
                    @php($i=1)
                    @foreach ($sales as $sale)
                        <tr>
                            <td>{{$i}}</td>
                            <td><a href="javascript:;" onclick="getDetail({{$sale->id}})" class="btn btn-primary btn-trans waves-effect w-md waves-danger m-b-5">SO.{{$sale->id}}</a></td>
                            <td>{{$sale->trx_date}}</td>
                            <td>{{$sale->customer->apname}}</td>
                            <td>Rp {{number_format($sale->ttl_harga+$sale->ongkir,2,",",".")}}</td>
                            <td>
                                @if (array_search("PSSLU",$page))
                                <a href="{{route('sales.edit',['id'=>$sale->id])}}" class="btn btn-purple btn-trans waves-effect w-md waves-danger m-b-5">Edit</a>
                                @endif
                                @if (array_search("PSSLD",$page))
                                <a href="javascript:;" class="btn btn-pink btn-trans waves-effect w-md waves-danger m-b-5" onclick="deletePurchase({{$sale->id}})">Delete</a>
                                @endif
                                @if ($sale->approve == 0)
                                <?php
                                    $url_register		= base64_encode(route('salesApprove',['user_id'=>session('user_id'),'trx_id'=>$sale->id,'role'=>session('role')]));
                                ?>
                                    @if (array_search("PSSLA",$page))
                                    <a href="finspot:FingerspotVer;<?=$url_register?>" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Sales</a>
                                    @endif
                                @else
                                    <?php
                                        $count_temp = TempSales::where('trx_id',$sale->id)->count('trx_id');
                                        $status_temp = TempSales::where('trx_id',$sale->id)->where('status',1)->count('trx_id');
                                    ?>
                                    @if($count_temp > 0 && $status_temp == 1)
                                        <?php
                                            $url_register		= base64_encode(route('salesApprove',['user_id'=>session('user_id'),'trx_id'=>$sale->id,'role'=>session('role')]));
                                        ?>
                                        @if (array_search("PSSLA",$page))
                                        <a href="finspot:FingerspotVer;<?=$url_register?>" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Sales yang sudah diupdate</a>
                                        @endif
                                    @else
                                        <a class="btn btn-inverse btn-trans waves-effect w-md waves-danger m-b-5">Sales sudah di approve</a>
                                    @endif
                                @endif
                                @if (array_search("PSSLN",$page))
                                <a href="javascript:;" class="btn btn-info btn-trans waves-effect w-md waves-danger m-b-5" onclick="previewInvoice({{$sale->id}})"><i class="fa fa-file-pdf-o"></i> Preview Invoice</a>
                                @endif
                                @if (array_search("PSSLP",$page))
                                    @php($jenis = "print")
                                <input type="hidden" id="route{{$sale->id}}" value="{{route('invoicePrint',['jenis' =>$jenis,'trx_id' => $sale->id])}}">
                                <a href="javascript:;" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="printPdf({{$sale->id}})"><i class="fa fa-file-pdf-o"></i> Print Invoice</a>
                                @endif
                            </td>
                        </tr>
                    @php($i++)
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

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
// Responsive Datatable
$('#responsive-datatable').DataTable({
     columnDefs: [
       { type: 'natural', targets: '_all' }
     ]
  } );

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
