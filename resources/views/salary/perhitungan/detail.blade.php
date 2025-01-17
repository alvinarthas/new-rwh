<div class="text-right">
    <input type="hidden" id="route{{$saldet->employee->id}}" value="{{route('detGajiPegawai',['employee' => $saldet->employee->id, 'bulan' =>$bulan,'tahun' => $tahun])}}">
    <a href="javascript:;" class="btn btn-danger btn-trans btn-rounded waves-effect w-md waves-danger m-b-5" onclick="printPdf({{$saldet->employee->id}})"><i class="fa fa-file-pdf-o"></i> Print Detail Gaji</a>
</div>
<div class="container">
    <div class="row">
        <div class="card-box">
            <div class="row">
                <div class="form-group col-sm-2">
                    <img src="{{ asset('assets/images/employee/foto/'.$saldet->employee->scanfoto) }}"  alt="user-img" title="{{ $saldet->employee->name }}" class="rounded-circle img-thumbnail img-responsive photo">
                </div>
                <div class="form-group col-sm-8">
                    <h2 class="text-dark">{{$saldet->employee->name}}</h2>
                    <h2 class="text-dark">{{$saldet->employee->nip}}</h2>
                    <h2 class="text-dark">Take Home Pay: <strong> Rp {{number_format($saldet->take_home_pay,2,",",".")}}</strong> <h2>
                    <h2 class="text-dark">Total Bonus: <strong> Rp {{number_format($saldet->bonus,2,",",".")}}</strong> <h2>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-sm-6">
            <div class="card-box">
                <h4 class="card-title">Bonus Pegawai</h4>
                <ul class="list-group m-b-0 user-list">
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/internal.png') }}" alt="">
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
                                <img src="{{ asset('assets/images/flaticon/logistic.png') }}" alt="">
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
                                <img src="{{ asset('assets/images/flaticon/company.png') }}" alt="">
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
                                <img src="{{ asset('assets/images/flaticon/top3.png') }}" alt="">
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
                                <img src="{{ asset('assets/images/flaticon/eom.png') }}" alt="">
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
                                <img src="{{ asset('assets/images/flaticon/bonus.png') }}" alt="">
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
        <div class="col-sm-6">
            <div class="card-box">
                <h4 class="card-title">Detail Bonus Pegawai</h4>
                <ul class="list-group m-b-0 user-list">
                    <li class="list-group-item">
                        <div class="user-list-item">
                            <div class="avatar">
                                <img src="{{ asset('assets/images/flaticon/internal.png') }}" alt="">
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
                                <img src="{{ asset('assets/images/flaticon/logistic.png') }}" alt="">
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
                                <img src="{{ asset('assets/images/flaticon/company.png') }}" alt="">
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
                                <img src="{{ asset('assets/images/flaticon/top3.png') }}" alt="">
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
                                <img src="{{ asset('assets/images/flaticon/discount.png') }}" alt="">
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
                                <img src="{{ asset('assets/images/flaticon/discount2.png') }}" alt="">
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
    </div>
</div>

<script type="text/javascript">
    function printPdf(id){
        windowUrl = $('#route'+id).val();
        // console.log(windowUrl)
        windowName = "Print Gaji";
        var printWindow = window.open(windowUrl, windowName, 'left=50000,top=50000,width=0,height=0');
        printWindow.focus();
        printWindow.print();
    }
</script>
