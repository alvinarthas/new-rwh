@extends('layout.main')

@section('css')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- form Uploads -->
    <link href="{{ asset('assets/plugins/fileuploads/css/dropify.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('judul')
@php
    if($jenis == "create"){
        $judul = "Tambah Data Customer";
    }elseif($jenis == "edit"){
        $judul = "Update Data Customer";
    }
@endphp
{{ $judul }}
@endsection

@section('content')
    @if($jenis == "create")
        <form class="form-horizontal" role="form" action="{{ route('customer.store') }}" enctype="multipart/form-data" method="POST">
    @elseif($jenis == "edit")
        <form class="form-horizontal" role="form" action="{{ route('customer.update',['id' => $customer->id]) }}" enctype="multipart/form-data" method="POST">
            {{ method_field('PUT') }}
    @endif

    @csrf
    {{-- Informasi Pribadi --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                @if($jenis == "create")
                    <h4 class="m-t-0 header-title">Tambah Data Customer</h4>
                @elseif($jenis == "edit")
                    <h4 class="m-t-0 header-title">Update Data Customer</h4>
                @endif
                {{-- <p class="text-muted m-b-30 font-14">Customer Information</p> --}}

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <p class="text-muted font-13">Informasi Customer</p>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Customer ID Number</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="customer_id" id="customer_id" value="@isset($customer->cid){{$customer->cid}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nama</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="name" id="name" value="@isset($customer->apname){{$customer->apname}}@endisset" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor Telepon</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="phone" id="phone" value="@isset($customer->apphone){{$customer->apphone}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Fax</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="fax" id="fax" value="@isset($customer->apfax){{$customer->apfax}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Email</label>
                                <div class="col-10">
                                    <input type="email" class="form-control" parsley-trigger="change" name="email" id="email" value="@isset($customer->apemail){{$customer->apemail}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Alamat</label>
                                <div class="col-10">
                                    <textarea class="form-control" name="address" id="address">@isset($customer->apadd){{$customer->apadd}}@endisset</textarea>
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
            <div class="card-box">

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <p class="text-muted font-13">Informasi Perusahaan</p>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nama Perusahaan</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="cname" id="cname" value="@isset($customer->cicn){{$customer->cicn}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Alamat</label>
                                <div class="col-10">
                                    <textarea class="form-control" name="cadd" id="cadd">@isset($customer->ciadd){{$customer->ciadd}}@endisset</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Kota</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="ccity" id="ccity" value="@isset($customer->cicty){{$customer->cicty}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Kode Pos</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="czipcode" id="czipcode" value="@isset($customer->cizip){{$customer->cizip}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Provinsi</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="cprovince" id="cprovince" value="@isset($customer->cipro){{$customer->cipro}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Website</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="cwebsite" id="cwebsite" value="@isset($customer->ciweb){{$customer->ciweb}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Email</label>
                                <div class="col-10">
                                    <input type="email" class="form-control" parsley-trigger="change" name="cemail" id="cemail" value="@isset($customer->ciemail){{$customer->ciemail}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor Telepon</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="cphone" id="cphone" value="@isset($customer->ciphone){{$customer->ciphone}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Fax</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="cfax" id="cfax" value="@isset($customer->cifax){{$customer->cifax}}@endisset">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group text-right m-b-0">
        @if($jenis=="edit")
            <a href="javascript:;" type="button" class="btn btn-danger waves-effect waves-danger" onclick="deleteCustomer({{ $customer->id}})" >Delete</a>
        @endif
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
        function deleteCustomer(id){
            var token = $("meta[name='csrf-token']").attr("content");
            // console.log(id);
            $.ajax({
                url : "/deletecustomer/"+id,
                type : "GET",
                data:{
                    id : id,
                    _token : token,
                },success   :	function(){
                    window.location="http://localhost:8000/customer";
                    alert('Data Berhasil dihapus');
                },error     :   function(){
                    alert('Gagal menampilkan data, silahkan refresh halaman.');
                }
            })
        }
    </script>
@endsection
