<div class="card-box table-responsive">
    <ul class="nav nav-tabs">
        @isset($temp_PO)
        <li class="nav-item">
            <a href="#edited" data-toggle="tab" aria-expanded="true" class="nav-link active">
                Edited
            </a>
        </li>
        <li class="nav-item">
            <a href="#original" data-toggle="tab" aria-expanded="true" class="nav-link">
                Original
            </a>
        </li>
        @endisset
    </ul>
    <div class="tab-content">
        @isset($temp_PO)
        <div role="tabpanel" class="tab-pane fade show active" id="edited">
            <h4>Purchase Order #{{$purchase->id}}</h4>
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
                                    <td>{{$temp_PO->supplier()->first()->nama}}</td>
                                    <td>{{$temp_PO->supplier()->first()->alamat}}</td>
                                    <td>{{$temp_PO->supplier()->first()->telp}}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="form-group row">
                            <label class="col-2 col-form-label">Posting Period</label>
                            <div class="col-10">
                                <input type="text" class="form-control" value="{{date("F", mktime(0, 0, 0, $temp_PO->month, 10))}} {{$temp_PO->year}}">
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
                        @foreach ($temp_POdet as $detail)
                            @isset($detail->product->name)
                            <tr style="width:100%" id="trow{{$i}}">
                                <td>{{$i}}</td>
                                <td>{{$detail->prod_id}}</td>
                                <td>{{$detail->product->name}}</td>
                                <td>{{$detail->qty}}</td>
                                <td>{{$detail->unit}}</td>
                                <td>Rp {{number_format($detail->price_dist,2,",",".")}}</td>
                                <td>Rp {{number_format($detail->price,2,",",".")}}</td>
                                <td>Rp {{number_format($detail->price_dist*$detail->qty,2,",",".")}}</td>
                                <td>Rp {{number_format($detail->price*$detail->qty,2,",",".")}}</td>
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
                    <input type="text" class="form-control" name="ttl_harga_distributor" id="ttl_harga_distributor" parsley-trigger="change" value="Rp {{number_format($temp_PO->total_harga_dist,2,",",".")}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Harga Modal</label>
                <div class="col-10">
                    <input type="text" class="form-control" name="ttl_harga_modal" id="ttl_harga_modal" parsley-trigger="change" value="Rp {{number_format($temp_PO->total_harga_modal,2,",",".")}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total sudah dibayar</label>
                <div class="col-10">
                    <input type="text" class="form-control" name="ttl_harga_modal" id="ttl_harga_modal" parsley-trigger="change" value="Rp {{number_format($purchasepay,2,",",".")}}" readonly>
                </div>
            </div>
        </div>
        @endisset
        <div role="tabpanel" class="tab-pane fade show active" id="original">
            <h4>Purchase Order #{{$purchase->id}}</h4>
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
                                <td>Rp {{number_format($detail->price_dist,2,",",".")}}</td>
                                <td>Rp {{number_format($detail->price,2,",",".")}}</td>
                                <td>Rp {{number_format($detail->price_dist*$detail->qty,2,",",".")}}</td>
                                <td>Rp {{number_format($detail->price*$detail->qty,2,",",".")}}</td>
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
                    <input type="text" class="form-control" name="ttl_harga_distributor" id="ttl_harga_distributor" parsley-trigger="change" value="Rp {{number_format($purchase->total_harga_dist,2,",",".")}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Harga Modal</label>
                <div class="col-10">
                    <input type="text" class="form-control" name="ttl_harga_modal" id="ttl_harga_modal" parsley-trigger="change" value="Rp {{number_format($purchase->total_harga_modal,2,",",".")}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total sudah dibayar</label>
                <div class="col-10">
                    <input type="text" class="form-control" name="ttl_harga_modal" id="ttl_harga_modal" parsley-trigger="change" value="Rp {{number_format($purchasepay,2,",",".")}}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
