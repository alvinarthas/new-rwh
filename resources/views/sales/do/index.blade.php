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
Index Delivery Order
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
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Pilih Customer</label>
                    <div class="col-10">
                        <select class="form-control select2" parsley-trigger="change" name="customer" id="customer">
                            <option value="#">Pilih Customer</option>
                            @foreach($customers as $cust)
                                <option value="{{$cust->id}}">{{$cust->apname}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Choose Product Name</label>
                    <div class="col-10">
                        <select class="form-control select2" parsley-trigger="change" name="select_product" id="select_product">
                            <option value="#" selected>Pilih Product</option>
                            @foreach ($products as $product)
                                <option value="{{$product->prod_id}}">{{$product->prod_id}} - {{$product ->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group text-left m-b-0">
                    <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="showData()">Show Data</a>
                </div>
            </div>

            <div id="do-list" style="display:none">
                <section id="showdo">
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
    // Select2
    $(".select2").select2();
    // Date Picker
    jQuery('#trx_start').datepicker({
        todayHighlight: true,
    });
    jQuery('#trx_end').datepicker({
        todayHighlight: true,
    });

    function showData(){
        start = $('#trx_start').val();
        end = $('#trx_end').val();
        customer = $('#customer').val();
        prod_id = $('#select_product').val();
        console.log(start, end, customer, prod_id)

        $.ajax({
            url : "{{route('indexDo')}}",
            type : "get",
            dataType: 'json',
            data:{
                start_date: start,
                end_date: end,
                customer: customer,
                prod_id: prod_id,
            },
        }).done(function (data) {
            document.getElementById("do-list").style.display = 'block';
            $('#showdo').html(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection
