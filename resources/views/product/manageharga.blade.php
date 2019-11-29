@extends('layout.main')

@php

    use App\Perusahaan;
    use Illuminate\Support\Facades\DB;
@endphp

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!--select2-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <!--venobox lightbox-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/magnific-popup/dist/magnific-popup.css') }}"/>
    {{-- Fingerprint --}}
    <link href="{{ asset('assets/fingerprint/ajaxmask.css') }}" rel="stylesheet">
    <style>
    img.photo{
        display:block; width:50%; height:auto;
    }
    </style>
@endsection

@section('content')
{{-- <form class="form-horizontal" role="form" action="{{ route('product.show') }}" enctype="multipart/form-data" method="POST"> --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Data Produk</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Pilih Periode</label>
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
                    <button class="btn btn-primary waves-effect waves-light" onclick="showLog()">
                        Show
                    </button>
                </div>
            </div>
        </div>
    </div>
{{-- </form> --}}

<div id="logTabel">
</div>
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
    <script type="text/javascript" src="{{ asset('assets/plugins/magnific-popup/dist/jquery.magnific-popup.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <!-- number-divider -->
    <script src="{{ asset('assets/plugins/number-divider/number-divider.min.js') }}"></script>
@endsection

@section('script-js')
<script type="text/javascript">
    $(document).ready(function () {
        // Responsive Datatable
        $('#responsive-datatable').DataTable();

        // Select2
        $("#bulan").select2({
            templateResult: formatState,
            templateSelection: formatState
        });
        $("#tahun").select2({
            templateResult: formatState,
            templateSelection: formatState
        });

        function formatState (opt) {
            if (!opt.id) {
                return opt.text.toUpperCase();
            }

            var optimage = $(opt.element).attr('data-image');
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

    function showLog(){
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            $.ajax({
                url         :   "{{route('showProdAjx')}}",
                data        :   {
                    bulan : bulan,
                    tahun : tahun,
                },
                type		:	"GET",
                dataType    :   "html",
                success		:	function(data){
                    $("#logTabel").html(data);
                },
                error       :   function(data){
                    document.getElementById('period').value = '2018-06';
                }
            });
    }
</script>
@endsection
