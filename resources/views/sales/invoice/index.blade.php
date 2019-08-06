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
Index Invoice
@endsection

@section('content')
    <div class="container">
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

                    <h4 class="m-t-0 header-title">Choose Customer</h4>
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Pilih Customer</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="customer" id="customer">
                                        <option value="all" selected>All</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{$customer->id}}">{{$customer->apname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4 class="m-t-0 header-title">Choose Product</h4>
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Pilih Product</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="product" id="product">
                                        <option value="all" selected>All</option>
                                        @foreach ($products as $product)
                                            <option value="{{$product->prod_id}}">{{$product->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-left m-b-0">
                        <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="showData()">Show Data</a>
                    </div>
                </div>

                <div id="invoice-list" style="display:none">
                    <section id="showinvoice">
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
@endsection

@section('script-js')
<script>
    // Date Picker
    jQuery('#trx_start').datepicker({
        todayHighlight: true,
    });
    jQuery('#trx_end').datepicker({
        todayHighlight: true,
    });

    $(".select2").select2();

    function showData(){
        start = $('#trx_start').val();
        end = $('#trx_end').val();
        customer = $('#customer').val();
        product = $('#product').val();

        $.ajax({
            url : "{{route('invoiceView')}}",
            type : "get",
            dataType: 'json',
            data:{
                start: start,
                end: end,
                customer: customer,
                product: product,
            },
        }).done(function (data) {
            document.getElementById("invoice-list").style.display = 'block';
            $('#showinvoice').html(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection