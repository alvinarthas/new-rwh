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
                                <label class="col-2 col-form-label">Account Grup</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="account_grup">
                                        <option value="#" disabled selected>Pilih Acc Grup</option>
                                        @foreach ($coagrup as $grup)
                                            @isset($coa->grup_id)
                                                @if ($coa->grup_id == $grup->id)
                                                    <option value="{{$grup->id}}" selected>{{$grup->grup}}</option>
                                                @else
                                                    <option value="{{$grup->id}}" >{{$grup->grup}}</option>
                                                @endif
                                            @else
                                                <option value="{{$grup->id}}" >{{$grup->grup}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
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
                                        <option value="Db">Debet</option>
                                        <option value="Cr">Kredit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Status Account</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="status_account">
                                        <option value="Grup">Grup</option>
                                        <option value="Detail">Detail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Account Parent</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="account_parent">
                                        <option value="#" disabled selected>Pilih Account Parent</option>
                                        @foreach ($parents as $parent)
                                            @if($coa->StatusAccount == "Detail")
                                                @if ($coa->AccParent == $parent->AccNo)
                                                    <option value="{{$parent->AccNo}}" selected>{{$parent->AccName}} - {{$parent->AccNo}}</option>
                                                @else
                                                    <option value="{{$parent->AccNo}}" >{{$parent->AccName}} - {{$parent->AccNo}}</option>
                                                @endif
                                            @else
                                                <option value="{{$parent->AccNo}}" >{{$parent->AccName}} - {{$parent->AccNo}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nominal Saldo Awal</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="saldo_awal" id="saldo_awal" value="@isset($coa->SaldoAwal){{$coa->SaldoAwal}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Company Name</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="company">
                                        
                                        <option value="{{$company->company_id}}">{{$company->company_name}}</option>
                                    </select>
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
    </script>
@endsection
