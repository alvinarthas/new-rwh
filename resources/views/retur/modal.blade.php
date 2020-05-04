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
                        @if($jenis == "PB")
                            <th>Supplier Name</th>
                            <th>PO ID</th>
                        @elseif($jenis == "PJ")
                            <th>Customer Name</th>
                            <th>SO ID</th>
                        @endif
                        <th>Creator</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $retur->id_jurnal }}</td>
                            <td>{{ $retur->tgl }} </td>
                            @if($jenis == "PB")
                                <td>{{ $retur->supplier()->first()->nama }}</td>
                                <td>{{ $retur->po_id }}</td>
                            @elseif($jenis == "PJ")
                                <td>{{ $retur->customer()->first()->apname }}</td>
                                <td>{{ $retur->so_id }}</td>
                            @endif
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
                @if($jenis == "PB")
                    <th>Qty saat PO</th>
                @elseif($jenis == "PJ")
                    <th>Qty saat SO</th>
                @endif
                <th>Qty Retur</th>
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
                        @if($jenis == "PB")
                            <td>{{PurchaseDetail::where('trx_id', $detail->trx_id)->where('prod_id', $detail->prod_id)->first()->qty }}</td>
                        @elseif($jenis == "PJ")
                            <td>{{SalesDet::where('trx_id', $detail->trx_id)->where('prod_id', $detail->prod_id)->first()->qty }}</td>
                        @endif
                        <td>{{$detail->qty}}</td>
                        <td>{{$detail->reason}}</td>
                    </tr>
                    @endisset
                @endforeach
            </tbody>
        </table>
    </div>
    <hr>
</div>
