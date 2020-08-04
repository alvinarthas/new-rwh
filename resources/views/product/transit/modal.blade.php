<div class="card-box table-responsive">
    <div class="row">
        <div class="col-12">
            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <th>No</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Gudang Asal</th>
                    <th></th>
                    <th>Gudang Tujuan</th>
                </thead>
                <tbody>
                    @php($i = 1)
                    @foreach ($details as $detail)
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$detail->product()->first()->name}}</td>
                        <td>{{$detail->qty}}</td>
                        <td>{{$detail->gudang_awal()->first()->nama}}</td>
                        <td>=></td>
                        <td>{{$detail->gudang_akhir()->first()->nama}}</td>
                    </tr>
                    @php($i++)
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
