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
                        <select class="form-control select2" parsley-trigger="change" name="product_parent" id="product_parent">
                            <option value="#" selected>Pilih Product</option>
                            @foreach ($products as $product)
                                <option value="{{$product->prod_id}}">{{$product->prod_id}} - {{$product->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Quantity</label>
                    <div class="col-10">
                        <input type="number" class="form-control" name="qty_parent" id="qty_parent" parsley-trigger="change" required>
                    </div>
                </div>
                <hr>
                <strong>Dikonversikan Menjadi</strong>
                <hr>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Choose Product Name</label>
                    <div class="col-10">
                        <select class="form-control select2" parsley-trigger="change" name="product_child" id="product_child">
                            <option value="#" selected>Pilih Product</option>
                            @foreach ($products as $product)
                                <option value="{{$product->prod_id}}">{{$product->prod_id}} - {{$product->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Quantity</label>
                    <div class="col-10">
                        <input type="number" class="form-control" name="qty_child" id="qty_child" parsley-trigger="change" required>
                    </div>
                </div>
                <div class="form-group text-right m-b-0">
                    <a href="javascript:;" class="btn btn-danger btn-rounded w-md waves-effect waves-light m-b-5" onclick="addItem()">Tambah Item</a>
                </div>
            </div>
        </div>
    </div>
</div>

<form class="form-horizontal" id="form" role="form" action="{{ route('konversi.store') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <input type="hidden" name="supplierKonversi" id="supplierKonversi" value="{{$supplier->id}}">

    <div class="card-box">
        <div class="row">
            <h4 class="m-t-0 header-title">Konversi Barang Details</h4>
            <div class="col-12">
                <div class="p-20">
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Product Name</th>
                            <th>Qty</th>
                            <th></th>
                            <th>Product Name</th>
                            <th>Qty</th>
                            <th>Option</th>
                        </thead>
                        <tbody id="konversi-list-body">
                            <input type="hidden" name="count" id="count" value="0">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <h4 class="m-t-0 header-title">Keterangan Konversi Barang</h4>
            <div class="col-12">
                <div class="p-20">
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Keterangan</label>
                        <div class="col-10">
                            <input type="text" class="form-control" name="keterangan" id="keterangan" parsley-trigger="change">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group text-right m-b-0">
            <button class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Simpan Konversi Barang</a>
        </div>
    </div>
</form>

<script>
// Select2
$(".select2").select2();

function addItem(){
    product_parent = $('#product_parent').val();
    product_child = $('#product_child').val();
    qty_parent = $('#qty_parent').val();
    qty_child = $('#qty_child').val();
    count = $('#count').val();

    if(qty_parent == 0 || qty_parent == null || qty_parent == '' || qty_child == 0 || qty_child == null || qty_child == ''){
        toastr.warning("qty tidak boleh kosong!", 'Warning!')
    }else{
        $.ajax({
            url : "{{route('addKonversi')}}",
            type : "get",
            dataType: 'json',
            data:{
                product_parent: product_parent,
                product_child: product_child,
                qty_parent: qty_parent,
                qty_child: qty_child,
                count:count,
            },
        }).done(function (data) {
            $('#konversi-list-body').append(data.append);
            $('#count').val(data.count);
            resetall();
            changeTotalHarga();
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

}

function resetall(){
    $('#product_parent').val("#").change();
    $('#product_child').val("#").change();
    $('#qty_parent').val("");
    $('#qty_child').val("");
}

function deleteItem(id){
    count = parseInt($('#count').val()) - 1;
    $('#trow'+id).remove();
    $('#count').val(count);
    changeTotalHarga();
}
</script>
