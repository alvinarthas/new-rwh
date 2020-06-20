@php
    use App\ReturDetail;
    use App\Product;
    use App\Customer;
@endphp

<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <h4 class="m-t-0 header-title">Please Click "Transaction ID" For Retur Penjualan Barang</h4>
            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                <thead>
                    <th>No</th>
                    <th>Trx ID</th>
                    <th>Transaction Date</th>
                    <th>Customer Name</th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th style="background-color:rgb(255, 255, 214)">Qty Retur</th>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                        $total_bonus = 0;
                    @endphp
                    @foreach($sales as $s)
                        @php
                            $qtyretur = ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblretur.source_id', $s->jurnal_id)->where('tblretur.status', 1)->where('tblreturdet.prod_id', $s['prod_id'])->sum('qty');
                        @endphp
                        <tr>
                            <td class="text-center">{{$i++}}</td>
                            <td class="text-center"><a href="{{ route('returjual.edit',['id' => $s['trx_id']]) }}">{{$s->jurnal_id}}</a></td>
                            <td>{{ $s['trx_date'] }}</td>
                            <td>{{ Customer::where('id', $s['customer_id'])->first()->apname }}</td>
                            <td>{{ $s['prod_id'] }}</td>
                            <td>{{ Product::where('prod_id', $s['prod_id'])->first()->name }}</td>
                            <td>{{ $s['qty'] }}</td>
                            <td>Rp {{number_format($s['price'],2,",", ".") }}</td>
                            <td style="background-color:rgb(255, 255, 214)">{{ $qtyretur }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Responsive Datatable
        $('#responsive-datatable').DataTable();
    });
</script>
