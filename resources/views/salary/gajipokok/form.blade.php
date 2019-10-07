@extends('layout.main')

@section('css')
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('judul')
Tambah Data Gaji Pokok Pegawai
@endsection

@section('content')

    @if($jenis == "create")
        <form class="form-horizontal" role="form" action="{{ route('storeGajiEmp',['jenis'=>'store']) }}" enctype="multipart/form-data" method="POST">
    @elseif($jenis == "edit")
        <form class="form-horizontal" role="form" action="{{ route('storeGajiEmp',['jenis'=>'update','id' => $employee->employee_id]) }}" enctype="multipart/form-data" method="POST">
    @endif

    @csrf
    {{-- Informasi Pribadi --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Gaji Pokok</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Pegawai</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="employee">
                                        <option value="#" disabled selected>Pilih Pegawai</option>
                                        @foreach ($employees as $employee2)
                                            @isset($employee->employee_id)
                                                @if ($employee2->id == $employee->employee_id)
                                                    <option value="{{$employee2->id}}" selected>{{$employee2->username}}</option>
                                                @else
                                                    <option value="{{$employee2->id}}" >{{$employee2->username}}</option>
                                                @endif
                                            @else
                                                <option value="{{$employee2->id}}" >{{$employee2->username}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Gaji Pokok Pegawai</label>
                                <div class="col-10">
                                    <input type="number" class="form-control" parsley-trigger="change" required name="gaji_pokok" id="gaji_pokok" value="@isset($employee->gaji_pokok){{$employee->gaji_pokok}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Tunjangan Jabatan</label>
                                <div class="col-10">
                                    <input type="number" class="form-control" parsley-trigger="change" required name="tunjangan_jabatan" id="tunjangan_jabatan" value="@isset($employee->tunjangan_jabatan){{$employee->tunjangan_jabatan}}@endisset">
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
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
@endsection

@section('script-js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('form').parsley();
            // Select2
            $(".select2").select2();
        });
    </script>
@endsection