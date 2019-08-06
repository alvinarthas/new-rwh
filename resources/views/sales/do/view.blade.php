<div class="card-box table-responsive">
    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <th>No</th>
            <th>Option</th>
            <th>Transaction ID</th>
            <th>Transaction Date</th>
            <th>Customer</th>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Unit</th>
            <th>Sub Total</th>
            <th>BV/Unit</th>
            <th>Sub Total BV</th>
        </thead>
        <tbody>
            @csrf
            @php($i=1)
            @foreach ($invoice['data'] as $sale)
                <tr>
                    <td>{{$i}}</td>
                    <td>
                        <a href="javascript:;" class="btn btn-info btn-rounded waves-effect w-md waves-danger m-b-5" onclick="cetakDO({{$sale->id}})"><i class="fa fa-file-pdf-o"></i> Cetak DO</a>
                    </td>
                    <td>SO.{{$sale->trx_id}}</td>
                    <td>{{$sale->trx->trx_date}}</td>
                    <td>{{$sale->trx->customer->apname}}</td>
                    <td>{{$sale->prod_id}}</td>
                    <td>{{$sale->product->name}}</td>
                    <td>Rp. {{number_format($sale->price)}}</td>
                    <td>{{$sale->qty}}</td>
                    <td>{{$sale->unit}}</td>
                    <td>Rp. {{number_format($sale->sub_ttl)}}</td>
                    <td>Rp. {{number_format($sale->pv)}}</td>
                    <td>Rp. {{number_format($sale->sub_ttl_pv)}}</td>
                </tr>
            @php($i++)
            @endforeach
        </tbody>
    </table>
</div>

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
        url : "{{route('doPrint')}}",
        type : "get",
        dataType: 'json',
        data:{
            trx_id:id,
        },
    }).done(function (data) {
    }).fail(function (msg) {
        alert('Gagal menampilkan data, silahkan refresh halaman.');
    });
}
</script>
