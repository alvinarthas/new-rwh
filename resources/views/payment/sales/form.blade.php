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
@endsection

@section('judul')
Form Sales Payment
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">Transaction Item Details</h4>
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Product Price</th>
                            <th>Buy Qty</th>
                            <th>Sub Total</th>
                            <th>Jurnal</th>
                        </thead>
                        <tbody>
                            @php($i=1)
                            @foreach ($details as $item)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$item->prod_id}}</td>
                                    <td>{{$item->product->name}}</td>
                                    <td>Rp. {{number_format($item->price)}}</td>
                                    <td>{{$item->qty}}</td>
                                    <td>Rp. {{number_format($item->sub_ttl)}}</td>
                                    <td></td>
                                </tr>
                            @php($i++)
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-box">
                    <h4 class="m-t-0 header-title">Transaction Information</h4>
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Transaction ID</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" value="{{$sales->id}}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Transaction Date</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" value="{{$sales->trx_date}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="m-t-0 header-title">Total Transaction</h4>
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Total Transaction Amount</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($sales->ongkir+$sales->ttl_harga)}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="m-t-0 header-title">Customer Information</h4>
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Customer Name</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" value="{{$sales->customer->apname}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="m-t-0 header-title">Payment Status</h4>
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Payment Status</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" value="@if($sales->status == 1)Lunas @else() Belum Lunas @endif" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-box">
                    <form class="form-horizontal" role="form" action="{{ route('salesStore') }}" enctype="multipart/form-data" method="POST" id="form">
                        @csrf
                        <input type="hidden" name="trx_id" value="{{$sales->id}}">
                        <h4 class="m-t-0 header-title">Insert Payment</h4>
                        <div class="col-12">
                            <div class="p-20">
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Total Transaction to Paid</label>
                                    <div class="col-10">
                                        <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format(($sales->ongkir+$sales->ttl_harga)-$ttl_pay)}}" readonly>
                                        <input type="hidden" name="paid" value="{{($sales->ongkir+$sales->ttl_harga)-$ttl_pay}}">
                                        <input type="hidden" id="customer_info" value="{{$sales->customer_id}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Payment Date</label>
                                    <div class="col-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="payment_date" id="payment_date"  data-date-format='yyyy-mm-dd' autocomplete="off">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="ti-calendar"></i></span>
                                            </div>
                                        </div><!-- input-group -->
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Payment Method</label>
                                    <div class="col-10">
                                        <select class="form-control select2" parsley-trigger="change" name="payment_method" id="payment_method" onchange="ifSaldo(this.value)" required>
                                            <option value="#" disabled>Pilih Method</option>
                                            @foreach ($coas as $coa)
                                                <option value="{{$coa->AccNo}}">{{$coa->AccName}}</option>
                                            @endforeach
                                            <option value="saldo">Saldo</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="saldo" style="display:none">
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label">Current Saldo</label>
                                        <div class="col-10">
                                            <input type="text" class="form-control" parsley-trigger="change" id="current_saldo" name="current_saldo" value="0" readonly>
                                            <input type="hidden" id="current_saldoraw" name="current_saldoraw" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Payment Amount</label>
                                    <div class="col-10">
                                        <input type="text" class="form-control" parsley-trigger="change" id="payment_amount" name="payment_amount" value="{{($sales->ongkir+$sales->ttl_harga)-$ttl_pay}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Payment Deduction</label>
                                    <div class="col-10">
                                        <select class="form-control select2" parsley-trigger="change" name="payment_deduction" id="payment_deduction" required onchange="deduction(this.value)">
                                            <option value="No_Deduction">No Deduction</option>
                                            <option value="Biaya_Transfer_Bank">Biaya Transfer Bank</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="deduct" style="display:none">
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label">Deduction Amount</label>
                                        <div class="col-10">
                                            <input type="text" class="form-control" parsley-trigger="change" id="deduct_amount" name="deduct_amount" value="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Payment Description</label>
                                    <div class="col-10">
                                        <textarea class="form-control" rows="5" id="payment_description" name="payment_description"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Next Due Date</label>
                                    <div class="col-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="next_due_date" id="next_due_date"  data-date-format='yyyy-mm-dd' autocomplete="off">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="ti-calendar"></i></span>
                                            </div>
                                        </div><!-- input-group -->
                                    </div>
                                </div>
                                @if ($sales->status == 0)
                                <div class="form-group text-right m-b-0">
                                    <button onsubmit="checkPay()" class="btn btn-danger btn-rounded w-md waves-effect waves-light m-b-5">Simpan Sales Order</a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">Payment History Details</h4>
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Payment Date</th>
                            <th>Payment Amount</th>
                            <th>Payment Method</th>
                            <th>Payment Description</th>
                            <th>Deduction Category</th>
                            <th>Next Due Date</th>
                            <th>Jurnal</th>
                            <th>Option</th>
                        </thead>
                        <tbody>
                            @php($i=1)
                            @foreach ($payment as $pay)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$pay->payment_date}}</td>
                                    <td>Rp. {{number_format($pay->payment_amount)}}</td>
                                    <td>{{$pay->payment->AccName}}</td>
                                    <td>{{$pay->payment_desc}}</td>
                                    <td>{{$pay->deduct_category}}</td>
                                    <td>{{$pay->due_date}}</td>
                                    <td>{{$pay->jurnal_id}}</td>
                                    <td>
                                            @if (array_search("PSSPD",$page))
                                            <a href="javascript:;" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5" onclick="deletePayment({{$pay->id}})">Delete</a>
                                            @endif
                                        </td>
                                </tr>
                            @php($i++)
                            @endforeach
                        </tbody>
                    </table>
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
    jQuery('#payment_date').datepicker({
        todayHighlight: true,
        autoclose: true
    });
    jQuery('#next_due_date').datepicker({
        todayHighlight: true,
        autoclose: true
    });

    function ifSaldo(id){
        customer = $('#customer_info').val();
        if(id == "saldo"){
            $.ajax({
                url : "{{route('checkSaldo')}}",
                type : "get",
                dataType: 'json',
                data:{
                    customer: customer,
                },
            }).done(function (data) {
                document.getElementById("saldo").style.display = 'block';
                $('#current_saldo').val(formatNumber(data));
                $('#current_saldoraw').val(data);
            }).fail(function (msg) {
                alert('Gagal menampilkan data, silahkan refresh halaman.');
            });
        }
    }

    function formatNumber(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    }

    function deduction(id) {
        if(id == "Biaya_Transfer_Bank"){
            document.getElementById("deduct").style.display='block';
        }else{
            document.getElementById("deduct").style.display='none';
        }
    }

    function deletePayment(id){
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger m-l-10',
            buttonsStyling: false
        }).then(function () {
            $.ajax({
                url : "{{route('salesPayDestroy')}}",
                type : "get",
                dataType: 'json',
                data:{
                    id: id,
                },
            }).done(function (data) {
                swal(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                )
                location.reload();
            }).fail(function (msg) {
                swal(
                    'Failed',
                    'Your imaginary file is safe :)',
                    'error'
                )
            });
            
        }, function (dismiss) {
            // dismiss can be 'cancel', 'overlay',
            // 'close', and 'timer'
            if (dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your imaginary file is safe :)',
                    'error'
                )
            }
        })
    }

    function checkPay(){
        console.log("check");
        input = $('#payment_amount').val();
        method = $('#payment_method').val();
        saldo = $('#current_saldoraw').val();

        if(method == "saldo" && input>saldo){
            toastr.error("Saldo anda tidak mencukupi", 'Error!!!')
            event.preventDefault();
        }else{
            document.getElementById("form").submit();
        }
    }

    $("form").submit(function(){
        input = parseInt($('#payment_amount').val());
        method = $('#payment_method').val();
        saldo = parseInt($('#current_saldoraw').val());
        if(method == "saldo" && input>saldo){
            toastr.error("Saldo anda tidak mencukupi", 'Error!!!')
            event.preventDefault();
        }
    });

</script>
@endsection