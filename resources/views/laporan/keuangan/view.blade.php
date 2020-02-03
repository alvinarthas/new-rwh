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
                            <td><strong>Nett Sales</strong></td>
                            <td></td>
                            <td></td>
                            <td>Rp {{number_format($nett_sales,2,',','.')}}</td>
                        </tr>
                        <tr>
                            <td><strong>COGS</strong></td>
                            <td></td>
                            <td></td>
                            <td>(Rp {{number_format($cogs,2,',','.')}})</td>
                        </tr>
                        <tr>
                            <td><strong>Gross Profit</strong></td>
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
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach ($key1['data'] as $key2)
                                <tr>
                                    <td>&emsp;&emsp;&emsp;&emsp;{{$key2['name']}}</td>
                                    @if ($key2['amount'] < 0)
                                    <td>(Rp {{number_format($key2['amount']*-1,2,',','.')}})</td>
                                    @else
                                    <td>Rp {{number_format($key2['amount'],2,',','.')}}</td>
                                    @endif
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>&emsp;&emsp;<strong>Total {{$key1['name']}}</strong></td>
                                <td></td>
                                @if ($key1['amount'] < 0)
                                <td>(Rp {{number_format($key1['amount']*-1,2,',','.')}})</td>
                                @else
                                <td>Rp {{number_format($key1['amount'],2,',','.')}}</td>
                                @endif
                                <td></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td><strong>Total Biaya</strong></td>
                            <td></td>
                            <td></td>
                            @if ($biayaa['amount'] < 0)
                            <td>(Rp {{number_format($biayaa['amount']*-1,2,',','.')}})</td>
                            @else
                            <td>Rp {{number_format($biayaa['amount'],2,',','.')}}</td>
                            @endif
                        </tr>

                        <tr>
                            <td><strong>Laba/Rugi Bersih Operasional</strong></td>
                            <td></td>
                            <td></td>
                            @if ($laba_operasional < 0)
                            <td>(Rp {{number_format($laba_operasional*-1,2,',','.')}})</td>
                            @else
                            <td>Rp {{number_format($laba_operasional,2,',','.')}}</td>
                            @endif
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
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach ($item1['data'] as $item2)
                                <tr>
                                    <td>&emsp;&emsp;&emsp;&emsp;{{$item2['name']}}</td>
                                    @if ($item2['amount'] < 0)
                                    <td>(Rp {{number_format($item2['amount']*-1,2,',','.')}})</td>
                                    @else
                                    <td>Rp {{number_format($item2['amount'],2,',','.')}}</td>
                                    @endif
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>&emsp;&emsp;<strong>Total {{$item1['name']}}</strong></td>
                                <td></td>
                                @if ($item1['amount'] < 0)
                                <td>(Rp {{number_format($item1['amount']*-1,2,',','.')}})</td>
                                @else
                                <td>Rp {{number_format($item1['amount'],2,',','.')}}</td>
                                @endif
                                <td></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td><strong>Total Laba/Rugi Bersih Non Operasional</strong></td>
                            <td></td>
                            <td></td>
                            @if ($laba_bersih_non['amount'] < 0)
                            <td>(Rp {{number_format($laba_bersih_non['amount']*-1,2,',','.')}})</td>
                            @else
                            <td>Rp {{number_format($laba_bersih_non['amount'],2,',','.')}}</td>
                            @endif
                        </tr>

                        @foreach($laba_rugi as $laba)
                            <tr>
                                <td><strong>{{$laba['name']}}</strong></td>
                                <td></td>
                                <td></td>
                                @if ($laba['amount'] < 0)
                                <td>(Rp {{number_format($laba['amount']*-1,2,',','.')}})</td>
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
                            @if ($nett_profit < 0)
                                <td><strong>Nett Loss</strong></td>
                                <td></td>
                                <td></td>
                                <td>(Rp {{number_format($nett_profit*-1,2,',','.')}})</td>
                            @else
                                <td><strong>Nett Profit</strong></td>
                                <td></td>
                                <td></td>
                                <td>Rp {{number_format($nett_profit,2,',','.')}}</td>
                            @endif
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
                            <td><strong>Awal</strong></td>
                            <td></td>
                            <td></td>
                            @if ($modal_awal < 0)
                            <td>(Rp {{number_format($modal_awal*-1,2,',','.')}})</td>
                            @else
                            <td>Rp {{number_format($modal_awal,2,',','.')}}</td>
                            @endif
                        </tr>
                        <tr>
                            <td><strong>Setoran Modal</strong></td>
                            <td></td>
                            @if ($set_modal < 0)
                            <td>(Rp {{number_format($set_modal*-1,2,',','.')}})</td>
                            @else
                            <td>Rp {{number_format($set_modal,2,',','.')}}</td>
                            @endif
                            <td></td>
                        </tr>
                        <tr>
                            <td><strong>Prive/Pengeluaran Pribadi</strong></td>
                            <td></td>
                            @if ($prive < 0)
                            <td>(Rp {{number_format($prive*-1,2,',','.')}})</td>
                            @else
                            <td>Rp {{number_format($prive,2,',','.')}}</td>
                            @endif
                            <td></td>
                        </tr>
                        <tr>
                            <td><strong>Nett Profit/Loss</strong></td>
                            <td></td>
                            @if ($nett_profit < 0)
                            <td>(Rp {{number_format($nett_profit*-1,2,',','.')}})</td>
                            @else
                            <td>Rp {{number_format($nett_profit,2,',','.')}}</td>
                            @endif
                            <td></td>
                        </tr>
                        <tr>
                            <td><strong>Perubahan Modal</strong></td>
                            <td></td>
                            <td></td>
                            @if ($perubahan_modal < 0)
                            <td>(Rp {{number_format($perubahan_modal*-1,2,',','.')}})</td>
                            @else
                            <td>Rp {{number_format($perubahan_modal,2,',','.')}}</td>
                            @endif
                        </tr>
                        <tr>
                            <td><strong>Modal Akhir</strong></td>
                            <td></td>
                            <td></td>
                            @if ($modal_akhir < 0)
                            <td>(Rp {{number_format($modal_akhir*-1,2,',','.')}})</td>
                            @else
                            <td>Rp {{number_format($modal_akhir,2,',','.')}}</td>
                            @endif
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
                                    <td></td>
                                    <td></td>
                                </tr>
                                @foreach ($asset2['data'] as $asset3)
                                    <tr>
                                        <td>&emsp;&emsp;&emsp;&emsp;{{$asset3['no']}} {{$asset3['name']}}</td>
                                        <td></td>
                                        @if(count($asset3['data']) > 0)
                                            <td></td>
                                        @else
                                            @if ($asset3['amount'] < 0)
                                            <td>(Rp {{number_format($asset3['amount']*-1,2,',','.')}})</td>
                                            @else
                                            <td>Rp {{number_format($asset3['amount'],2,',','.')}}</td>
                                            @endif
                                        @endif
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    @foreach ($asset3['data'] as $asset4)
                                        <tr>
                                            <td>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{{$asset4['no']}} {{$asset4['name']}}</td>
                                            <td></td>
                                            @if(count($asset4['data']) > 0)
                                                <td></td>
                                            @else
                                            @if ($asset4['amount'] < 0)
                                            <td>(Rp {{number_format($asset4['amount']*-1,2,',','.')}})</td>
                                            @else
                                            <td>Rp {{number_format($asset4['amount'],2,',','.')}}</td>
                                            @endif
                                            @endif
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        @foreach ($asset4['data'] as $asset5)
                                            <tr>
                                                <td>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{{$asset5['no']}} {{$asset5['name']}}</td>
                                                @if(count($asset5['data']) > 0)
                                                <td></td>
                                                @else
                                                    @if ($asset5['amount'] < 0)
                                                    <td>(Rp {{number_format($asset5['amount']*-1,2,',','.')}})</td>
                                                    @else
                                                    <td>Rp {{number_format($asset5['amount'],2,',','.')}}</td>
                                                    @endif
                                                @endif
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            @foreach ($asset5['data'] as $asset6)
                                            <tr>
                                                <td>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;{{$asset6['no']}} {{$asset6['name']}}</td>
                                                @if ($asset6['amount'] < 0)
                                                <td>(Rp {{number_format($asset6['amount']*-1,2,',','.')}})</td>
                                                @else
                                                <td>Rp {{number_format($asset6['amount'],2,',','.')}}</td>
                                                @endif
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            @endforeach
                                            @if(count($asset5['data']) > 0)
                                            <tr>
                                                <td>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<strong>Total {{$asset5['name']}}</strong></td>
                                                @if ($asset5['amount'] < 0)
                                                <td>(Rp {{number_format($asset5['amount']*-1,2,',','.')}})</td>
                                                @else
                                                <td>Rp {{number_format($asset5['amount'],2,',','.')}}</td>
                                                @endif
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            @endif
                                            
                                        @endforeach
                                        @if(count($asset4['data']) > 0)
                                        <tr>
                                            <td>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<strong>Total {{$asset4['name']}}</strong></td>
                                            <td></td>
                                            @if ($asset4['amount'] < 0)
                                            <td>(Rp {{number_format($asset4['amount']*-1,2,',','.')}})</td>
                                            @else
                                            <td>Rp {{number_format($asset4['amount'],2,',','.')}}</td>
                                            @endif
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    @if(count($asset3['data']) > 0)
                                    <tr>
                                        <td>&emsp;&emsp;&emsp;&emsp;<strong>Total {{$asset3['name']}}</strong></td>
                                        <td></td>
                                        @if ($asset3['amount'] < 0)
                                            <td>(Rp {{number_format($asset3['amount']*-1,2,',','.')}})</td>
                                        @else
                                            <td>Rp {{number_format($asset3['amount'],2,',','.')}}</td>
                                        @endif
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    @endif
                                @endforeach
                                <tr>
                                    <td>&emsp;&emsp;<strong>Total {{$asset2['name']}}</strong></td>
                                    <td></td>
                                    <td></td>
                                    @if ($asset2['amount'] < 0)
                                    <td>(Rp {{number_format($asset2['amount']*-1,2,',','.')}})</td>
                                    @else
                                    <td>Rp {{number_format($asset2['amount'],2,',','.')}}</td>
                                    @endif
                                    <td></td>
                                </tr>
                            @endforeach
                            <tr>
                                <td><strong>Total {{$assets['name']}}</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                @if ($assets['amount'] < 0)
                                <td><strong>(Rp {{number_format($assets['amount']*-1,2,',','.')}})</strong></td>
                                @else
                                <td><strong>Rp {{number_format($assets['amount'],2,',','.')}}</strong></td>
                                @endif
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
                                    <td></td>
                                    <td></td>
                                </tr>
                                @foreach ($hutang2['data'] as $hutang3)
                                    <tr>
                                        <td>&emsp;&emsp;&emsp;&emsp;{{$hutang3['no']}} {{$hutang3['name']}}</td>
                                        <td></td>
                                        @if ($hutang3['amount'] < 0)
                                        <td>(Rp {{number_format($hutang3['amount']*-1,2,',','.')}})</td>
                                        @else
                                        <td>Rp {{number_format($hutang3['amount'],2,',','.')}}</td>
                                        @endif
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>&emsp;&emsp;<strong> Total {{$hutang2['name']}}</strong></td>
                                    <td></td>
                                    <td></td>
                                    @if ($hutang2['amount'] < 0)
                                    <td>(Rp {{number_format($hutang2['amount']*-1,2,',','.')}})</td>
                                    @else
                                    <td>Rp {{number_format($hutang2['amount'],2,',','.')}}</td>
                                    @endif
                                    <td></td>
                                </tr>
                            @endforeach       
                            <tr>
                                <td><strong>Total {{$hutangs['name']}}</strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                @if ($hutangs['amount'] < 0)
                                <td><strong>(Rp {{number_format($hutangs['amount']*-1,2,',','.')}})</strong></td>
                                @else
                                <td><strong>Rp {{number_format($hutangs['amount'],2,',','.')}}</strong></td>
                                @endif
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
                                @if ($modal_akhir < 0)
                                <td><strong>(Rp {{number_format($modal_akhir*-1,2,',','.')}})</strong></td>
                                @else
                                <td><strong>Rp {{number_format($modal_akhir,2,',','.')}}</strong></td>
                                @endif
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