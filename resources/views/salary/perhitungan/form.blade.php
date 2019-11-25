@extends('layout.main')

@section('css')
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('judul')
Tambah Data Gaji Pokok Pegawai
@endsection

@section('content')

    <form class="form-horizontal" role="form" action="{{ route('storePerhitunganGaji') }}" enctype="multipart/form-data" method="POST">

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
                                <label class="col-2 col-form-label">Periode Gaji</label>
                                <div class="col-5">
                                    <select class="form-control select2" parsley-trigger="change" name="bulan" id="bulan" onchange="getBV()" required>
                                        <option value="#" selected disabled>Pilih Bulan</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            @if ($i == date('m'))
                                                <option value="{{$i}}" selected>{{date("F", mktime(0, 0, 0, $i, 10))}}</option>
                                            @else
                                                <option value="{{$i}}">{{date("F", mktime(0, 0, 0, $i, 10))}}</option>
                                            @endif
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-5">
                                    <select class="form-control select2" parsley-trigger="change" name="tahun" id="tahun" onchange="getBV()" required>
                                        <option value="#" selected disabled>Pilih Tahun</option>
                                        @for ($i = 2018; $i <= date('Y'); $i++)
                                            @if ($i == date('Y'))
                                                <option value="{{$i}}" selected>{{$i}}</option>
                                            @else
                                                <option value="{{$i}}">{{$i}}</option>
                                            @endif
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Keterangan</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="keterangan" id="keterangan">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Total BV</label>
                                <div class="col-10">
                                    <input type="number" class="form-control" parsley-trigger="change" required name="bv" id="bv" value="{{$bv}}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Jumlah Hari Kerja</label>
                                <div class="col-10">
                                    <input type="number" class="form-control" parsley-trigger="change" required name="hari_kerja" id="hari_kerja">
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

        function getBV() {
            bulan = $('#bulan').val();
            tahun = $('#tahun').val();

            $.ajax({
                url : "{{route('createPerhitunganGaji')}}",
                type : "get",
                dataType: 'json',
                data:{
                    bulan: bulan,
                    tahun: tahun,
                },
            }).done(function (data) {
                console.log(data);
                $('#bv').val(data);
            }).fail(function (msg) {
                alert('Gagal menampilkan data, silahkan refresh halaman.');
            });
        }
    </script>
@endsection