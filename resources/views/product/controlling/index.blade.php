@extends('layout.main')
@php
    use App\Perusahaan;
    use App\Product;
@endphp

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
    img.photo{
        display:block; width:50%; height:auto;
    }
    </style>
@endsection

@section('content')
<form class="form-horizontal" role="form" action="{{ route('exportStock') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Index Product</h4>
                <div class="form-group text-right m-b-0">
                    <button class="btn btn-success btn-trans btn-rounded waves-effect waves-light w-xs m-b-5" onclick="cetakXls()">
                        <span class="mdi mdi-file-excel">
                            Cetak Excel
                        </span>
                    </button>
                    <button class="btn btn-danger btn-trans btn-rounded waves-effect waves-light w-xs m-b-5" onclick="cetakPdf()">
                        <span class="mdi mdi-file-pdf-box">
                            Cetak PDF
                        </span>
                    </button>
                </div>

                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th style="width:5%">No</th>
                        {{-- <th>Supplier</th> --}}
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Indent</th>
                        <th>di Gudang</th>
                        <th>milik Customer</th>
                        <th>Nett</th>
                        {{-- <th>Detail Stock</th> --}}
                    </thead>

                    <tbody>
                        @php
                            $i = 1
                        @endphp
                        @foreach($products as $prd)
                            <tr>
                                <td>{{$i}}</td>
                                {{-- <td>{{$prd->supplier()->first()->nama}}</td> --}}
                                <td>{{$prd->prod_id}}</td>
                                <td>{{$prd->name}}</td>
                                    @php
                                        $i++;
                                        $indent = Product::getIndent($prd->prod_id);
                                        $gudang = Product::getGudang($prd->prod_id);
                                        $brgcust = Product::getBrgCust($prd->prod_id);
                                        $nett = $indent + $gudang - $brgcust;
                                    @endphp
                                <td><a href="javascript:;" onclick="getIndent('{{ $prd->id }}')" disabled="disabled"><strong>{{ $indent }}</strong></a></td>
                                <td><a href="javascript:;" onclick="getGudang('{{ $prd->id }}')" disabled="disabled"><strong>{{ $gudang }}</strong></a></td>
                                <td><a href="javascript:;" onclick="getBrgCust('{{ $prd->id }}')" disabled="disabled"><strong>{{ $brgcust }}</strong></a></td>
                                <td><strong>{{ $nett }}</strong></td>
                                {{-- <td>
                                    <a href="javascript:;" onclick="getDetail('{{$prd->prod_id}}')" class="btn btn-primary btn-rounded waves-effect waves-light w-md m-b-5" disabled="disabled">Show Detail</a>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <input type="hidden" name="xto" id="xto">
            </div>
        </div>
    </div> <!-- end row -->
</form>

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" id="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" id="do-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Mutasi Product</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
            </div>
            <div class="modal-body" id="modalView">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <!-- Modal-Effect -->
    <script src="{{ asset('assets/plugins/custombox/dist/custombox.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/custombox/dist/legacy.min.js') }}"></script>
@endsection

@section('script-js')

<script type="text/javascript">

    $(document).ready(function () {
        // Responsive Datatable
        $('#responsive-datatable').DataTable();
    });

    function getDetail(id){
        $.ajax({
            url : "{{route('stockControlling')}}",
            type : "get",
            dataType: 'json',
            data:{
                prod_id:id,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modal').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function getIndent(id){
        $.ajax({
            url : '/stockcontrolling/'+id+'/mutasi/brgindent',
            type : "get",
            dataType: 'json',
            data:{
                id:id,
            },
        }).done(function (data) {
            $('h4.modal-title').text('Mutasi Barang Indent');
            $('#modalView').html(data);
            $('#modal').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function getGudang(id){
        $.ajax({
            url : '/stockcontrolling/'+id+'/mutasi/brggudang',
            type : "get",
            dataType: 'json',
            data:{
                id:id,
            },
        }).done(function (data) {
            $('h4.modal-title').text('Mutasi Barang di Gudang');
            $('#modalView').html(data);
            $('#modal').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function getBrgCust(id){
        $.ajax({
            url : '/stockcontrolling/'+id+'/mutasi/brgcustomer',
            type : "get",
            dataType: 'json',
            data:{
                id:id,
            },
        }).done(function (data) {
            $('h4.modal-title').text('Mutasi Barang milik Customer');
            $('#modalView').html(data);
            $('#modal').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function cetakXls(){
        $('#xto').val("0");
    }

    function cetakPdf(){
        $('#xto').val("1");
    }
</script>
@endsection
