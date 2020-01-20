@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Date Picker --}}
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    {{-- Switchery --}}
    <link href="{{ asset('assets/plugins/switchery/switchery.min.css') }}" rel="stylesheet" />
@endsection

@section('judul')
Index Laporan Keuangan
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Choose Date</h4>
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

                <div class="form-group text-left m-b-0">
                    <a href="javascript:;" class="btn btn-primary btn-rounded waves-effect waves-light w-md m-b-5" onclick="showData()">Show Data</a>
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
{{-- Switchery --}}
<script src="{{ asset('assets/plugins/switchery/switchery.min.js') }}"></script>
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

    function showData(param=null){
        start_date = $('#start_date').val();
        end_date = $('#end_date').val();

        $.ajax({
            url : "{{route('financeReport')}}",
            type : "get",
            dataType: 'json',
            data:{
                start_date: start_date,
                end_date: end_date,
            },
        }).done(function (data) {
            document.getElementById("jurnal-list").style.display = 'block';
            $('#showjurnal').html(data);
            var switch1 = document.querySelector('#switch1');
            var switch2 = document.querySelector('#switch2');
            var switch3 = document.querySelector('#switch3');
            new Switchery(switch1, {size: 'small'});
            new Switchery(switch2, {size: 'small'});
            new Switchery(switch3, {size: 'small'});
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection