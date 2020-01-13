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
    Laporan Laba/Rugi
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <h4 class="m-t-0 header-title">Laporan Laba/Rugi Periode XXXXX s/d XXXXX</h4>

            <table id="responsive-datatable" class="table dt-responsive nowrap">
                <tbody>
                    <tr>
                        <td>Nett Sales</td>
                        <td></td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                    </tr>
                    <tr>
                        <td>COGS</td>
                        <td></td>
                        <td></td>
                        <td>Rp (10.000.000,00)</td>
                    </tr>
                    <tr>
                        <td>Gross Profit</td>
                        <td></td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                    </tr>
                    <tr>
                        <td>Biaya-Biaya</td>
                        <td></td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                    </tr>
                    @foreach (Coa::where('AccParent','6')->where('AccNo','NOT LIKE','6')->Where('AccNo','NOT LIKE','6.3')->Where('AccNo','NOT LIKE','6.4')->where('StatusAccount','Grup')->get() as $item)
                    <tr>
                        <td>&emsp;&emsp;{{$item->AccName}}</td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                        <td></td>
                    </tr>
                        @foreach (Coa::where('AccParent',$item->AccNo)->where('StatusAccount','Detail')->get() as $item2)
                        <tr>
                            <td>&emsp;&emsp;&emsp;&emsp;{{$item2->AccName}}</td>
                            <td>Rp 10.000.000,00</td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endforeach
                    {{-- <tr>
                        <td>&emsp;&emsp;<strong>Total {{$item->AccName}}</strong></td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                        <td></td>
                    </tr> --}}
                    @endforeach
                    {{-- <tr>
                        <td>Total Biaya</td>
                        <td></td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                    </tr> --}}
                    @foreach (Coa::where('AccParent','7')->where('AccNo','NOT LIKE','7.3')->where('AccNo','NOT LIKE','7.4')->where('StatusAccount','Detail')->get() as $item5)
                    <tr>
                        <td>{{$item5->AccName}}</td>
                        <td></td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td>Laba/Rugi Bersih Operasional</td>
                        <td></td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                    </tr>
                    <tr>
                        <td>Laba/Rugi Bersih Non Operasional</td>
                        <td></td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                    </tr>
                    @foreach (Coa::where('AccNO','4.3')->orWhere('AccNo','4.4')->where('StatusAccount','Grup')->get() as $item3)
                    <tr>
                        <td>&emsp;&emsp;{{$item3->AccName}}</td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                        <td></td>
                    </tr>
                        @foreach (Coa::where('AccParent',$item3->AccNo)->where('StatusAccount','Detail')->get() as $item4)
                        <tr>
                            <td>&emsp;&emsp;&emsp;&emsp;{{$item4->AccName}}</td>
                            <td>Rp 10.000.000,00</td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endforeach
                    @endforeach
                    @foreach (Coa::where('AccNO','6.3')->orWhere('AccNo','6.4')->where('StatusAccount','Grup')->get() as $item3)
                    <tr>
                        <td>&emsp;&emsp;{{$item3->AccName}}</td>
                        <td></td>
                        <td>Rp (10.000.000,00)</td>
                        <td></td>
                    </tr>
                        @foreach (Coa::where('AccParent',$item3->AccNo)->where('StatusAccount','Detail')->get() as $item4)
                        <tr>
                            <td>&emsp;&emsp;&emsp;&emsp;{{$item4->AccName}}</td>
                            <td>Rp 10.000.000,00</td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endforeach
                    @endforeach
                    @foreach (Coa::where('AccParent','7')->where('AccNo','NOT LIKE','7.1')->where('AccNo','NOT LIKE','7.2')->where('StatusAccount','Detail')->get() as $item5)
                    <tr>
                        <td>{{$item5->AccName}}</td>
                        <td></td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td><strong>Nett Profit</strong></td>
                        <td></td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                    </tr>
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