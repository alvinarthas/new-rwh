@extends('layout.main')
@php
    use App\Koordinator;
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
                <h4 class="m-t-0 header-title">Index Koordinator Member</h4>
                <p class="text-muted font-14 m-b-30">
                    <a href="{{ route('koordinator.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Koordinator Member</a>
                </p>

                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>No Telp</th>
                        <th>No KTP</th>
                        <th>Member ID</th>
                        <th>Actions</th>
                    </thead>

                    <tbody>
                        @php($i = 1)
                        @foreach($koordinator as $kor)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$kor->nama}}</td>
                            <td>{{$kor->alamat}}</td>
                            <td>{{$kor->telp}}</td>
                            <td>{{$kor->ktp}}</td>
                            <td>{{$kor->memberid}}</td>
                            <td><a href="{{ route('koordinator.edit', ['id' => $kor->id]) }}" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5">Edit</a>
                                <form class="" action="{{ route('koordinator.destroy', ['id' => $kor->id]) }}" method="post">
                                    {{ csrf_field() }}
                                    {{ method_field('delete') }}
                                    <button type="submit" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5">Hapus </button>
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
