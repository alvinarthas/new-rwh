@extends('layout.main')

@section('css')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- form Uploads -->
    <link href="{{ asset('assets/plugins/fileuploads/css/dropify.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('judul')
Tambah Data Perusahaan
@endsection

@section('content')
    @if($jenis == "create")
        <form class="form-horizontal" role="form" action="{{ route('perusahaan.store') }}" enctype="multipart/form-data" method="POST">
    @elseif($jenis == "edit")
        <form class="form-horizontal" role="form" action="{{ route('perusahaan.update',['id' => $perusahaan->id]) }}" enctype="multipart/form-data" method="POST">
            {{ method_field('PUT') }}
    @endif

    @csrf

    {{-- Perusahaan --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Data Perusahaan</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nama Perusahaan</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="nama" id="nama" value="@isset($perusahaan->nama){{$perusahaan->nama}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Alamat</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="alamat" id="alamat" value="@isset($perusahaan->alamat){{$perusahaan->alamat}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor Telepon</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="telepon" id="telepon" value="@isset($perusahaan->telp){{$perusahaan->telp}}@endisset">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Contact Person 1</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change"  name="cp1" id="cp1" value="@isset($perusahaan->cp1){{$perusahaan->cp1}}@endisset">
                                    <span class="help-block">format : 08xxxxxxxxx (NAMA CONTACT PERSON)</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Contact Person 2</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change"  name="cp2" id="cp2" value="@isset($perusahaan->cp2){{$perusahaan->cp2}}@endisset">
                                    <span class="help-block">format : 08xxxxxxxxx (NAMA CONTACT PERSON)</span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Contact Person 3</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change"  name="cp3" id="cp3" value="@isset($perusahaan->cp3){{$perusahaan->cp3}}@endisset">
                                    <span class="help-block">format : 08xxxxxxxxx (NAMA CONTACT PERSON)</span>
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
