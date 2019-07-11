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
                        <select class="form-control select2" parsley-trigger="change" name="select_product" id="select_product">
                            <option value="#" selected>Pilih Product</option>
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
                    <a href="javascript:;" class="btn btn-danger btn-rounded w-md waves-effect waves-light m-b-5" onclick="addItem()">Tambah Item</a>
                </div>
            </div>
        </div>
    </div>
</div>

<form class="form-horizontal" role="form" action="{{ route('purchase.store') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <input type="hidden" name="bulanpost" id="bulanpost" value="{{$month}}">
    <input type="hidden" name="tahunpost" id="tahunpost" value="{{$year}}">
    <input type="hidden" name="supplierpost" id="supplierpost" value="{{$supplier->id}}">

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
                        <tbody id="purchase-list-body">
                            <input type="hidden" name="count" id="count" value="0">

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
                        <label class="col-2 col-form-label">Total Harga Distributor</label>
                        <div class="col-10">
                            <input type="number" class="form-control" name="ttl_harga_distributor" id="ttl_harga_distributor" parsley-trigger="change" value="0" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Total Harga Modal</label>
                        <div class="col-10">
                            <input type="text" class="form-control" name="ttl_harga_modal" id="ttl_harga_modal" parsley-trigger="change" value="0" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">PO Date</label>
                        <div class="col-10">
                            <div class="input-group">
                                <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="po_date" id="po_date"  data-date-format='yyyy-mm-dd' autocomplete="off">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="ti-calendar"></i></span>
                                </div>
                            </div><!-- input-group -->
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Notes</label>
                        <div class="col-10">
                            <input type="text" class="form-control" name="notes" id="notes" parsley-trigger="change">
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

// Date Picker
jQuery('#po_date').datepicker();

function addItem(){
    bulanpost = $('#bulanpost').val();
    tahunpost = $('#tahunpost').val();
    select_product = $('#select_product').val();
    qty = $('#qty').val();
    unit = $('#unit').val();
    count = $('#count').val();

    $.ajax({
        url : "{{route('addPurchase')}}",
        type : "get",
        dataType: 'json',
        data:{
            select_product: select_product,
            bulan: bulanpost,
            tahun: tahunpost,
            qty: qty,
            unit: unit,
            count:count,
        },
    }).done(function (data) {
        $('#purchase-list-body').append(data.append);
        $('#count').val(data.count);
        resetall();
        changeTotalHarga(data.sub_ttl_mod, data.sub_ttl_dist);
    }).fail(function (msg) {
        alert('Gagal menampilkan data, silahkan refresh halaman.');
    });
}

function changeTotalHarga(sub_ttl_mod,sub_ttl_dist){
    ttl_harga_distributor = parseInt($('#ttl_harga_distributor').val());
    ttl_harga_modal = parseInt($('#ttl_harga_modal').val());
    new_total_dist = ttl_harga_distributor+sub_ttl_dist;
    new_total_modal = ttl_harga_modal+sub_ttl_mod;
    $('#ttl_harga_distributor').val(new_total_dist);
    $('#ttl_harga_modal').val(new_total_modal);
}

function decreaseTotalHarga(id){
    ttl_harga_distributor = parseInt($('#ttl_harga_distributor').val());
    ttl_harga_modal = parseInt($('#ttl_harga_modal').val());
    sub_ttl_dist = $('#sub_ttl_dist'+id).val();
    sub_ttl_mod = $('#sub_ttl_mod'+id).val();
    new_total_dist = ttl_harga_distributor-sub_ttl_dist;
    new_total_modal = ttl_harga_modal-sub_ttl_mod;
    $('#ttl_harga_distributor').val(new_total_dist);
    $('#ttl_harga_modal').val(new_total_modal);
}

function resetall(){
    $('#select_product').val("#").change();
    $('#qty').val("");
    $('#unit').val("");
}

function deleteItem(id){
    count = parseInt($('#count').val()) - 1;
    decreaseTotalHarga(id);
    $('#trow'+id).remove();
    $('#count').val(count);
}
</script>
        