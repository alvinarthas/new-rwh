@extends('layout.main')
@php
    use App\ManageHarga;
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
@endsection

@section('judul')
Form Update Purchasing
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            {{-- Data Supplier --}}
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
            {{-- Form Data --}}
            <form class="form-horizontal" role="form" action="{{ route('purchase.update',['id'=>$purchase->id]) }}" enctype="multipart/form-data" method="POST">
                {{ method_field('PUT') }}
                @csrf
                
                <input type="hidden" name="bulanpost" id="bulanpost" value="{{$purchase->month}}">
                <input type="hidden" name="tahunpost" id="tahunpost" value="{{$purchase->year}}">
                <input type="hidden" name="supplierpost" id="supplierpost" value="{{$purchase->supplier}}">
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
                                        @php($i=1)
                                        @foreach ($details as $detail)
                                            <tr style="width:100%" id="trow{{$i}}">
                                                <td>{{$i}}</td>
                                                <td>{{$detail->prod_id}}</td>
                                                <td>{{$detail->product->name}}</td>
                                                <td>{{$detail->qty}}</td>
                                                <td>{{$detail->unit}}</td>
                                                <td>Rp. {{number_format($detail->price_dist)}}</td>
                                                <td>Rp. {{number_format($detail->price)}}</td>
                                                <td><input type="hidden" value="{{$detail->price_dist*$detail->qty}}" id="sub_ttl_dist{{$i}}">Rp. {{number_format($detail->price_dist*$detail->qty)}}</td>
                                                <td><input type="hidden" value="{{$detail->price*$detail->qty}}" id="sub_ttl_mod{{$i}}">Rp. {{number_format($detail->price*$detail->qty)}}</td>
                                                <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem({{$i}},{{$detail->id}})">Delete</a></td>
                                            </tr>
                                        @php($i++)
                                        @endforeach
                                        <input type="hidden" name="count" id="count" value="{{$i-1}}">
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
                                        <input type="number" class="form-control" name="ttl_harga_distributor" id="ttl_harga_distributor" parsley-trigger="change" value="{{$ttl_harga_dist}}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Total Harga Modal</label>
                                    <div class="col-10">
                                        <input type="text" class="form-control" name="ttl_harga_modal" id="ttl_harga_modal" parsley-trigger="change" value="{{$ttl_harga_modal}}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">PO Date</label>
                                    <div class="col-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="po_date" id="po_date" value="{{$purchase->tgl}}"  data-date-format='yyyy-mm-dd' autocomplete="off">
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
                        @if ($purchase->approve == 0)
                        <?php
                            $url_register		= base64_encode(route('purchaseApprove',['user_id'=>session('user_id'),'trx_id'=>$purchase->id]));
                        ?>
                            <a href="finspot:FingerspotVer;<?=$url_register?>" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Purchase</a>
                        @else
                            <a class="btn btn-inverse btn-trans waves-effect w-md waves-danger m-b-5">Purchase sudah di approve</a>
                        @endif
                        <button class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Simpan Purchase Order</a>
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

function deleteItem(id,purdet){
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
            url : "{{route('destroyPurchaseDetail')}}",
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