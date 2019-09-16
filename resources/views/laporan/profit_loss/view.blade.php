<div class="card-box">
    <div class="form-group row">
        <label class="col-2 col-form-label">Start Date</label>
        <div class="col-10">
            <input type="text" class="form-control" parsley-trigger="change" value="{{$start}}" readonly>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-2 col-form-label">End Date</label>
        <div class="col-10">
            <input type="text" class="form-control" parsley-trigger="change" value="{{$end}}" readonly>
        </div>
    </div>
</div>

<div class="card-box table-responsive">
    <h4 class="m-t-0 header-title">Sales Revenue</h4>
    <table id="tbl-salesrev" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead style="background-color:lightblue;">
            <th>Account Number</th>
            <th>Account Name</th>
            <th>Amount</th>
        </thead>
        <tbody>
            @foreach ($sales_revenues as $sales_revenue)
                <tr>
                    <td style="align:left">{{$sales_revenue['AccNo']}}</td>
                    <td style="align:left">{{$sales_revenue['AccName']}}</td>
                    <td style="align:right">Rp. {{number_format($sales_revenue['total'])}}</td>
                </tr>
            @endforeach
    </table>
    <div class="form-group row">
        <label class="col-2 col-form-label">Total Pendapatan</label>
        <div class="col-10">
            <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($total_pendapatan)}}" readonly>
        </div>
    </div>
</div>

<div class="card-box table-responsive">
    <h4 class="m-t-0 header-title">COGS</h4>
    <table id="tbl-salesrev" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead style="background-color:lightblue;">
            <th>Account Number</th>
            <th>Account Name</th>
            <th>Amount</th>
        </thead>
        <tbody>
            @foreach ($cogss as $cogs)
                <tr>
                    <td style="align:left">{{$cogs['AccNo']}}</td>
                    <td style="align:left">{{$cogs['AccName']}}</td>
                    <td style="align:right">Rp. {{number_format($cogs['total'])}}</td>
                </tr>
            @endforeach
    </table>
    <div class="form-group row">
        <label class="col-2 col-form-label">Total Pendapatan</label>
        <div class="col-10">
            <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($total_cogs)}}" readonly>
        </div>
    </div>
</div>

<div class="card-box table-responsive">
    <h4 class="m-t-0 header-title">Expenses</h4>
    <table id="tbl-expenses" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead style="background-color:lightblue;">
            <th>Account Number</th>
            <th>Account Name</th>
            <th>Amount</th>
        </thead>
        <tbody>
            @foreach ($expensess as $expenses)
                <tr>
                    <td style="align:left">{{$expenses['AccNo']}}</td>
                    <td style="align:left">{{$expenses['AccName']}}</td>
                    <td style="align:right">Rp. {{number_format($expenses['total'])}}</td>
                </tr>
            @endforeach
    </table>
    <div class="form-group row">
        <label class="col-2 col-form-label">Total Pendapatan &amp; Beban Lainnya</label>
        <div class="col-10">
            <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($total_expense)}}" readonly>
        </div>
    </div>
</div>

<div class="card-box table-responsive">
    <h4 class="m-t-0 header-title">Pendapatan & Beban Lainnya</h4>
    <table id="tbl-salesrev" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead style="background-color:lightblue;">
            <th>Account Number</th>
            <th>Account Name</th>
            <th>Amount</th>
        </thead>
        <tbody>
            @foreach ($pendapatans as $pendapatan)
                <tr>
                    <td style="align:left">{{$pendapatan['AccNo']}}</td>
                    <td style="align:left">{{$pendapatan['AccName']}}</td>
                    <td style="align:right">Rp. {{number_format($pendapatan['total'])}}</td>
                </tr>
            @endforeach
    </table>
    <div class="form-group row">
        <label class="col-2 col-form-label">Total Pendapatan &amp; Beban Lainnya</label>
        <div class="col-10">
            <input type="text" class="form-control" parsley-trigger="change" value="Rp. {{number_format($total_pendapatan_lain)}}" readonly>
        </div>
    </div>
</div>

<div class="card-box">
        <div class="form-group row">
            <label class="col-2 col-form-label">Laba / Rugi</label>
            <div class="col-10">
                <input type="text" class="form-control" parsley-trigger="change" value="{{$profit_loss}}" readonly>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-2 col-form-label">Selisih Anggaran vs Pembayaran Bonus</label>
            <div class="col-10">
                <input type="text" class="form-control" parsley-trigger="change" value="{{$datalabarugi}}" readonly>
            </div>
        </div>
    </div>

<script>
    $('#tbl-expenses').DataTable();
</script>