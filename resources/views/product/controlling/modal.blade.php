<div class="card-box">
    <div class="row">
        <div class="form-group col-sm-12">
            <h4 class="text-dark">Product ID: <strong>{{$product->prod_id}}</strong> <h4>
            <h4 class="text-dark">Product Name: <strong>{{$product->name}}</strong> <h4>
            <h4 class="text-dark">Supplier : <strong>{{$product->supplier}}</strong> <h4>

            @isset($gudangs)
            <hr>
                @foreach ($gudangs as $gds)
                    <h4 class="text-dark">{{$gds->nama}}: <strong>{{$gds->qty}}</strong> <h4>
                @endforeach
            <hr>
            @endisset
        </div>
    </div>
    <hr>
    <div class="row">
        @if($modal=="stock")
            <div class="form-group col-sm-8">
                <h2 class="text-dark">Barang Indent: <strong>{{$indent}}</strong><h2>
                <h2 class="text-dark">Barang Di Gudang: <strong>{{$gudang}}</strong><h2>
                <h2 class="text-dark">Barang milik Customer: <strong>{{$brg_cust}}</strong> <h2>
                <hr>
                <h2 class="text-dark">Total Barang: <strong>{{$total}}</strong> <h2>
            </div>
        @elseif($modal=="mutasi")
            {{-- <table id="datatable" class="table table-bordered"> --}}
            @isset($konversidetail)
                @if (count($konversidetail) > 0)
                    <h4>Record Konversi Barang</h4>
                    <table id="datatableKonversi" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Qty</th>
                            <th>Status</th>
                        </thead>
                        <tbody>
                            <?php $a = 1; ?>
                            @foreach ($konversidetail as $konvdet)
                                <tr>
                                    <td>{{$a}}</td>
                                    <td>{{$konvdet->created_at}}</td>
                                    <td>{{$konvdet->qty}}</td>
                                    @if ($konvdet->status == 1)
                                    <td>Barang Keluar</td>
                                    @else
                                    <td>Barang Masuk</td>
                                    @endif
                                </tr>
                                <?php $a++; ?>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endisset
            <table id="datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal Transaksi</th>
                        <th>Transaksi ID</th>
                        @isset($jenis)
                            <th>Customer</th>
                        @endisset
                        <th>Status</th>
                        <th>Jumlah</th>
                        <th>Gudang</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $i=0;
                @endphp
                @foreach ($result as $r)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $r['tanggal'] }}</td>
                        <td>{{ $r['trx_id'] }}</td>
                        @isset($jenis)
                            <td>{{ $r['customer'] }}</td>
                        @endisset
                        <td>{{ $r['status'] }}</td>
                        <td>{{ $r['qty'] }}</td>
                        <td>{{ $r['gudang'] }}</td>
                    </tr>
                    @php
                        $i++;
                    @endphp
                @endforeach
                </tbody>
            </table>
            <hr>
            <div class="form-group row">
                <label for="trx_id" class="col-4 col-form-label">Total Stock</label>
                <div class="col-8">
                    <div class="input-group">
                        <input type="text" class="form-control" name="total" id="total" value="{{ $total }}" readonly>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // Responsive Datatable
        $('#datatable').DataTable();
    });
</script>
