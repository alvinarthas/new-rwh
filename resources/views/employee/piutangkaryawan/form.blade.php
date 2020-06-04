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

    @isset($piutang)
        <form class="form-horizontal" role="form" action="{{ route('piutang.update', ['id' => $piutang->id]) }}" enctype="multipart/form-data" method="POST">
            {{ method_field('PUT') }}
    @else
        <form class="form-horizontal" role="form" action="{{ route('piutang.store') }}" enctype="multipart/form-data" method="POST">
    @endif
    @csrf

    {{-- Informasi Pribadi --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                @isset($piutang)
                    <h4 class="m-t-0 header-title">Edit Piutang Karyawan {{ $piutang->id_jurnal }}</h4>
                @else
                    <h4 class="m-t-0 header-title">Tambah Piutang Karyawan</h4>
                @endif
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            @isset($piutang)
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">ID Jurnal</label>
                                    <div class="col-10">
                                        <input type="text" class="form-control" parsley-trigger="change" required name="jurnal_id" id="jurnal_id" value="@isset($piutang->id_jurnal){{ $piutang->id_jurnal }}@endisset" disabled>
                                    </div>
                                </div>
                            @endisset
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nama Karyawan</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="employee" required>
                                        <option value="#" disabled selected>Pilih Karyawan</option>
                                        @isset($piutang->employee_id)
                                            @foreach ($employees as $employee)
                                                @if($employee->id == $piutang->employee_id)
                                                    <option value="{{$employee->id}}" selected>{{$employee->name}}</option>
                                                @else
                                                    <option value="{{$employee->id}}">{{$employee->name}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            @foreach ($employees as $employee)
                                                <option value="{{$employee->id}}">{{$employee->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Jumlah</label>
                                <div class="col-10">
                                    <input type="number" class="form-control" parsley-trigger="change" required name="amount" id="amount" value="@isset($piutang->amount){{ $piutang->amount }}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Status Piutang</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="status" required>
                                        @isset($piutang->status)
                                            <option value="#" disabled>Pilih Status Piutang</option>
                                            @if($piutang->status == 0)
                                                <option value="0" selected>Uang Keluar</option>
                                                <option value="1">Uang Masuk</option>
                                            @elseif($piutang->status == 1)
                                                <option value="0">Uang Keluar</option>
                                                <option value="1" selected>Uang Masuk</option>
                                            @endif
                                        @else
                                            <option value="#" disabled selected>Pilih Status Piutang</option>
                                            <option value="0">Uang Keluar</option>
                                            <option value="1">Uang Masuk</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Kas / Bank</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="method" required>
                                        @isset($piutang->AccNo)
                                            <option value="#" disabled>Pilih COA</option>
                                            @foreach ($coas as $coa)
                                                @if($piutang->AccNo == $coa->AccNo)
                                                    <option value="{{$coa->AccNo}}" selected>{{$coa->AccName}}</option>
                                                @else
                                                    <option value="{{$coa->AccNo}}">{{$coa->AccName}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="#" disabled selected>Pilih COA</option>
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
                                    <textarea class="form-control" rows="5" id="keterangan" name="keterangan">@isset($piutang->description){{ $piutang->description }}@endisset</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Tanggal</label>
                                <div class="col-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="tanggal" id="tanggal" data-date-format='yyyy-mm-dd' autocomplete="off" value="@isset($piutang->date){{ $piutang->date }}@endisset">
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
