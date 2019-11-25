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
    <!--Token-->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
@php
    use App\Coa;
@endphp
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">

                @if($bonusapa=="perhitungan" OR $bonusapa=="bonusgagal")
                    @if($bonusapa=="perhitungan")
                        <h3 class="m-t-0 header-title">Perhitungan Bonus Member</h3>
                        @if (array_search("BMPBC",$page))
                            @if($jenis == "index")
                                <p class="text-muted font-14 m-b-30">
                                    <a href="{{ route('bonus.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Perhitungan Bonus</a>
                                </p>
                            @endif
                        @endif
                    @elseif($bonusapa=="bonusgagal")
                        <h3 class="m-t-0 header-title">Laporan Upload Gagal Perhitungan Bonus Member</h3>
                    @endif
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Nama Perusahaan</label>
                        <div class="col-10">
                            <select class="form-control select2" parsley-trigger="change" id="perusahaan" name="perusahaan">
                                <option value="#" disabled selected>Pilih Perusahaan</option>
                                @foreach ($perusahaans as $prs)
                                    <option value="{{$prs->id}}">{{$prs->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @elseif($bonusapa=="pembayaran" OR $bonusapa=="topup")
                    @if($bonusapa=="pembayaran")
                        <h3 class="m-t-0 header-title">Penerimaan Bonus Member</h3>
                    @elseif($bonusapa=="topup")
                        <h3 class="m-t-0 header-title">Top Up Bonus Member</h3>
                    @endif
                    @if($jenis == "index")
                        @if($bonusapa=="pembayaran")
                            @if (array_search("BMBBC",$page))
                                <p class="text-muted font-14 m-b-30">
                                    <a href="{{ route('bonus.createPenerimaan') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Penerimaan Bonus</a>
                                </p>
                            @endif
                        @elseif($bonusapa=="topup")
                            @if (array_search("BMTUC",$page))
                                <p class="text-muted font-14 m-b-30">
                                    <a href="{{ route('bonus.createtopup') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Top Up Bonus Member</a>
                                </p>
                            @endif
                        @endif
                    @endif
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Tanggal Transaksi</label>
                        <div class="col-10">
                            <div class="input-group">
                                <input type="text" class="form-control" parsley-trigger="change" required placeholder="YYYY/MM/DD" name="tgl_transaksi" id="tgl_transaksi"  value=""  data-date-format='yyyy-mm-dd' autocomplete="off">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="ti-calendar"></i></span>
                                </div>
                            </div><!-- input-group -->
                        </div>
                    </div>
                @endif
                @if($bonusapa=="perhitungan" OR $bonusapa=="pembayaran" OR $bonusapa=="laporan" OR $bonusapa=="bonusgagal")
                    @if($bonusapa=="laporan")
                        <h3 class="m-t-0 header-title">Laporan Realisasi Bonus Member</h3>
                    @endif
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Bulan & Tahun Bonus</label>
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
                @endif
                @if($bonusapa=="pembayaran" OR $bonusapa=="topup")
                    <div class="form-group row">
                        @if($bonusapa=="pembayaran")
                            <label class="col-2 col-form-label">Rekening Bank Tujuan</label>
                        @elseif($bonusapa=="topup")
                            <label class="col-2 col-form-label">Sumber Rekening</label>
                        @endif
                        <div class="col-10">
                            <select class="form-control" parsley-trigger="change" id="rekening" name="rekening">
                            </select>
                        </div>
                    </div>
                    {{-- <div id="supplier_show" style="display:none">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Supplier</label>
                            <div class="col-10">
                                <select class="form-control" parsley-trigger="change" id="supplier" name="supplier">
                                    @foreach($supplier as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div> --}}
                @endif
            </div>
        </div>
    </div>
    <div class="form-group text-right m-b-0">
        @if($bonusapa=="perhitungan")
            @if($jenis=="index")
                <button class="btn btn-primary waves-effect waves-light" onclick="showBonusPerhitungan()" type="submit">
                    Show Bonus
                </button>
            @elseif($jenis=="create")
                <a href="{{ route('bonus.index') }}"><button class="btn btn-warning waves-effect waves-light" type="submit">
                    Back
                </button></a>
                <button class="btn btn-primary waves-effect waves-light" onclick="createBonusPerhitungan()" type="submit">
                    Create Bonus
                </button>
            @endif
        @elseif($bonusapa=="pembayaran")
            @if($jenis=="index")
                <button class="btn btn-primary waves-effect waves-light" onclick="showBonusPenerimaan()" type="submit">
                    Show Bonus
                </button>
            @elseif($jenis=="create")
                <a href="{{ route('bonus.penerimaan') }}"><button class="btn btn-warning waves-effect waves-light" type="submit">
                    Back
                </button></a>
                <button class="btn btn-primary waves-effect waves-light" onclick="createBonusPenerimaan()" type="submit">
                    Create Bonus
                </button>
            @endif
        @elseif($bonusapa=="topup")
            @if($jenis=="index")
                <button class="btn btn-primary waves-effect waves-light" onclick="showBonusTopup()" type="submit">
                    Show Bonus
                </button>
            @elseif($jenis=="create")
                <a href="{{ route('bonus.topup') }}"><button class="btn btn-warning waves-effect waves-light" type="submit">
                    Back
                </button></a>
                <button class="btn btn-primary waves-effect waves-light" onclick="createBonusTopup()" type="submit">
                    Create Bonus
                </button>
            @endif
        @elseif($bonusapa=="laporan")
            @if($jenis=="index")
                <button class="btn btn-primary waves-effect waves-light" onclick="showLaporanBonus()" type="submit">
                    Show Bonus
                </button>
            @endif
        @elseif($bonusapa=="bonusgagal")
            @if($jenis=="index")
                <button class="btn btn-primary waves-effect waves-light" onclick="showLaporanBonusGagal()" type="submit">
                    Show Bonus
                </button>
            @endif
        @endif
    </div>
    <div id="tblBonus">

    </div> <!-- end row -->
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

    <!-- Datepicker -->
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection

@section('script-js')

<script type="text/javascript">

    $(document).ready(function () {
        ajx_coa();
        jQuery('#tgl_transaksi').datepicker();
        // Responsive Datatable
        $('#responsive-datatable').DataTable();

        $('.image-popup').magnificPopup({
            type: 'image',
        });

        // Select2
        $("#bulan").select2({
            templateResult: formatState,
            templateSelection: formatState
        });
        $("#tahun").select2({
            templateResult: formatState,
            templateSelection: formatState
        });
        $("#perusahaan").select2({
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

    function ajx_coa(){
        $("#rekening").select2({
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

    function showBonusPerhitungan(){
            var bln = $("#bulan").val()
            var thn = $("#tahun").val()
            var perusahaan = $("#perusahaan").val()
            $.ajax({
                url         :   "{{route('showBonusPerhitungan')}}",
                data        :   {
                    tahun : thn,
                    bulan : bln,
                    perusahaan : perusahaan,
                },
                type		:	"GET",
                dataType    :   "html",
                success		:	function(data){
                    $("#tblBonus").html(data);
                },
                error       :   function(data){
                    document.getElementById('tahun').value = '2018';
                }
            });
    }

    function createBonusPerhitungan(){
            var bln = $("#bulan").val()
            var thn = $("#tahun").val()
            var perusahaan = $("#perusahaan").val()
            $.ajax({
                url         :   "{{route('createBonusPerhitungan')}}",
                data        :   {
                    tahun : thn,
                    bulan : bln,
                    perusahaan : perusahaan,
                },
                type		:	"GET",
                dataType    :   "html",
                success		:	function(data){
                    $("#tblBonus").html(data);
                },
                error       :   function(data){
                    document.getElementById('tahun').value = '2016';
                }
            });
    }

    function showBonusPenerimaan(){
        var tgl = $("#tgl_transaksi").val()
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        var rekening = $("#rekening").val()
        $.ajax({
            url         :   "{{route('showBonusPenerimaan')}}",
            data        :   {
                tgl : tgl,
                tahun : thn,
                bulan : bln,
                rkng : rekening,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tblBonus").html(data);
            },
            error       :   function(data){
                document.getElementById('tgl_transaksi').value = '1945-08-17';
            }
        });
    }

    function createBonusPenerimaan(){
        var tgl = $("#tgl_transaksi").val()
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        var rekening = $("#rekening").val()
        $.ajax({
            url         :   "{{route('createBonusPenerimaan')}}",
            data        :   {
                tgl : tgl,
                tahun : thn,
                bulan : bln,
                rkng : rekening,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tblBonus").html(data);
            },
            error       :   function(data){
                document.getElementById('tgl_transaksi').value = '1945-08-17';
            }
        });
    }

    function showBonusTopup(){
        var tgl = $("#tgl_transaksi").val()
        var rekening = $("#rekening").val()
        $.ajax({
            url         :   "{{route('showBonusTopup')}}",
            data        :   {
                tgl : tgl,
                rkng : rekening,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tblBonus").html(data);
            },
            error       :   function(data){
                document.getElementById('tgl_transaksi').value = '1945-08-17';
            }
        });
    }

    function createBonusTopup(){
        var tgl = $("#tgl_transaksi").val()
        var rekening = $("#rekening").val()
        $.ajax({
            url         :   "{{route('createBonusTopup')}}",
            data        :   {
                tgl : tgl,
                rkng : rekening,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tblBonus").html(data);
            },
            error       :   function(data){
                document.getElementById('tgl_transaksi').value = '1945-08-17';
            }
        });
    }

    function showLaporanBonus(){
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        $.ajax({
            url         :   "{{route('showLaporanBonus')}}",
            data        :   {
                bulan : bln,
                tahun : thn,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tblBonus").html(data);
            },
            error       :   function(data){
                document.getElementById('tahun').value = '2018';
            }
        });
    }

    function showLaporanBonusGagal(){
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        var perusahaan = $("#perusahaan").val()
        $.ajax({
            url         :   "{{route('showLaporanBonusGagal')}}",
            data        :   {
                bulan : bln,
                tahun : thn,
                prshn : perusahaan,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tblBonus").html(data);
            },
            error       :   function(data){
                document.getElementById('tahun').value = '2018';
            }
        });
    }
</script>
@endsection
