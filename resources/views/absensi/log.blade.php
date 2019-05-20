@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    {{-- Datepicker --}}
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection

@section('judul')
    Log Lengkap Kehadiran
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
                            <label class="col-2 col-form-label">Tanggal Awal</label>
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" parsley-trigger="change" placeholder="yyyy/mm/dd" name="tanggal_awal" id="tanggal_awal" data-date-format='yyyy-mm-dd'>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="ti-calendar"></i></span>
                                    </div>
                                </div><!-- input-group -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Tanggal Awal</label>
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" parsley-trigger="change" placeholder="yyyy/mm/dd" name="tanggal_akhir" id="tanggal_akhir" data-date-format='yyyy-mm-dd'>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="ti-calendar"></i></span>
                                    </div>
                                </div><!-- input-group -->
                            </div>
                        </div>
                    </div>
                </div>
                <a href="javaScript:void(0);" class="btn btn-primary btn-rounded waves-effect waves-light w-md m-b-5" onclick="showLog()">Lihat Log</a>
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

    {{-- Datepicker --}}
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection

@section('script-js')
    <script>
        // Date Picker
        jQuery('#tanggal_awal').datepicker();
        jQuery('#tanggal_akhir').datepicker();

        function showLog(id){
            var tanggal_awal = $("#tanggal_awal").val();
            var tanggal_akhir = $("#tanggal_akhir").val();

            $.ajax({
                url         :   "{{route('fingerAjxFullLog')}}",
                data        :   {
                    tanggal_awal : tanggal_awal,
                    tanggal_akhir: tanggal_akhir,
                },
                type		:	"GET",
                dataType    :   "html",
                success		:	function(data){
                    $("#logTabel").html(data);
                    $('#responsive-datatable').DataTable();
                }
            });
        }
    </script>
@endsection