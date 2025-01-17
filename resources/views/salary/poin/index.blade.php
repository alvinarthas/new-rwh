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
Daftar Poin Pegawai
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card-box">
                    <h4 class="m-t-0 header-title">Pilih Tanggal Periode Poin</h4>
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
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Pilih Jenis Poin</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="jenis" id="jenis">
                                        <option value="#" selected disabled>Pilih Jenis Poin</option>
                                        <option value="1">Share Internal</option>
                                        <option value="0">Share Logistik</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-left m-b-0">
                        <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="showData()">Show Data</a>
                        @if (array_search("EMEPC",$page))
                            <a href="{{ route('formPoin') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Poin</a>
                        @endif
                    </div>
                </div>

                <div id="poin-list" style="display:none">
                    <section id="showpoin">
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
{{-- Select2 --}}
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

{{-- Date Picker --}}
<script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

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

    function showData(){
        start_date = $('#start_date').val();
        end_date = $('#end_date').val();
        jenis = $('#jenis').val();

        $.ajax({
            url : "{{route('indexPoin')}}",
            type : "get",
            dataType: 'json',
            data:{
                start_date: start_date,
                end_date: end_date,
                jenis: jenis,
            },
        }).done(function (data) {
            document.getElementById("poin-list").style.display = 'block';
            $('#showpoin').html(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection