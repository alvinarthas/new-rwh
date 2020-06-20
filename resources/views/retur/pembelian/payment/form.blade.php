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
    Form Retur Purchase Payment
@endsection

@section('content')
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
                        <th>Qty Retur</th>
                        <th>Sub Total</th>
                    </thead>
                    <tbody>
                        @php
                            $i=1;
                        @endphp
                        @foreach ($details as $item)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{$item->prod_id}}</td>
                                <td>{{$item->product->name}}</td>
                                <td>Rp {{number_format($item->harga_dist,2,",",".")}}</td>
                                <td>{{$item->qty}}</td>
                                <td>Rp {{number_format($item->harga_dist * $item->qty,2,",",".")}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-box">
                <h4 class="m-t-0 header-title">Transaction Information</h4>
                <div class="col-12">
                    <div class="p-20">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Retur ID</label>
                            <div class="col-10">
                                <input type="text" class="form-control" parsley-trigger="change" value="@isset($retur->id_jurnal){{$retur->id_jurnal}}@endisset" readonly>
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
                                <input type="text" class="form-control" parsley-trigger="change" value="@isset($ttl_order)Rp {{number_format($ttl_order,2,",",".")}}@endisset" readonly>
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
                                <input type="text" class="form-control" parsley-trigger="change" value="{{$retur->supplier()->first()->nama}}" readonly>
                                <input type="hidden" class="form-control" id="supplier" parsley-trigger="change" value="{{$retur->supplier}}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <h4 class="m-t-0 header-title">Retur Payment Status</h4>
                <div class="col-12">
                    <div class="p-20">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Retur Payment Status</label>
                            <div class="col-10">
                                <input type="text" class="form-control" parsley-trigger="change" value="@if($ttl_pay == $ttl_order)Selesai @else() Belum Selesai @endif" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-box">
                <form class="form-horizontal" role="form" id="form" action="{{ route('returbeli.updatepayment', ['id' => $retur->id]) }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <input type="hidden" name="trx_id" value="{{$retur->id}}">

                    <h4 class="m-t-0 header-title">Create Retur Payment</h4>
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Total Retur Transaction</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($ttl_order-$ttl_pay,2,",",".")}}" readonly>
                                    <input type="hidden" name="returned" id="returned" value="{{$ttl_order-$ttl_pay}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Retur Payment Date</label>
                                <div class="col-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="date" id="date"  data-date-format='yyyy-mm-dd' autocomplete="off" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="ti-calendar"></i></span>
                                        </div>
                                    </div><!-- input-group -->
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Retur Payment Method</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="AccNo" id="AccNo" onchange="ifSaldo(this.value)" required>
                                        <option value="#" disabled>Pilih Method</option>
                                        @foreach ($coas as $coa)
                                            <option value="{{$coa->AccNo}}">{{$coa->AccName}}</option>
                                        @endforeach
                                        <option value="1.1.3.3">Deposit Pembelian</option>
                                    </select>
                                </div>
                            </div>
                            <div id="deposit" style="display:none">
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Retur Saldo Deposit Pembelian</label>
                                    <div class="col-10">
                                        <input type="text" class="form-control" parsley-trigger="change" id="deposit_amount" value="0">
                                        <input type="hidden" class="form-control" parsley-trigger="change" id="deposit_amountraw" name="deposit_amount" value="0">
                                    </div>
                                </div>
                            </div>
                            <div id="saldo"></div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Retur Payment Amount</label>
                                <div class="col-10">
                                    <input type="number" min="0" class="form-control" parsley-trigger="change" id="amount" name="amount" value="{{$ttl_order-$ttl_pay}}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Retur Payment Deduction</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="payment_deduction" id="payment_deduction" required onchange="deduction(this.value)">
                                        <option value="No_Deduction">No Deduction</option>
                                        <option value="Biaya_Transfer_Bank">Biaya Transfer Bank</option>
                                    </select>
                                </div>
                            </div>
                            <div id="deduct" style="display:none">
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Retur Deduction Amount</label>
                                    <div class="col-10">
                                        <input type="text" class="form-control" parsley-trigger="change" id="deduct_amount" name="deduct_amount" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Retur Payment Description</label>
                                <div class="col-10">
                                    <textarea class="form-control" rows="5" id="description" name="description"></textarea>
                                </div>
                            </div>
                            @if($ttl_pay < $ttl_order)
                            <div class="form-group text-right m-b-0">
                                <button class="btn btn-danger btn-rounded w-md waves-effect waves-light m-b-5">Simpan Retur Payment</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Retur Payment History Details</h4>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Retur Payment Date</th>
                        <th>Retur Payment Amount</th>
                        <th>Payment Method</th>
                        <th>Description</th>
                        <th>Jurnal</th>
                        <th>Option</th>
                    </thead>
                    <tbody>
                        @php($i=1)
                        @foreach ($payment as $pay)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$pay->date}}</td>
                                <td>Rp {{number_format($pay->amount,2,",",".")}}</td>
                                <td>{{$pay->payment->AccName}}</td>
                                <td>{{$pay->description}}</td>
                                <td>{{$pay->id_jurnal}}</td>
                                <td>
                                    @if (array_search("RBPPD",$page))
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
    jQuery('#date').datepicker({
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
                url : "{{route('returbeli.destroypayment')}}",
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

    function deduction(id) {
        if(id == "Biaya_Transfer_Bank"){
            document.getElementById("deduct").style.display='block';
        }else{
            document.getElementById("deduct").style.display='none';
        }
    }

    function formatNumber(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
    }

    function ifSaldo(id){
        supplier = $('#supplier').val();
        if(id == "1.1.3.3"){
            $.ajax({
                url : "{{route('checkDeposit')}}",
                type : "get",
                dataType: 'json',
                data:{
                    supplier: supplier,
                },
            }).done(function (data) {
                document.getElementById("deposit").style.display = 'block';
                $('#deposit_amount').val(formatNumber(data));
                $('#deposit_amountraw').val(data);
            }).fail(function (msg) {
                alert('Gagal menampilkan data, silahkan refresh halaman.');
            });
        }else{
            document.getElementById("deposit").style.display='none';
            $('#deposit_amount').val(formatNumber(0));
            $('#deposit_amountraw').val(0);
        }
    }

    $("form").submit(function(){
        payamount = parseInt($('#payment_amount').val());
        topaid = parseInt($('#paid').val());
        method = $('#payment_method').val();
        saldo = parseInt($('#deposit_amountraw').val());

        if(payamount > topaid){
            toastr.error("Biaya yang akan dibayar melebihi jumlah yang seharusnya dibayar", 'Error!!!')
            event.preventDefault();
        }else{
            if(method == "1.1.3.3"){
                if(payamount > saldo){
                    toastr.error("Saldo anda tidak cukup untuk melakukan pembayaran", 'Error!!!')
                    event.preventDefault();
                }else{
                    document.getElementById("form").submit();
                }
            }else{
                document.getElementById("form").submit();
            }
        }
    });
</script>
@endsection
