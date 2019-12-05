@extends('layout.main')

@section('css')
{{-- Select2 --}}
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
{{-- Date Picker --}}
<link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection

@section('judul')
Tambah Data Modul
@endsection

@section('content')

    <form class="form-horizontal" role="form" action="{{ route('deposit.store') }}" enctype="multipart/form-data" method="POST">
    @csrf

    {{-- Informasi Pribadi --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Tambah Deposit Pembelian</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Supplier</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="supplier" required>
                                        <option value="#" disabled selected>Pilih Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{$supplier->id}}">{{$supplier->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Jumlah</label>
                                <div class="col-10">
                                    <input type="number" class="form-control" parsley-trigger="change" required name="amount" id="amount" value="0">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Metode Deposit</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="method" required>
                                        <option value="#" disabled>Pilih Method</option>
                                        @foreach ($coas as $coa)
                                            <option value="{{$coa->AccNo}}">{{$coa->AccName}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Keterangan</label>
                                <div class="col-10">
                                    <textarea class="form-control" rows="5" id="keterangan" name="keterangan"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Tanggal</label>
                                <div class="col-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="tanggal" id="tanggal"  data-date-format='yyyy-mm-dd' autocomplete="off">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="ti-calendar"></i></span>
                                        </div>
                                    </div><!-- input-group -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group text-right m-b-0">
        <button class="btn btn-primary waves-effect waves-light" type="submit">
            Submit
        </button>
    </div>

</form>
@endsection

@section('js')
<!-- Validation js (Parsleyjs) -->
<script type="text/javascript" src="{{ asset('assets/plugins/parsleyjs/dist/parsley.min.js') }}"></script>
{{-- Select2 --}}
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
{{-- Date Picker --}}
<script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection

@section('script-js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('form').parsley();
            $(".select2").select2();
            jQuery('#tanggal').datepicker({
                todayHighlight: true,
                autoclose: true
            });
        });
    </script>
@endsection