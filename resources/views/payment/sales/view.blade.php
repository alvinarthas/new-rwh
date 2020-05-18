@php
    use App\SalesPayment;
@endphp
<div class="card-box table-responsive">
    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <th>No</th>
            <th>Transaction ID</th>
            <th>Transaction Date</th>
            <th>Customer</th>
            <th>Ongkir</th>
            <th>Total Transaksi</th>
            <th>Total Bayar</th>
            <th>Status</th>
            <th>Option</th>
        </thead>
        <tbody>
            @csrf
            @php($i=1)
            @foreach ($sales['data'] as $sale)
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$sale->jurnal_id}}</td>
                    <td>{{$sale->trx_date}}</td>
                    <td>{{$sale->customer->apname}}</td>
                    <td>{{$sale->ongkir}}</td>
                    <?php
                        $total_sale = $sale->ttl_harga+$sale->ongkir;
                        $ttlpayment = SalesPayment::where('trx_id',$sale->id)->sum('payment_amount');
                    ?>

                    <td>Rp {{number_format($sale->ttl_harga+$sale->ongkir,2,",",".")}}</td>
                    <td>Rp {{number_format($ttlpayment,2,",",".")}}</td>

                    @if($ttlpayment == $total_sale)
                    <td><a href="javascript:;" disabled="disabled" class="btn btn-success btn-rounded waves-effect w-md waves-danger m-b-5" >Lunas</a></td>
                    @elseif($ttlpayment > $total_sale)
                    <td><a href="javascript:;" disabled="disabled" class="btn btn-warning btn-rounded waves-effect w-md waves-danger m-b-5" >Kelebihan Bayar</a></td>
                    @else
                    <td><a href="javascript:;" disabled="disabled" class="btn btn-danger btn-rounded waves-effect w-md waves-danger m-b-5" >Belum Lunas</a></td>
                    @endif

                    <td>
                        @if (array_search("PSSPC",$page))
                        <a href="{{route('salesCreate',['id'=>$sale->id])}}" class="btn btn-info btn-rounded waves-effect w-md waves-danger m-b-5" >Detail Payment</a>
                        @endif
                    </td>
                </tr>
            @php($i++)
            @endforeach
        </tbody>
    </table>
</div>

<div class="card-box">
    <h4 class="m-t-0 header-title">Transaksi Summary</h4>
    <div class="col-12">
        <div class="p-20">
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Transaction</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="{{$sales['ttl_trx']}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Sales Order</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($sales['ttl_sales'],2,",",".")}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Payment</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($sales['ttl_payment'],2,",",".")}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Remaining Payment</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($sales['ttl_sales']-$sales['ttl_payment'],2,",",".")}}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Responsive Datatable
$('#responsive-datatable').DataTable({
    "order": [[ 7, "asc" ]]
});
</script>
