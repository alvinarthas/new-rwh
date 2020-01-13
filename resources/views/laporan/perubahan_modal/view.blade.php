<style>
    table {border: none;}
</style>

<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <h4 class="m-t-0 header-title">Laporan Perubahan Modal Periode {{$start}} s/d {{$end}}</h4>

            <table id="responsive-datatable" class="table dt-responsive nowrap">
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