@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Select2 --}}
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Date Picker --}}
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <style>
        input {
            width: 100%;
            box-sizing: border-box;
        }
    </style>
@endsection

@section('judul')
Form Sales Order
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card-box">
                    <h4 class="m-t-0 header-title">Customer Detail</h4>
                    <div class="row">
                        <div class="col-12">
                            <div class="p-20">
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Pilih Customer</label>
                                    <div class="col-10">
                                        <select class="form-control select2" parsley-trigger="change" name="customer" id="customer">
                                            <option value="#" selected disabled>Pilih Customer</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{$customer->id}}">{{$customer->apname}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-left m-b-0">
                        <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="chooseCustomer()">Pilih Customer</a>
                    </div>
                </div>

                <div id="sales-list" style="display:none">
                    <section id="showsales">
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
@endsection

@section('script-js')
<script>
    // Select2
    $(".select2").select2();

    function chooseCustomer(){
        customer = $('#customer').val();

        $.ajax({
            url : "{{route('showSales')}}",
            type : "get",
            dataType: 'json',
            data:{
                customer: customer,
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