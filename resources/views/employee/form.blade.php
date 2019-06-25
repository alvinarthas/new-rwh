@extends('layout.main')

@section('css')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- form Uploads -->
    <link href="{{ asset('assets/plugins/fileuploads/css/dropify.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('judul')
Tambah Data Pegawai
@endsection

@section('content')
    @if($jenis == "create")
        <form class="form-horizontal" role="form" action="{{ route('employee.store') }}" enctype="multipart/form-data" method="POST">
    @elseif($jenis == "edit")
        <form class="form-horizontal" role="form" action="{{ route('employee.update',['id' => $employee->id]) }}" enctype="multipart/form-data" method="POST">
            {{ method_field('PUT') }}
    @endif

    @csrf
    {{-- Informasi Pribadi --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Informasi Pribadi</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nama Lengkap</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" requiredq name="nama" id="nama" value="@isset($employee->name){{$employee->name}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor Induk Pegawai</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" requiredq name="nip" id="nip" value="@isset($employee->nip){{$employee->nip}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Alamat</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" requiredq name="alamat" id="alamat" value="@isset($employee->address){{$employee->address}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Telepon</label>
                                <div class="col-10">
                                    <input type="number" class="form-control" parsley-trigger="change" requiredq name="telepon" id="telepon" value="@isset($employee->phone){{$employee->phone}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">E-mail</label>
                                <div class="col-10">
                                    <input type="email" class="form-control" parsley-trigger="change" requiredq name="email" id="email" value="@isset($employee->email){{$employee->email}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Tempat Lahir</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" name="tempat_lahir" parsley-trigger="change" requiredq id="tempat_lahir" value="@isset($employee->tmpt_lhr){{$employee->tmpt_lhr}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Tanggal Lahir</label>
                                <div class="col-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" parsley-trigger="change" requiredq placeholder="yyyy/mm/dd" name="tanggal_lahir" id="tanggal_lahir"  value="@isset($employee->tgl_lhr){{$employee->tgl_lhr}}@endisset"  data-date-format='yyyy-mm-dd'>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="ti-calendar"></i></span>
                                        </div>
                                    </div><!-- input-group -->
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Mulai Bekerja</label>
                                <div class="col-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" parsley-trigger="change" requiredq placeholder="yyyy/mm/dd" name="mulai_kerja" id="mulai_kerja"  value="@isset($employee->mulai_kerja){{$employee->mulai_kerja}}@endisset" data-date-format='yyyy-mm-dd'>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="ti-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Upload Foto Personal</label>
                                <div class="col-10">
                                    <input type="file" class="dropify" data-height="100" name="scanfoto" id="scanfoto" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Account --}}
    @if($jenis=="create")
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Informasi Akun</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Username</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" name="username" id="username" parsley-trigger="change" requiredq>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Password</label>
                                <div class="col-10">
                                    <input type="password" class="form-control" name="password" id="password" parsley-trigger="change" requiredq>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Informasi Tabungan --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Informasi Tabungan</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Bank</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="bank">
                                        <option value="#" disabled selected>Pilih Bank</option>
                                        @foreach ($banks as $bank)
                                            <option value="{{$bank->kode}}" data-image="{{asset('assets/images/bank/'.$bank->icon)}}">{{$bank->nama}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor Rekening</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" name="rekening" id="rekening" parsley-trigger="change" value="@isset($employee->norek){{$employee->norek}}@endisset">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi KTP --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Informasi KTP</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor KTP</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" name="ktp" id="ktp" value="@isset($employee->ktp){{$employee->ktp}}@endisset" parsley-trigger="change" requiredq>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Upload KTP</label>
                                <div class="col-10">
                                    <input type="file" class="dropify" data-height="100" name="scanktp" id="scanktp" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Informasi SIM A --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Informasi SIM A</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor KTP</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" name="sima" id="sima" value="@isset($employee->sima){{$employee->sima}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Upload KTP</label>
                                <div class="col-10">
                                    <input type="file" class="dropify" data-height="100" name="scansima" id="scansima" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi SIM C --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Informasi SIM C</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor SIM C</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" name="simc" id="simc" value="@isset($employee->simc){{$employee->simc}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Upload KTP</label>
                                <div class="col-10">
                                    <input type="file" class="dropify" data-height="100" name="scansimc" id="scansimc"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi SIM B1 --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Informasi SIM B1</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor SIM B1</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" name="simb" id="simb" value="@isset($employee->simb){{$employee->simb}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Upload KTP</label>
                                <div class="col-10">
                                    <input type="file" class="dropify" data-height="100" name="scansimb" id="scansimb" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi NPWP --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Informasi NPWP</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor NPWP</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" name="npwp" id="npwp" value="@isset($employee->npwp){{$employee->npwp}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Upload KTP</label>
                                <div class="col-10">
                                    <input type="file" class="dropify" data-height="100" name="scannpwp" id="scannpwp" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi BPJS --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Informasi BPJS</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor BPJS</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" name="bpjs" id="bpjs" value="@isset($employee->bpjs){{$employee->bpjs}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Upload KTP</label>
                                <div class="col-10">
                                    <input type="file" class="dropify" data-height="100" name="scanbpjs" id="scanbpjs" />
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
<!-- Plugin -->
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<!-- file uploads js -->
<script src="{{ asset('assets/plugins/fileuploads/js/dropify.min.js') }}"></script>
<!-- Validation js (Parsleyjs) -->
<script type="text/javascript" src="{{ asset('assets/plugins/parsleyjs/dist/parsley.min.js') }}"></script>
@endsection

@section('script-js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('form').parsley();
        });
    </script>

    <script>
        // Date Picker
        jQuery('#tanggal_lahir').datepicker();
        jQuery('#mulai_kerja').datepicker();

        // Select2
        $(".select2").select2({
            templateResult: formatState,
            templateSelection: formatState
        });

        function formatState (opt) {
            if (!opt.id) {
                return opt.text.toUpperCase();
            } 

            var optimage = $(opt.element).attr('data-image'); 
            console.log(optimage)
            if(!optimage){
            return opt.text.toUpperCase();
            } else {                    
                var $opt = $(
                '<span><img src="' + optimage + '" width="60px" /> ' + opt.text.toUpperCase() + '</span>'
                );
                return $opt;
            }
        };

    </script>

    <script type="text/javascript">
        $('.dropify').dropify({
            messages: {
                'default': 'Drag and drop a file here or click',
                'replace': 'Drag and drop or click to replace',
                'remove': 'Remove',
                'error': 'Ooops, something wrong appended.'
            },
            error: {
                'fileSize': 'The file size is too big (1M max).'
            }
        });
    </script>
@endsection