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

    <style>
        input {
            width: 100%;
            box-sizing: border-box;
        }
    </style>
@endsection

@section('judul')
Form Pindah Barang
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Insert Item</h4>
                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Choose Product Name</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="product_init" id="product_init" onchange="showAvailableGudang(this.value)">
                                        <option value="#" disabled selected>Pilih Product</option>
                                        @foreach ($products as $product)
                                            <option value="{{$product->prod_id}}">{{$product->prod_id}} - {{$product->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-6">
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label">Gudang Asal</label>
                                        <div class="col-10">
                                            <select class="form-control select2" parsley-trigger="change" name="gudang_asal" id="gudang_asal" onchange="showTotalGudang(this.value)">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label">Gudang Tujuan</label>
                                        <div class="col-10">
                                            <select class="form-control select2" parsley-trigger="change" name="gudang_tujuan" id="gudang_tujuan">
                                                <option value="#" disabled selected>Pilih Gudang Tujuan</option>
                                                @foreach ($gudangs as $gudang)
                                                    <option value="{{$gudang->id}}">{{$gudang->nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6">
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label">Stok Sekarang</label>
                                        <div class="col-10">
                                            <input type="number" class="form-control" name="stock_sekarang" id="stock_sekarang" parsley-trigger="change" value="0" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label">Jumlah</label>
                                        <div class="col-10">
                                            <input type="number" class="form-control" name="qty_init" id="qty_init" parsley-trigger="change" value="0" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group text-right m-b-0">
                    <a href="javascript:;" class="btn btn-danger btn-rounded w-md waves-effect waves-light m-b-5" onclick="addItem()">Tambah Item</a>
                </div>
            </div>

            @if($jenis == "create")
                <form class="form-horizontal" id="form" role="form" action="{{ route('transit.store') }}" enctype="multipart/form-data" method="POST">
            @else
                <form class="form-horizontal" id="form" role="form" action="{{ route('transit.update',['id'=>$transit->id]) }}" enctype="multipart/form-data" method="POST">
                    {{ method_field('PUT') }}
            @endif

                @csrf
                <div class="card-box">
                    <div class="row">
                        <label class="col-2 col-form-label">Detail Pindah Gudang</label>
                        <div class="col-12">
                            <div class="p-20">
                                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <th>No</th>
                                        <th>Product Name</th>
                                        <th>Qty</th>
                                        <th>Asal Gudang</th>
                                        <th></th>
                                        <th>Tujuan Gudang</th>
                                        <th>Option</th>
                                    </thead>
                                    <tbody id="transit-list-body">
                                        @if($jenis == "create")
                                            <input type="hidden" name="count" id="count" value="0">
                                        @else
                                            @php($i=1)
                                            @foreach ($details as $detail)
                                            <tr style="width:100%" id="trow{{$i}}">
                                                <td>{{$i}}</td>
                                                <input type="hidden" name="detail[]" id="detail{{$i}}" value="{{$detail->id}}">
                                                <td><input type="hidden" name="product[]" id="product{{$i}}" value="{{$detail->product_id}}">{{$detail->product_id}} - {{$detail->product()->first()->name}}</td>
                                                <td><input type="number" name="qty[]" value="{{$detail->qty}}" id="qty{{$i}}"></td>
                                                <td><input type="hidden" name="gudangStart[]" id="gudangStart{{$i}}" value="{{$detail->gudang_awal}}">{{$detail->gudang_awal()->first()->nama}}</td>
                                                <td style="align:center"> => </td>
                                                <td><input type="hidden" name="gudangEnd[]" id="gudangEnd{{$i}}" value="{{$detail->gudang_akhir}}">{{$detail->gudang_akhir()->first()->nama}}</td>
                                                <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItemOld({{$detail->id}})" >Delete</a></td>
                                            </tr>
                                            @php($i++)
                                            @endforeach
                                            <input type="hidden" name="count" id="count" value="{{$i-1}}">
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-box">
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Tanggal</label>
                        <div class="col-10">
                            <div class="input-group">
                                <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="tanggal" id="tanggal"  data-date-format='yyyy-mm-dd' autocomplete="off" value="@isset($transit->tgl){{ $transit->tgl }}@endisset">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="ti-calendar"></i></span>
                                </div>
                            </div><!-- input-group -->
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Keterangan</label>
                        <div class="col-10">
                            <input type="text" class="form-control" name="keterangan" id="keterangan" parsley-trigger="change" value="@isset($transit->keterangan){{ $transit->keterangan }}@endisset">
                        </div>
                    </div>
                    <div class="form-group text-right m-b-0">
                        <button class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Simpan Pindah Gudang</a>
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
@endsection

@section('script-js')
<script>
    // Select2
    $(".select2").select2();

    // Date Picker
    jQuery('#tanggal').datepicker();

    function resetall(){
        $('#product_init').val("#").change();
        $('#gudang_asal').val("");
        $('#gudang_tujuan').val("#").change();
        $('#qty_init').val(0);
        $('#stock_sekarang').val(0);
    }

    function deleteItem(id){
        count = parseInt($('#count').val()) - 1;
        $('#trow'+id).remove();
        $('#count').val(count);
    }

    function addItem(){
        product_init = $('#product_init').val();
        gudang_asal = $('#gudang_asal').val();
        gudang_tujuan = $('#gudang_tujuan').val();
        qty_init = $('#qty_init').val();
        stock_sekarang = $('#stock_sekarang').val();
        count = $('#count').val();

        if(stock_sekarang == 0 || stock_sekarang == null || stock_sekarang == '' || qty_init == 0 || qty_init == null || qty_init == ''){
            toastr.warning("Stock sekarang atau jumlah barang yang dipindahkan tidak boleh kosong", 'Warning!')
        }else if(gudang_asal == gudang_tujuan){
            toastr.warning("Gudang Asal dan Gudang Tujuan tidak boleh sama!", 'Warning!')
        }else{
            $.ajax({
                url : "{{route('transit.create')}}",
                type : "get",
                dataType: 'json',
                data:{
                    product_init: product_init,
                    gudang_asal: gudang_asal,
                    gudang_tujuan: gudang_tujuan,
                    qty_init: qty_init,
                    count:count,
                },
            }).done(function (data) {
                $('#transit-list-body').append(data.append);
                $('#count').val(data.count);
                resetall();
            }).fail(function (msg) {
                alert('Gagal menampilkan data, silahkan refresh halaman.');
            });
        }
    }

    function deleteItemOld(id){
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
                url : "{{route('destroyTransitDetail')}}",
                type : "get",
                dataType: 'json',
                data:{
                    id: id,
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

    function showAvailableGudang(id){
        $.ajax({
            url : "{{route('getAvailableGudang')}}",
            type : "get",
            dataType: 'json',
            data:{
                product: id,
            },
        }).done(function (data) {
            $('#gudang_asal').html(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function showTotalGudang(id){
        product = $('#product_init').val();

        $.ajax({
            url : "{{route('getGudangTotal')}}",
            type : "get",
            dataType: 'json',
            data:{
                product: product,
                gudang: id,
            },
        }).done(function (data) {
            $('#stock_sekarang').val(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection
