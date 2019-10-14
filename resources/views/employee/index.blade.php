@extends('layout.main')
@php
    use App\Employee;
    use App\DemoFinger;
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
                <h4 class="m-t-0 header-title">Index Employee</h4>
                <p class="text-muted font-14 m-b-30">
                    @if (array_search("EMEMC",$page))
                        <a href="{{ route('employee.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Pegawai</a>
                    @endif
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
                            <?php
                                $url_register		= base64_encode(route('fingerRegister',['user_id'=>$emp->id]));
                                $finger = DemoFinger::where('user_id',$emp->id)->count();
                            ?>
                            <code style="display: none;" id="user_finger_{{$emp->id}}">{{$finger}}</code>
                            <td>
                                @if (array_search("EMEMU",$page))
                                    <a href="{{route('employee.edit',['id'=>$emp->id])}}" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5">Edit</a>
                                @endif
                                @if (array_search("EMEMD",$page))
                                    <a href="" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5">Hapus</a>
                                @endif
                                <div id="fingerprint{{$emp->id}}">
                                @if ($finger == 0)
                                    <a href="finspot:FingerspotReg;<?=$url_register?>" class="btn btn-purple btn-rounded waves-effect waves-light w-md m-b-5" onclick="user_register({{$emp->id}},'{{$emp->username}}')">Daftar Fingerprint</a>
                                @else
                                    <a class="btn btn-purple btn-rounded waves-effect waves-light w-md m-b-5">Fingerprint sudah terdaftar</a>
                                @endif
                                </div>
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
    
    function user_register(user_id,user_name) {
				
        $('body').ajaxMask();
    
        regStats = 0;
        regCt = -1;
        try
        {
            timer_register.stop();
        }
        catch(err)	
        {
            console.log('Registration timer has been init');
        }
        
        
        var limit = 4;
        var ct = 1;
        var timeout = 5000;
        
        timer_register = $.timer(timeout, function() {					
            console.log("'"+user_name+"' registration checking...");
            user_checkregister(user_id,$("#user_finger_"+user_id).html());
            if (ct>=limit || regStats==1) 
            {
                timer_register.stop();
                console.log("'"+user_name+"' registration checking end");
                console.log(regStats);
                if (ct>=limit && regStats==0)
                {
                    alert("'"+user_name+"' registration fail!");
                    $('body').ajaxMask({ stop: true });
                }						
                if (regStats==1)
                {
                    $("#user_finger_"+user_id).html(regCt);
                    alert("'"+user_name+"' registration success!");
                    $('body').ajaxMask({ stop: true });
                    var linkText = '<a class="btn btn-secondary btn-rounded waves-effect waves-light w-md m-b-5">Fingerprint sudah terdaftar</a>';
                    $('#fingerprint'+user_id).innerHTML = linkText;
                }
            }
            ct++;
        });
    }

    function user_checkregister(user_id, current) {
        $.ajax({
            url         :   "{{route('fingerCheckReg')}}",
            data        :   {
                user_id : user_id,
                current : current,
            },
            type		:	"GET",
            success		:	function(data)
                            {
                                try
                                {
                                    var res = jQuery.parseJSON(data);
                                    console.log("before: "+res.result);
                                    if (res.result)
                                    {
                                        console.log("after: "+res.result);
                                        regStats = 1;
                                        $.each(res, function(key, value){
                                            if (key=='current')
                                            {														
                                                regCt = value;
                                            }
                                        });
                                    }
                                }
                                catch(err)
                                {
                                    alert(err.message);
                                }
                            }
        });
    }
</script>
@endsection