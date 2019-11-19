@extends('layout.main')
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
@endsection

@section('judul')
Form Update Sales Order
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
                                        <td>{{$sales->customer->apname}}</td>
                                        <td>{{$sales->customer->apphone}}</td>
                                        <td>{{$sales->customer->cicn}}</td>
                                        <td>{{$sales->customer->ciphone}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
                                            <option value="{{$product->prod_id}}">{{$product->prod_id}} - {{$product->prod->name}}</option>
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
            {{-- Form Data --}}
            <form class="form-horizontal" role="form" action="{{ route('sales.update',['id'=>$sales->id]) }}" enctype="multipart/form-data" method="POST">
                {{ method_field('PUT') }}
                @csrf
                
                <input type="hidden" name="customer" id="customer" value="{{$sales->customer_id}}">
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
                                        @php($i=1)
                                        @foreach ($salesdet as $detail)
                                            @isset($detail->product->name)
                                            <tr style="width:100%" id="trow{{$i}}">
                                                <td>{{$i}}</td>
                                                <td>{{$detail->prod_id}}</td>
                                                <td>{{$detail->product->name}}</td>
                                                <td>Rp. {{number_format($detail->price)}}</td>
                                                <td>{{$detail->qty}}</td>
                                                <td>{{$detail->unit}}</td>
                                                <td><input type="hidden" value="{{$detail->sub_ttl}}" id="sub_ttl_price{{$i}}">Rp. {{number_format($detail->sub_ttl)}}</td>
                                                <td>Rp. {{number_format($detail->pv)}}</td>
                                                <td>Rp. {{number_format($detail->sub_ttl_bv)}}</td>
                                                <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItemOld({{$i}},{{$detail->id}})">Delete</a></td>
                                            </tr>
                                            @endisset
                                        @php($i++)
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
                            $url_register		= base64_encode(route('salesApprove',['user_id'=>session('user_id'),'trx_id'=>$sales->id,'role'=>session('role')]));
                        ?>
                            <a href="finspot:FingerspotVer;<?=$url_register?>" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Sales</a>
                        @else
                            <a class="btn btn-inverse btn-trans waves-effect w-md waves-danger m-b-5">Sales sudah di approve</a>
                        @endif
                        <button class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Simpan Sales Order</a>
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
        changeTotalHarga(data.sub_ttl_price);
    }).fail(function (msg) {
        alert('Gagal menampilkan data, silahkan refresh halaman.');
    });
}

function changeTotalHarga(sub_ttl_price){
    raw_ttl_trx = parseInt($('#raw_ttl_trx').val());
    input = parseInt($('#ongkir').val(),10);

    $('#raw_ttl_trx').val(raw_ttl_trx+sub_ttl_price)
    new_ttl_trx = raw_ttl_trx+sub_ttl_price+input;
    $('#ttl_trx').val(new_ttl_trx);
}

function decreaseTotalHarga(id){
    console.log(id);
    raw_ttl_trx = parseInt($('#raw_ttl_trx').val());
    input = parseInt($('#ongkir').val(),10);
    sub_ttl_price = $('#sub_ttl_price'+id).val();
    console.log(sub_ttl_price);

    $('#raw_ttl_trx').val(raw_ttl_trx-sub_ttl_price)
    new_ttl_trx = (raw_ttl_trx-sub_ttl_price)+input;
    $('#ttl_trx').val(new_ttl_trx);
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
    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
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
            },
        }).done(function (data) {
            swal(
                'Deleted!',
                'Your file has been deleted.',
                'success'
            )
            count = parseInt($('#count').val()) - 1;
            decreaseTotalHarga(id);
            $('#trow'+id).remove();
            $('#count').val(count);
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
</script>
@endsection