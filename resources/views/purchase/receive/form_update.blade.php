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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <style>
        input {
            width: 100%;
            box-sizing: border-box;
        }
    </style>
@endsection

@section('judul')
Form Update Receive Product {{$receive[0]->id_jurnal}}
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            {{-- Form Data --}}
            <form class="form-horizontal" id="form" role="form" action="{{ route('receiveProdUpd',['id'=>$receive[0]->id_jurnal]) }}" enctype="multipart/form-data" method="POST">
                {{ method_field('PUT') }}
                @csrf

                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">Receive Product Data</h4>
                    <div class="row">
                        <div class="col-12">
                            <div class="p-20">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="salesdate" class="col-form-label">Purchase ID</label>
                                        <input type="text" class="form-control" id="salesdate" value="PO.{{$receive[0]->trx_id}}" readonly>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="salesid" class="col-form-label">Receive Item ID</label>
                                        <input type="text" class="form-control" id="salesid" value="{{$receive[0]->id_jurnal}}" readonly>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="salesdate" class="col-form-label">Creator</label>
                                        <input type="text" class="form-control" id="salesdate" value="{{$receive[0]->creator()->first()->name}}" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Receive Date</label>
                                    <div class="col-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="receive_date" id="receive_date" data-date-format='yyyy-mm-dd' autocomplete="off" value="{{ $receive[0]->receive_date }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="ti-calendar"></i></span>
                                            </div>
                                        </div><!-- input-group -->
                                    </div>
                                </div>
                                <div class="form-group text-right m-b-0">
                                    <button class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5">Update Receive Date</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Form Data --}}
            <form class="form-horizontal" id="form" role="form" action="{{ route('receiveProdAddProd') }}" enctype="multipart/form-data" method="POST">
                @csrf
                <input type="hidden" name="receive_date" id="receive_date" value="{{ $receive[0]->receive_date }}">
                <input type="hidden" name="trx_id" id="trx_id" value="{{ $receive[0]->trx_id }}">
                <input type="hidden" name="id_jurnal" id="id_jurnal" value="{{ $receive[0]->id_jurnal }}">

                {{-- Insert Item Card --}}
                <div class="card-box">
                    <h4 class="m-t-0 header-title">Insert Item</h4>
                    <div class="row">
                        <div class="col-12">
                            <div class="p-20">
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Choose Product Name</label>
                                    <div class="col-10">
                                        <select class="form-control select2" parsley-trigger="change" name="select_product" id="select_product" required>
                                            <option value="#" disabled selected>Pilih Product</option>
                                            @foreach ($producttrx as $product)
                                                <option value="{{$product->prod_id}}">{{$product->prod_id}} - {{$product->product->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Quantity Receive</label>
                                    <div class="col-10">
                                        <input type="number" class="form-control" name="qty" id="qty" parsley-trigger="change" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Expired Date</label>
                                    <div class="col-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" parsley-trigger="change" placeholder="yyyy/mm/dd" name="expired_date" id="expired_date" data-date-format='yyyy-mm-dd' autocomplete="off">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="ti-calendar"></i></span>
                                            </div>
                                        </div><!-- input-group -->
                                    </div>
                                </div>
                                <div class="form-group text-right m-b-0">
                                    <button class="btn btn-danger btn-rounded w-md waves-effect waves-light m-b-5">Tambah Item</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="card-box table-responsive">
                <div class="row">
                    <h4 class="m-t-0 header-title">Receive Item Details</h4>
                    <div class="col-12">
                        <div class="p-20">
                            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <th>No</th>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Qty</th>
                                    <th>Expired Date</th>
                                    <th>Action</th>
                                </thead>
                                <tbody id="ri-list-body">
                                    @php($i=1)
                                    @foreach ($receive as $rec)
                                        @isset($rec->prod->name)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $rec->prod_id }}</td>
                                            <td>{{ $rec->prod->name }}</td>
                                            <td>{{ $rec->qty }}</td>
                                            <td>{{ $rec->expired_date }}</td>
                                            <td>
                                                <a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem({{ $rec->id }})" >Delete</a>
                                            </td>
                                        </tr>
                                        @endisset
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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

jQuery('#receive_date').datepicker();
jQuery('#expired_date').datepicker();

function deleteItem(id){
    var token = $("meta[name='csrf-token']").attr("content");

    console.log(id)
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
            url : "{{route('receiveProdDelProd')}}",
            type : "DELETE",
            dataType: 'json',
            data:{
                id:id,
                _token: token,
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
</script>
@endsection
