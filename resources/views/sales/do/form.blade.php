@php
    use App\Sales;
@endphp
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
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('judul')
Form Sales Order
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Sales Order Detail</h4>
                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="salesid" class="col-form-label">Sales ID</label>
                                    <input type="text" class="form-control" id="salesid" value="SO.{{$sales->id}}" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="salesdate" class="col-form-label">Sales Date</label>
                                    <input type="text" class="form-control" id="salesdate" value="{{$sales->trx_date}}" readonly>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="customer" class="col-form-label">Customer</label>
                                    <input type="text" class="form-control" id="customer" value="{{$sales->customer->apname}}" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="total" class="col-form-label">Total Harga</label>
                                    <input type="text" class="form-control" id="total" value="Rp {{number_format($sales->ttl_harga+$sales->ongkir,2,",",".")}}" readonly>
                                </div>
                            </div>
                            <br>
                            <h4 class="m-t-0 header-title">Product Detail</h4>
                            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Sent</th>
                                </thead>
                                <tbody>
                                    @foreach ($salesdets as $salesdet)
                                        <tr>
                                            <td>{{$salesdet->prod_id}}</td>
                                            <td>{{$salesdet->product->name}}</td>
                                            <td>{{$salesdet->qty}}</td>
                                            <td>{{$salesdet->unit}}</td>
                                            @php($count_prod = Sales::checkSent($salesdet->prod_id,$sales->id))
                                            @if ($salesdet->qty == $count_prod)
                                                <td><a href="javascrip:;" class="btn btn-success btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$count_prod}}</a></td>
                                            @elseif ($count_prod > $salesdet->qty)
                                                <td><a href="javascrip:;" class="btn btn-warning btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$count_prod}}</a></td>
                                            @else
                                                <td><a href="javascrip:;" class="btn btn-danger btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$count_prod}}</a></td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <br>
                            <h4 class="m-t-0 header-title">List Delivery Order</h4>
                            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <th>Delivery ID</th>
                                    <th>Delivery Date</th>
                                    <th>Creator</th>
                                    <th>Option</th>
                                </thead>
                                <tbody>
                                    @foreach ($dos as $do)
                                        <tr>
                                            <td><a href="javascript:;" onclick="getDetail({{$do->id}})" class="btn btn-purple btn-rounded waves-effect w-xs waves-danger m-b-5">DO.{{$do->id}}</a></td>
                                            <td>{{$do->date}}</td>
                                            <td>{{$do->petugas()->first()->name}}</td>
                                            <td>
                                                @if (array_search("PSDOD",$page))
                                                    <a href="javascript:;" onclick="deleteDO({{ $do->id}})" class="btn btn-danger btn-rounded waves-effect w-xs waves-danger m-b-5">Delete DO</a>
                                                @endif
                                                <a href="javascript:;" onclick="printdo({{$do->id}})" class="btn btn-success btn-rounded waves-effect w-xs waves-success m-b-5">Print DO</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!--  Modal content for the above example -->
            <div class="modal fade bs-example-modal-lg" id="modalLarge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-lg" id="do-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myLargeModalLabel">Delivery Order Detail</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
                        </div>
                        <div class="modal-body" id="modalView">
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

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
                                            <option value="{{$product->prod_id}}">{{$product->prod_id}} - {{$product->name}}</option>
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
                            <div class="form-group text-right m-b-0">
                                <a href="javascript:;" class="btn btn-danger btn-rounded w-md waves-effect waves-light m-b-5" onclick="addItem()">Tambah Item</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form class="form-horizontal" role="form" action="{{ route('storeDo') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" name="sales_id" id="sales_id" value="{{$sales->id}}">
                <div class="card-box table-responsive">
                    <div class="row">
                        <h4 class="m-t-0 header-title">Create DO Item Details</h4>
                        <div class="col-12">
                            <div class="p-20">
                                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <th>Product ID</th>
                                        <th>Product Name</th>
                                        <th>Qty</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody id="do-list-body">
                                        <input type="hidden" name="count" id="count" value="0">

                                    </tbody>
                                </table>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Delivery Order Date</label>
                                    <div class="col-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="do_date" id="do_date"  data-date-format='yyyy-mm-dd' autocomplete="off">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="ti-calendar"></i></span>
                                            </div>
                                        </div><!-- input-group -->
                                    </div>
                                </div>
                            </div>
                            @if (array_search("PSDOC",$page))
                            <div class="form-group text-right m-b-0">
                                <button class="btn btn-primary btn-rounded w-md waves-effect waves-light m-b-5">Create Delivery Order</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
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
    <!-- Required datatable js -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
@endsection

@section('script-js')
<script>
    // Select2
    $(".select2").select2();
    jQuery('#do_date').datepicker();

    function addItem(){
        select_product = $('#select_product').val();
        qty = $('#qty').val();
        count = $('#count').val();

        $.ajax({
            url : "{{route('addBrgDo')}}",
            type : "get",
            dataType: 'json',
            data:{
                select_product: select_product,
                qty: qty,
                count:count,
            },
        }).done(function (data) {
            $('#do-list-body').append(data.append);
            $('#count').val(data.count);
            resetall();
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function deleteItem(id){
        count = parseInt($('#count').val()) - 1;
        $('#trow'+id).remove();
        $('#count').val(count);
    }

    function resetall(){
        $('#select_product').val("#").change();
        $('#qty').val("");
    }

    function getDetail(id){
        $.ajax({
            url : "{{route('viewDo')}}",
            type : "get",
            dataType: 'json',
            data:{
                do_id:id,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modalLarge').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function printing(id, data){
        var token = $("meta[name='csrf-token']").attr("content");
        console.log("printing");
        $.ajax({
            url : "http://rwhserver:8060/new-rwh/do/"+id+"/print",
            type : "get",
            data : {
                data : data,
                _token : token,
            },
        })
    }

    function printdo(id){
        //console.log("print");
        $.ajax({
            url : "{{route('printDo')}}",
            type : "get",
            dataType : 'json',
            data:{
                trx_id:id,
            },
        }).done(function (data) {
            console.log(data);
            printing(data[0].trx_id, data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.'+msg);
        });
    }

    function deleteDO(id){
        var token = $("meta[name='csrf-token']").attr("content");

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
                url: "{{route('deleteDo')}}",
                type: 'DELETE',
                data: {
                    "id": id,
                    "_token": token,
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
