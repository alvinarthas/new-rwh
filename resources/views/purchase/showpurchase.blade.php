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
                            <td>{{$supplier->nama}}</td>
                            <td>{{$supplier->alamat}}</td>
                            <td>{{$supplier->telp}}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="form-group row">
                    <label class="col-2 col-form-label">Posting Period</label>
                    <div class="col-10">
                        <input type="text" class="form-control" value="{{date("F", mktime(0, 0, 0, $month, 10))}} {{$year}}">
                        <input type="hidden" name="bulan" value="{{$month}}">
                        <input type="hidden" name="tahun" value="{{$year}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card-box">
    <h4 class="m-t-0 header-title">Insert Item</h4>
    <div class="row">
        <div class="col-12">
            <div class="p-20">
                <div class="form-group row">
                    <label class="col-2 col-form-label">Choose Product Name</label>
                    <div class="col-10">
                        <select class="form-control select2" parsley-trigger="change" name="select-product" id="select-product">
                            <option value="#" selected disabled>Pilih Product</option>
                            @foreach ($products as $product)
                                <option value="{{$product->prod_id}}">{{$product->prod_id}} - {{$product->name}} - Rp.{{number_format($product->harga_distributor)}} - Rp.{{number_format($product->harga_modal)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Quantity</label>
                    <div class="col-10">
                        <input type="number" class="form-control" name="qty" id="qty" parsley-trigger="change" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Unit</label>
                    <div class="col-10">
                        <input type="text" class="form-control" name="unit" id="unit" parsley-trigger="change" required>
                    </div>
                </div>
                <div class="form-group text-right m-b-0">
                    <a href="javascript:;" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5" onclick="addItem()">Tambah Item</a>
                </div>
            </div>
        </div>
    </div>
</div>

<form class="form-horizontal" role="form" action="{{ route('purchase.store') }}" enctype="multipart/form-data" method="POST">
    <div class="card-box">
        <div class="row">
            <h4 class="m-t-0 header-title">Purchase Order Item Details</h4>
            <div class="col-12">
                <div class="p-20">
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
                            <th>Option</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <h4 class="m-t-0 header-title">Purchase Order Date and Notes</h4>
            <div class="col-12">
                <div class="p-20">
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Quantity</label>
                        <div class="col-10">
                            <input type="number" class="form-control" name="qty" id="qty" parsley-trigger="change" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Unit</label>
                        <div class="col-10">
                            <input type="text" class="form-control" name="unit" id="unit" parsley-trigger="change" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Quantity</label>
                        <div class="col-10">
                            <input type="number" class="form-control" name="qty" id="qty" parsley-trigger="change" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Unit</label>
                        <div class="col-10">
                            <input type="text" class="form-control" name="unit" id="unit" parsley-trigger="change" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group text-right m-b-0">
            <button class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Simpan Purchase Order</a>
        </div>
    </div>
</form>

<script>
// Select2
$(".select2").select2();
</script>
        