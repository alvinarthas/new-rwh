@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Date Picker --}}
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('judul')
List Receive Product
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
                                        <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="start" id="start"  data-date-format='yyyy-mm-dd' autocomplete="off">
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
                                        <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="end" id="end"  data-date-format='yyyy-mm-dd' autocomplete="off">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="ti-calendar"></i></span>
                                        </div>
                                    </div><!-- input-group -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group text-left m-b-0">
                    <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="choosePurchase()">Show Data</a>
                    <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="choosePurchase('all')">Show ALL Data</a>
                </div>
            </div>

            <div id="receive-list" style="display:none">
                <section id="showreceive">
                </section>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- Required datatable js -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

{{-- Date Picker --}}
<script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection

@section('script-js')
<script>
    // Date Picker
    jQuery('#start').datepicker();
    jQuery('#end').datepicker();

    function choosePurchase(id=null){
        start = $('#start').val();
        end = $('#end').val();
        
        $.ajax({
            url : "{{route('receiveProdAjx')}}",
            type : "get",
            dataType: 'json',
            data:{
                start: start,
                end: end,
                jenis: id,
            },
        }).done(function (data) {
            document.getElementById("receive-list").style.display = 'block';
            $('#showreceive').html(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection