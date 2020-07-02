@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Select2 -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Date Picker -->
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- Sweet Alert css -->
    <link href="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
@endsection

@section('judul')
Form Receive Item
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">PO Details</h4>
                <div class="form-group row">
                    <label class="col-2 col-form-label">PO ID</label>
                    <div class="col-10">
                        <input type="text" class="form-control"disabled value="PO.{{$trx->id}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Tanggal PO</label>
                    <div class="col-10">
                        <input type="text" class="form-control"disabled value="{{$trx->tgl}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Supplier Name</label>
                    <div class="col-10">
                        <input type="text" class="form-control" disabled value="{{$trx->supplier()->first()->nama}}">
                    </div>
                </div>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Qty Receive</th>
                    </thead>
                    <tbody>
                        @php($i=1)
                        @foreach ($details as $detail)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$detail->prod_id}}</td>
                                <td>{{$detail->prod_name}}</td>
                                <td>{{$detail->qty}}</td>
                                <td>{{$detail->unit}}</td>
                                @if ($detail->qtyrec == 0)
                                    <td><a href="javascrip:;" class="btn btn-danger btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$detail->qtyrec}}</a></td>
                                @elseif(($detail->qtyrec - $detail->qty) == 0 )
                                    <td><a href="javascrip:;" class="btn btn-success btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$detail->qtyrec}}</a></td>
                                @elseif(($detail->qtyrec-$detail->qty) < 0 )
                                    <td><a href="javascrip:;" class="btn btn-warning btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$detail->qtyrec}}</a></td>
                                @else
                                    <td><a href="javascrip:;" class="btn btn-info btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$detail->qtyrec}}</a></td>
                                @endif
                            </tr>
                        @php($i++)
                        @endforeach
                    </tbody>
                </table>
                <br>
                <h4 class="m-t-0 header-title">List Receive Item</h4>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>Receive Item ID</th>
                        <th>Receive Date</th>
                        <th>Creator</th>
                        <th>Option</th>
                    </thead>
                    <tbody>
                        @foreach ($receives as $receive)
                            <tr>
                                <td>
                                    <a href="javascript:;" onclick="getDetail('{{ $receive->id_jurnal }}')" class="btn btn-purple btn-rounded waves-effect w-xs waves-danger m-b-5">
                                        {{$receive->id_jurnal}}
                                    </a>
                                </td>
                                <td>{{$receive->receive_date}}</td>
                                <td>{{$receive->creator()->first()->name}}</td>
                                <td>
                                    @if(array_search("PURPU",$page))
                                        <a href="{{route('receiveProdEdit',['id'=>$receive['id_jurnal']])}}" class="btn btn-primary btn-rounded waves-effect w-xs waves-danger m-b-5 ">Edit</a>
                                    @endif
                                    @if(array_search("PURPD",$page))
                                        <a href="javascript:;" onclick="deleteReceive('{{$receive->id_jurnal}}')" class="btn btn-danger btn-rounded waves-effect w-xs waves-danger m-b-5">Delete</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!--  Modal content for the above example -->
            <div class="modal fade bs-example-modal-lg" id="modalLarge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-lg" id="do-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myLargeModalLabel">Receive Item Detail</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
                        </div>
                        <div class="modal-body" id="modalView">
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div class="card-box">
                <h4 class="m-t-0 header-title">Insert Item</h4>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Choose Product Name</label>
                    <div class="col-10">
                        <select class="form-control select2" parsley-trigger="change" name="select_product" id="select_product" required>
                            <option value="#" disabled selected>Pilih Product</option>
                            @foreach ($producttrx as $product)
                                <option value="{{$product->id}}">{{$product->prod_id}} - {{$product->product->name}} (Rp {{ number_format($product->price, 2, ",", ".") }})</option>
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
                <div class="form-group row">
                    <label class="col-2 col-form-label">Gudang</label>
                    <div class="col-10">
                        <select class="form-control select2" parsley-trigger="change" name="gudang" id="gudang" required>
                            @foreach ($gudangs as $gudang)
                                <option value="{{$gudang->id}}">{{$gudang->nama}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group text-right m-b-0">
                    <a href="javascript:;" class="btn btn-danger btn-rounded w-md waves-effect waves-light m-b-5" onclick="addItem()">Add Item</a>
                </div>
            </div>
            <form class="form-horizontal" role="form" action="{{ route('receiveProdStr') }}" enctype="multipart/form-data" method="POST">
                @csrf
                <input type="hidden" name="trx_id" value="{{$trx->id}}">
                <div class="card-box table-responsive">
                    <div class="row">
                        <h4 class="m-t-0 header-title">Create Receive Item Details</h4>
                        <div class="col-12">
                            <div class="p-20">
                                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <th>Product ID</th>
                                        <th>Product Name</th>
                                        <th>Qty</th>
                                        <th>Expired Date</th>
                                        <th>Gudang</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody id="ri-list-body">
                                        <input type="hidden" name="count" id="count" value="0">

                                    </tbody>
                                </table>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Receive Date</label>
                                    <div class="col-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="receive_date" id="receive_date" data-date-format='yyyy-mm-dd' autocomplete="off">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="ti-calendar"></i></span>
                                            </div>
                                        </div><!-- input-group -->
                                    </div>
                                </div>
                            </div>
                            @if (array_search("PURPC",$page))
                                <div class="form-group text-right m-b-0">
                                    <button class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5">Create Receive Item</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <!-- Date Picker -->
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

    jQuery('#receive_date').datepicker();
    jQuery('#expired_date').datepicker();

    function addItem(){
        select_product = $('#select_product').val();
        qty = $('#qty').val();
        count = $('#count').val();
        expired = $('#expired_date').val();
        gudang = $('#gudang').val();

        $.ajax({
            url : "{{route('addBrgReceive')}}",
            type : "get",
            dataType: 'json',
            data:{
                select_product: select_product,
                qty: qty,
                count:count,
                expired:expired,
                gudang:gudang,
            },
        }).done(function (data) {
            $('#ri-list-body').append(data.append);
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
        $('#expired_date').val("");
    }

    function getDetail(id){
        console.log(id)
        $.ajax({
            url : "{{route('viewRI')}}",
            type : "get",
            dataType: 'json',
            data:{
                ri_id : id,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modalLarge').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function deleteReceive(id){
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
            buttonsStyling: false,
        }).then(function () {
            $.ajax({
                url: "{{route('receiveProdDel')}}",
                type: 'DELETE',
                data: {
                    jurnal_id: id,
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
