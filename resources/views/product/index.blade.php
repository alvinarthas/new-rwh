@extends('layout.main')
@php
    use App\Perusahaan;
@endphp

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <!--venobox lightbox-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/magnific-popup/dist/magnific-popup.css') }}"/>

    {{-- Fingerprint --}}
    <link href="{{ asset('assets/fingerprint/ajaxmask.css') }}" rel="stylesheet">

    <style>
    img.photo{
        display:block; width:50%; height:auto;
    }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Index Product</h4>
                <p class="text-muted font-14 m-b-30">
                    <a href="{{ route('product.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Product</a>
                    <a href="{{ route('manageproduct') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Manage Harga</a>
                </p>

                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th style="width:5%">No</th>
                        <th>Supplier</th>
                        <th>Product ID</th>
                        <th>Product ID Perusahaan</th>
                        <th>Product Name</th>
                        <th>Product Brand</th>
                        <th>Stock Awal</th>
                        <th>Total Stock In</th>
                        <th>Total Stock Out</th>
                        <th>Stock Saat Ini</th>
                        <th>Actions</th>
                    </thead>

                    <tbody>
                        @php($i = 1)
                        @foreach($products as $prd)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$prd->supplier}}</td>
                            <td>{{$prd->prod_id}}</td>
                            <td>{{$prd->prod_id_new}}</td>
                            <td>{{$prd->name}}</td>
                            <td>{{$prd->category}}</td>
                            <td>{{$prd->stock}}</td>
                            <td>
                                @php($stock_in = DB::table('tblreceivedet')->where('prod_id', $prd->prod_id)->sum('qty'))
                                {{ $stock_in }}
                            </td>
                            <td>
                                @php($stock_out = DB::table('tblproducttrxdet')->where('prod_id', $prd->prod_id)->sum('qty'))
                                {{ $stock_out }}
                            </td>
                            <td>
                                @php($sisa=$stock_in - $stock_out + $prd->stock)
                                {{ $sisa }}
                            </td>
                            <td><a href="{{ route('product.edit', ['id' => $prd->id]) }}" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5">Edit</a>
                                <form class="" action="{{ route('product.destroy', ['id' => $prd->id]) }}" method="post">
                                    {{ csrf_field() }}
                                    {{ method_field('delete') }}
                                    <button type="submit" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5">Hapus </button></a>
                                </form>
                            </td>
                        </tr>
                        @php($i++)
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- end row -->
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

    <!-- Magnific popup -->
    <script type="text/javascript" src="{{ asset('assets/plugins/magnific-popup/dist/jquery.magnific-popup.min.js') }}"></script>

    {{-- Fingerprint --}}
    <script src="{{ asset('assets/fingerprint/jquery.timer.js') }}"></script>
    <script src="{{ asset('assets/fingerprint/ajaxmask.js') }}"></script>
@endsection

@section('script-js')

<script type="text/javascript">

    $(document).ready(function () {
        // Responsive Datatable
        $('#responsive-datatable').DataTable();

        $('.image-popup').magnificPopup({
            type: 'image',
        });

    });

</script>
@endsection
