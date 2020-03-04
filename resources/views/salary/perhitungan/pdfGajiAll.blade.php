<!DOCTYPE html>
<html>
<head>
    <title>Detail Gaji</title>
</head>
<body>
<style type="text/css">
    th, td {
        border: 1px solid black;
        text-align: left;
        padding: 1px;
        font-size: 8px;
    }

    table{
        font-family: arial, sans-serif;
        border-collapse: collapse;
    }

    h6 {
        display: block;
        font-size: .67em;
        margin-top: 1em;
        margin-bottom: 1em;
        margin-left: 0;
        margin-right: 0;
        font-weight: bold;
        text-align: center;
    }

    li {
        font-size: 10px;
    }
</style>

<div class="row" id="print-area">
    <div class="col-md-12">
        {{-- <div class="clearfix"> --}}
            {{-- <center> --}}
                <h6><b>DETAIL GAJI KARYAWAN BULAN {{strtoupper(date("F", mktime(0, 0, 0, $bulan, 10)))}} {{$tahun}}</b></h6>
            {{-- </center> --}}
        {{-- </div> --}}
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    {{-- <font size="1" face="Courier New" > --}}
                    <li>Periode Gaji      : <b>{{date("F", mktime(0, 0, 0, $bulan, 10))}} {{$tahun}}</b></li>
                    <li>Total BV          : <b>Rp {{number_format($salary->bv, 2, ",", ".")}}</b></li>
                    <li>Jumlah Hari Kerja : <b>{{$salary->hari_kerja}}</b></li>
                    {{-- </font> --}}
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        {{-- <font size="1" face="Courier New" > --}}
                        <table class="table m-t-30">
                            <thead>
                                <tr>
                                <th>No</th>
                                <th>No Induk Karyawan</th>
                                <th>Nama Karyawan</th>
                                <th style="word-wrap: break-word;">Jumlah Share Tugas Internal</th>
                                <th>Percentage (%)</th>
                                <th style="word-wrap: break-word;">Jumlah Share Logistik</th>
                                <th>Percentage (%)</th>
                                <th style="word-wrap: break-word;">Share Perusahaan Posting</th>
                                <th>Percentage (%)</th>
                                <th style="word-wrap: break-word;">3 Perusahaan Posting Besar</th>
                                <th>Percentage (%)</th>
                                <th style="word-wrap: break-word;">Tunjangan Percentage (%)</th>
                                <th style="word-wrap: break-word;">Total of Percentage</th>
                                </tr>
                            </thead>
                            @php
                                $i=0;
                            @endphp
                            <tbody>
                                @foreach ($bonpegdet as $a)
                                    <tr>
                                        <td>{{++$i}}</td>
                                        <td>{{$a->employee->nip}}</td>
                                        <td>{{$a->employee->name}}</td>
                                        <td style="text-align: right;">{{number_format($a->poin_internal, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">{{number_format($a->persen_internal, 2, ",", ".")}}%</td>
                                        <td style="text-align: right;">{{number_format($a->poin_logistik, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">{{number_format($a->persen_logistik, 2, ",", ".")}}%</td>
                                        <td style="text-align: right;">{{number_format($a->poin_kendali, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">{{number_format($a->persen_kendali, 2, ",", ".")}}%</td>
                                        <td style="text-align: right;">{{number_format($a->poin_top3, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">{{number_format($a->persen_top3, 2, ",", ".")}}%</td>
                                        <td style="text-align: right;">{{number_format($a->tunjangan_persen, 2, ",", ".")}}%</td>
                                        <td style="text-align: right;">{{number_format($a->total_persen, 2, ",", ".")}}%</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td style="text-align: right;" colspan="3"><b>TOTAL</b></td>
                                    <td style="text-align: right;"><b>{{number_format($sub_bonpegdet->total_poin_internal, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>{{number_format($sub_bonpegdet->total_persen_internal, 2, ",", ".")}}%</b></td>
                                    <td style="text-align: right;"><b>{{number_format($sub_bonpegdet->total_poin_logistik, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>{{number_format($sub_bonpegdet->total_persen_logistik, 2, ",", ".")}}%</b></td>
                                    <td style="text-align: right;"><b>{{number_format($sub_bonpegdet->total_poin_kendali, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>{{number_format($sub_bonpegdet->total_persen_kendali, 2, ",", ".")}}%</b></td>
                                    <td style="text-align: right;"><b>{{number_format($sub_bonpegdet->total_poin_top3, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>{{number_format($sub_bonpegdet->total_persen_top3, 2, ",", ".")}}%</b></td>
                                    <td style="text-align: right;"><b>{{number_format($sub_bonpegdet->total_tunjangan_persen, 2, ",", ".")}}%</b></td>
                                    <td style="text-align: right;"><b>{{number_format($sub_bonpegdet->total_total_persen, 2, ",", ".")}}%</b></td>
                                </tr>
                            </tbody>
                        </table>
                        {{-- </font> --}}
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        {{-- <font size="1" face="Courier New" > --}}
                        <table class="table m-t-30">
                            <thead>
                                <tr>
                                <th>No</th>
                                <th>No Induk Karyawan</th>
                                <th>Nama Karyawan</th>
                                <th style="word-wrap: break-word;">Tugas Internal 30%</th>
                                <th>Logistik 25%</th>
                                <th style="word-wrap: break-word;">Perusahaan yang Posting 10%</th>
                                <th style="word-wrap: break-word;">3 Perusahaan Posting Besar 0%</th>
                                <th style="text-align: center;">EOM</th>
                                <th style="text-align: center;">Total Bonus</th>
                                </tr>
                            </thead>
                            @php
                                $i=0;
                            @endphp
                            <tbody>
                                @foreach($bonpeg as $b)
                                    <tr>
                                        <td>{{++$i}}</td>
                                        <td>{{$b->employee->nip}}</td>
                                        <td>{{$b->employee->name}}</td>
                                        <td style="text-align: right;">Rp {{number_format($b->tugas_internal, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">Rp {{number_format($b->logistik, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">Rp {{number_format($b->kendali_perusahaan, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">Rp {{number_format($b->top3, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">Rp {{number_format($b->eom, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">Rp {{number_format($b->total_bonus, 2, ",", ".")}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td style="text-align: right;" colspan="3"><b>TOTAL</b></td>
                                    <td style="text-align: right;"><b>Rp {{number_format($sub_bonpeg->total_tugas_inernal, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>Rp {{number_format($sub_bonpeg->total_logistik, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>Rp {{number_format($sub_bonpeg->total_kendali_perusahaan, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>Rp {{number_format($sub_bonpeg->total_top3, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>Rp {{number_format($sub_bonpeg->total_eom, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>Rp {{number_format($sub_bonpeg->total_total_bonus, 2, ",", ".")}}</b></td>
                                </tr>
                            </tbody>
                        </table>
                        {{-- </font> --}}
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        {{-- <font size="1" face="Courier New" > --}}
                        <table class="table m-t-30">
                            <thead>
                                <tr>
                                <th>No</th>
                                <th>No Induk Karyawan</th>
                                <th>Nama Karyawan</th>
                                <th>Total Bonus</th>
                                <th style="text-align: center;">Gaji Pokok</th>
                                <th style="text-align: center;">Tunjangan</th>
                                <th style="word-wrap: break-word;">Temporary Take Home Pay</th>
                                <th>Bonus Lemburan</th>
                                <th>Bonus Jabatan</th>
                                <th>Take Home Pay</th>
                                </tr>
                            </thead>
                            @php
                                $i=0;
                            @endphp
                            <tbody>
                                @foreach($saldet as $c)
                                    <tr>
                                        <td>{{++$i}}</td>
                                        <td>{{$c->employee->nip}}</td>
                                        <td>{{$c->employee->name}}</td>
                                        <td style="text-align: right;">Rp {{number_format($c->bonus, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">Rp {{number_format($c->gaji_pokok, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">Rp {{number_format($c->tunjangan_jabatan, 2, ",", ".")}}</td>
                                        @php
                                            $temptakehomepay = $c->gaji_pokok + $c->tunjangan_jabatan;
                                        @endphp
                                        <td style="text-align: right;">Rp {{number_format($temptakehomepay, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">Rp {{number_format($c->bonus_divisi, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">Rp {{number_format($c->bonus_jabatan, 2, ",", ".")}}</td>
                                        <td style="text-align: right;">Rp {{number_format($c->take_home_pay, 2, ",", ".")}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td style="text-align: right;" colspan="3"><b>TOTAL</b></td>
                                    <td style="text-align: right;"><b>Rp {{number_format($sub_saldet->total_bonus, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>Rp {{number_format($sub_saldet->total_gaji_pokok, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>Rp {{number_format($sub_saldet->total_tunjangan_jabatan, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>Rp {{number_format($sub_saldet->total_temp_take_home_pay, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>Rp {{number_format($sub_saldet->total_bonus_divisi, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>Rp {{number_format($sub_saldet->total_bonus_jabatan, 2, ",", ".")}}</b></td>
                                    <td style="text-align: right;"><b>Rp {{number_format($sub_saldet->total_take_home_pay, 2, ",", ".")}}</b></td>
                                </tr>
                            </tbody>
                        </table>
                        {{-- </font> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
