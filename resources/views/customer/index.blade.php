@extends('layout.main')
@php
    use App\Customer;
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
                @if($jenis == "customer")
                    <h4 class="m-t-0 header-title">Daftar Customer</h4>
                    <p class="text-muted font-14 m-b-30">
                        <a href="{{ route('customer.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Customer</a>
                        <a href="{{ route('pricebycustomer') }}" class="btn btn-purple btn-rounded w-md waves-effect waves-light m-b-5">Manage Price & BV by Customer</a>
                    </p>
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Customer ID</th>
                            <th>Nama Customer</th>
                            <th>Personal Phone</th>
                            <th>Company Name</th>
                            <th>Company Phone</th>
                            <th width="200px">Action</th>
                        </thead>
                        <tbody>
                            @php($i = 1)
                            @foreach($customers as $cus)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$cus->cid}}</td>
                                <td>{{$cus->apname}}</td>
                                <td>{{$cus->apphone}}</td>
                                <td>{{$cus->cicn}}</td>
                                <td>{{$cus->ciphone}}</td>
                                <td>
                                    <a href="{{route('customer.edit',['id'=>$cus->id])}}" class="btn btn-custom btn-rounded waves-effect waves-light w-75 m-b-5">Update Data</a>
                                    <a href="{{route('customer.pricebv',['id'=>$cus->id])}}" class="btn btn-warning btn-rounded waves-effect waves-light w-75 m-b-5">Update Price & BV</a>
                                </td>
                            </tr>
                            @php($i++)
                            @endforeach
                        </tbody>
                    </table>
                @elseif($jenis == "pricebycustomer")
                    <h4 class="m-t-0 header-title">Update Price & BV by Customer</h4>
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Supplier</th>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Product Brand</th>
                            <th width="200px">Manage Customer Price</th>
                        </thead>
                        <tbody>
                            @php($i = 1)
                            @foreach($products as $p)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$p->namasupplier}}</td>
                                <td>{{$p->prod_id}}</td>
                                <td>{{$p->name}}</td>
                                <td>{{$p->category}}</td>
                                <td>
                                    <a href="{{route('managepricebycustomer',['id'=>$p->pid])}}" class="btn btn-warning btn-rounded waves-effect waves-light w-75 m-b-5">Update Price & BV</a>
                                </td>
                            </tr>
                            @php($i++)
                            @endforeach
                        </tbody>
                    </table>
                @endif
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
