@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Date Picker --}}
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/morris/morris.css') }}"/>
@endsection

@section('judul')
    Laporan Sales
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box">
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
                <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="showData()">Show Data</a>
            </div>
        </div>

        <div id="report-list" style="display:none">
            <section id="showreport">
            </section>
        </div>
    </div>
</div>
@endsection

@section('js')
    {{-- Date Picker --}}
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <!-- Required datatable js -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <!--Morris Chart-->
    <script src="{{ asset('assets/plugins/morris/morris.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/raphael/raphael-min.js') }}"></script>

    <!--Number divider-->
    <script src="{{ asset('assets/plugins/number-divider/number-divider.min.js') }}"></script>
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

    function showData(){
        start = $('#start_date').val();
        end = $('#end_date').val();

        $.ajax({
            url         :   "{{route('salesReport')}}",
            data        :   {
                start : start,
                end : end,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                document.getElementById("report-list").style.display = 'block';
                $('#showreport').html(data);
            },
            error       :   function(data){
                alert('Gagal menampilkan data, silahkan refresh halaman.');
            }
        });
    }
    </script>
@endsection