@extends('layout.main')
@php
    use App\Customer;
    use App\Coa;
    use App\Employee;
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
                        @if (array_search("CRCSC",$page))
                            <a href="{{ route('customer.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Customer</a>
                        @endif
                        @if(array_search("CRCSP",$page))
                            <a href="{{ route('pricebycustomer') }}" class="btn btn-purple btn-rounded w-md waves-effect waves-light m-b-5">Manage Price & BV by Customer</a>
                        @endif
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
                            @php
                                $i = 1;
                            @endphp
                            @foreach($customers as $cus)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$cus->cid}}</td>
                                <td>{{$cus->apname}}</td>
                                <td>{{$cus->apphone}}</td>
                                <td>{{$cus->cicn}}</td>
                                <td>{{$cus->ciphone}}</td>
                                <td>
                                    @if (array_search("CRCSU",$page))
                                        <a href="{{route('customer.edit',['id'=>$cus->id])}}" class="btn btn-custom btn-rounded waves-effect waves-light w-75 m-b-5">Update Data</a>
                                    @endif
                                    @if (array_search("CRCSG",$page))
                                        <a href="{{route('customer.pricebv',['id'=>$cus->id])}}" class="btn btn-warning btn-rounded waves-effect waves-light w-75 m-b-5">Update Price & BV</a>
                                    @endif
                                </td>
                            </tr>
                            @php
                                $i++;
                            @endphp
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
                            @php
                                $i = 1;
                            @endphp
                            @foreach($products as $p)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$p->namasupplier}}</td>
                                <td>{{$p->prod_id}}</td>
                                <td>{{$p->name}}</td>
                                <td>{{$p->category}}</td>
                                <td>
                                    @if (array_search("CRCSR",$page))
                                        <a href="{{route('managepricebycustomer',['id'=>$p->pid])}}" class="btn btn-warning btn-rounded waves-effect waves-light w-75 m-b-5">Update Price & BV</a>
                                    @endif
                                </td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                @elseif($jenis=="topup")
                    <h4 class="m-t-0 header-title">Saldo Customer</h4>
                    <p class="text-muted font-14 m-b-30">
                        @if (array_search("CRTPC",$page))
                            <a href="{{ route('saldo.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Customer</a>
                        @endif
                    </p>
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th>Rekening Penerima</th>
                            <th>Nominal</th>
                            <th>Keterangan</th>
                            <th>Creator</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach($saldo as $s)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$s->apname}}</td>
                                <td>{{$s->tanggal}}</td>
                                @php
                                    $coa = Coa::where('AccNo', $s->accNo)->select('AccName')->first();
                                    $creator = Employee::where('id', $s->creator)->select('name')->first();
                                    $i++;
                                @endphp
                                <td>{{$coa['AccName']}}</td>
                                <td>Rp. <span class="divide">{{$s->amount}}</span></td>
                                <td>{{$s->keterangan}}</td>
                                <td>{{$creator['name']}}</td>
                                <td>
                                    @if (array_search("CRTPU",$page))
                                        <a href="{{route('saldo.edit',['id'=>$s->sid])}}" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5">Update</a>
                                    @endif
                                    @if (array_search("CRTPD",$page))
                                        <form class="" action="{{ route('saldo.destroy', ['id' => $s->sid]) }}" method="post">
                                            {{ csrf_field() }}
                                            {{ method_field('delete') }}
                                            <button type="submit" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5">Hapus </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
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

    <!-- number-divider -->
    <script src="{{ asset('assets/plugins/number-divider/number-divider.min.js') }}"></script>
@endsection

@section('script-js')
<script type="text/javascript">

    $(document).ready(function () {
        // Responsive Datatable
        $('#responsive-datatable').DataTable();

        $('.image-popup').magnificPopup({
            type: 'image',
        });

        $(".divide").divide();
    });
</script>
@endsection
