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
                    @if (array_search("MDPDC",$page))
                    <a href="{{ route('product.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Product</a>
                    @endif
                    <a href="{{ route('manageproduct') }}" class="btn btn-purple btn-rounded w-md waves-effect waves-light m-b-5">Manage Harga</a>
                </p>

                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                    <thead>
                        <th style="width:5%">No</th>
                        <th>Supplier</th>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Product Brand</th>
                        <th>Harga Distributor</th>
                        <th>Harga Modal</th>
                        <th>Actions</th>
                    </thead>

                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach($products as $prd)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$prd->supplier()->first()->nama}}</td>
                            <td>{{$prd->prod_id}}</td>
                            {{-- <td>{{$prd->prod_id_new}}</td> --}}
                            <td>{{$prd->name}}</td>
                            <td>{{$prd->category}}</td>
                            @php
                                if(($prd->harga_distributor == null) OR ($prd->harga_distributor == "")){
                                    $harga_dis = 0;
                                }else{
                                    $harga_dis = $prd->harga_distributor;
                                }

                                if(($prd->harga_modal == null) OR ($prd->harga_modal == "")){
                                    $harga_mod = 0;
                                }else{
                                    $harga_mod = $prd->harga_modal;
                                }
                            @endphp
                            <td>{{$harga_dis}}</td>
                            <td>{{$harga_mod}}</td>
                            <td>
                                @if (array_search("MDPDU",$page))
                                <a href="{{ route('product.edit', ['id' => $prd->id]) }}" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5">Edit</a>
                                @endif
                                @if (array_search("MDPDD",$page))
                                <form class="" action="{{ route('product.destroy', ['id' => $prd->id]) }}" method="post">
                                    {{ csrf_field() }}
                                    {{ method_field('delete') }}
                                    <button type="submit" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5">Hapus </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @php
                            $i++;
                        @endphp
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
