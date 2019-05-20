@extends('layout.main')
@php
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
    {{-- Fingerprint --}}
    <link href="{{ asset('assets/fingerprint/ajaxmask.css') }}" rel="stylesheet">
@endsection

@section('judul')
Registrasi Fingerprint
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <h4 class="m-t-0 header-title">Daftar User</h4>

            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <th>No</th>
                    <th>Username</th>
                    <th>Action</th>
                </thead>

                <tbody>
                    @php($i = 1)
                    @foreach($users as $user)
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$user->user_name}}</td>
                        <?php
                            $url_register		= base64_encode(route('fingerRegister',['user_id'=>$user->user_id]));
                            $finger = DemoFinger::where('user_id',$user->user_id)->count();
                        ?>
                        <code style="display: none;" id="user_finger_{{$user->user_id}}">{{$finger}}</code>
                        <td>
                            <div id="fingerprint{{$user->user_id}}">
                            @if ($finger == 0)
                                <a href="finspot:FingerspotReg;<?=$url_register?>" class="btn btn-purple btn-rounded waves-effect waves-light w-md m-b-5" onclick="user_register({{$user->user_id}},'{{$user->user_name}}')">Daftar Fingerprint</a>
                            @else
                                <a class="btn btn-purple btn-rounded waves-effect waves-light w-md m-b-5">Fingerprint sudah terdaftar</a>
                            @endif
                            </div>
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

    {{-- Fingerprint --}}
    <script src="{{ asset('assets/fingerprint/jquery.timer.js') }}"></script>
    <script src="{{ asset('assets/fingerprint/ajaxmask.js') }}"></script>
@endsection

@section('script-js')
<script type="text/javascript">

    $(document).ready(function () {

        // Responsive Datatable
        $('#responsive-datatable').DataTable();
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
        var timeout = 10000;
        
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
                    toastr.error("'"+user_name+"' registration fail!", 'Error!!!')
                    $('body').ajaxMask({ stop: true });
                }						
                if (regStats==1)
                {
                    $("#user_finger_"+user_id).html(regCt);
                    toastr.success("'"+user_name+"' registration success!", 'Success')
                    $('body').ajaxMask({ stop: true });
                    var linkText = '<a class="btn btn-secondary btn-rounded waves-effect waves-light w-md m-b-5">Fingerprint sudah terdaftar</a>';
                    $('#fingerprint'+user_id).empty().append(linkText);
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