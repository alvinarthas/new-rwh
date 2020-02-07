@extends('layout.main')
@php
    use App\Modul;
    use App\SubModul;
@endphp

@section('css')

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
            <p class="text-dark">SELAMAT {{ $time }}, DAN SELAMAT BEKERJA!</p>
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
            {{-- <div class="form-group row">
                <label class="col-2 col-form-label">Gaji Pokok:</label>
                <label class="col-10 col-form-label">Rp 2.200.000,00</label>
            </div> --}}
            <hr>
            <h4 style="color:mediumblue"><strong>Poin dan Bonus</strong></h4>
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
                                    <span class="name"><strong>Poin Internal</strong></span>
                                    <span class="desc">10</span>
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
                                    <span class="desc">10</span>
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
                                    <span class="name"><strong>Gaji Pokok</strong></span>
                                    <span class="desc">Rp {{number_format(3500000,2,",",".")}}</span>
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
                                    <span class="desc">Rp {{number_format(100000,2,",",".")}}</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

@endsection

@section('script-js')

@endsection
