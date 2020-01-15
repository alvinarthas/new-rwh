<style>
    table {border: none;}
</style>

<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <h4 class="m-t-0 header-title">Laporan Laba/Rugi Periode {{$start}} s/d {{$end}}</h4>

            <table id="responsive-datatable" class="table dt-responsive nowrap">
                <tbody>
                    <tr>
                        <td>Nett Sales</td>
                        <td></td>
                        <td></td>
                        <td>Rp {{number_format($nett_sales,2,',','.')}}</td>
                    </tr>
                    <tr>
                        <td>COGS</td>
                        <td></td>
                        <td></td>
                        <td>Rp ({{number_format($cogs,2,',','.')}})</td>
                    </tr>
                    <tr>
                        <td>Gross Profit</td>
                        <td></td>
                        <td></td>
                        <td>Rp {{number_format($gross_profit,2,',','.')}}</td>
                    </tr>
                    <tr>
                        <td>{{$biayaa['name']}}</td>
                        <td></td>
                        <td></td>
                        <td>Rp ({{number_format($biayaa['amount'],2,',','.')}})</td>
                    </tr>
                    
                    @foreach ($biayaa['data'] as $key1)
                        <tr>
                            <td>&emsp;&emsp;{{$key1['name']}}</td>
                            <td></td>
                            <td>Rp {{number_format($key1['amount'],2,',','.')}}</td>
                            <td></td>
                        </tr>
                        @foreach ($key1['data'] as $key2)
                            <tr>
                                <td>&emsp;&emsp;&emsp;&emsp;{{$key2['name']}}</td>
                                <td>Rp {{number_format($key2['amount'],2,',','.')}}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endforeach

                    <tr>
                        <td>Laba/Rugi Bersih Operasional</td>
                        <td></td>
                        <td></td>
                        <td>Rp {{number_format($laba_operasional,2,',','.')}}</td>
                    </tr>

                    <tr>
                        <td>{{$laba_bersih_non['name']}}</td>
                        <td></td>
                        <td></td>
                        <td>Rp {{number_format($laba_bersih_non['amount'],2,',','.')}}</td>
                    </tr>
                    @foreach ($laba_bersih_non['data'] as $item1)
                        
                        <tr>
                            <td>&emsp;&emsp;{{$item1['name']}}</td>
                            <td></td>
                            @if ($item1['no'] == '6.3' || $item1['no'] == '6.4')
                                <td>Rp ({{number_format($item1['amount'],2,',','.')}})</td>
                            @else
                                <td>Rp {{number_format($item1['amount'],2,',','.')}}</td>
                            @endif
                            
                            <td></td>
                        </tr>
                        @foreach ($item1['data'] as $item2)
                            <tr>
                                <td>&emsp;&emsp;&emsp;&emsp;{{$item2['name']}}</td>
                                <td>Rp {{number_format($item2['amount'],2,',','.')}}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endforeach

                    @foreach($laba_rugi as $laba)
                        <tr>
                            <td>{{$laba['name']}}</td>
                            <td></td>
                            <td></td>
                            <td>Rp {{number_format($laba['amount'],2,',','.')}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><strong>Nett Profit</strong></td>
                        <td></td>
                        <td></td>
                        <td><strong>Rp {{number_format($nett_profit,2,',','.')}}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>