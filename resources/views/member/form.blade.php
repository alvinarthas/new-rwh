@extends('layout.main')
@php
    use App\DataKota;
@endphp
@section('css')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- form Uploads -->
    <link href="{{ asset('assets/plugins/fileuploads/css/dropify.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('judul')
Tambah Data Member
@endsection

@section('content')
    @if($jenis == "create")
        <form class="form-horizontal" role="form" action="{{ route('member.store') }}" enctype="multipart/form-data" method="POST">
    @elseif($jenis == "edit")
        <form class="form-horizontal" role="form" action="{{ route('member.update',['id' => $member->id]) }}" enctype="multipart/form-data" method="POST">
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
                                    <input type="text" class="form-control" parsley-trigger="change" required name="nama" id="nama" value="@isset($member->nama){{$member->nama}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor KTP</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="ktp" id="ktp" value="@isset($member->ktp){{$member->ktp}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nama Ibu Kandung</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="ibu" id="ibu" value="@isset($member->ibu){{$member->ibu}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Alamat</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="alamat" id="alamat" value="@isset($member->alamat){{$member->alamat}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Telepon</label>
                                <div class="col-10">
                                    <input type="number" class="form-control" parsley-trigger="change" required name="telp" id="telp" value="@isset($member->telp){{$member->telp}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Tempat Lahir</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" name="tempat_lahir" parsley-trigger="change" required id="tempat_lahir" value="@isset($member->tempat_lahir){{$member->tempat_lahir}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Tanggal Lahir</label>
                                <div class="col-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="tanggal_lahir" id="tanggal_lahir"  value="@isset($member->tanggal_lahir){{$member->tanggal_lahir}}@endisset"  data-date-format='yyyy-mm-dd'>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="ti-calendar"></i></span>
                                        </div>
                                    </div><!-- input-group -->
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Provinsi</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="prov" id="prov" onchange="getKota(this.value)">
                                        <option value="#" disabled selected>Pilih Provinsi</option>
                                        @foreach ($provinsi as $prov)
                                            @isset($member->prov)
                                                @if ($prov->kode_pusdatin_prov == $member->prov)
                                                    <option value="{{$prov->kode_pusdatin_prov}}" selected>{{$prov->provinsi}}</option>
                                                @else
                                                    <option value="{{$prov->kode_pusdatin_prov}}">{{$prov->provinsi}}</option>
                                                @endif
                                            @else
                                                <option value="{{$prov->kode_pusdatin_prov}}">{{$prov->provinsi}}</option>
                                            @endisset
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Kab/Kota</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="city" id="city">
                                        <option value="#" disabled selected>Pilih Kab/Kota</option>
                                            @isset($member->city)
                                                @php($city = DataKota::getCity($member->city))
                                                <option value="{{$prov->kode_pusdatin_kota}}">{{$modul->kab_kota}}</option>
                                            @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Koordinator</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="koordinator" id="koordinator">
                                        <option value="#" disabled selected>Pilih Koordinator</option>
                                        @foreach ($koordinator as $koor)
                                            @isset($member->koor)
                                                @if ($koordinator->id == $member->koordinator)
                                                    <option value="{{$koordinator->id}}" selected>{{$koordinator->nama}}</option>
                                                @else
                                                    <option value="{{$koordinator->id}}">{{$koordinator->nama}}</option>
                                                @endif
                                            @else
                                                <option value="{{$koordinator->id}}">{{$koordinator->nama}}</option>
                                            @endisset
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Sub Koordinator</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="koordinator" id="koordinator">
                                        <option value="#" disabled selected>Pilih Sub Koordinator</option>
                                        @foreach ($subkoor as $sub)
                                            @isset($member->subkoor)
                                                @if ($sub->id == $member->subkoor)
                                                    <option value="{{$sub->id}}" selected>{{$sub->nama}}</option>
                                                @else
                                                    <option value="{{$sub->id}}">{{$sub->nama}}</option>
                                                @endif
                                            @else
                                                <option value="{{$sub->id}}">{{$sub->nama}}</option>
                                            @endisset
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Upload Foto KTP</label>
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
        function getKota(id){
            $.ajax({
                url : "{{route('getDataKota')}}",
                type : "get",
                dataType: 'html',
                data:{
                    prov: id,
                },
            }).done(function (data) {
                $('#city').html(data);
            }).fail(function (msg) {
                alert('Gagal menampilkan data, silahkan refresh halaman.');
            });
        }
        // Date Picker
        jQuery('#tanggal_lahir').datepicker();
        jQuery('#mulai_kerja').datepicker();

        // Select2
        $(".select2").select2({
        });
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