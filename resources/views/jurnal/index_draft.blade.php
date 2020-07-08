@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Select2 --}}
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Date Picker --}}
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- Sweet Alert css -->
    <link href="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('judul')
Index Jurnal Report
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Choose Jurnal Date</h4>
                <div class="col-12">
                    <div class="p-20">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Start Date</label>
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="start_date" id="start_date"  data-date-format='yyyy-mm-dd' autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="ti-calendar"></i></span>
                                    </div>
                                </div><!-- input-group -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label">End Date</label>
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="end_date" id="end_date"  data-date-format='yyyy-mm-dd' autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="ti-calendar"></i></span>
                                    </div>
                                </div><!-- input-group -->
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="m-t-0 header-title">Choose Chart of Account</h4>
                <div class="col-12">
                    <div class="p-20">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Pilih COA</label>
                            <div class="col-10">
                                <select class="form-control select2" parsley-trigger="change" name="coa" id="coa" required>
                                    <option value="#" disabled>Pilih Method</option>
                                    <option value="all">All Coa</option>
                                    @foreach ($coas as $coa)
                                        <option value="{{$coa->AccNo}}">{{$coa->AccName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Position</label>
                            <div class="col-10">
                                <select class="form-control select2" parsley-trigger="change" name="position" id="position" required>
                                    <option value="all">Debit &amp; Credit</option>
                                    <option value="Debet">Debet</option>
                                    <option value="Credit">Credit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group text-left m-b-0">
                    @if(array_search("FIJBC",$page))
                    <a href="{{ route('jurnal.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Jurnal</a>
                    @endif
                    <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="showData('umum')">Show Jurnal Umum</a>
                    <a href="javascript:;" class="btn btn-purple btn-rounded waves-effect waves-light w-md m-b-5" onclick="showData('mutasi')">Show Mutasi Jurnal</a>
                </div>
            </div>

            <div id="jurnal-list" style="display:none">
                <section id="showjurnal">
                </section>
            </div>
        </div>
    </div>
@endsection

@section('js')
{{-- Select2 --}}
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

{{-- Date Picker --}}
<script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

<!-- Required datatable js -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/natural.js') }}"></script>
{{-- <script src="http://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script> --}}

<!-- Responsive examples -->
<script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

<!-- Sweet Alert Js  -->
<script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('script-js')
<script>
    // Date Picker
    jQuery('#start_date').datepicker({
        todayHighlight: true,
        autoclose: true
    });
    jQuery('#end_date').datepicker({
        todayHighlight: true,
        autoclose: true
    });

    $(".select2").select2();

    function showData(param){
        start_date = $('#start_date').val();
        end_date = $('#end_date').val();
        coa = $('#coa').val();
        position = $('#position').val();
        console.log(param);

        $.ajax({
            url : "{{route('jurnalDraft')}}",
            type : "get",
            dataType: 'json',
            data:{
                start_date: start_date,
                end_date: end_date,
                coa: coa,
                position: position,
                param: param,
            },
        }).done(function (data) {
            document.getElementById("jurnal-list").style.display = 'block';
            $('#showjurnal').html(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection
