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

    @isset($deposit)
        <form class="form-horizontal" role="form" action="{{ route('deposit.update', ['id' => $deposit->id]) }}" enctype="multipart/form-data" method="POST">
            {{ method_field('PUT') }}
    @else
        <form class="form-horizontal" role="form" action="{{ route('deposit.store') }}" enctype="multipart/form-data" method="POST">
    @endif
    @csrf

    {{-- Informasi Pribadi --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                @isset($deposit)
                    <h4 class="m-t-0 header-title">Edit Deposit Pembelian {{ $deposit->jurnal_id }}</h4>
                @else
                    <h4 class="m-t-0 header-title">Tambah Deposit Pembelian</h4>
                @endif
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
                                        @isset($deposit->supplier_id)
                                            @foreach ($suppliers as $supplier)
                                                @if($supplier->id == $deposit->supplier_id)
                                                    <option value="{{$supplier->id}}" selected>{{$supplier->nama}}</option>
                                                @else
                                                    <option value="{{$supplier->id}}">{{$supplier->nama}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{$supplier->id}}">{{$supplier->nama}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Jumlah</label>
                                <div class="col-10">
                                    <input type="number" class="form-control" parsley-trigger="change" required name="amount" id="amount" value="@isset($deposit->amount){{ $deposit->amount }}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Metode Deposit</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="method" required>
                                        <option value="#" disabled selected>Pilih Method</option>
                                        @isset($deposit->AccNo)
                                            @foreach ($coas as $coa)
                                                @if($deposit->AccNo == $coa->AccNo)
                                                    <option value="{{$coa->AccNo}}" selected>{{$coa->AccName}}</option>
                                                @else
                                                    <option value="{{$coa->AccNo}}">{{$coa->AccName}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            @foreach ($coas as $coa)
                                                <option value="{{$coa->AccNo}}">{{$coa->AccName}}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Keterangan</label>
                                <div class="col-10">
                                    <textarea class="form-control" rows="5" id="keterangan" name="keterangan">@isset($deposit->keterangan){{ $deposit->keterangan }}@endisset</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Tanggal</label>
                                <div class="col-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="tanggal" id="tanggal" data-date-format='yyyy-mm-dd' autocomplete="off" value="@isset($deposit->date){{ $deposit->date }}@endisset">
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
