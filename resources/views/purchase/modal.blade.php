<div class="card-box table-responsive">
    <h4 class="m-t-0 header-title">Supplier Data</h4>
    <div class="row">
        <div class="col-12">
            <div class="p-20">
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Supplier Name</th>
                        <th>Company Address</th>
                        <th>Company Phone</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>{{$purchase->supplier()->first()->nama}}</td>
                            <td>{{$purchase->supplier()->first()->alamat}}</td>
                            <td>{{$purchase->supplier()->first()->telp}}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="form-group row">
                    <label class="col-2 col-form-label">Posting Period</label>
                    <div class="col-10">
                        <input type="text" class="form-control" value="{{date("F", mktime(0, 0, 0, $purchase->month, 10))}} {{$purchase->year}}">
                    </div>
                </div>
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
                <th>Qty</th>
                <th>Unit</th>
                <th>Harga Distributor</th>
                <th>Harga Modal</th>
                <th>Sub Total Distributor</th>
                <th>Sub Total Modal</th>
            </thead>
            <tbody id="purchase-list-body">
                @php($i=1)
                @foreach ($purchasedet as $detail)
                    @isset($detail->product->name)
                    <tr style="width:100%" id="trow{{$i}}">
                        <td>{{$i}}</td>
                        <td>{{$detail->prod_id}}</td>
                        <td>{{$detail->product->name}}</td>
                        <td>{{$detail->qty}}</td>
                        <td>{{$detail->unit}}</td>
                        <td>Rp. {{number_format($detail->price_dist)}}</td>
                        <td>Rp. {{number_format($detail->price)}}</td>
                        <td>Rp. {{number_format($detail->price_dist*$detail->qty)}}</td>
                        <td>Rp. {{number_format($detail->price*$detail->qty)}}</td>
                    </tr>
                    @php($i++)
                    @endisset
                @endforeach
            </tbody>
        </table>
    </div>
    <hr>
    <div class="form-group row">
        <label class="col-2 col-form-label">Total Harga Distributor</label>
        <div class="col-10">
            <input type="text" class="form-control" name="ttl_harga_distributor" id="ttl_harga_distributor" parsley-trigger="change" value="Rp. {{number_format($purchase->total_harga_dist)}}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-2 col-form-label">Total Harga Modal</label>
        <div class="col-10">
            <input type="text" class="form-control" name="ttl_harga_modal" id="ttl_harga_modal" parsley-trigger="change" value="Rp. {{number_format($purchase->total_harga_modal)}}" readonly>
        </div>
    </div>
</div>