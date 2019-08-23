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
Form Purchase Payment
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
                            <th>Harga Distributor</th>
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
                                    <td>Rp. {{number_format($item->price_dist)}}</td>
                                    <td>{{$item->qty}}</td>
                                    <td>Rp. {{number_format($item->price_dist*$item->qty)}}</td>
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
                                    <input type="text" class="form-control" parsley-trigger="change" value="{{$purchase->id}}" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Posting Period</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" value="{{date("F", mktime(0, 0, 0, $item['month'], 10))}} {{$item['year']}}" readonly>
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
                                    <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($ttl_order)}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h4 class="m-t-0 header-title">Supplier Information</h4>
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Supplier Name</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" value="{{$purchase->supplier()->first()->nama}}" readonly>
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
                                    <input type="text" class="form-control" parsley-trigger="change" value="@if($purchase->status == 1)Lunas @else() Belum Lunas @endif" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <form class="form-horizontal" role="form" action="{{ route('purchaseStore') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <input type="hidden" name="trx_id" value="{{$purchase->id}}">
                        <h4 class="m-t-0 header-title">Insert Payment</h4>
                        <div class="col-12">
                            <div class="p-20">
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Total Transaction to Paid</label>
                                    <div class="col-10">
                                        <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($ttl_order-$ttl_pay)}}" readonly>
                                        <input type="hidden" name="paid" value="{{$ttl_order-$ttl_pay}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Payment Date</label>
                                    <div class="col-10">
                                        <div class="input-group">
                                            <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="payment_date" id="payment_date"  data-date-format='yyyy-mm-dd' autocomplete="off" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="ti-calendar"></i></span>
                                            </div>
                                        </div><!-- input-group -->
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Payment Method</label>
                                    <div class="col-10">
                                        <select class="form-control select2" parsley-trigger="change" name="payment_method" id="payment_method" required>
                                            <option value="#" disabled>Pilih Method</option>
                                            @foreach ($coas as $coa)
                                                <option value="{{$coa->AccNo}}">{{$coa->AccName}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="saldo"></div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Payment Amount</label>
                                    <div class="col-10">
                                        <input type="number" min="0" class="form-control" parsley-trigger="change" id="payment_amount" name="payment_amount" value="{{$ttl_order-$ttl_pay}}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Payment Deduction</label>
                                    <div class="col-10">
                                        <select class="form-control select2" parsley-trigger="change" name="payment_deduction" id="payment_deduction" required>
                                            <option value="No_Deduction">No Deduction</option>
                                            <option value="Biaya_Transfer_Bank">Biaya Transfer Bank</option>
                                        </select>
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
                                            <input type="text" class="form-control" parsley-trigger="change" placeholder="yyyy/mm/dd" name="next_due_date" id="next_due_date"  data-date-format='yyyy-mm-dd' autocomplete="off">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="ti-calendar"></i></span>
                                            </div>
                                        </div><!-- input-group -->
                                    </div>
                                </div>
                                @if ($purchase->status == 0)
                                <div class="form-group text-right m-b-0">
                                    <button class="btn btn-danger btn-rounded w-md waves-effect waves-light m-b-5">Simpan Purchase Order</a>
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
                                    <td></td>
                                    <td><a href="javascript:;" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5" onclick="deletePayment({{$pay->id}})">Delete</a></td>
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

    $(".select2").select2();

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
                url : "{{route('purchasePayDestroy')}}",
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
</script>
@endsection