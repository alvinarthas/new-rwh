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
    <!-- Sweet Alert css -->
    <link href="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('judul')
Index
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                @if($jenis == "customer")
                    <h4 class="m-t-0 header-title">Daftar Customer</h4>
                    <p class="text-muted font-14 m-b-30">
                        @if (array_search("MDCSC",$page))
                            <a href="{{ route('customer.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Customer</a>
                        @endif
                    </p>
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Customer ID</th>
                            <th>Nama Customer</th>
                            <th>Tanggal Lahir</th>
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
                                <td><a href="javascript:;" class="btn btn-primary btn-trans waves-effect w-md waves-danger m-b-5" onclick="getDescribe('{{ $cus['id'] }}')" disabled="disabled">{{$cus->apname}}</td>
                                <td>{{$cus->apbirthdate}}</td>
                                <td>{{$cus->apphone}}</td>
                                <td>{{$cus->cicn}}</td>
                                <td>{{$cus->ciphone}}</td>
                                <td>
                                    @if (array_search("MDCSU",$page))
                                        <a href="{{route('customer.edit',['id'=>$cus->id])}}" class="btn btn-custom waves-effect waves-light w-md">Update</a>
                                    @endif
                                    @if (array_search("MDCSD",$page))
                                        <a href="javascript:;" type="button" class="btn btn-danger waves-effect waves-danger w-md" onclick="deleteCustomer({{ $cus->id}})" >Delete</a>
                                    @endif
                                </td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                @elseif($jenis == "pricebyproduct")
                    <h4 class="m-t-0 header-title">Update Price & BV by Product</h4>
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
                                    <a href="{{route('managePriceByProduct',['id'=>$p->pid])}}" class="btn btn-warning btn-rounded waves-effect waves-light w-75 m-b-5">Update Price & BV</a>
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
                            @if (array_search("PSDCC",$page))
                                <a href="{{ route('saldo.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Deposit</a>
                            @endif
                        </p>
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Customer Name</th>
                            <th>Saldo</th>
                            <th>Option</th>
                        </thead>
                        <tbody>
                            @php($i=1)
                            @foreach ($data as $key)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$key['name']}}</td>
                                    <td>Rp {{number_format($key['saldo'],2,",",".")}}</td>
                                    <td>
                                        @if (array_search("PSDCS",$page))
                                            <a href="javascript:;" onclick="getDetail({{$key['id']}}, `{{$key['name']}}`)" class="btn btn-custom btn-trans waves-effect w-md waves-danger m-b-5">Detail</a>
                                        @endif
                                    </td>
                                </tr>
                            @php($i++)
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <!--  Modal content for the above example -->
    <div class="modal fade bs-example-modal-lg" id="modalLarge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg" id="do-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Detail</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
                </div>
                <div class="modal-body" id="modalView">
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('js')
<!-- Sweet Alert Js  -->
<script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>

<!-- Required datatable js -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

<!-- Sweet Alert Js  -->
<script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('script-js')
<script type="text/javascript">

    $(document).ready(function () {
        // Responsive Datatable
        $('#responsive-datatable').DataTable();
    });
    function getDetail(id, name){
        $.ajax({
            url : "{{route('saldo.show',['id'=>1])}}",
            type : "get",
            dataType: 'json',
            data:{
                customer_id:id,
                name:name,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modalLarge').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function deleteCustomer(id){
        var token = $("meta[name='csrf-token']").attr("content");
        console.log(id);

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
                url: "customer/"+id,
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

    function getDescribe(id){

        $.ajax({
            url : '{{route('customer.show',['id'=>1])}}',
            type : "get",
            dataType: 'json',
            data:{
                id:id,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modalLarge').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection
