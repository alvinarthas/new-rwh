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
Form Update Konversi Barang
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
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
                                        <td>{{$konversi->supplier()->first()->nama}}</td>
                                        <td>{{$konversi->supplier()->first()->alamat}}</td>
                                        <td>{{$konversi->supplier()->first()->telp}}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
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

    <form class="form-horizontal" id="form" role="form" action="{{ route('konversi.update',['id'=>$konversi->id]) }}" enctype="multipart/form-data" method="POST">
        {{ method_field('PUT') }}
        @csrf
        <input type="hidden" name="supplierKonversi" id="supplierKonversi" value="{{$konversi->supplier}}">

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
                                @php($a = 1)
                                @for ($i = 0; $i < count($details);$i++)
                                    <tr style="width:100%" id="trow{{$a}}">
                                        <td>{{$a}}</td>
                                        <input type="hidden" name="detail[]" id="detail{{$a}}" value="lama">
                                        <td><input type="hidden" name="productParent[]" id="productParent{{$a}}" value="{{$details[$i]->product_id}}">{{$details[$i]->product_id}} - {{$details[$i]->product()->first()->name}}</td>
                                        <td><input type="number" name="qtyParent[]" value="{{$details[$i]->qty}}" id="qtyParent{{$a}}"></td>
                                        @php($i++)
                                        <td style="align-text:center">=></td>
                                        <td><input type="hidden" name="productChild[]" id="productChild{{$a}}" value="{{$details[$i]->product_id}}">{{$details[$i]->product_id}} - {{$details[$i]->product()->first()->name}}</td>
                                        <td><input type="number" name="qtyChild[]" value="{{$details[$i]->qty}}" id="qtyChild{{$a}}"></td>
                                        <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItemOld({{$a}},{{$details[$i-1]->id}},{{$details[$i]->id}})" >Delete</a></td>
                                    </tr>
                                    @php($a++)
                                @endfor
                                <input type="hidden" name="count" id="count" value="{{$i-1}}">
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
                                <input type="text" class="form-control" name="keterangan" id="keterangan" parsley-trigger="change" value="{{$konversi->keterangan}}">
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

@endsection

@section('js')
    {{-- Select2 --}}
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <!-- Sweet Alert Js  -->
    <script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('script-js')
<script>
    // Select2
    $(".select2").select2();

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

    function deleteItemOld(id,parent,child){
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
                url : "{{route('destroyKonversiDetail')}}",
                type : "get",
                dataType: 'json',
                data:{
                    parent: parent,
                    child: child,
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





