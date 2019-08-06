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
            @foreach ($invoice['data'] as $sale)
                <tr>
                    <td>{{$i}}</td>
                    <td>SO.{{$sale->id}}</td>
                    <td>{{$sale->trx_date}}</td>
                    <td>{{$sale->customer->apname}}</td>
                    <td>Rp. {{number_format($sale->ttl_harga+$sale->ongkir)}}</td>
                    <td>
                        <a href="javascript:;" class="btn btn-info btn-rounded waves-effect w-md waves-danger m-b-5" onclick="previewInvoice({{$sale->id}})"><i class="fa fa-file-pdf-o"></i> Preview Invoice</a>
                        @php($jenis = "print")
                            <input type="hidden" id="route{{$sale->id}}" value="{{route('invoicePrint',['jenis' =>$jenis,'trx_id' => $sale->id])}}">
                        <a href="javascript:;" class="btn btn-danger btn-rounded waves-effect w-md waves-danger m-b-5" onclick="printPdf({{$sale->id}})"><i class="fa fa-file-pdf-o"></i> Print Invoice</a>
                    </td>
                </tr>
            @php($i++)
            @endforeach
        </tbody>
    </table>
</div>

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" id="modalLarge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" id="custom-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Large modal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
            </div>
            <div class="modal-body" id="modalView">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="card-box">
    <h4 class="m-t-0 header-title">Transaksi Detail</h4>
    <div class="col-12">
        <div class="p-20">
            <div class="form-group row">
                <label class="col-2 col-form-label">Start Date</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="{{$invoice['start']}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">End Date</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="{{$invoice['end']}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Transaksi</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="{{$invoice['ttl_trx']}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Pendapatan</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($invoice['ttl_pemasukan'])}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total BV</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($invoice['ttl_total'])}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Qty</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="{{$invoice['ttl_count']}}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Responsive Datatable
$('#responsive-datatable').DataTable();

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
