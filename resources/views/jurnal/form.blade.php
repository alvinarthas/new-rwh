@extends('layout.main')

@section('css')
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
                @if($jenis == "create")
                <form class="form-horizontal" role="form" action="{{ route('jurnal.store') }}" enctype="multipart/form-data" method="POST">
                @elseif($jenis == "edit")
                <form class="form-horizontal" role="form" action="{{ route('jurnal.update',['id' => $id]) }}" enctype="multipart/form-data" method="POST">
                {{ method_field('PUT') }}
                @endif
                @csrf
                    <div class="card-box">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Pilih Chart of Account</label>
                            <div class="col-10">
                                <select class="form-control select2" parsley-trigger="change" name="coa" id="coa" required>
                                    <option value="#" disabled>Pilih Coa</option>
                                    @foreach ($coas as $coa)
                                        <option value="{{$coa->AccNo}}">{{$coa->AccName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Position</label>
                            <div class="col-10">
                                <select class="form-control select2" parsley-trigger="change" name="position" id="position" required>
                                    <option value="Debet">Debet</option>
                                    <option value="Credit">Credit</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Amount</label>
                            <div class="col-10">
                                <input type="number" class="form-control" parsley-trigger="change" required name="amount" id="amount" value="0">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Catatan</label>
                            <div class="col-10">
                                <textarea class="form-control" rows="5" id="notes" name="notes"></textarea>
                            </div>
                        </div>
                        <div class="form-group text-right m-b-0">
                            <a href="javascript:;" class="btn btn-danger btn-rounded w-md waves-effect waves-light m-b-5" onclick="addAccount()">Add Account</a>
                        </div>
                    </div>
                    <div class="card-box">
                        <h4 class="m-t-0 header-title">Jurnal Entry Details</h4>
                        <div class="col-12">
                            <div class="p-20">
                                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <th>No</th>
                                        <th>Account Number</th>
                                        <th>Account Name</th>
                                        <th>Position</th>
                                        <th>Amount</th>
                                        <th>Notes</th>
                                        <th>Option</th>
                                    </thead>
                                    <tbody id="jurnal-list-body">
                                        <input type="hidden" name="count" id="count" value="@isset($count_edit){{$count_edit}}@endisset">
                                        @if ($jenis == "edit")
                                            @php($i=1)
                                            @foreach ($jurnals as $item)
                                                <tr style="width:100%" id="trow{{$i}}">
                                                    <td>{{$i}}</td>
                                                    <td>{{$item->AccNo}}</td>
                                                    <td>{{$item->coa->AccName}}</td>
                                                    <td><input type="hidden" value="{{$item->AccPos}}" id="position{{$i}}">{{$item->AccPos}}</td>
                                                    <td><input type="hidden" value="{{$item->Amount}}" id="amount{{$i}}">Rp. {{number_format($item->Amount)}}</td>
                                                    <td>{{$item->notes_item}}</td>
                                                    <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItemJurnal({{$i}},{{$item->id}})" >Delete</a></td>
                                                </tr>
                                            @php($i++)
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-box">
                        @if ($jenis == "edit")
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Jurnal ID</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" value="{{$id}}" readonly>
                                </div>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Total Debet</label>
                            <div class="col-10">
                                <input type="number" class="form-control" name="ttl_debet" id="ttl_debet" parsley-trigger="change" value="@isset($ttl_debet){{$ttl_debet}}@else{{0}}@endisset">
                            </div>
                        </div>
                        <div class="form-group row">
                                <label class="col-2 col-form-label">Total Credit</label>
                                <div class="col-10">
                                    <input type="number" class="form-control" name="ttl_credit" id="ttl_credit" parsley-trigger="change" value="@isset($ttl_credit){{$ttl_credit}}@else{{0}}@endisset">
                                </div>
                            </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Transaction Date</label>
                            <div class="col-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="trx_date" id="trx_date"  data-date-format='yyyy-mm-dd' autocomplete="off" value="@isset($jurnals[0]->date){{$jurnals[0]->date}}@endisset">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="ti-calendar"></i></span>
                                    </div>
                                </div><!-- input-group -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Description</label>
                            <div class="col-10">
                                <textarea class="form-control" rows="5" id="deskripsi" name="deskripsi">@isset($jurnals[0]->description){{$jurnals[0]->description}}@endisset</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-right m-b-0">
                        <button class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Simpan Jurnal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
{{-- Select2 --}}
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

{{-- Date Picker --}}
<script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<!-- Sweet Alert Js  -->
<script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('script-js')
<script>
    // Date Picker
    jQuery('#trx_date').datepicker({
        todayHighlight: true,
        autoclose: true
    });

    function addAccount(){
        coa = $('#coa').val();
        position = $('#position').val();
        amount = $('#amount').val();
        notes = $('#notes').val();
        count = $('#count').val();

        $.ajax({
            url : "{{route('addJurnal')}}",
            type : "get",
            dataType: 'json',
            data:{
                coa: coa,
                position: position,
                amount: amount,
                notes: notes,
                count:count,
            },
        }).done(function (data) {
            $('#jurnal-list-body').append(data.append);
            $('#count').val(data.count);
            resetall();
            changeTotalHarga(data.ttl_debet,data.ttl_credit);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function resetall(){
        $('#coa').val("#").change();
        $('#position').val("Debet").change();
        $('#amount').val("0");
        $('#notes').val("");
    }

    function changeTotalHarga(ttl_debet_add,ttl_credit_add){
        ttl_debet = parseInt($('#ttl_debet').val());
        ttl_credit = parseInt($('#ttl_credit').val());
        $('#ttl_debet').val(ttl_debet+parseInt(ttl_debet_add))
        $('#ttl_credit').val(ttl_credit+parseInt(ttl_credit_add))
    }

    function deleteItem(id){
        count = parseInt($('#count').val()) - 1;
        decreaseTotalHarga(id);
        $('#trow'+id).remove();
        $('#count').val(count);
    }

    function decreaseTotalHarga(id){
        amount = parseInt($('#amount'+id).val());
        position = $('#position'+id).val();

        if(position == "Debet"){
            ttl_debet = parseInt($('#ttl_debet').val());
            $('#ttl_debet').val(ttl_debet-amount);
        }else{
            ttl_credit = parseInt($('#ttl_credit').val());
            $('#ttl_credit').val(ttl_credit-amount);
        }
    }

    function deleteItemJurnal(i,id){
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
                url : "{{route('detailJuralDestroy')}}",
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
                deleteItem(i);
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
                console.log("eh ga kehapus");
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