@extends('layout.main')
@php
    use App\Coa;
@endphp

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        table {border: none;}
    </style>
@endsection

@section('judul')
    Laporan Neraca Per XX-XX-XXXX
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <h4 class="m-t-0 header-title"></h4>

            <table id="responsive-datatable" class="table dt-responsive nowrap">
                <tbody>
                    <tr>
                        <td>1. ASSET</td>
                        <td></td>
                        <td></td>
                        <td>Rp 1.000.000,00</td>
                    </tr>
                    @foreach (Coa::where('AccParent',1)->where('AccNo','NOT LIKE',1)->where('AccNo','NOT LIKE','1.10')->get() as $item)
                    <tr>
                        <td>&emsp;{{$item->AccNo}} {{$item->AccName}}</td>
                        <td></td>
                        <td>Rp 1.000.000,00</td>
                        <td></td>
                    </tr>
                        @foreach (Coa::where('AccParent',$item->AccNo)->where('AccNo','NOT LIKE',$item->AccNo)->get() as $item2)
                        <tr>
                            <td>&emsp;&emsp;{{$item2->AccNo}} {{$item2->AccName}}</td>
                            <td></td>
                            <td>Rp 1.000.000,00</td>
                            <td></td>
                        </tr>
                            @foreach (Coa::where('AccParent',$item2->AccNo)->where('AccNo','NOT LIKE',$item2->AccNo)->get() as $item3)
                            <tr>
                                <td>&emsp;&emsp;&emsp;{{$item3->AccNo}} {{$item3->AccName}}</td>
                                <td></td>
                                <td>Rp 1.000.000,00</td>
                                <td></td>
                            </tr>
                                @foreach (Coa::where('AccParent',$item3->AccNo)->where('AccNo','NOT LIKE',$item3->AccNo)->get() as $item4)
                                <tr>
                                    <td>&emsp;&emsp;&emsp;&emsp;{{$item4->AccNo}} {{$item4->AccName}}</td>
                                    <td></td>
                                    <td>Rp 1.000.000,00</td>
                                    <td></td>
                                </tr>
                                    @foreach (Coa::where('AccParent',$item4->AccNo)->where('AccNo','NOT LIKE',$item4->AccNo)->get() as $item5)
                                    <tr>
                                        <td>&emsp;&emsp;&emsp;&emsp;&emsp;{{$item4->AccNo}} {{$item4->AccName}}</td>
                                        <td>Rp 1.000.000,00</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                        @foreach (Coa::where('AccParent',$item5->AccNo)->where('AccNo','NOT LIKE',$item5->AccNo)->get() as $item6)
                                        <tr>
                                            <td>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{{$item6->AccNo}} {{$item6->AccName}}</td>
                                            <td>Rp 1.000.000,00</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
@endsection

@section('script-js')
    
@endsection