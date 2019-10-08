<div class="card-box table-responsive">
    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead style="background-color:lightblue;">
            <th colspan="4">LAPORAN PERUBAHAN MODAL (Per: s/d {{$end}})</th>
        </thead>
        <tbody>
            <tr>
                <td colspan="2" style="align:left">Modal Awal</td>
                <td colspan="2" style="align:right">Rp. {{number_format($saldoawal)}}</td>
            </tr>
            <tr>
                <td colspan="2" style="align:left">Laba / Rugi</td>
                <td colspan="2" style="align:right">Rp. {{number_format($profit_loss)}}</td>
            </tr>
            <tr>
                <td colspan="2" style="align:left">Setoran Modal</td>
                <td colspan="2" style="align:right">Rp. {{number_format($setoran_modal)}}</td>
            </tr>
            <tr>
                <td colspan="2" style="align:left">Pengeluaran Pribadi</td>
                <td colspan="2" style="align:right">Rp. {{number_format($total_expense_pribadi)}}</td>
            </tr>
        </tbody>
        <tfoot style="text-align:">
            <td colspan="2" style="align:left"><strong>Modal Akhir</strong></td>
            <td colspan="2" style="align:right"><strong>Rp. {{number_format($modal_akhir)}}</strong></td>
        </tfoot>
    </table>
</div>