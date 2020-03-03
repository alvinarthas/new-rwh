@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Sweet Alert css -->
    <link href="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Date Picker --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('judul')
Index Sales Order
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Periode Gaji</label>
                                <div class="col-5">
                                    <select class="form-control select2" parsley-trigger="change" name="bulan" id="bulan" required>
                                        <option value="#" selected disabled>Pilih Bulan</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{$i}}">{{date("F", mktime(0, 0, 0, $i, 10))}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-5">
                                    <select class="form-control select2" parsley-trigger="change" name="tahun" id="tahun" required>
                                        <option value="#" selected disabled>Pilih Tahun</option>
                                        @for ($i = 2018; $i <= date('Y'); $i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group text-left m-b-0">
                    @if (array_search("EMPGC",$page))
                    <a href="{{route('createPerhitunganGaji')}}" class="btn btn-success btn-rounded waves-effect waves-light w-md m-b-5">Add New</a>
                    @endif
                    <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="detGajiPegawai()">Show Data</a>
                </div>
            </div>

            <div id="salary-list" style="display:none">
                <section id="showsalary">
                </section>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- Sweet Alert Js  -->
<script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>

<!-- Required datatable js -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
@endsection

@section('script-js')
<script>
    function detGajiPegawai(){
        bulan = $('#bulan').val();
        tahun = $('#tahun').val();

        $.ajax({
            url : "{{route('indexPerhitunganGaji')}}",
            type : "get",
            dataType: 'json',
            data:{
                bulan: bulan,
                tahun: tahun,
            },
        }).done(function (data) {
            document.getElementById("salary-list").style.display = 'block';
            $('#showsalary').html(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection