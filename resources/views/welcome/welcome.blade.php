@extends('layout.main')
@php
    use App\Coa;
@endphp

@section('css')
<style>
    span.desc{
        font-size: 14px;
    }
</style>
@endsection

@section('judul')

@endsection

@section('content')
@php
    use Carbon\Carbon;
@endphp
{{-- <div class="card-box">
    <div class="row">
        <div class="col-md-12">
            <div class="home-fullscreen">
                <div class="full-screen">
                    <div class="home-wrapper home-wrapper-alt">
                        <div class="row">
                            <div class="form-group col-md-2">
                                <img src="{{ asset('assets/images/employee/foto/'.session('foto')) }}"  alt="user-img" title="{{ session('name') }}" class="rounded-circle img-thumbnail img-responsive photo">
                            </div>
                            <div class="form-group col-md-8">
                                <h1 class="text-dark">Hai {{ session('name') }},</h1>
                                @php
                                    Carbon::setLocale('id');
                                    $getTime = Carbon::now()->toTimeString();
                                    if($getTime < '12:00:00'){
                                        $time = "PAGI";
                                    }elseif(($getTime >='12:00:00') && ($getTime < '15:00:00')){
                                        $time = "SIANG";
                                    }elseif(($getTime >='15:00:00') && ($getTime < '18:00:00')){
                                        $time = "SORE";
                                    }elseif($getTime >='18:00:00'){
                                        $time = "MALAM";
                                    }
                                @endphp
                                <p class="text-dark">SELAMAT {{ $time }}, DAN SELAMAT BEKERJA!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}
{{-- <div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="form-group col-md-1">
            </div>
            <div class="form-group col-md-8">
                <h1 class="text-dark">Hai {{ session('name') }},</h1>
                @php
                    Carbon::setLocale('id');
                    $getTime = Carbon::now()->toTimeString();
                    if($getTime < '12:00:00'){
                        $time = "PAGI";
                    }elseif(($getTime >='12:00:00') && ($getTime < '15:00:00')){
                        $time = "SIANG";
                    }elseif(($getTime >='15:00:00') && ($getTime < '18:00:00')){
                        $time = "SORE";
                    }elseif($getTime >='18:00:00'){
                        $time = "MALAM";
                    }
                @endphp
                <p class="text-dark">SELAMAT {{ $time }}, DAN SELAMAT BEKERJA!</p>
            </div>
        </div>
    </div>
</div> --}}

<div class="card-box">
    <div class="row">
        <div class="form-group col-md-8">
            <h1 class="text-dark">Hai {{ session('name') }},</h1>
            @php
                Carbon::setLocale('id');
                $getTime = Carbon::now()->toTimeString();
                if($getTime < '12:00:00'){
                    $time = "PAGI";
                }elseif(($getTime >='12:00:00') && ($getTime < '15:00:00')){
                    $time = "SIANG";
                }elseif(($getTime >='15:00:00') && ($getTime < '18:00:00')){
                    $time = "SORE";
                }elseif($getTime >='18:00:00'){
                    $time = "MALAM";
                }
            @endphp
            <p class="text-dark"><strong>SELAMAT {{ $time }}, DAN SELAMAT BEKERJA!</strong></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <img src="{{ asset('assets/images/employee/foto/'.session('foto')) }}"  alt="user-img" title="{{ session('name') }}" class="rounded-circle img-thumbnail img-responsive photo">
        </div>
        <div class="form-group col-md-8" style="font-size:initial;">
            <div class="form-group row">
                <label class="col-2 col-form-label">Nama:</label>
                <label class="col-10 col-form-label">{{$user->name}}</label>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">NIP:</label>
                <label class="col-10 col-form-label">{{$user->nip}}</label>
            </div>
            @if(session('role') != 'Direktur Utama')
            <hr>
            <h4 style="color:mediumblue"><strong>Bonus dan Gaji Per Hari ini</strong></h4>
            <hr>
            <div class="row">
                <div class="col-sm-6">
                    <ul class="list-group m-b-0 user-list">
                        <li class="list-group-item">
                            <div class="user-list-item">
                                <div class="avatar">
                                    <img src="{{ asset('assets/images/flaticon/internal.png') }}" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name"><strong>Tugas Internal</strong></span>
                                    <span class="desc">Rp {{number_format($bonus['internal'],2,",",".")}}</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="user-list-item">
                                <div class="avatar">
                                    <img src="{{ asset('assets/images/flaticon/logistic.png') }}" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name"><strong>Tugas Logistik</strong></span>
                                    <span class="desc">Rp {{number_format($bonus['logistik'],2,",",".")}}</span>
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
                                    <span class="desc">Rp {{number_format($bonus['kendali'],2,",",".")}}</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <ul class="list-group m-b-0 user-list">
                        <li class="list-group-item">
                            <div class="user-list-item">
                                <div class="avatar">
                                    <img src="{{ asset('assets/images/flaticon/discount.png') }}" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name"><strong>Gaji Pokok & Tunjangan Jabatan</strong></span>
                                    <span class="desc">Rp {{number_format($bonus['gaji_pokok'],2,",",".")}} - Rp {{number_format($bonus['tunjangan_jabatan'],2,",",".")}}</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="user-list-item">
                                <div class="avatar">
                                    <img src="{{ asset('assets/images/flaticon/bonus.png') }}" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name"><strong>Total Bonus sekarang</strong></span>
                                    <span class="desc">Rp {{number_format($bonus['total_bonus'],2,",",".")}}</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="user-list-item">
                                <div class="avatar">
                                    <img src="{{ asset('assets/images/flaticon/money.png') }}" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name"><strong>Take Home Pay</strong></span>
                                    <span class="desc">Rp {{number_format($bonus['take_home_pay'],2,",",".")}}</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
    @if(session('role') == 'Superadmin' || session('role') == 'Direktur Utama')
        <div class="card-box table-responsive">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a href="#hutang" data-toggle="tab" aria-expanded="false" class="nav-link active">
                        Hutang
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#piutang" data-toggle="tab" aria-expanded="true" class="nav-link">
                        Piutang
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#kas" data-toggle="tab" aria-expanded="true" class="nav-link">
                        Kas/Bank
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="hutang">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-box table-responsive">
                                <h4>SISA HUTANG SUPPLIER</h4>
                                <table id="datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Purchase Order</th>
                                            <th>Sisa Hutang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($i=1)
                                        @foreach ($hutang as $item)
                                            @if($item['sisa'] > 20000000)
                                                <tr>
                                                    <td>{{$i}}</td>
                                                    <td>PO.{{$item['id']}}</td>
                                                    <td><strong>Rp {{number_format($item['sisa'],2,',','.')}}</strong></td>
                                                </tr>
                                                @php($i++)
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="piutang">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-box table-responsive">
                                <h4>SISA PIUTANG</h4>
                                <table id="datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Sales Order</th>
                                            <th>Sisa Piutang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($i=1)
                                        @foreach ($piutang as $item2)
                                            @if($item2['sisa'] > 20000000)
                                                <tr>
                                                    <td>{{$i}}</td>
                                                    <td>SO.{{$item2['id']}}</td>
                                                    <td><strong>Rp {{number_format($item2['sisa'],2,',','.')}}</strong></td>
                                                </tr>
                                                @php($i++)
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="kas">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-box table-responsive">
                                <h4>KAS / Bank</h4>
                                <table id="datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Account</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($coa = Coa::where('AccParent',"1.1.1")->where('AccNo','NOT LIKE',"1.1.1")->get())
                                        @php(Coa::kasBank($coa))
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('js')

@endsection

@section('script-js')

@endsection
