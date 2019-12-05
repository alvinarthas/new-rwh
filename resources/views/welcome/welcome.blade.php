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
    </div>
@endsection

@section('js')

@endsection

@section('script-js')

@endsection
