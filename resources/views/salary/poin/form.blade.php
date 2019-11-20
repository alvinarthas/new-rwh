@extends('layout.main')

@section('css')
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
{{-- Date Picker --}}
<link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection

@section('judul')
Tambah Poin Pegawai
@endsection

@section('content')

    <form class="form-horizontal" role="form" action="{{ route('storePoin') }}" enctype="multipart/form-data" method="POST">
    @csrf
    {{-- Informasi Pribadi --}}
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card-box">
                    <h4 class="m-t-0 header-title">Form Poin Pegawai</h4>
                    <p class="text-muted m-b-30 font-14">
                    </p>

                    <div class="row">
                        <div class="col-12">
                            <div class="p-20">
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Pegawai</label>
                                    <div class="col-10">
                                        <select class="form-control select2" parsley-trigger="change" name="employee">
                                            <option value="#" disabled selected>Pilih Pegawai</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{$employee->id}}" data-image="{{asset('assets/images/employee/foto/'.$employee->scanfoto)}}">{{$employee->username}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Tanggal</label>
                                    <div class="col-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="date" id="date"  data-date-format='yyyy-mm-dd' autocomplete="off" data-date-end-date="0d">
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
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Poin</label>
                                    <div class="col-10">
                                        <input type="number" step="0.01" class="form-control" parsley-trigger="change" required name="poin" id="poin" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group text-right m-b-0">
            <button class="btn btn-primary waves-effect waves-light" type="submit">
                Submit
            </button>
        </div>
    </div>

</form>
@endsection

@section('js')
<!-- Validation js (Parsleyjs) -->
<script type="text/javascript" src="{{ asset('assets/plugins/parsleyjs/dist/parsley.min.js') }}"></script>
{{-- Select2 --}}
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

{{-- Date Picker --}}
<script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection

@section('script-js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('form').parsley();
            // Select2
            $(".select2").select2({
                templateResult: formatState,
                templateSelection: formatState
            });

            // Date Picker
            jQuery('#date').datepicker({
                todayHighlight: true,
                autoclose: true,
            });
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
    </script>
@endsection