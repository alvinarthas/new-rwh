@extends('layout.main')

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
    {{-- Date Picker --}}
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    {{-- Fingerprint --}}
    <link href="{{ asset('assets/fingerprint/ajaxmask.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('judul')
Index Sales Order
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Start Date</label>
                                <div class="col-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="trx_start" id="trx_start"  data-date-format='yyyy-mm-dd' autocomplete="off">
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
                                        <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="trx_end" id="trx_end"  data-date-format='yyyy-mm-dd' autocomplete="off">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="ti-calendar"></i></span>
                                        </div>
                                    </div><!-- input-group -->
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Pilih Method</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="method" id="method">
                                        <option value="#" selected disabled>Pilih Method</option>
                                        @if (array_search("PSSLV",$page))
                                        <option value="0">Offline</option>
                                        @endif
                                        @if (array_search("PSSLVO",$page))
                                        <option value="1">Online</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group text-left m-b-0">
                    @if (array_search("PSSLC",$page) || array_search("PSSLCO",$page))
                    <a href="{{route('sales.create')}}" class="btn btn-success btn-rounded waves-effect waves-light w-md m-b-5">Add Sales</a>
                    @endif
                    <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="chooseSales()">Show Data</a>
                    <a href="javascript:;" class="btn btn-purple btn-rounded waves-effect waves-light w-md m-b-5" onclick="chooseSales('all')">Show All</a>
                    <input type="hidden" id="choose" value="">
                </div>
            </div>

            <div id="sales-list" style="display:none">
                <section id="showsales">
                </section>
            </div>
        </div>
    </div>
@endsection

@section('js')
{{-- Date Picker --}}
<script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<!-- Sweet Alert Js  -->
<script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>

<!-- Required datatable js -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/natural.js"></script>

{{-- Fingerprint --}}
<script src="{{ asset('assets/fingerprint/jquery.timer.js') }}"></script>
<script src="{{ asset('assets/fingerprint/ajaxmask.js') }}"></script>
@endsection

@section('script-js')
<script>
    // Date Picker
    jQuery('#trx_start').datepicker();
    jQuery('#trx_end').datepicker({
        orientation: "top"
    });

    function chooseSales(param=null){
        start = $('#trx_start').val();
        end = $('#trx_end').val();
        method = $('#method').val();

        $('#choose').val(param);

        $.ajax({
            url : "{{route('showIndexSales')}}",
            type : "get",
            dataType: 'json',
            data:{
                start: start,
                end: end,
                param: param,
                method: method,
            },
        }).done(function (data) {
            document.getElementById("sales-list").style.display = 'block';
            $('#showsales').html(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection
