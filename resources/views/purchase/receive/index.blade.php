@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('judul')
List Receive Product
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-12">
                            <div class="p-20">
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Posting Period</label>
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
                        <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="choosePurchase()">Show Data</a>
                        <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="choosePurchase('all')">Show ALL Data</a>
                    </div>
                </div>

                <div id="receive-list" style="display:none">
                    <section id="showreceive">
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- Required datatable js -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
@endsection

@section('script-js')
<script>

    function choosePurchase(id=null){
        bulan = $('#bulan').val();
        tahun = $('#tahun').val();
        
        $.ajax({
            url : "{{route('receiveProdAjx')}}",
            type : "get",
            dataType: 'json',
            data:{
                bulan: bulan,
                tahun: tahun,
                jenis: id,
            },
        }).done(function (data) {
            document.getElementById("receive-list").style.display = 'block';
            $('#showreceive').html(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection