@extends('layout.main')

@section('css')
@endsection

@section('judul')
Balance Sheet (Neraca Saldo Awal)
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">Balance Sheet(Neraca Saldo Awal)</h4>
                    
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th colspan="2" style="text-align:center;background:dodgerblue">Aktiva</th>
                        </thead>
                        <tbody>
                            @foreach ($dataaktvia as $item)
                                <tr style="font-weight: bold">
                                    <td>{{$item['grup']['AccNo']}} - {{$item['grup']['AccName']}}</td>
                                    <td style="text-align:right">Rp. {{number_format($item['sum'])}}</td>
                                </tr>

                                @foreach ($item['data'] as $item2)
                                    <tr>
                                        <td>&ensp;&ensp;{{$item2['AccNo']}} - {{$item2['AccName']}}</td>
                                        <td style="text-align:right">Rp. {{number_format($item2['SaldoAwal'])}}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                        <tfoot>
                            <td colspan="2" style="text-align:center;font-weight: bold">Total Aktiva: Rp. {{number_format($sum_aktiva)}}</td>
                        </tfoot>
                    </table>
                    <br><br><br>
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th colspan="2" style="text-align:center;background:lightseagreen">Pasiva</th>
                        </thead>
                        <tbody>
                            @foreach ($datapasiva as $item3)
                                <tr style="font-weight: bold">
                                    <td>{{$item3['grup']['AccNo']}} - {{$item3['grup']['AccName']}}</td>
                                    <td style="text-align:right">Rp. {{number_format($item3['sum'])}}</td>
                                </tr>

                                @foreach ($item3['data'] as $item4)
                                    <tr>
                                        <td>&ensp;&ensp;{{$item4['AccNo']}} - {{$item4['AccName']}}</td>
                                        <td style="text-align:right">Rp. {{number_format($item4['SaldoAwal'])}}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                        <tfoot>
                            <td colspan="2" style="text-align:center;font-weight: bold">Total Pasiva: Rp. {{number_format($sum_pasiva)}}</td>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection