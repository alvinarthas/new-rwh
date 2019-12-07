@extends('layout.main')
@php
    use App\DataKota;
@endphp
@section('css')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- form Uploads -->
    <link href="{{ asset('assets/plugins/fileuploads/css/dropify.min.css') }}" rel="stylesheet" type="text/css" />
     <!-- Sweet Alert css -->
     <link href="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- form Uploads -->
    <link href="{{ asset('assets/plugins/fileuploads/css/dropify.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('judul')
Informasi Data Member
@endsection

@section('content')

    <form class="form-horizontal" role="form" action="{{ route('member.update',['id' => $member->ktp]) }}" enctype="multipart/form-data" method="POST">
        {{ method_field('PUT') }}
        @csrf
        {{-- Informasi Pribadi --}}
        <div class="row">
            <div class="col-12">
                <div class="card-box">
                    <h4 class="m-t-0 header-title">Profile Member</h4>
                    <p class="text-muted m-b-30 font-14">
                    </p>

                    <div class="row">
                        <div class="col-12">
                            <div class="p-20">
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Member ID</label>
                                    <div class="col-10">
                                        <input type="text" class="form-control" parsley-trigger="change" required name="member_id" id="member_id" value="@isset($member->member_id){{$member->member_id}}@endisset">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Nomor KTP</label>
                                    <div class="col-10">
                                        <input type="text" class="form-control" parsley-trigger="change" required name="ktp" id="ktp" value="@isset($member->ktp){{$member->ktp}}@endisset">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Nama Lengkap</label>
                                    <div class="col-10">
                                        <input type="text" class="form-control" parsley-trigger="change" required name="nama" id="nama" value="@isset($member->nama){{$member->nama}}@endisset">
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
                                            <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="tanggal_lahir" id="tanggal_lahir"  value="@isset($member->tgl_lahir){{$member->tgl_lahir}}@endisset"  data-date-format='yyyy-mm-dd'>
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
                                                    <option value="{{$city->kode_pusdatin_kota}}" selected>{{$city->kab_kota}}</option>
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
                                                    @if ($koor->id == $member->koordinator)
                                                        <option value="{{$koor->id}}" selected>{{$koor->nama}}</option>
                                                    @else
                                                        <option value="{{$koor->id}}">{{$koor->nama}}</option>
                                                    @endif
                                                @else
                                                    <option value="{{$koor->id}}">{{$koor->nama}}</option>
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
                                        <input type="file" class="dropify" data-height="100" name="scanktp" id="scanktp" data-default-file="@isset($member->scanktp){{ asset('assets/images/member/ktp/'.$member->scanktp) }}@endisset"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (array_search("MBMMU",$page))
                        <div class="form-group text-right m-b-0">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">
                                Submit
                            </button>
                        </div>
                    @endif
                    
                </div>
            </div>
        </div>
    </form>

    {{-- Data Bank Member --}}
    @if (array_search("MBMMBV",$page))
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Bank Member</h4>
                {{-- List Data Bank Member --}}
                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">Data Bank Member</h4>
                    @if (array_search("MBMMBC",$page))
                    <p class="text-muted font-14 m-b-30">
                        <a href="javascript:;" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5" onclick="funcBank('create')">Tambah Bank</a>
                    </p>
                    @endif

                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Nama Bank</th>
                            <th>Nomor Rekening</th>
                            <th>No Kartu ATM</th>
                            <th>No Buku Tabungan</th>
                            <th>Cabang Pembuka</th>
                            <th>Status Rekening</th>
                            <th>Primary</th>
                            <th>Action</th>
                        </thead>

                        <tbody>
                            @php($i = 1)
                            @foreach ($bankmember as $bm)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$bm->bank->nama}}</td>
                                    <td>{{$bm->norek}}</td>
                                    <td>{{$bm->noatm}}</td>
                                    <td>{{$bm->nobuku}}</td>
                                    <td>{{$bm->cabbank}}</td>
                                    <td>{{$bm->status}}</td>
                                    <td>{{$bm->p_status}}</td>
                                    <td>
                                        @if (array_search("MBMMBU",$page))
                                        <a href="javascript:;" class="btn btn-info btn-rounded waves-effect w-md waves-info m-b-5" onclick="funcBank('edit',{{$bm->id}})">Update</a>
                                        @endif
                                        @if (array_search("MBMMBD",$page))
                                        <a href="javascript:;" class="btn btn-danger btn-rounded waves-effect w-md waves-danger m-b-5" onclick="funcBank('delete',{{$bm->id}})">Delete</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @php($i++)
                        </tbody>
                    </table>
                </div>

                {{-- Form Create/Update --}}
                <div id="bankmember"></div>
            </div>
        </div>
    </div>
    @endif
    
    {{-- Data Perusahaan Member --}}
    @if (array_search("MBMMPV",$page))
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Perusahaan Member</h4>
                {{-- List Data --}}
                <div class="card-box table-responsive" id="tblperusahaan">
                    <h4 class="m-t-0 header-title">Data Perusahaan</h4>
                    @if (array_search("MBMMPC",$page))
                    <p class="text-muted font-14 m-b-30">
                        <a href="javascript:;" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5" onclick="funcPerusahaan('create')">Tambah Perusahaan</a>
                    </p>
                    @endif
                    
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>Nomor ID</th>
                            <th>Password</th>
                            <th>Action</th>
                        </thead>
    
                        <tbody>
                            @php($i = 1)
                            @foreach ($perusahaanmember as $pm)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$pm->perusahaan->nama}}</td>
                                    <td>{{$pm->noid}}</td>
                                    <td>{{$pm->passid}}</td>
                                    <td>
                                        @if (array_search("MBMMPU",$page))
                                            <a href="javascript:;" class="btn btn-info btn-rounded waves-effect w-md waves-info m-b-5" onclick="funcPerusahaan('edit',{{$pm->id}})">Update</a>
                                        @endif
                                        @if (array_search("MBMMPD",$page))
                                            <a href="javascript:;" class="btn btn-danger btn-rounded waves-effect w-md waves-danger m-b-5" onclick="funcPerusahaan('delete',{{$pm->id}})">Delete</a>
                                        @endif
                                        
                                    </td>
                                </tr>
                            @endforeach
                            @php($i++)
                        </tbody>
                    </table>
                </div>

                {{-- Form Create/Update --}}
                <div id="perusahaanmember"></div>
            </div>
        </div>
    </div>
    @endif
    
    
    

@endsection

@section('js')
<!-- Plugin -->
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<!-- file uploads js -->
<script src="{{ asset('assets/plugins/fileuploads/js/dropify.min.js') }}"></script>
<!-- Validation js (Parsleyjs) -->
<script type="text/javascript" src="{{ asset('assets/plugins/parsleyjs/dist/parsley.min.js') }}"></script>
<!-- Sweet Alert Js  -->
<script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>
<!-- file uploads js -->
<script src="{{ asset('assets/plugins/fileuploads/js/dropify.min.js') }}"></script>
@endsection

@section('script-js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('form').parsley();
        });

        var ktp = $('#ktp').val();
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
        $(".select2").select2();
    </script>

    <script>
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

        function funcPerusahaan(jenis,id=null){
            if(jenis == "create"){
                $.ajax({
                    url : "{{route('createPerusahaanMember')}}",
                    type : "get",
                    dataType: 'json',
                    data:{
                        ktp: ktp,
                    },
                }).done(function (data) {
                    $('#perusahaanmember').html(data);
                    element = document.getElementById("perusahaanmember");
                    element.scrollIntoView();
                }).fail(function (msg) {
                    alert('Gagal menampilkan data, silahkan refresh halaman.');
                });
            }else if(jenis == "edit"){
                $.ajax({
                    url : "{{route('editPerusahaanMember')}}",
                    type : "get",
                    dataType: 'json',
                    data:{
                        ktp: ktp,
                        pid: id,
                    },
                }).done(function (data) {
                    $('#perusahaanmember').html(data);
                    element = document.getElementById("perusahaanmember");
                    element.scrollIntoView();
                }).fail(function (msg) {
                    alert('Gagal menampilkan data, silahkan refresh halaman.');
                });destroyPerusahaanMember
            }else if(jenis == "delete"){
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger m-l-10',
                    buttonsStyling: false
                }).then(function () {
                    $.ajax({
                        url : "{{route('destroyPerusahaanMember')}}",
                        type : "get",
                        dataType: 'json',
                        data:{
                            ktp: ktp,
                            pid: id,
                        },
                    }).done(function (data) {
                        swal(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                        location.reload();
                    }).fail(function (msg) {
                        swal(
                            'Failed',
                            'Your imaginary file is safe :)',
                            'error'
                        )
                    });
                    
                }, function (dismiss) {
                    // dismiss can be 'cancel', 'overlay',
                    // 'close', and 'timer'
                    if (dismiss === 'cancel') {
                        console.log("eh ga kehapus");
                        swal(
                            'Cancelled',
                            'Your imaginary file is safe :)',
                            'error'
                        )
                    }
                })
            }
            
        }

        function funcBank(jenis,id=null){
            if(jenis == "create"){
                $.ajax({
                    url : "{{route('createBankMember')}}",
                    type : "get",
                    dataType: 'json',
                    data:{
                        ktp: ktp,
                    },
                }).done(function (data) {
                    $('#bankmember').html(data);
                    element = document.getElementById("bankmember");
                    element.scrollIntoView();
                }).fail(function (msg) {
                    alert('Gagal menampilkan data, silahkan refresh halaman.');
                });
            }else if(jenis == "edit"){
                $.ajax({
                    url : "{{route('editBankMember')}}",
                    type : "get",
                    dataType: 'json',
                    data:{
                        ktp: ktp,
                        bid: id,
                    },
                }).done(function (data) {
                    $('#bankmember').html(data);
                    element = document.getElementById("bankmember");
                    element.scrollIntoView();
                }).fail(function (msg) {
                    alert('Gagal menampilkan data, silahkan refresh halaman.');
                });destroyPerusahaanMember
            }else if(jenis == "delete"){
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger m-l-10',
                    buttonsStyling: false
                }).then(function () {
                    $.ajax({
                        url : "{{route('destroyBankMember')}}",
                        type : "get",
                        dataType: 'json',
                        data:{
                            ktp: ktp,
                            bid: id,
                        },
                    }).done(function (data) {
                        swal(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                        location.reload();
                    }).fail(function (msg) {
                        swal(
                            'Failed',
                            'Your imaginary file is safe :)',
                            'error'
                        )
                    });
                    
                }, function (dismiss) {
                    // dismiss can be 'cancel', 'overlay',
                    // 'close', and 'timer'
                    if (dismiss === 'cancel') {
                        console.log("eh ga kehapus");
                        swal(
                            'Cancelled',
                            'Your imaginary file is safe :)',
                            'error'
                        )
                    }
                })
            }
            
        }
    </script>
@endsection