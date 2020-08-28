@php
    use App\BankMember;
    $i=1;
@endphp

{{-- Select2 --}}
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

<style>
    #loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 50px;
        height: 50px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
        margin-left:10px;
        margin-right:10px;
        margin-top:10px;
    }
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<form class="form-horizontal" role="form" action="{{ route('exportMember') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="modal fade bs-example-modal-lg" role="dialog" aria-labelledby="myLargeModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Menu Cetak</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Pilih Cetak</label>
                        <div class="col-10">
                            <select class="form-control select2a" parsley-trigger="change" name="menu" id="menu" required>
                                <option value="#" selected disabled>Pilih Jenis Cetak</option>
                                <option value="0">Cetak Seluruh Isi Tabel</option>
                                <option value="1">Cetak Data Tertentu</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="body2" class="modal-body" style="display:none">
                    <div id="search_member" class="form-group row">
                        <label class="col-7 col-form-label">Tambahkan List Member yang akan dicetak</label>
                        <div class="col-7">
                            <select class="form-control" id="s_member" name="s_member" parsley-trigger="change" onchange="addRowCetak(this.value)">
                            </select>
                        </div>
                    </div>
                    <table id="table" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th width="13%">Nama</th>
                                <th width="13%">ID Member</th>
                                <th width="12%">No KTP</th>
                                <th width="27%">Alamat</th>
                                <th width="20%">Tempat Tanggal Lahir</th>
                                <th width="5%">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <input type="hidden" name="counts" id="counts" value="0">
                        </tbody>
                    </table>
                    <input type="hidden" name="ctr" id="ctr" value="{{ $i }}">
                    <input type="hidden" name="prs" id="prs">
                    <input type="hidden" name="bnk" id="bnk">
                    <input type="hidden" name="jns" id="jns">
                    <input type="hidden" name="xto" id="xto">
                </div>
                <div id="btn_cetak" class="modal-body form-group text-right m-b-0" style="display:none">
                    <button class="btn btn-rounded btn-success w-md waves-effect waves-light m-b-5" onclick="clickXls()">Cetak file Excel</button>
                    <button class="btn btn-rounded btn-warning w-md waves-effect waves-light m-b-5" onclick="clickPdf()">Cetak file PDF</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
</form>
<div class="card-box">
    <table id="load" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Gambar KTP</th>
                <th>Gambar Tabungan</th>
                <th>Gambar ATM</th>
                <th>Status Rekening</th>
                <th>Status Cetak</th>
            </tr>
        </thead>
    </table>
    <input type="hidden" name="perusahaan" id="perusahaan" value="@isset($perusahaan){{ $perusahaan }}@endisset">
    <input type="hidden" name="bank" id="bank" value="@isset($bank){{ $bank }}"@endisset>
    <input type="hidden" name="jenis" id="jenis" value="@isset($jenis){{ $jenis }}@endisset">
    <input type="hidden" name="statusrek" id="statusrek" value="@isset($statusrek){{ $statusrek }}@endisset">
    <input type="hidden" name="statusnoid" id="statusnoid" value="@isset($statusnoid){{ $statusnoid }}@endisset">

    <div class="form-group text-right m-b-0">
        <button class="btn btn-rounded btn-inverse w-md waves-effect waves-light m-b-5" data-toggle="modal" data-target=".bs-example-modal-lg">Cetak</button>
    </div>
</div>

    {{-- Select2 --}}
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

<script>
    $(".select2a").select2({
        templateResult: formatState,
        templateSelection: formatState,
    });

    $(document).ready(function () {
        $('#load').DataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                "url" : "{{ route('getDataMember') }}",
                "type" : "POST",
                "data" : {
                    "perusahaan" : $("#perusahaan").val(),
                    "bank" : $("#bank").val(),
                    "jenis" : $("#jenis").val(),
                    "statusrek" : $("#statusrek").val(),
                    "statusnoid" : $("#statusnoid").val(),
                    "_token" : $("meta[name='csrf-token']").attr("content"),
                }
            },"columns" : [{data : "no", name : "no"},
                {data : "nama", name : "nama"},
                {data : "gambar_ktp",name : "gambar_ktp", searchable:false, orderable:false},
                {data : "gambar_tabungan", name : "gambar_tabungan", searchable:false, orderable:false},
                {data : "gambar_atm", name : "gambar_atm", searchable:false, orderable:false},
                {data : "status_rekening", name : "status_rekening", searchable:false, orderable:false},
                {data : "status_cetak", name : "status_cetak", searchable:false, orderable:false},
            ],"columnDefs" : [
                {
                    targets: '_all',
                    type: 'natural'
                }
            ],"order": [[ 1, "asc" ]
            ],oLanguage : {sProcessing: "<div id='loader'></div>"},
        });

        ajxMemberOrder();
        $('#menu').on('change', function () {
            id = $('#menu').val();
            pr = $('#perusahaan').val();
            bn = $('#bank').val();
            jn = $('#jenis').val();
            console.log(id)
            if(id == 0){
                // document.getElementById("table").style.display = 'none';
                // document.getElementById("search_member").style.display = 'none';
                document.getElementById("body2").style.display = 'none';
                $("#table-body").children().remove();
            }else if(id == 1){
                // document.getElementById("table").style.display = 'block';
                // document.getElementById("search_member").style.display = 'block';
                document.getElementById("body2").style.display = 'block';
            }else{
                // document.getElementById("table").style.display = 'none';
                // document.getElementById("search_member").style.display = 'none';
                document.getElementById("body2").style.display = 'none';
                $("#table-body").children().remove();
            }
            document.getElementById("btn_cetak").style.display = "block";
            $('#prs').val(pr);
            $('#bnk').val(bn);
            $('#jns').val(jn);
            console.log(pr,bn,jn);
        });
    });

    function formatState (opt) {
        if (!opt.id){
            return opt.text.toUpperCase();
        }
        var optimage = $(opt.element).attr('data-image');
        console.log(optimage)
        if(!optimage){
            return opt.text.toUpperCase();
        }else{
            var $opt = $(
            '<span><img src="' + optimage + '" width="60px" /> ' + opt.text.toUpperCase() + '</span>'
            );
            return $opt;
        }
    };

    function ajxMemberOrder(){
        var jns = $('#jenis').val();
        var prs = $('#perusahaan').val();
        var bnk = $('#bank').val();

        $("#s_member").select2({
            templateResult: formatState,
            templateSelection: formatState,
            placeholder:'Cari Member',
            ajax:{
                url: "{{route('ajxMemberOrder')}}",
                dataType:'json',
                delay:250,
                data:function(params){
                    return{
                        keyword:params.term,
                        bankid:bnk,
                        perusahaanid:prs,
                        jenis:jns,
                    };
                },
                processResults:function(data){
                    var item = $.map(data, (value)=>{ //map buat ngemap object data kyk foreach
                        return { id: value.id, text: value.ktp+" - "+value.nama};
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

    function addRowCetak(id){
        var token = $("meta[name='csrf-token']").attr("modal-content");
        var cnt = $("#ctr").val();
        console.log(cnt)
        $.ajax({
            url : "{{route('ajxAddRowCetak')}}",
            type : "GET",
            dataType: 'json',
            data:{
                id_member : id,
                count : cnt,
                _token : token,
            },
        }).done(function (data) {
            $('#table-body').append(data.append);
            var cnt = parseInt($('#ctr').val()) + 1;
            $('#ctr').val(cnt);
            resetall();
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function resetall(){
        $('#s_member').empty().trigger('click')
    }

    function deleteItem(id){
        count = parseInt($('#ctr').val());
        $('#trow'+id).remove();
        $('#ctr').val(count);
    }

    function clickXls(){
        $('#xto').val("0");
    }

    function clickPdf(){
        $('#xto').val("1");
    }
</script>
