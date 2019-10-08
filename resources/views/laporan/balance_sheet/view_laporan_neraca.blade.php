<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <h4 class="m-t-0 header-title">Laporan Neraca</h4>
            
            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <th colspan="2" style="text-align:center;background:dodgerblue">Aktiva</th>
                </thead>
                <tbody>
                    @foreach ($dataaktiva as $item)
                        <tr style="font-weight: bold">
                            <td>{{$item['grup']['AccNo']}} - {{$item['grup']['AccName']}}</td>
                            <td style="text-align:right">Rp. {{number_format($item['sum'])}}</td>
                        </tr>

                        @foreach ($item['data'] as $item2)
                            <tr>
                                <td>&ensp;&ensp;{{$item2['data']['AccNo']}} - {{$item2['data']['AccName']}}</td>
                                <td style="text-align:right">Rp. {{number_format($item2['total'])}}</td>
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
                    <th colspan="2" style="text-align:center;background:dodgerblue">Pasiva</th>
                </thead>
                <tbody>
                    @foreach ($datapasiva as $itemp)
                        <tr style="font-weight: bold">
                            <td>{{$itemp['grup']['AccNo']}} - {{$itemp['grup']['AccName']}}</td>
                            <td style="text-align:right">Rp. {{number_format($itemp['sum'])}}</td>
                        </tr>

                        @foreach ($itemp['data'] as $itemp2)
                            <tr>
                                <td>&ensp;&ensp;{{$itemp2['data']['AccNo']}} - {{$itemp2['data']['AccName']}}</td>
                                <td style="text-align:right">Rp. {{number_format($itemp2['total'])}}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <td colspan="2" style="text-align:center;font-weight: bold">Total Aktiva: Rp. {{number_format($sum_pasiva)}}</td>
                </tfoot>
            </table>
        </div>
    </div>
</div>