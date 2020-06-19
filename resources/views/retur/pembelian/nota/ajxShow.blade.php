@php
    use App\ReturDetail;
    use App\Product;
    use App\Perusahaan;
@endphp

<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
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
                    <th style="background-color:rgb(255, 255, 214)">Qty Retur</th>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                        $total_bonus = 0;
                    @endphp
                    @foreach($purchase as $p)
                        @php
                            $qtyretur = ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblretur.source_id', $p->jurnal_id)->where('tblretur.status', 0)->where('tblreturdet.prod_id', $p['prod_id'])->sum('qty');
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
