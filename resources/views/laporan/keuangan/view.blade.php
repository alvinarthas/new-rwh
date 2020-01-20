<style>
    table {border: none;}
</style>

{{-- Profit Loss --}}
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <div class="pull-right">
                <input type="checkbox" checked id="switch1" data-plugin="switchery" data-color="#00b19d" data-size="small"/>
            </div>
            <h4 class="m-t-0 header-title">Laporan Laba/Rugi Periode {{$start}} s/d {{$end}}</h4>
            <div id="profitloss">
                <table id="responsive-datatable_profitloss" class="table dt-responsive nowrap">
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
                            <td></td>
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
                            <td><strong>&emsp;&emsp;Total Biaya</strong></td>
                            <td></td>
                            <td></td>
                            <td>(Rp {{number_format($biayaa['amount'],2,',','.')}})</td>
                        </tr>

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
                            <td></td>
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
                        <tr>
                            <td><strong>&emsp;&emsp;Total Laba/Rugi Bersih Non Operasional</strong></td>
                            <td></td>
                            <td></td>
                            <td>Rp {{number_format($laba_bersih_non['amount'],2,',','.')}}</td>
                        </tr>

                        @foreach($laba_rugi as $laba)
                            <tr>
                                <td>{{$laba['name']}}</td>
                                <td></td>
                                <td></td>
                                @if ($laba['amount'] < 0)
                                <td>(Rp {{number_format($laba['amount'],2,',','.')}})</td>
                                @else
                                <td>Rp {{number_format($laba['amount'],2,',','.')}}</td>
                                @endif
                                
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
</div>
{{-- Perubahan Modal --}}
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <div class="pull-right">
                <input type="checkbox" checked id="switch2" data-plugin="switchery" data-color="#ef5350" data-size="small"/>
            </div>
            <h4 class="m-t-0 header-title">Laporan Perubahan Modal Periode {{$start}} s/d {{$end}}</h4>
            <div id="perubahanModal">
                <table id="responsive-datatable_modal" class="table dt-responsive nowrap">
                    <tbody>
                        <tr>
                            <td>Modal Awal</td>
                            <td></td>
                            <td></td>
                            <td>Rp {{number_format($modal_awal,2,',','.')}}</td>
                        </tr>
                        <tr>
                            <td>Setoran Modal</td>
                            <td></td>
                            <td>Rp {{number_format($set_modal,2,',','.')}}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Prive/Pengeluaran Pribadi</td>
                            <td></td>
                            <td>Rp ({{number_format($prive,2,',','.')}})</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Nett Profit/Loss</td>
                            <td></td>
                            <td>Rp {{number_format($nett_profit,2,',','.')}}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Perubahan Modal</td>
                            <td></td>
                            <td></td>
                            <td>Rp {{number_format($perubahan_modal,2,',','.')}}</td>
                        </tr>
                        <tr>
                            <td><strong>Modal Akhir</strong></td>
                            <td></td>
                            <td></td>
                            <td>Rp {{number_format($modal_akhir,2,',','.')}}</td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- Neraca --}}
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <div class="pull-right">
                <input type="checkbox" checked id="switch3" data-plugin="switchery" data-color="#7266ba" data-size="small"/>
            </div>
            <h4 class="m-t-0 header-title">Laporan Neraca Per {{$end}}</h4>
            
            <div id="neraca">
                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">ASSET</h4>
                    <table id="responsive-datatable_neraca" class="table dt-responsive nowrap">
                        <tbody>
                            <tr>
                                <td><strong>{{$assets['no']}} {{$assets['name']}}</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach ($assets['data'] as $asset2)
                                <tr>
                                    <td>&emsp;&emsp;{{$asset2['no']}} {{$asset2['name']}}</td>
                                    <td></td>
                                    <td></td>
                                    <td>Rp {{number_format($asset2['amount'],2,',','.')}}</td>
                                    <td></td>
                                </tr>
                                @foreach ($asset2['data'] as $asset3)
                                    <tr>
                                        <td>&emsp;&emsp;&emsp;&emsp;{{$asset3['no']}} {{$asset3['name']}}</td>
                                        <td></td>
                                        <td>Rp {{number_format($asset3['amount'],2,',','.')}}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    @foreach ($asset3['data'] as $asset4)
                                        <tr>
                                            <td>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{{$asset4['no']}} {{$asset4['name']}}</td>
                                            <td></td>
                                            <td>Rp {{number_format($asset4['amount'],2,',','.')}}</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        @foreach ($asset4['data'] as $asset5)
                                            <tr>
                                                <td>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{{$asset5['no']}} {{$asset5['name']}}</td>
                                                <td>Rp {{number_format($asset5['amount'],2,',','.')}}</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endforeach
                            <tr>
                                <td><strong>Total {{$assets['name']}}</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><strong>Rp {{number_format($assets['amount'],2,',','.')}}</strong></td>
                            </tr>      
                        </tbody>
                    </table>
                </div>

                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">HUTANG</h4>
                    <table id="responsive-datatable" class="table dt-responsive nowrap">
                        <tbody>
                            <tr>
                                <td><strong>{{$hutangs['no']}} {{$hutangs['name']}}</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach ($hutangs['data'] as $hutang2)
                                <tr>
                                    <td>&emsp;&emsp;{{$hutang2['no']}} {{$hutang2['name']}}</td>
                                    <td></td>
                                    <td></td>
                                    <td>Rp {{number_format($hutang2['amount'],2,',','.')}}</td>
                                    <td></td>
                                </tr>
                                @foreach ($hutang2['data'] as $hutang3)
                                    <tr>
                                        <td>&emsp;&emsp;&emsp;&emsp;{{$hutang3['no']}} {{$hutang3['name']}}</td>
                                        <td></td>
                                        <td>Rp {{number_format($hutang3['amount'],2,',','.')}}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            @endforeach       
                            <tr>
                                <td><strong>Total {{$hutangs['name']}}</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><strong>Rp {{number_format($hutangs['amount'],2,',','.')}}</strong></td>
                            </tr>       
                        </tbody>
                    </table>
                </div>

                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">Modal</h4>
                    <table id="responsive-datatable" class="table dt-responsive nowrap">
                        <tbody>
                            <tr>
                                <td><strong>3.1 Modal</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><strong>Rp {{number_format($modal_akhir,2,',','.')}}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Switchery 1
switch1.onchange = function() {
    if(switch1.checked == true){
        document.getElementById("profitloss").style.display = 'block';
    }else{
        document.getElementById("profitloss").style.display = 'none';
    }
};

switch2.onchange = function() {
    if(switch2.checked == true){
        document.getElementById("perubahanModal").style.display = 'block';
    }else{
        document.getElementById("perubahanModal").style.display = 'none';
    }
};

switch3.onchange = function() {
    if(switch3.checked == true){
        document.getElementById("neraca").style.display = 'block';
    }else{
        document.getElementById("neraca").style.display = 'none';
    }
};

</script>