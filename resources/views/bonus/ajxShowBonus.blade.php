@php
    use Illuminate\Support\Facades\DB;
    use App\Member;
    use App\BankMember;
    use App\Bank;
    use App\PerusahaanMember;
    use App\Perusahaan;
    use App\Bonus;
    use App\BonusBayar;
    use App\TopUpBonus;
    use App\PurchaseDetail;
@endphp
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
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <h4 class="m-t-0 header-title">Detail Bonus Member</h4>
            @if($bonusapa=="estimasi")
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Nama Perusahaan</th>
                        <th>Estimasi Bonus</th>
                        <th>Perhitungan Bonus</th>
                        <th>Selisih</th>
                    </thead>

                    <tbody>
                        @php
                            $i = 1;
                            $selisih = 0;
                        @endphp
                        @foreach($data as $d)
                            @php
                                $estimasi = 0;
                                $purchasedetail = PurchaseDetail::join('tblpotrx', 'tblpotrxdet.trx_id', 'tblpotrx.id')->where('tblpotrx.month', $bulan)->where('tblpotrx.year', $tahun)->where('tblpotrx.supplier', $d->supplier)->select(DB::raw('(tblpotrxdet.price_dist - tblpotrxdet.price) * tblpotrxdet.qty AS bonus'))->get();
                                foreach($purchasedetail as $pd){
                                    $estimasi += $pd['bonus'];
                                }
                                $bonus = Bonus::where('bulan', $bulan)->where('tahun', $tahun)->where('perusahaan_id', $d->supplier)->sum('bonus');
                                $selisih = $estimasi - $bonus;
                            @endphp
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$d->nama}}</td>
                                <td>Rp {{ number_format($estimasi, 2, ",", ".") }}</td>
                                <td>Rp {{ number_format($bonus, 2, ",", ".") }}</td>
                                @if($selisih != 0)
                                    <td class="table-danger">
                                @else
                                    <td class="table-success">
                                @endif
                                    Rp {{ number_format($selisih, 2, ",", ".") }}
                                </td>
                                @php
                                    $i++;
                                @endphp
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <input type="hidden" name="ctr" value="{{ $i }}"/>
            @elseif($bonusapa=="laporan")
                <form class="form-horizontal" role="form" action="{{ route('exportBonus') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <div class="form-group text-right m-b-0">
                        <button class="btn btn-success btn-trans btn-rounded waves-effect waves-light w-xs m-b-5">
                            <span class="mdi mdi-file-excel">
                                Cetak Excel
                            </span>
                        </button>
                        <input type="hidden" name="bonusapa" value="{{ $bonusapa }}">
                        <input type="hidden" name="id_jurnal" value="">
                        <input type="hidden" name="bulan" id="bulan" value="{{ $bulan }}">
                        <input type="hidden" name="tahun" id="tahun" value="{{ $tahun }}">
                    </div>
                </form>
                <table id="realisasi-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>KTP</th>
                        <th>Nama</th>
                        <th>Perhitungan Bonus</th>
                        <th>Detail Realisasi Bonus</th>
                        <th>Selisih</th>
                    </thead>

                    {{-- <tbody>
                        @php
                            $i = 1;
                            $total_bonus = 0;
                            $selisih = 0;
                        @endphp
                        @foreach($member as $m)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{$m['ktp']}}</td>
                                <td>{{$m['nama']}}</td>
                                <td>
                                    @foreach($m['perhitungan'] as $mp)
                                        {{ $mp['noid']}}<br>
                                        <b>&ensp;&ensp;{{ $mp['bonus']}}</b><br>
                                    @endforeach
                                    <br><b>{{$m['ttl_perhitungan']}}</b>
                                </td>
                                <td>
                                    @foreach($m['penerimaan'] as $mb)
                                        {{ $mb['norek'] }}<br>
                                        <b>&ensp;&ensp;{{ $mb['bonus'] }}</b><br>
                                        &ensp;&ensp;{{ $mb['tgl'] }}<br>
                                    @endforeach
                                    <br><b>{{$m['ttl_penerimaan']}}</b>
                                </td>
                                @if($m['selisih'] <= 50000 AND $m['selisih'] > 0)
                                    <td style="background-color:#ffdf7b">
                                @elseif($m['selisih'] > 50000)
                                    <td style="background-color:#ff8484">
                                @elseif($m['selisih'] == 0)
                                    <td style="background-color:#62ff60">
                                @elseif($m['selisih'] < 0)
                                    <td style="background-color:#7d6dff">
                                @endif
                                    Rp {{ number_format($m['selisih'], 2, ",", ".")}}</td>
                            </tr>
                        @endforeach
                    </tbody> --}}
                </table>
                <input type="hidden" id="offset">
            @endif
            @if($bonusapa=="perhitungan" OR $bonusapa=="pembayaran" OR $bonusapa=="topup" OR $bonusapa=="bonusgagal")
                <div class="form-group row">
                    <label class="col-2 col-form-label">Total Bonus</label>
                    <div class="col-10">
                        <input type="text" class="form-control number" parsley-trigger="change" required name="total_bonus" id="total_bonus" value="@isset($total_bonus){{ $total_bonus }}@endisset" readonly="readonly">
                    </div>
                </div>
                @if($bonusapa=="perhitungan")
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Estimasi Bonus</label>
                        <div class="col-10">
                            <input type="text" class="form-control number" parsley-trigger="change" required name="estimasi_bonus" id="estimasi_bonus" value="@isset($estimasi_bonus){{ $estimasi_bonus }}@endisset" readonly="readonly">
                        </div>
                    </div>
                @elseif($bonusapa=="pembayaran")
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Piutang Bonus Tertahan</label>
                        <div class="col-10">
                            <input type="text" class="form-control number" min="0" parsley-trigger="change" required name="bonus_tertahan" id="bonus_tertahan" value="{{ $bonus_tertahan }}" readonly="readonly">
                        </div>
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Responsive Datatable
        $('#responsive-datatable').DataTable();

        var offset = $("#offset").val();

        $('#realisasi-datatable').DataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                "url" : "{{ route('showLaporanBonus') }}",
                "type" : "POST",
                "data" : {
                    "bulan" : $("#bulan").val(),
                    "tahun" : $("#tahun").val(),
                    "offset" : $("#offset").val(),
                    "_token" : $("meta[name='csrf-token']").attr("content"),
                },"dataFilter" : function(data){
                    var obj = JSON.parse(data);
                    offset = parseInt(obj.offset);
                    console.log($("#offset").val())
                    $("#offset").val(offset);
                    console.log($("#offset").val())

                    return JSON.stringify(obj);

                },
            },"columns" : [{data : "no", name : "no", searchable : false},
                    {data : "ktp", name : "ktp"},
                    {data : "nama", name : "nama"},
                    {data : "perhitungan", name : "perhitungan", orderable : false, searchable : false},
                    {data : "penerimaan", name : "penerimaan", orderable : false, searchable : false},
                    {data : "selisih", name : "selisih", searchable : false, render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' )},
            ],"columnDefs" : [
                {
                    targets: '_all',
                    type: 'natural'
                },
                {
                    "targets": [ 5 ],
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        if ((sData <= 50000) && (sData > 0)) {
                            $(nTd).css('background-color', '#ffdf7b').css('font-weight', 'bold');
                        } else if (sData > 50000) {
                            $(nTd).css('background-color', '#ff8484').css('font-weight', 'bold');
                        } else if (sData == 0) {
                            $(nTd).css('background-color', '#62ff60').css('font-weight', 'bold');
                        } else if (sData < 0) {
                            $(nTd).css('background-color', '#7d6dff').css('font-weight', 'bold');
                        }
                    },
                },
            ],oLanguage : {sProcessing: "<div id='loader'></div>"},
        });
    });
</script>
