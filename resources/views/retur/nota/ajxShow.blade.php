@php
    use App\Supplier;
    use App\Product;
    use App\ReturDetail;
    use App\Perusahaan;
    use App\Customer;
@endphp
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            @if($jenisretur=="pembelian")
                <h4 class="m-t-0 header-title">Please Click "Transaction ID" For Retur Pembelian Barang</h4>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Trx ID</th>
                        <th>Posting Period</th>
                        <th>Supplier Name</th>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Harga Distributor</th>
                        <th>Qty Retur</th>
                        <th>Alasan Retur</th>
                        <th>Tgl Retur</th>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                            $total_bonus = 0;
                        @endphp
                        @foreach($purchase as $p)
                            @php
                                $dataretur = ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblretur.source_id', $p->jurnal_id)->where('tblretur.status', 0)->where('tblreturdet.prod_id', $p['prod_id'])->first();
                            @endphp
                            <tr>
                                <td class="text-center">{{$i++}}</td>
                                <td class="text-center"><a href="{{ route('returbeli.edit',['id' => $p['trx_id']]) }}">{{$p->jurnal_id}}</a></td>
                                <td>{{ date("F",strtotime("2017-".$p['month']."-01"))." ".$p['year'] }}</td>
                                <td>{{ Perusahaan::where('id', $p['supplier'])->first()->nama }}</td>
                                <td>{{ $p['prod_id'] }}</td>
                                <td>{{ Product::where('prod_id', $p['prod_id'])->first()->name }}</td>
                                <td>{{ $p['qty'] }}</td>
                                <td>Rp {{ number_format($p['price'], 2, ",",".") }}</td>
                                <td>Rp {{ number_format($p['price_dist'], 2, ",", ".") }}</td>
                                <td style="background-color:yellow">{{ $dataretur['qty'] }}</td>
                                <td style="background-color:yellow">{{ $dataretur['reason'] }}</td>
                                <td style="background-color:yellow">{{ $dataretur['tgl'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif($jenisretur=="penjualan")
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
                        <th>Qty Retur</th>
                        <th>Alasan Retur</th>
                        <th>Tgl Retur</th>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                            $total_bonus = 0;
                        @endphp
                        @foreach($sales as $s)
                            @php
                                $dataretur = ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblretur.source_id', $s->jurnal_id)->where('tblretur.status', 1)->where('tblreturdet.prod_id', $s['prod_id'])->first();
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
                                <td style="background-color:yellow">{{ $dataretur['qty'] }}</td>
                                <td style="background-color:yellow">{{ $dataretur['reason'] }}</td>
                                <td style="background-color:yellow">{{ $dataretur['tgl'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Responsive Datatable
        $('#responsive-datatable').DataTable();
    });
</script>
