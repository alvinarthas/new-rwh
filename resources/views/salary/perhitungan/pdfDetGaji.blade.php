<!DOCTYPE html>
<html>
<head>
    <title>Detail Gaji</title>
    <!-- App css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
<style type="text/css">
    img {
    border-radius: 50%;
    }

    th, td {
        border: 2px solid black;
        text-align: left;
        padding: 10px;
    }

    table{
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }
</style>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="card-box">
            <div class="row">
                <div class="form-group col-sm-4">
                    <img src="{{ asset('assets/images/employee/foto/'.$saldet->employee->scanfoto) }}" alt="logo" width="200px">
                </div>
                <div class="form-group col-sm-4">
                    <h2>{{$saldet->employee->name}}</h2>
                    <h2>{{$saldet->employee->nip}}</h2>
                    <h4>Take Home Pay: <strong> Rp {{number_format($saldet->take_home_pay,2,",",".")}}</strong></h4>
                    <h4>Total Bonus: <strong> Rp {{number_format($saldet->bonus,2,",",".")}}</strong> </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2"></div>

    <hr>

    {{-- <div class="row"> --}}
        <div class="col-md-2"></div>
        <div class="col-md-4">
            <div class="card-box">
                <h4 class="card-title">Bonus Pegawai</h4>
                <ul class="list-group m-b-0 user-list">
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/small-icon/internal.png') }}" alt="logo" width="30px">
                            </div>
                            <div class="user-desc">
                                <span class="name"><strong>Tugas Internal</strong></span>
                                <span class="desc">Rp {{number_format($bonpeg->tugas_internal,2,",",".")}}</span>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/small-icon/logistic.png') }}" alt="logo" width="30px">
                            </div>
                            <div class="user-desc">
                                <span class="name"><strong>Logistik</strong></span>
                                <span class="desc">Rp {{number_format($bonpeg->logistik,2,",",".")}}</span>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/small-icon/company.png') }}" alt="logo" width="30px">
                            </div>
                            <div class="user-desc">
                                <span class="name"><strong>Kendali Perusahaan</strong></span>
                                <span class="desc">Rp {{number_format($bonpeg->kendali_perusahaan,2,",",".")}}</span>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/small-icon/top3.png') }}" alt="logo" width="30px">
                            </div>
                            <div class="user-desc">
                                <span class="name"><strong>Top 3 Posting</strong></span>
                                <span class="desc">Rp {{number_format($bonpeg->top3,2,",",".")}}</span>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/small-icon/eom.png') }}" alt="logo" width="30px">
                            </div>
                            <div class="user-desc">
                                <span class="name"><strong>Employee Of the Month</strong></span>
                                <span class="desc">Rp {{number_format($bonpeg->eom,2,",",".")}}</span>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/small-icon/bonus.png') }}" alt="logo" width="30px">
                            </div>
                            <div class="user-desc">
                                <span class="name"><strong>Bonus Divisi</strong></span>
                                <span class="desc">Rp {{number_format($bonpeg->bonus_divisi,2,",",".")}}</span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-box">
                <h4 class="card-title">Detail Bonus Pegawai</h4>
                <ul class="list-group m-b-0 user-list">
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/small-icon/internal.png') }}" alt="logo" width="30px">
                            </div>
                            <div class="user-desc">
                                <span class="name"><strong>Poin Internal</strong></span>
                                <span class="desc"> <strong> {{$bonpegdet->poin_internal}} </strong>({{$bonpegdet->persen_internal}}%)</span>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/small-icon/logistic.png') }}" alt="logo" width="30px">
                            </div>
                            <div class="user-desc">
                                <span class="name"><strong>Poin Logistik</strong></span>
                                <span class="desc"><strong> {{$bonpegdet->poin_logistik}} </strong> ({{$bonpegdet->persen_logistik}}%)</span>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/small-icon/company.png') }}" alt="logo" width="30px">
                            </div>
                            <div class="user-desc">
                                <span class="name"><strong>Poin Kendali Perusahaan</strong></span>
                                <span class="desc"><strong> {{$bonpegdet->poin_kendali}} </strong> ({{$bonpegdet->persen_kendali}}%)</span>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/small-icon/top3.png') }}" alt="logo" width="30px">
                            </div>
                            <div class="user-desc">
                                <span class="name"><strong>Poin Top 3 Posting</strong></span>
                                <span class="desc"><strong> {{$bonpegdet->poin_top3}} </strong> ({{$bonpegdet->persen_top3}}%)</span>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/small-icon/discount.png') }}" alt="logo" width="30px">
                            </div>
                            <div class="user-desc">
                                <span class="name"><strong>Tunjangan Presentase</strong></span>
                                <span class="desc"><strong>({{$bonpegdet->tunjangan_persen}}%)</strong></span>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/small-icon/discount2.png') }}" alt="logo" width="30px">
                            </div>
                            <div class="user-desc">
                                <span class="name"><strong>Total Presentase</strong></span>
                                <span class="desc"><strong>({{$bonpegdet->total_persen}}%)</strong></span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-md-2"></div>
    {{-- </div> --}}
</div>
</body>
</html>
