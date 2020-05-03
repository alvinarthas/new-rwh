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

    <style>
        input {
            width: 100%;
            box-sizing: border-box;
        }
    </style>
@endsection

@section('judul')
Form Konversi Barang
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            @if($jenis == "create")
            <div class="card-box">
                <h4 class="m-t-0 header-title">Supplier</h4>
                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Pilih Supplier</label>
                                <div class="col-10">
                                    <select class="form-control select2" parsley-trigger="change" name="supplier" id="supplier">
                                        <option value="#" selected disabled>Pilih Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                        @if (session('role') == "Superadmin" || session('role') == "Direktur Utama")
                                            <option value="{{$supplier->id}}">{{$supplier->nama}}</option>
                                        @else
                                            <option value="{{$supplier->supplier->id}}">{{$supplier->supplier->nama}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group text-left m-b-0">
                    <a href="javascript:;" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5" onclick="chooseSupplier()">Pilih Supplier</a>
                </div>
            </div>
            @endif

            <div id="konversi-list" style="display:none">
                <section id="showkonversi">
                </section>
            </div>
        </div>
    </div>

@endsection

@section('js')
    {{-- Select2 --}}
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
@endsection

@section('script-js')
<script>
    // Select2
    $(".select2").select2();

    function chooseSupplier(){
        supplier = $('#supplier').val();

        $.ajax({
            url : "{{route('konversi.create')}}",
            type : "get",
            dataType: 'json',
            data:{
                supplier: supplier,
            },
        }).done(function (data) {
            document.getElementById("konversi-list").style.display = 'block';
            $('#showkonversi').html(data);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection
