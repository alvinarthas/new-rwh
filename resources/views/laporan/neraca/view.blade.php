<style>
    table {border: none;}
</style>

<div class="row">
    <div class="col-12">
        <h4 class="m-t-0 header-title">Laporan Neraca Per {{$date}}</h4><hr>
        <h4 class="m-t-0 header-title">ASSET</h4><hr>
        <div class="card-box table-responsive">

            <table id="responsive-datatable" class="table dt-responsive nowrap">
                <tbody>
                    <tr>
                        <td><strong>{{$assets['no']}} {{$assets['name']}}</strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><strong>Rp {{number_format($assets['amount'],2,',','.')}}</strong></td>
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
                </tbody>
            </table>
        </div>

        <hr><h4 class="m-t-0 header-title">HUTANG</h4><hr>
        <div class="card-box table-responsive">

            <table id="responsive-datatable" class="table dt-responsive nowrap">
                <tbody>
                    <tr>
                        <td><strong>{{$hutangs['no']}} {{$hutangs['name']}}</strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><strong>Rp {{number_format($hutangs['amount'],2,',','.')}}</strong></td>
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
                </tbody>
            </table>
        </div>

        <hr><h4 class="m-t-0 header-title">Modal</h4><hr>
        <div class="card-box table-responsive">

            <table id="responsive-datatable" class="table dt-responsive nowrap">
                <tbody>
                    <tr>
                        <td><strong>3.1 Modal</strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><strong>Rp {{number_format($modal,2,',','.')}}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>