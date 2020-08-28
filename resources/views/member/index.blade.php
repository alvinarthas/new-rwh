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
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection
@section('content')
<!-- sample modal content -->
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
                                        <option value="1">Terdaftar di Perusahaan</option>
                                        <option value="2">Tidak terdaftar di perusahaan</option>
                                        <option value="3">Semua Member</option>
                                        <option value="4">Berdasarkan Bank</option>
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
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Status Rekening</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="statusrek" id="statusrek" required>
                                        <option value="#" selected>Semua Status</option>
                                        @foreach($statusrek as $status)
                                            <option value="{{ $status->id }}">{{ $status->status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Status Keanggotaan Member</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="statusnoid" id="statusnoid" required>
                                        <option value="#" selected>Semua Status</option>
                                        @foreach($statusnoid as $status)
                                            <option value="{{ $status->id }}">{{ $status->status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group text-left m-b-0">
                    <a href="{{route('member.create')}}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Member</a>
                    <a href="javascript:;" class="btn btn-primary btn-rounded w-md waves-effect waves-light m-b-5" onclick="showMember()">Show Data</a>
                </div>
            </div>

            <div id="member-list" style="display:none">
                <section id="showmember">
                </section>
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

    <!-- Modal-Effect -->
    <script src="{{ asset('assets/plugins/custombox/dist/custombox.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/custombox/dist/legacy.min.js') }}"></script>
@endsection

@section('script-js')
<script>
    $(document).ready(function() {
        $(".select2").select2();

        $('#jenis').on('change', function () {
            id = $('#jenis').val();
            console.log(id)
            if(id == 3 || id == 5 || id == 6 || id == 7 || id == 8){
                document.getElementById("company_show").style.display = 'none';
                document.getElementById("bank_show").style.display = 'none';
            }else if(id == 4){
                document.getElementById("company_show").style.display = 'none';
                document.getElementById("bank_show").style.display = 'block';
            }else{
                document.getElementById("company_show").style.display = 'block';
                document.getElementById("bank_show").style.display = 'none';
            }
        });
    });

    function showMember(){
        var jenis = $('#jenis').val();
        var perusahaan = $('#perusahaan').val();
        var bank = $('#bank').val();
        var statusrek = $('#statusrek').val();
        console.log(jenis,perusahaan, bank, statusrek)
        $.ajax({
            url : "{{route('member.index')}}",
            type : "get",
            dataType: 'json',
            data:{
                jenis: jenis,
                perusahaan: perusahaan,
                bank: bank,
                statusrek: statusrek,
            },
        }).done(function (data) {
            document.getElementById("member-list").style.display = 'block';
            $('#showmember').html(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection
