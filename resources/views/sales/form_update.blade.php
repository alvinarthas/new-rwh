@extends('layout.main')
@php
    use App\TempSales;
    use App\PurchaseDetail;
@endphp
@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Select2 --}}
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Date Picker --}}
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- Sweet Alert css -->
    <link href="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Fingerprint --}}
    <link href="{{ asset('assets/fingerprint/ajaxmask.css') }}" rel="stylesheet">

    <style>
    input {
        width: 100%;
        box-sizing: border-box;
    }
    </style>
@endsection

@section('judul')
Form Update Sales Order #{{$sales->id}} @if($sales->method <> 0 ) / {{$sales->online->nama}} # {{$sales->online_id}} @endif
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            {{-- Data Supplier --}}
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
                                        <td>
                                            <select class="form-control select2" parsley-trigger="change" name="cust" id="cust" onchange="changeCustomer(this.value)" required>
                                                @foreach($customer as $c)
                                                    @if($c->id == $sales->customer->id)
                                                        <option value="{{$c->id}}" selected>{{$c->apname}}</option>
                                                    @else
                                                        <option value="{{$c->id}}">{{$c->apname}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td id="cphone">{{$sales->customer->apphone}}</td>
                                        <td id="comname">{{$sales->customer->cicn}}</td>
                                        <td id="comphone">{{$sales->customer->ciphone}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if ($status == 1)
                    @php($sales_id = $sales->trx_id)
                    <a href="javascript:;" class="btn btn-danger btn-trans w-md waves-effect waves-light m-b-5" onclick="getDetail({{$sales_id}})">Show Original Data</a>
                @else
                    @php($sales_id = $sales->id)
                @endif
                    <input type="hidden" name="status" id="status" value="{{$status}}">
            </div>
            {{-- Insert Item Card --}}
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
                                            <option value="{{$product->prod_id}}">{{$product->prod_id}} - {{$product ->name}}</option>
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
            <!--  Modal content for the above example -->
            <div class="modal fade bs-example-modal-lg" id="modalLarge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-lg" id="do-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myLargeModalLabel">Sales Order Detail</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
                        </div>
                        <div class="modal-body" id="modalView">
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            {{-- Form Data --}}
            <form class="form-horizontal" role="form" action="{{ route('sales.update',['id'=>$sales_id]) }}" enctype="multipart/form-data" method="POST">
                {{ method_field('PUT') }}
                @csrf

                <input type="hidden" name="customer" id="customer" value="{{$sales->customer_id}}">
                <input type="hidden" name="method" id="method" value="{{$sales->method}}">
                <input type="hidden" name="online_id" id="online_id" value="{{$sales->online_id}}">

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
                                        <th>Avg Cost</th>
                                        <th>Qty</th>
                                        <th>Unit</th>
                                        <th>Sub Total Price</th>
                                        <th>BV/ Unit</th>
                                        <th>Sub Total BV</th>
                                        <th>Option</th>
                                    </thead>
                                    <tbody id="sales-list-body">
                                        @php($i=1)
                                        @foreach ($salesdet as $detail)
                                            @isset($detail->product->name)
                                            <tr style="width:100%" id="trow{{$i}}">
                                                <input type="hidden" name="detail[]" id="detail{{$i}}" value="{{$detail->id}}">
                                                <td>{{$i}}</td>
                                                <td><input type="hidden" name="prod_id[]" id="prod_id{{$i}}" value="{{$detail->prod_id}}">{{$detail->prod_id}}</td>
                                                <td><input type="hidden" name="prod_name[]" id="prod_name{{$i}}" value="{{$detail->product->name}}">{{$detail->product->name}}</td>
                                                <td><input type="text" name="price[]" id="price{{$i}}" value="{{$detail->price}}" onkeyup="changeTotal({{$i}})"></td>
                                                <td>Rp&nbsp;{{number_format(PurchaseDetail::avgCost($detail->prod_id),2,",",".")}}</td>
                                                <td><input type="text" name="qty[]" id="qty{{$i}}" value="{{$detail->qty}}" onkeyup="changeTotal({{$i}})"></td>
                                                <td><input type="hidden" name="unit[]" id="unit{{$i}}" value="{{$detail->unit}}">{{$detail->unit}}</td>
                                                <td><input type="text" name="sub_ttl_price[]" id="sub_ttl_price{{$i}}" value="{{$detail->sub_ttl}}" readonly></td>
                                                <td><input type="text" name="bv_unit[]" id="bv_unit{{$i}}" value="{{$detail->pv}}" onkeyup="changeTotal({{$i}},'bv')"></td>
                                                <td><input type="text" name="sub_ttl_bv[]" id="sub_ttl_bv{{$i}}" value="{{$detail->sub_ttl_pv}}" readonly></td>
                                                <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItemOld({{$i}},{{$detail->id}})">Delete</a></td>
                                            </tr>
                                            @php($i++)
                                            @endisset
                                        @endforeach
                                        <input type="hidden" name="count" id="count" value="{{$i-1}}">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <h4 class="m-t-0 header-title">Sales Order Date</h4>
                        <div class="col-12">
                            <div class="p-20">
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Total Transaksi</label>
                                    <div class="col-10">
                                        <input type="number" class="form-control" name="ttl_trx" id="ttl_trx" parsley-trigger="change" value="{{$sales->ttl_harga+$sales->ongkir}}" readonly>
                                        <input type="hidden" class="form-control" name="raw_ttl_trx" id="raw_ttl_trx" parsley-trigger="change" value="{{$sales->ttl_harga}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Ongkos Kirim</label>
                                    <div class="col-10">
                                        <input type="number" class="form-control" name="ongkir" id="ongkir" parsley-trigger="change" value="{{$sales->ongkir}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Transaction Date</label>
                                    <div class="col-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="trx_date" id="trx_date"  data-date-format='yyyy-mm-dd' autocomplete="off" value="{{$sales->trx_date}}">
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
                        @if ($sales->approve == 0)
                            <?php
                                $url_register		= base64_encode(route('salesApprove',['user_id'=>session('user_id'),'trx_id'=>$sales_id,'role'=>session('role')]));
                            ?>
                            @if (array_search("PSSLA",$page))
                            <a href="finspot:FingerspotVer;<?=$url_register?>" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Sales</a>
                            @endif
                        @else
                            <?php
                                $count_temp = TempSales::where('trx_id',$sales_id)->count('trx_id');
                                $status_temp = TempSales::where('trx_id',$sales_id)->where('status',1)->count('trx_id');
                            ?>
                            @if($count_temp > 0 && $status_temp == 1)
                                <?php
                                    $url_register		= base64_encode(route('salesApprove',['user_id'=>session('user_id'),'trx_id'=>$sales_id,'role'=>session('role')]));
                                ?>
                                @if (array_search("PSSLA",$page))
                                <a href="finspot:FingerspotVer;<?=$url_register?>" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Sales yang sudah diupdate</a>
                                @endif
                            @else
                                <a class="btn btn-inverse btn-trans waves-effect w-md waves-danger m-b-5">Sales sudah di approve</a>
                            @endif
                        @endif
                        <button class="btn btn-primary btn-rounded w-md waves-effect waves-light m-b-5">Update Sales Order</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
    {{-- Select2 --}}
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    {{-- Date Picker --}}
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- Sweet Alert Js  -->
    <script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>
    {{-- Fingerprint --}}
    <script src="{{ asset('assets/fingerprint/jquery.timer.js') }}"></script>
    <script src="{{ asset('assets/fingerprint/ajaxmask.js') }}"></script>

@endsection

@section('script-js')
<script>
// Select2
$(".select2").select2();

// Date Picker
jQuery('#trx_date').datepicker();

$("#form").submit(function(e){
    ttl = 0;
    $('input[name="prod_id[]"]').each(function() {
        ttl++;
    });

    if(ttl == 0){
        toastr.warning("Belum ada data yang dimasukkan", 'Warning!')
        e.preventDefault();
    }else{
        $( "#form" ).submit();
    }
});

function addItem(){
    customer = $('#customer').val();
    qty = $('#qty').val();
    unit = $('#unit').val();
    count = $('#count').val();
    select_product = $('#select_product').val();

    if(unit == null || unit == '' || qty == 0 || qty == null || qty == ''){
        toastr.warning("Unit atau qty tidak boleh kosong!", 'Warning!')
    }else{
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

function changeTotal(i,param=null){
    setTimeout(function(){
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

        if(param != "bv"){
            new_bv = price*0.003;
            bv = $('#bv_unit'+i).val(new_bv);
        }
        bv = $('#bv_unit'+i).val();
        if(bv == NaN || bv == null || bv == ""){
            $('#bv_unit'+i).val(0);
            bv = parseFloat($('#bv_unit'+i).val());
        }else{
            bv = parseFloat($('#bv_unit'+i).val(),10);
        }
        $('#bv_unit'+i).val(bv);

        ttl_price = price*qty;
        ttl_bv = bv*qty;

        $('#sub_ttl_price'+i).val(ttl_price);
        $('#sub_ttl_bv'+i).val(ttl_bv)

        changeTotalHarga();
    }, 1000);
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

function deleteItemOld(id,purdet){
    status_so = $('#status').val();
    swal({
        title: 'Are you sure?',
        text: "SANGAT TIDAK DISARANKAN UNTUK MENGHAPUS DATA INI. Menghapus data ini akan merubah ulang jurnal yang telah dibikin, merubah jumlah pembayaran yang mengakibatkan perbedaan nominal, Jika telah melakukan Delivery Order menggunakan Product ini, maka Delivery Order Tersebut akan terhapuskan dari semesta sistem ini",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger m-l-10',
        buttonsStyling: false
    }).then(function () {
        $.ajax({
            url : "{{route('destroySalesDetail')}}",
            type : "get",
            dataType: 'json',
            data:{
                detail: purdet,
                status: status_so,
            },
        }).done(function (data) {
            swal(
                'Deleted!',
                'Your file has been deleted.',
                'success'
            )
            location.reload();
        }).fail(function (msg) {
            swal(
                'Failed',
                'Your imaginary file is safe :)',
                'error'
            )
        });

    }, function (dismiss) {
        // dismiss can be 'cancel', 'overlay',
        // 'close', and 'timer'
        if (dismiss === 'cancel') {
            console.log("eh ga kehapus");
            swal(
                'Cancelled',
                'Your imaginary file is safe :)',
                'error'
            )
        }
    })
}

function getDetail(id){
    $.ajax({
        url : "{{route('sales.show',['id'=>1])}}",
        type : "get",
        dataType: 'json',
        data:{
            id:id,
        },
    }).done(function (data) {
        $('#modalView').html(data);
        $('#modalLarge').modal("show");
    }).fail(function (msg) {
        alert('Gagal menampilkan data, silahkan refresh halaman.');
    });
}

function changeCustomer(id){
    // console.log(id);
    $.ajax({
        url : "{{route('getCustomer')}}",
        type : "get",
        dataType: 'json',
        data:{
            id : id,
        },
    }).done(function (data) {
        console.log(data);
        $("#cphone").html(data.phone);
        $("#comname").html(data.cname);
        $("#comphone").html(data.cphone);
        $("#customer").val(data.id);
    }).fail(function (msg) {
        alert('Gagal menampilkan data, silahkan refresh halaman.'+msg);
    });
}
</script>
@endsection
