@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css"/>
    <!--venobox lightbox-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/magnific-popup/dist/magnific-popup.css') }}"/>
    <!--select2-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <!--datepicker-->
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- Sweet Alert css -->
    <link href="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!--Token-->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
@php
    use App\Perusahaan;
    use App\Customer;
    use App\Product;
    use App\ReturPembelianDet;
    use App\ReturPenjualanDet;
@endphp
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <h2>Manage Retur Pembelian Barang</h2>
            <h3 class="m-t-0 header-title">Please Choose Purchase Order</h3>
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
<div class="form-group text-right m-b-0">
    <button class="btn btn-primary waves-effect waves-light" onclick="showReturPembelian()" type="submit">
        Show Purchase Order
    </button>
</div>

<div id="tabelPO"></div>
@endsection

@section('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <!-- Modal-Effect -->
    <script src="{{ asset('assets/plugins/custombox/dist/custombox.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/custombox/dist/legacy.min.js') }}"></script>

    <!-- Magnific popup -->
    <script src="{{ asset('assets/plugins/magnific-popup/dist/jquery.magnific-popup.min.js') }}" type="text/javascript"></script>

    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- number-divider -->
    <script src="{{ asset('assets/plugins/number-divider/number-divider.min.js') }}"></script>

    <!-- Datepicker -->
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <!-- Sweet Alert Js  -->
    <script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('script-js')
<script type="text/javascript">

    $(document).ready(function () {
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

    });

    function showReturPembelian(){
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        console.log(bln,thn)
        $.ajax({
            url         :   "{{route('showReturPembelian')}}",
            data        :   {
                tahun : thn,
                bulan : bln,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tabelPO").html(data);
                // console.log(data)
            },
            error       :   function(data){
                document.getElementById('tahun').value = '2018';
            }
        });
    }
</script>
@endsection
