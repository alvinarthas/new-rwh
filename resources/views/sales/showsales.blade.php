<div class="card-box table-responsive">
    <h4 class="m-t-0 header-title">Customer Data</h4>
    <div class="row">
        <div class="col-12">
            <div class="p-20">
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Company Name</th>
                        <th>Company Phone</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>{{$customer->apname}}</td>
                            <td>{{$customer->apphone}}</td>
                            <td>{{$customer->cicn}}</td>
                            <td>{{$customer->ciphone}}</td>
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
                        <select class="form-control select2" parsley-trigger="change" name="select_product" id="select_product">
                            <option value="#" selected>Pilih Product</option>
                            @foreach ($products as $product)
                                <option value="{{$product->prod_id}}">{{$product->prod_id}} - {{$product->prod->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Quantity</label>
                    <div class="col-10">
                        <input type="number" min="0" class="form-control" name="qty" id="qty" parsley-trigger="change" required>
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

<form class="form-horizontal" role="form" action="{{ route('sales.store') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <input type="hidden" name="customer" id="customer" value="{{$customer->id}}">

    <div class="card-box">
        <div class="row">
            <h4 class="m-t-0 header-title">Sales Order Item Details</h4>
            <div class="col-12">
                <div class="p-20">
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Sub Total Price</th>
                            <th>BV/ Unit</th>
                            <th>Sub Total BV</th>
                            <th>Option</th>
                        </thead>
                        <tbody id="sales-list-body">
                            <input type="hidden" name="count" id="count" value="0">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <h4 class="m-t-0 header-title">Sales Order Date and Notes</h4>
            <div class="col-12">
                <div class="p-20">
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Total Transaksi</label>
                        <div class="col-10">
                            <input type="number" class="form-control" name="ttl_trx" id="ttl_trx" parsley-trigger="change" value="0" readonly>
                            <input type="hidden" class="form-control" name="raw_ttl_trx" id="raw_ttl_trx" parsley-trigger="change" value="0">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Ongkos Kirim</label>
                        <div class="col-10">
                            <input type="number" class="form-control" name="ongkir" id="ongkir" parsley-trigger="change" value="0">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Transaction Date</label>
                        <div class="col-10">
                            <div class="input-group">
                                <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="trx_date" id="trx_date"  data-date-format='yyyy-mm-dd' autocomplete="off">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="ti-calendar"></i></span>
                                </div>
                            </div><!-- input-group -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group text-right m-b-0">
            <button class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Simpan Sales Order</a>
        </div>
    </div>
</form>

<script>
// Select2
$(".select2").select2();

// Date Picker
jQuery('#trx_date').datepicker();

function addItem(){
    customer = $('#customer').val();
    qty = $('#qty').val();
    unit = $('#unit').val();
    count = $('#count').val();
    select_product = $('#select_product').val();

    $.ajax({
        url : "{{route('addSales')}}",
        type : "get",
        dataType: 'json',
        data:{
            select_product: select_product,
            customer: customer,
            qty: qty,
            unit: unit,
            count:count,
        },
    }).done(function (data) {
        $('#sales-list-body').append(data.append);
        $('#count').val(data.count);
        resetall();
        changeTotalHarga();
    }).fail(function (msg) {
        alert('Gagal menampilkan data, silahkan refresh halaman.');
    });
}

function changeTotalHarga(){
    raw_ttl_trx = 0;
    $('input[name="sub_ttl_price[]"]').each(function() {
        raw_ttl_trx+=parseInt(this.value);
    });
    input = parseInt($('#ongkir').val(),10);

    $('#raw_ttl_trx').val(raw_ttl_trx)
    new_ttl_trx = raw_ttl_trx+input;
    $('#ttl_trx').val(new_ttl_trx);
}

function resetall(){
    $('#select_product').val("#").change();
    $('#qty').val("");
    $('#unit').val("");
}

function deleteItem(id){
    count = parseInt($('#count').val()) - 1;
    $('#trow'+id).remove();
    $('#count').val(count);
    changeTotalHarga();
}

function changeTotal(i){
    price = $('#price'+i).val();
    if(price == NaN || price == null || price == ""){
        $('#price'+i).val(0);
        price = parseInt($('#price'+i).val());
    }else{
        price = parseInt($('#price'+i).val(),10);
    }
    $('#price'+i).val(price);

    qty = $('#qty'+i).val();
    if(qty == NaN || qty == null || qty == ""){
        $('#qty'+i).val(0);
        qty = parseInt($('#qty'+i).val());
    }else{
        qty = parseInt($('#qty'+i).val(),10);
    }
    $('#qty'+i).val(qty);

    bv = $('#bv_unit'+i).val();
    if(bv == NaN || bv == null || bv == ""){
        $('#bv_unit'+i).val(0);
        bv = parseInt($('#bv_unit'+i).val());
    }else{
        bv = parseInt($('#bv_unit'+i).val(),10);
    }
    $('#bv_unit'+i).val(bv);

    ttl_price = price*qty;
    ttl_bv = bv*qty;
    $('#sub_ttl_price'+i).val(ttl_price);
    $('#sub_ttl_bv'+i).val(ttl_bv)
    
    changeTotalHarga();
}

//setup before functions
input = document.getElementById("ongkir")
input.addEventListener("mousewheel", function(event){ this.blur() })

var typingTimer;                //timer identifier
var doneTypingInterval = 100;  //time in ms, 5 second for example
var $input = $('#ongkir');

//on keyup, start the countdown
$input.on('keyup', function () {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(ongkosKirim, doneTypingInterval);
});

//on keydown, clear the countdown 
$input.on('keydown', function () {
    clearTimeout(typingTimer);
    typingTimer = setTimeout(ongkosKirim, doneTypingInterval);
});

function ongkosKirim() {
    input = $('#ongkir').val();
    if(input == NaN || input == null || input == ""){
        $('#ongkir').val(0);
        input = parseInt($('#ongkir').val());
    }else{
        input = parseInt($('#ongkir').val(),10);
    }
    $('#ongkir').val(input);
    raw_ttl_trx = parseInt($('#raw_ttl_trx').val());
    result = raw_ttl_trx+input;
    $('#ttl_trx').val(result);
}
</script>
        