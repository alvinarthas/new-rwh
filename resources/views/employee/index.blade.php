@extends('layout.main')
@php
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
                <h4 class="m-t-0 header-title">Index Employee</h4>
                <p class="text-muted font-14 m-b-30">
                    <a href="{{ route('createEmployee') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Pegawai</a>
                </p>

                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>NIP</th>
                        <th>Mulai Kerja</th>
                        <th>Alamat</th>
                        <th>No Hp</th>
                        <th>Action</th>
                        <th>Tempat Lahir</th>
                        <th>Tanggal Lahir</th>
                        <th>KTP</th>
                        <th>SIM A</th>
                        <th>SIM B</th>
                        <th>SIM C</th>
                        <th>NPWP</th>
                        <th>BPJS</th>
                    </thead>

                    <tbody>
                        @php($i = 1)
                        @foreach($employee as $emp)
                        <tr>
                            <td>{{$i}}</td>
                            <td>
                                <a href="{{ asset('assets/images/employee/foto/'.$emp->scanfoto) }}" class="image-popup" title="{{$emp->name}}">
                                    <img src="{{ asset('assets/images/employee/foto/'.$emp->scanfoto) }}"  alt="user-img" title="Mat Helme" class="rounded-circle img-thumbnail img-responsive photo">
                                </a>
                            </td>
                            <td>{{$emp->name}}</td>
                            <td>{{$emp->username}}</td>
                            <td>{{$emp->nip}}</td>
                            <td>{{$emp->start_work}}</td>
                            <td>{{$emp->address}}</td>
                            <td>{{$emp->phone}}</td>
                            <td>
                                <a href="{{route('editEmployee',['id'=>$emp->id])}}" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5">Edit</a>
                                <a href="" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5">Hapus</a>
                            </td>
                            <td>{{$emp->tmpt_lhr}}</td>
                            <td>{{$emp->tgl_lhr}}</td>
                            <td>
                                <a href="{{ asset('assets/images/employee/ktp/'.$emp->scanktp) }}" class="image-popup" title="KTP : {{$emp->ktp}}">
                                    <img src="{{ asset('assets/images/employee/ktp/'.$emp->scanktp) }}"  alt="user-img" title="Mat Helme" class="rounded-circle img-thumbnail img-responsive photo">
                                </a>
                            </td>
                            <td>
                                <a href="{{ asset('assets/images/employee/sima/'.$emp->scansima) }}" class="image-popup" title="SIM A: {{$emp->sima}}">
                                    <img src="{{ asset('assets/images/employee/sima/'.$emp->scansima) }}"  alt="user-img" title="Mat Helme" class="rounded-circle img-thumbnail img-responsive photo">
                                </a>
                            </td>
                            <td>
                                <a href="{{ asset('assets/images/employee/simb/'.$emp->scansimb) }}" class="image-popup" title="SIM B1: {{$emp->simb1}}">
                                    <img src="{{ asset('assets/images/employee/simb/'.$emp->scansimb) }}"  alt="user-img" title="Mat Helme" class="rounded-circle img-thumbnail img-responsive photo">
                                </a>
                            </td>
                            <td>
                                <a href="{{ asset('assets/images/employee/simc/'.$emp->scansimc) }}" class="image-popup" title="SIM C: {{$emp->simc}}">
                                    <img src="{{ asset('assets/images/employee/simc/'.$emp->scansimc) }}"  alt="user-img" title="Mat Helme" class="rounded-circle img-thumbnail img-responsive photo">
                                </a>
                            </td>
                            <td>
                                <a href="{{ asset('assets/images/employee/npwp/'.$emp->scannpwp) }}" class="image-popup" title="NPWP: {{$emp->npwp}}">
                                    <img src="{{ asset('assets/images/employee/npwp/'.$emp->scannpwp) }}"  alt="user-img" title="Mat Helme" class="rounded-circle img-thumbnail img-responsive photo">
                                </a>
                            </td>
                            <td>
                                <a href="{{ asset('assets/images/employee/bpjs/'.$emp->scanbpjs) }}" class="image-popup" title="BPJS: {{$emp->bpjs}}">
                                    <img src="{{ asset('assets/images/employee/bpjs/'.$emp->scanbpjs) }}"  alt="user-img" title="Mat Helme" class="rounded-circle img-thumbnail img-responsive photo">
                                </a>
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