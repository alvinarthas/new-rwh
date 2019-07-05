@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Select2 --}}
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card-box" >
                    <div class="row">
                        <div class="col-12">
                            <div class="p-20">
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Jenis Member</label>
                                    <div class="col-10">
                                        <select class="form-control select2" parsley-trigger="change" name="jenis" id="jenis" required>
                                            <option value="#" selected disabled>Pilih Jenis Member</option>
                                            <option value="0">Terdaftar di Perusahaan</option>
                                            <option value="1">Tidak terdaftar di perusahaan</option>
                                            <option value="2">Semua Member</option>
                                            <option value="3">Berdasarkan Bank</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="company_show" style="display:none">
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label">Jenis Perusahaan</label>
                                        <div class="col-10">
                                            <select class="form-control select2" parsley-trigger="change" name="perusahaan" id="perusahaan">
                                                <option value="#" selected disabled>Pilih Perusahaan</option>
                                                @foreach ($perusahaan as $per)
                                                    <option value="{{$per->id}}">{{$per->nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="bank_show" style="display:none">
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label">Nama Bank</label>
                                        <div class="col-10">
                                            <select class="form-control select2" parsley-trigger="change" name="bank" id="bank">
                                                <option value="#" disabled selected>Pilih Bank</option>
                                                @foreach ($bank as $ba)
                                                        <option value="{{$ba->id}}" data-image="{{asset('assets/images/bank/'.$ba->icon)}}">{{$ba->nama}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-left m-b-0">
                        <a href="{{route('member.create')}}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5" onclick="showMember()">Tambah Member</a>
                        <a href="javascript:;" class="btn btn-primary btn-rounded w-md waves-effect waves-light m-b-5" onclick="showMember()">Show Data</a>
                    </div>
                </div>
                <div class="card-box" id="member-list" style="display:none">
                    <div class="row">
                        <div class="col-12">
                            @csrf
                            <input type="text" class="form-control" name="search" id="search" value="{{ $keyword }}" placeholder="Search..">
                        </div>
                    </div>
                    <section class="datas" id="ajxlist">
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    {{-- Select2 --}}
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    
    <!-- Required datatable js -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
@endsection

@section('script-js')
<script>
    $(document).ready(function() {
        $(document).on('click', '.pagination a', function (e) {
            $('.datas').append('<img style="position: absolute; left: 0; top: 0; z-index: 100000;" src="../images/loading.gif" />');
            var url = $(this).attr('href'); 
            getDatas($(this).attr('href').split('page=')[1]);
            e.preventDefault();
        });
    });

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

    $('#jenis').on('change', function () {
        id = $('#jenis').val();
        console.log(id)
        if(id == 2){
            document.getElementById("company_show").style.display = 'none';
            document.getElementById("bank_show").style.display = 'none';
        }else if(id == 3){
            document.getElementById("company_show").style.display = 'none';
            document.getElementById("bank_show").style.display = 'block';
        }else{
            document.getElementById("company_show").style.display = 'block';
            document.getElementById("bank_show").style.display = 'none';
        }
    });

    //setup before functions
    var typingTimer;                //timer identifier
    var doneTypingInterval = 1000;  //time in ms, 5 second for example
    var $input = $('#search');

    //on keyup, start the countdown
    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

    //on keydown, clear the countdown 
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
        // typingTimer = setTimeout(doneTyping, doneTypingInterval);
    });

    //user is "finished typing," do something
    function doneTyping () {
        var search = $('#search').val();
        var jenis = $('#jenis').val();
        var perusahaan = $('#perusahaan').val();
        var bank = $('#bank').val();
        console.log(search)
        $.ajax({
            url : 'ajxmember',
            type : "get",
            dataType: 'json',
            data:{
                search: search,
                jenis: jenis,
                perusahaan: perusahaan,
                bank: bank,
            },
        }).done(function (data) {
            $('.datas').html(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function getDatas(page) {
        var jenis = $('#jenis').val();
        var perusahaan = $('#perusahaan').val();
        var bank = $('#bank').val();
        $.ajax({
            url : 'ajxmember?page=' + page,
            type : "get",
            dataType: 'json',
            data:{
                search: $('#search').val(),
                jenis: jenis,
                perusahaan: perusahaan,
                bank: bank,
            },
        }).done(function (data) {
            $('.datas').html(data);
            location.hash = page;
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function showMember(){
        var jenis = $('#jenis').val();
        var perusahaan = $('#perusahaan').val();
        var bank = $('#bank').val();
        $.ajax({
            url : 'ajxmember',
            type : "get",
            dataType: 'json',
            data:{
                jenis: jenis,
                perusahaan: perusahaan,
                bank: bank,
            },
        }).done(function (data) {
            document.getElementById("member-list").style.display = 'block';
            $('.datas').html(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection