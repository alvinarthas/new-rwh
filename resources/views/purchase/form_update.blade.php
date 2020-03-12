@extends('layout.main')
@php
    use App\TempPO;
    use App\TempPODet;
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
Form Update Purchasing #{{$purchase->id}}
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
                                <div class="col-5">
                                    <select class="form-control select2" parsley-trigger="change" name="bulan" id="bulan" onchange="changeBulan(this.value)" required>
                                        <option value="#" disabled>Pilih Bulan</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            @if($purchase->month == $i)
                                                <option value="{{$i}}" selected>{{date("F", mktime(0, 0, 0, $i, 10))}}</option>
                                            @else
                                                <option value="{{$i}}">{{date("F", mktime(0, 0, 0, $i, 10))}}</option>
                                            @endif
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-5">
                                    <select class="form-control select2" parsley-trigger="change" name="tahun" id="tahun" onchange="changeTahun(this.value)" required>
                                        <option value="#" disabled>Pilih Tahun</option>
                                        @for ($i = 2018; $i <= date('Y'); $i++)
                                            @if($purchase->year == $i)
                                                <option value="{{$i}}" selected>{{$i}}</option>
                                            @else
                                                <option value="{{$i}}">{{$i}}</option>
                                            @endif
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            @if ($status == 1)
                                @php($purchase_id = $purchase->purchase_id)
                            <a href="javascript:;" class="btn btn-danger btn-trans w-md waves-effect waves-light m-b-5" onclick="getDetail({{$purchase->purchase_id}})">Show Original Data</a>
                            @else
                                @php($purchase_id = $purchase->id)
                            @endif
                                <input type="hidden" name="status" id="status" value="{{$status}}">
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
                                            <option value="{{$product->prod_id}}">{{$product->prod_id}} - {{$product->name}} - Rp {{number_format($product->harga_distributor,2,",",".")}} - Rp {{number_format($product->harga_modal,2,",",".")}}</option>
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
                            <h4 class="modal-title" id="myLargeModalLabel">Purchase Order Detail</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
                        </div>
                        <div class="modal-body" id="modalView">
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            {{-- Form Data --}}
            <form class="form-horizontal" id="form" role="form" action="{{ route('purchase.update',['id'=>$purchase_id]) }}" enctype="multipart/form-data" method="POST">
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
                                        <th>Avg Cost</th>
                                        <th>Harga Distributor</th>
                                        <th>Harga Modal</th>
                                        <th>Sub Total Distributor</th>
                                        <th>Sub Total Modal</th>
                                        <th>Option</th>
                                    </thead>
                                    <tbody id="purchase-list-body">
                                        @php($i=1)
                                        @foreach ($details as $detail)
                                            @isset($detail->product->name)
                                            <tr style="width:100%" id="trow{{$i}}">
                                                <td>{{$i}}</td>
                                                <input type="hidden" name="detail[]" id="detail{{$i}}" value="{{$detail->id}}">
                                                <td><input type="hidden" name="prod_id[]" id="prod_id{{$i}}" value="{{$detail->prod_id}}">{{$detail->prod_id}}</td>
                                                <td><input type="hidden" name="prod_name[]" id="prod_name{{$i}}" value="{{$detail->product->name}}">{{$detail->product->name}}</td>
                                                <td><input type="number" name="qty[]" id="qty{{$i}}" value="{{$detail->qty}}" onchange="changeTotal({{$i}})" onkeyup="changeTotal({{$i}})"></td>
                                                <td><input type="hidden" name="unit[]" id="unit{{$i}}" value="{{$detail->unit}}">{{$detail->unit}}</td>
                                                <td>Rp&nbsp;{{number_format(PurchaseDetail::avgCost($detail->prod_id),2,",",".")}}</td>
                                                <td><input type="number" name="harga_dist[]" id="harga_dist{{$i}}" value="{{$detail->price_dist}}" onkeyup="changeTotal({{$i}})"></td>
                                                <td><input type="number" name="harga_mod[]" id="harga_mod{{$i}}" value="{{$detail->price}}" onkeyup="changeTotal({{$i}})"></td>
                                                <td><input type="number" readonly value="{{$detail->price_dist*$detail->qty}}" name="sub_ttl_dist[]" id="sub_ttl_dist{{$i}}"></td>
                                                <td><input type="number" name="sub_ttl_mod[]" readonly value="{{$detail->price*$detail->qty}}" id="sub_ttl_mod{{$i}}"></td>
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
                                        <input type="text" class="form-control" name="notes" id="notes" parsley-trigger="change" value="{{$purchase->notes}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-right m-b-0">
                        @if ($purchase->approve == 0)
                        <?php
                            $url_register		= base64_encode(route('purchaseApprove',['user_id'=>session('user_id'),'trx_id'=>$purchase_id,'role'=>session('role')]));
                        ?>
                            @if (array_search("PUPUA",$page))
                            <a href="finspot:FingerspotVer;<?=$url_register?>" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Purchase</a>
                            @endif
                        @else
                            <?php
                                $count_temp = TempPO::where('purchase_id',$purchase_id)->count('purchase_id');
                                $status_temp = TempPO::where('purchase_id',$purchase_id)->where('status',1)->count('purchase_id');
                            ?>
                            @if($count_temp > 0 && $status_temp == 1)
                                <?php
                                    $url_register		= base64_encode(route('purchaseApprove',['user_id'=>session('user_id'),'trx_id'=>$purchase_id,'role'=>session('role')]));
                                ?>
                                @if (array_search("PUPUA",$page))
                                <a href="finspot:FingerspotVer;<?=$url_register?>" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Purchase yang sudah diupdate</a>
                                @endif
                            @else
                                <a class="btn btn-inverse btn-trans waves-effect w-md waves-danger m-b-5">Purchase sudah di approve</a>
                            @endif
                        @endif
                        <button class="btn btn-primary btn-rounded w-md waves-effect waves-light m-b-5">Update Purchase Order</a>
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
    bulanpost = $('#bulanpost').val();
    tahunpost = $('#tahunpost').val();
    select_product = $('#select_product').val();
    qty = $('#qty').val();
    unit = $('#unit').val();
    count = $('#count').val();

    if(unit == null || unit == '' || qty == 0 || qty == null || qty == ''){
        toastr.warning("Unit atau qty tidak boleh kosong!", 'Warning!')
    }else{
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
            changeTotalHarga();
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
}

function deleteItem(id){
    count = parseInt($('#count').val()) - 1;
    $('#trow'+id).remove();
    $('#count').val(count);
    changeTotalHarga();
}

function changeTotalHarga(){
    // Harga Distributor
    ttl_harga_distributor = 0;
    $('input[name="sub_ttl_dist[]"]').each(function() {
        ttl_harga_distributor+=parseInt(this.value);
    });
    $('#ttl_harga_distributor').val(ttl_harga_distributor);

    // Harga Modal
    ttl_harga_modal = 0;
    $('input[name="sub_ttl_mod[]"]').each(function() {
        ttl_harga_modal+=parseInt(this.value);
    });
    $('#ttl_harga_modal').val(ttl_harga_modal);
}

function changeTotal(i){
    harga_dist = $('#harga_dist'+i).val();
    if(harga_dist == NaN || harga_dist == null || harga_dist == ""){
        $('#harga_dist'+i).val(0);
        harga_dist = parseInt($('#harga_dist'+i).val());
    }else{
        harga_dist = parseInt($('#harga_dist'+i).val(),10);
    }
    $('#harga_dist'+i).val(harga_dist);

    qty = $('#qty'+i).val();
    if(qty == NaN || qty == null || qty == ""){
        $('#qty'+i).val(0);
        qty = parseInt($('#qty'+i).val());
    }else{
        qty = parseInt($('#qty'+i).val(),10);
    }
    $('#qty'+i).val(qty);

    harga_mod = $('#harga_mod'+i).val();
    if(harga_mod == NaN || harga_mod == null || harga_mod == ""){
        $('#harga_mod'+i).val(0);
        harga_mod = parseInt($('#harga_mod'+i).val());
    }else{
        harga_mod = parseInt($('#harga_mod'+i).val(),10);
    }
    $('#harga_mod'+i).val(harga_mod);

    sub_ttl_dist = harga_dist*qty;
    sub_ttl_mod = harga_mod*qty;
    $('#sub_ttl_dist'+i).val(sub_ttl_dist);
    $('#sub_ttl_mod'+i).val(sub_ttl_mod)

    changeTotalHarga();
}

function resetall(){
    $('#select_product').val("#").change();
    $('#qty').val("");
    $('#unit').val("");
}

function deleteItemOld(id,purdet){
    status_po = $('#status').val();
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
                status: status_po,
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
        url : "{{route('purchase.show',['id'=>1])}}",
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

function changeBulan(bulan){
    $("#bulanpost").val(bulan);
}

function changeTahun(tahun){
    $("#tahunpost").val(tahun);
}
</script>
@endsection
