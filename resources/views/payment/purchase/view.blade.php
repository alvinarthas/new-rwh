<div class="card-box table-responsive">
    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <th>No</th>
            <th>Transaction ID</th>
            <th>Posting Period</th>
            <th>Supplier</th>
            <th>Total Amount</th>
            <th>Payment Amount</th>
            <th>Status</th>
            <th>Option</th>
        </thead>
        <tbody>
            @csrf
            @php($i=1)
            @foreach ($purchase['data'] as $item)
                <tr>
                    <td>{{$i}}</td>
                    <td>PO.{{$item['trx_id']}}</td>
                    <td>{{date("F", mktime(0, 0, 0, $item['month'], 10))}} {{$item['year']}}</td>
                    <td>{{$item['supplier']}}</td>
                    <td>Rp. {{number_format($item['order'])}}</td>
                    <td>Rp. {{number_format($item['paid'])}}</td>
                    <td>{{$item['status']}}</td>
                    <td>
                        @if (array_search("PUPPC",$page))
                        <a href="{{route('purchaseCreate',['id'=>$item['trx_id']])}}" class="btn btn-info btn-rounded waves-effect w-md waves-danger m-b-5" >Detail Payment</a>
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
                    <input type="text" class="form-control" parsley-trigger="change" value="{{$purchase['ttl_trx']}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Purchase Order</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($purchase['ttl_order'])}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Payment</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($purchase['ttl_pay'])}}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
    
<script>
// Responsive Datatable
$('#responsive-datatable').DataTable();
</script>
    