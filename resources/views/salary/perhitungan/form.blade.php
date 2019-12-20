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
                                    <select class="form-control select2" parsley-trigger="change" name="bulan" id="bulan" onchange="getBV('bv')" required>
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
                                    <select class="form-control select2" parsley-trigger="change" name="tahun" id="tahun" onchange="getBV('bv')" required>
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
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Divisi Bonus Pegawai</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="employee" id="employee" onchange="getBV('bonus',this.value)">
                                        <option value="#" disabled selected>Pilih Pegawai</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{$employee->id}}" data-image="{{asset('assets/images/employee/foto/'.$employee->scanfoto)}}">{{$employee->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Bonus Divisi</h4>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>Nama Pegawai</th>
                        <th>Role</th>
                        <th>Nilai</th>
                        <th>Action</th>
                    </thead>
    
                    <tbody id="tbondiv">
                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- end row -->

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
            $("#employee").select2({
                templateResult: formatState,
                templateSelection: formatState
            });
        });

        function getBV(param,emp=null) {
            bulan = $('#bulan').val();
            tahun = $('#tahun').val();

            $.ajax({
                url : "{{route('createPerhitunganGaji')}}",
                type : "get",
                dataType: 'json',
                data:{
                    bulan: bulan,
                    tahun: tahun,
                    employee: emp,
                    jenis: param,
                },
            }).done(function (data) {
                if(param == 'bv'){
                    $('#bv').val(data);
                }else{
                    html = '<tr id="tr'+data.id+'"><td>'+data.name+'</td><td>'+data.role_name+'</td><td><input type="text" name="bonus['+data.id+']" id="bonus'+data.id+'" value="0"></td><td><a href="javascript:;" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5" onclick="deletePegawai('+data.id+')">Delete Data Gaji</a></td></tr>';
                    $('#tbondiv').append(html);
                }
                
            }).fail(function (msg) {
                alert('Gagal menampilkan data, silahkan refresh halaman.');
            });
        }

        function deletePegawai(id){
            $('#tr'+id).remove();
        }

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
                '<span><img src="' + optimage + '" width="30px" /> ' + opt.text.toUpperCase() + '</span>'
                );
                return $opt;
            }
        }
    </script>
@endsection