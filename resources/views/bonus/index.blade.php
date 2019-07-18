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
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h2 class="l-h-34">Manage Bonus</h2>
                @if($bonusapa=="perhitungan")
                    <h4 class="m-t-0 header-title">Perhitungan Bonus Member</h4>
                    @if($jenis == "index")
                        <p class="text-muted font-14 m-b-30">
                            <a href="{{ route('bonus.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Perhitungan Bonus</a>
                        </p>
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
                @elseif($bonusapa=="pembayaran")
                    <h4 class="m-t-0 header-title">Pembayaran Bonus Member</h4>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Tanggal Transaksi</label>
                        <div class="col-10">
                            <div class="input-group">
                                <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="tgl_transaksi" id="tgl_transaksi"  value=""  data-date-format='yyyy-mm-dd' autocomplete="off">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="ti-calendar"></i></span>
                                </div>
                            </div><!-- input-group -->
                        </div>
                    </div>
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
                @if($bonusapa=="pembayaran")
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Rekening Bank Tujuan</label>
                        <div class="col-10">
                            <select class="form-control select2" parsley-trigger="change" id="rekening" name="rekening">
                                <option value="#" disabled selected>Pilih Rekening</option>
                                @foreach ($rekenings as $rek)
                                    <option value="{{$rek->id}}">{{$rek->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
                <button class="btn btn-primary waves-effect waves-light" onclick="createBonusPerhitungan()" type="submit">
                    Create Bonus
                </button>
            @endif
        @elseif($bonusapa=="pembayaran")
        <button class="btn btn-primary waves-effect waves-light" onclick="createBonusPembayaran()" type="submit">
            Show Bonus
        </button>
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
        // Responsive Datatable
        $('#responsive-datatable').DataTable();

        $('.image-popup').magnificPopup({
            type: 'image',
        });

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
                    console.log(data)
                },
                error       :   function(data){
                    document.getElementById('tahun').value = '2018';
                }
            });
    }
    function createBonusPerhitungan(){
            var bln = $("#bulan").val()
            var thn = $("#tahun").val()
            console.log(bln)
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
                    console.log(data)
                },
                error       :   function(data){
                    document.getElementById('tahun').value = '2018';
                }
            });
    }

    function createBonusPembayaran(){
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        console.log(bln)
        var rekening = $("#rekening").val()
        $.ajax({
            url         :   "{{route('createBonusPerhitungan')}}",
            data        :   {
                tahun : thn,
                bulan : bln,
                rekening : rekening,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tblBonus").html(data);
                console.log(data)
            },
            error       :   function(data){
                document.getElementById('tahun').value = '2018';
            }
        });
    }
</script>
@endsection
