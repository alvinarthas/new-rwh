@extends('layout.main')

@section('css')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- form Uploads -->
    <link href="{{ asset('assets/plugins/fileuploads/css/dropify.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('judul')
Tambah Data Product
@endsection

@section('content')
    @if($jenis == "create")
        <form class="form-horizontal" role="form" action="{{ route('product.store') }}" enctype="multipart/form-data" method="POST">
    @elseif($jenis == "edit")
        <form class="form-horizontal" role="form" action="{{ route('product.update', ['id' => $product->id]) }}" enctype="multipart/form-data" method="POST">
            {{ method_field('PUT') }}
    @endif

    @csrf

    {{-- Perusahaan --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Data Product</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Company Name</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="company_id">
                                        <option value="#" disabled selected>Pilih Company</option>
                                        @foreach ($companys as $cmp)
                                            @isset($product->company_id)
                                                @if ($cmp->company_id == $product->company_id)
                                                    <option value="{{$cmp->company_id}}" selected>{{$cmp->company_name}}</option>
                                                @else
                                                    <option value="{{$cmp->company_id}}" >{{$cmp->company_name}}</option>
                                                @endif
                                            @else
                                                <option value="{{$cmp->company_id}}" >{{$cmp->company_name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Product ID</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="prod_id" id="prod_id" value="@isset($product->prod_id){{$product->prod_id}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Product Name</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="name" id="name" value="@isset($product->name){{$product->name}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Product Brand</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" required name="category" id="category" value="@isset($product->category){{$product->category}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Supplier</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="supplier">
                                        <option value="#" disabled selected>Pilih Supplier</option>
                                        @foreach ($perusahaans as $prs)
                                            @isset($product->supplier)
                                                @if ($prs->id == $product->supplier)
                                                    <option value="{{$prs->id}}" selected>{{$prs->nama}}</option>
                                                @else
                                                    <option value="{{$prs->id}}" >{{$prs->nama}}</option>
                                                @endif
                                            @else
                                                <option value="{{$prs->id}}" >{{$prs->nama}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if($jenis == "edit")
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Stock Awal</label>
                                    <div class="col-10">
                                        <input type="text" class="form-control" parsley-trigger="change" required name="stock" id="stock" value="@isset($product->stock){{$product->stock}}@endisset">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-2 col-form-label">Product ID Perusahaan</label>
                                    <div class="col-10">
                                        <input type="text" class="form-control" parsley-trigger="change" name="prod_id_new" id="prod_id_new" value="@isset($product->prod_id_new){{$product->prod_id_new}}@endisset">
                                    </div>
                                </div>
                            @endif
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
