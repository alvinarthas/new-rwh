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
@endsection

@section('judul')
Index Sales Payment
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Choose Transaction Date</h4>
                <div class="col-12">
                    <div class="p-20">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Start Date</label>
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="start_trx" id="start_trx"  data-date-format='yyyy-mm-dd' autocomplete="off">
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
                                    <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="end_trx" id="end_trx"  data-date-format='yyyy-mm-dd' autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="ti-calendar"></i></span>
                                    </div>
                                </div><!-- input-group -->
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="m-t-0 header-title">Choose Payment Date</h4>
                <div class="col-12">
                    <div class="p-20">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Start Date</label>
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="start_pay" id="start_pay"  data-date-format='yyyy-mm-dd' autocomplete="off">
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
                                    <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="end_pay" id="end_pay"  data-date-format='yyyy-mm-dd' autocomplete="off">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="ti-calendar"></i></span>
                                    </div>
                                </div><!-- input-group -->
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="m-t-0 header-title">Choose Method & Customer</h4>
                <div class="col-12">
                    <div class="p-20">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Pilih Method</label>
                            <div class="col-10">
                                <select class="form-control select2" parsley-trigger="change" name="method" id="method" onchange="getCustomer(this.value)">
                                    <option value="#" disabled selected>Pilih Method</option>
                                    <option value="*">Semua</option>
                                    <option value="0">Offline</option>
                                    @if (array_search("PSSPVO",$page))
                                        @foreach ($ecoms as $ecom)
                                            <option value="{{$ecom->id}}">{{$ecom->nama}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Pilih Customer</label>
                            <div class="col-10">
                                <select class="form-control select2" parsley-trigger="change" name="customer" id="customer">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group text-left m-b-0">
                    <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="showData()">Show Data</a>
                    <a href="javascript:;" class="btn btn-purple btn-rounded waves-effect waves-light w-md m-b-5" onclick="showData('all')">Show All</a>
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
@endsection

@section('script-js')
<script>
    // Date Picker
    jQuery('#end_pay').datepicker({
        todayHighlight: true,
        autoclose: true
    });
    jQuery('#start_pay').datepicker({
        todayHighlight: true,
        autoclose: true
    });
    jQuery('#start_trx').datepicker({
        todayHighlight: true,
        autoclose: true
    });
    jQuery('#end_trx').datepicker({
        todayHighlight: true,
        autoclose: true
    });

    $(".select2").select2();

    function showData(param=null){
        start_trx = $('#start_trx').val();
        end_trx = $('#end_trx').val();
        start_pay = $('#start_pay').val();
        end_pay = $('#end_pay').val();
        customer = $('#customer').val();
        method = $('#method').val();

        $.ajax({
            url : "{{route('salesView')}}",
            type : "get",
            dataType: 'json',
            data:{
                start_trx: start_trx,
                end_trx: end_trx,
                start_pay: start_pay,
                end_pay: end_pay,
                customer: customer,
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

    function getCustomer(params) {
        $.ajax({
            url : "{{route('customerSales')}}",
            type : "get",
            dataType: 'json',
            data:{
                method: params,
            },
        }).done(function (data) {
            appen = '<option value="all">Semua</option>';
            $('#customer').html(appen);
            $('#customer').append(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection
