@php
    use App\Coa;
@endphp
@extends('layout.main')

@section('css')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- form Uploads -->
    <link href="{{ asset('assets/plugins/fileuploads/css/dropify.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('judul')
Tambah Data Coa
@endsection

@section('content')
    @if($jenis == "create")
        <form class="form-horizontal" role="form" action="{{ route('coa.store') }}" enctype="multipart/form-data" method="POST">
    @elseif($jenis == "edit")
        <form class="form-horizontal" role="form" action="{{ route('coa.update', ['id' => $coa->id]) }}" enctype="multipart/form-data" method="POST">
            {{ method_field('PUT') }}
    @endif

    @csrf

    {{-- COA --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Data Coa</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Account Number</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="account_number" id="account_number" value="@isset($coa->AccNo){{$coa->AccNo}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Account Name</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="account_name" id="account_name" value="@isset($coa->AccName){{$coa->AccName}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Posisi Saldo Normal</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="saldo_normal">
                                        @isset($coa->SaldoNormal)
                                            @if ($coa->SaldoNormal == 'Db')
                                            <option value="Db" selected>Debet</option>
                                            <option value="Cr">Kredit</option>
                                            @else
                                            <option value="Db">Debet</option>
                                            <option value="Cr"selected>Kredit</option>
                                            @endif
                                        @else
                                            <option value="#" disabled selected>Pilih Saldo Normal</option>
                                            <option value="Db">Debet</option>
                                            <option value="Cr">Kredit</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Status Account</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="status_account">
                                        @isset($coa->StatusAccount)
                                            @if ($coa->StatusAccount == 'Grup')
                                            <option value="Grup" selected>Grup</option>
                                            <option value="Detail">Detail</option>
                                            @else
                                            <option value="Grup">Grup</option>
                                            <option value="Detail" selected>Detail</option>
                                            @endif
                                        @else
                                            <option value="#" disabled selected>Pilih Status Account</option>
                                            <option value="Grup">Grup</option>
                                            <option value="Detail">Detail</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Account Parent</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="account_parent" id="account_parent">
                                        @isset($coa->AccParent)
                                            <option value="{{$coa->AccParent}}" selected>{{$coa->AccParent}} -  {{Coa::where('AccNo',$coa->AccParent)->select('AccName')->first()->AccName}}</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nominal Saldo Awal</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="saldo_awal" id="saldo_awal" value="@isset($coa->SaldoAwal){{$coa->SaldoAwal}}@endisset">
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
        // Date Picker
        jQuery('#tanggal_lahir').datepicker();
        jQuery('#mulai_kerja').datepicker();

        // Select2
        $(".select2").select2();

        $("#account_parent").select2({
            placeholder:"Pilih Coa Parent",
            ajax:{
                url: "{{route('ajxCoa')}}",
                dataType:'json',
                delay:250,
                data:function(params){
                    return{
                        params:params.term,
                    };
                },
                processResults:function(data){
                    var item = $.map(data, (value)=>{ //map buat ngemap object data kyk foreach
                        return { id: value.id, text: value.text };
                    });

                    return {
                        results: item
                    }
                },
                cache: true
            },
            minimumInputLength: 1,
        });

    </script>
@endsection
