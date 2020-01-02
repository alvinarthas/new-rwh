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
    Laporan Perubahan Modal Periode XXXXX s/d XXXXX
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <h4 class="m-t-0 header-title"></h4>

            <table id="responsive-datatable" class="table dt-responsive nowrap">
                <tbody>
                    <tr>
                        <td>Modal Awal</td>
                        <td></td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                    </tr>
                    <tr>
                        <td>Setoran Modal</td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Prive/Pengeluaran Pribadi</td>
                        <td></td>
                        <td>Rp (10.000.000,00)</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Nett Profit/Loss</td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Perubahan Modal</td>
                        <td></td>
                        <td></td>
                        <td>Rp 10.000.000,00</td>
                    </tr>
                    <tr>
                        <td><strong>Modal Akhir</strong></td>
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