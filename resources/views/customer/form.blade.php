@extends('layout.main')
@php
    use App\CoaNew;
@endphp

@section('css')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- form Uploads -->
    <link href="{{ asset('assets/plugins/fileuploads/css/dropify.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datepicker-->
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection

@section('judul')
@php
    if($jenis == "create"){
        $judul = "Tambah Data Customer";
    }elseif($jenis == "edit"){
        $judul = "Update Data Customer";
    }elseif($jenis == "topup" || $jenis=="edittopup"){
        $judul = "Top Up Saldo Customer";
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
    @elseif($jenis == "topup")
        <form class="form-horizontal" role="form" action="{{ route('saldo.store') }}" enctype="multipart/form-data" method="POST">
    @elseif($jenis == "edittopup")
        <form class="form-horizontal" role="form" action="{{ route('saldo.update',['id' => $saldo->id]) }}" enctype="multipart/form-data" method="POST">
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
                @if($jenis == "create" || $jenis == "edit")
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
                @elseif($jenis=="topup" || $jenis=="edittopup")
                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <p class="text-muted font-13">Top Up Saldo Customer</p>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Customer</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="customer_id">
                                        @if($jenis=="edittopup")
                                            @foreach($customers as $cs)
                                                @if($cs->id == $saldo->customer_id)
                                                    <option value="{{ $cs->id }}" selected>{{ $cs->apname }}</option>
                                                @else
                                                    <option value="{{ $cs->id }}">{{ $cs->apname }}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="#" disabled selected>Pilih Customer</option>
                                            @foreach ($customers as $cs)
                                                <option value="{{$cs->id}}">{{$cs->apname}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Rekening</label>
                                <div class="col-10">
                                    <select class="form-control select2" id="search" name="search" parsley-trigger="change">
                                        @isset($saldo->accNo)
                                            <option value="{{ $saldo->accNo }}" selected>{{ CoaNew::where('AccNo', $saldo->accNo)->first()->AccName}}</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nominal Top Up</label>
                                <div class="col-10">
                                    <input type="text" class="form-control divide" parsley-trigger="change" name="nominal" id="nominal" placeholder="Nominal top up" autocomplete="off" value="@isset($saldo->amount){{ $saldo->amount }}@endisset" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Tanggal Transaksi</label>
                                <div class="col-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control tanggal" parsley-trigger="change" required autocomplete="off" placeholder="yyyy/mm/dd" name="tanggal" id="tanggal"  value="@isset($saldo->tanggal){{$saldo->tanggal}}@endisset"  data-date-format='yyyy-mm-dd'>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="ti-calendar"></i></span>
                                        </div>
                                    </div><!-- input-group -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label">Upload bukti</label>
                                        <div class="col-10">
                                            @php
                                                if(!empty($saldo->buktitf)){
                                                    $source = $saldo->buktitf;
                                                }else{
                                                    $source = "noimage.jpg";
                                                }
                                            @endphp
                                            <input type="file" class="dropify" data-height="100" name="buktitf" id="buktitf" data-default-file="{{ asset('assets/images/saldo/topup/'.$source) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Keterangan</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" name="keterangan" id="keterangan" value="@isset($saldo->keterangan){{ $saldo->keterangan }}@endisset">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @if($jenis == "create" || $jenis == "edit")
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
    @endif

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
<!-- number-divider -->
<script src="{{ asset('assets/plugins/number-divider/number-divider.min.js') }}"></script>
<!-- Datepicker -->
<script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection

@section('script-js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('form').parsley();
            ajx_coa();
            $(".divide").divide();
        });

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

        // Date Picker
        jQuery('.tanggal').datepicker();

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
        function ajx_coa(){
            $("#search").select2({
                placeholder:'Masukan Kata Kunci',
                ajax:{
                    url: "{{route('ajxCoaOrder')}}",
                    dataType:'json',
                    delay:250,
                    data:function(params){
                        return{
                            keyword:params.term,
                        };
                    },
                    processResults:function(data){
                        var item = $.map(data, (value)=>{ //map buat ngemap object data kyk foreach
                            return { id: value.id, text: value.AccName};
                        });
                        return {
                            results: item
                        }
                    },
                    cache: false,
                },
                minimumInputLength: 3,
                // templateResult: formatRepo,
                // templateSelection: formatRepoSelection
            });
        }

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
