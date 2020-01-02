<div class="card-box">
    <h4 class="m-t-0 header-title">Data COA</h4>
    <div class="col-12">
        <div class="p-20">
            <div class="form-group row">
                <label class="col-2 col-form-label">Account Name</label>
                <div class="col-10">
                <input type="text" class="form-control" parsley-trigger="change" value="{{$coa->AccName}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Account Number</label>
                <div class="col-10">
                        <input type="text" class="form-control" parsley-trigger="change" value="{{$coa->AccNo}}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card-box table-responsive">
    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <th>Jurnal ID</th>
            <th>Transaction Date</th>
            <th>Notes</th>
            <th>Description</th>
            <th>Debet</th>
            <th>Credit</th>
            <th>Balance</th>
        </thead>
        <tbody>
            @php($balance = 0)
            @foreach ($jurnals['data'] as $jurnal)
                <tr>
                    <td>{{$jurnal->id_jurnal}}</td>
                    <td>{{$jurnal->date}}</td>
                    <td>{{$jurnal->notes_item}}</td>
                    <td>{{$jurnal->description}}</td>
                    @if ($jurnal->AccPos == "Debet")
                        <td>Rp {{number_format($jurnal->Amount,2,",",".")}}</td>
                        @php($balance+=$jurnal->Amount)
                    @else <td></td> @endif
                    @if ($jurnal->AccPos == "Credit")
                        <td>Rp {{number_format($jurnal->Amount,2,",",".")}}</td>
                        @php($balance-=$jurnal->Amount)
                    @else <td></td> @endif
                    <td>Rp {{number_format($balance,2,",",".")}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="card-box">
    <h4 class="m-t-0 header-title">Total Jurnal</h4>
    <div class="col-12">
        <div class="p-20">
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Debet</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($jurnals['ttl_debet'],2,',','.')}}" readonly>
                    
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Credit</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($jurnals['ttl_credit'],2,',','.')}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Current Balance</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($balance,2,',','.')}}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
    
{{-- <script>
// Responsive Datatable
$('#responsive-datatable').DataTable({
     columnDefs: [
       {targets: '_all', type: 'natural'}
     ]
  } );
</script>
     --}}