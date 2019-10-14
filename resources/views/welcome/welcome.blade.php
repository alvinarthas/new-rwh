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
<div class="bg-overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="home-fullscreen">
                    <div class="full-screen">
                        <div class="home-wrapper home-wrapper-alt">
                            <h1 class="text-dark">Hai {{ session('name') }},</h1>
                            @php
                                Carbon::setLocale('id');
                                $getTime = Carbon::now()->setTimezone('Asia/Phnom_Penh')->toTimeString();
                                if(($getTime>'11:00:00') && ($getTime<='15:00:00')){
                                    $time = "SIANG";
                                }elseif(($getTime>'15:00:00') && ($getTime<='18:00:00')){
                                    $time = "SORE";
                                }elseif(($getTime>'18:00:00') && ($getTime<='00:00:00')){
                                    $time = "MALAM";
                                }else{
                                    $time = "PAGI";
                                }
                            @endphp
                            <p class="text-dark">SELAMAT {{ $time }}, DAN SELAMAT BEKERJA!</p>
                        </div>
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
