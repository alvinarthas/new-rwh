@php
    use App\PurchaseDetail;
    use App\SalesDet;
@endphp
<div class="card-box table-responsive">

    <h4 class="m-t-0 header-title">Data Retur</h4>
    <div class="row">
        <div class="col-12">
            <div class="p-20">
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>Transaction ID</th>
                        <th>Transaction Date</th>
                        <th>Customer Name</th>
                        <th>SO ID</th>
                        <th>Creator</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $retur->id_jurnal }}</td>
                            <td>{{ $retur->tgl }} </td>
                            <td>{{ $retur->customer()->first()->apname }}</td>
                            <td>{{ $retur->source_id }}</td>
                            <td>{{ $retur->creator()->first()->name }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <th>No</th>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Qty saat SO</th>
                <th>Qty Retur</th>
                <th>Unit</th>
                <th>Harga</th>
                <th>Alasan Retur</th>
            </thead>
            <tbody id="purchase-list-body">
                @php($i=1)
                @foreach ($returdet as $detail)
                    @isset($detail->product->name)
                    <tr style="width:100%" id="trow{{$i}}">
                        <td>{{$i++}}</td>
                        <td>{{$detail->prod_id}}</td>
                        <td>{{$detail->product->name}}</td>
                        <td>{{SalesDet::where('trx_id', $so_trx)->where('prod_id', $detail->prod_id)->first()->qty }}</td>
                        <td>{{$detail->qty}}</td>
                        <td>{{$detail->unit }}</td>
                        <td>Rp {{ number_format($detail->harga, 2 , ",",".") }}</td>
                        <td>{{$detail->reason}}</td>
                    </tr>
                    @endisset
                @endforeach
            </tbody>
        </table>
    </div>
    <hr>
</div>
