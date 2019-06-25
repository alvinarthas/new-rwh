@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    {{-- Fingerprint --}}
    <link href="{{ asset('assets/fingerprint/ajaxmask.css') }}" rel="stylesheet">

    {{-- Select2 --}}
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('judul')
    Kehadiran
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Kehadiran</h4>
            <p class="text-muted m-b-30 font-14">
            </p>

            <div class="row">
                <div class="col-12">
                    <div class="p-20">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Daftar Anggota</label>
                            <div class="col-10">
                                <select class="form-control select2" parsley-trigger="change" name="anggota" onchange="chooseUser(this.value)">
                                    <option value="#" disabled selected>Pilih Anggota</option>
                                    @foreach ($users as $user)
                                        <option value="{{$user->user_id}}" data-image="{{asset('assets/images/employee/foto/'.$user->user->scanfoto)}}">{{$user->user_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-2 col-form-label">Keterangan</label>
                            <div class="col-10">
                                <select class="form-control select2" parsley-trigger="change" name="keterangan" id="keterangan">
                                    <option value="masuk">Masuk Kerja</option>
                                    <option value="pulang">Pulang Kerja</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="btnAbsen" style="display:none">
            </div>
        </div>
    </div>
</div>

<div class="row" id="logTabel">

</div>
@endsection

@section('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    {{-- Fingerprint --}}
    <script src="{{ asset('assets/fingerprint/ajaxmask.js') }}"></script>

    {{-- Select2 --}}
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
@endsection

@section('script-js')
    <script>
        // Select2
        $(".select2").select2({
            templateResult: formatState,
            templateSelection: formatState
        });

        function formatState (opt) {
            if (!opt.id) {
                return opt.text.toUpperCase();
            }

            var optimage = $(opt.element).attr('data-image');
            console.log(optimage)
            if(!optimage){
                return opt.text.toUpperCase();
            } else {
                var $opt = $(
                '<span><img src="' + optimage + '" width="30px" /> ' + opt.text.toUpperCase() + '</span>'
                );
                return $opt;
            }
        }

        function chooseUser(id){
            var keterangan = $("#keterangan").val();
            var base = "{!!route('fingerVerifikasi')!!}";
            var url = btoa(base+'?user_id='+id+'&keterangan='+keterangan+'');
            var button = '<a href="finspot:FingerspotVer;'+url+'" class="btn btn-purple btn-rounded waves-effect waves-light w-md m-b-5" onclick="verifikasiLog('+id+')">VERIFIKASI</a>';
            $('#btnAbsen').html(button).show();
            showLog(id);
        }

        function showLog(id){
            $.ajax({
                url         :   "{{route('fingerAjxLog')}}",
                data        :   {
                    user_id : id,
                },
                type		:	"GET",
                dataType    :   "html",
                success		:	function(data){
                    $("#logTabel").html(data);
                    // $('#responsive-datatable').DataTable();
                },
            });
        }

        function verifikasiLog(id){
            setTimeout(function(){
                $.ajax({
                    url         :   "{{route('fingerAjxLog')}}",
                    data        :   {
                        user_id : id,
                    },
                    type		:	"GET",
                    dataType    :   "html",
                    success		:	function(data){
                        $("#logTabel").html(data);
                        // $('#responsive-datatable').DataTable();
                    }
                });
            }, 10000);
        }
    </script>
@endsection
