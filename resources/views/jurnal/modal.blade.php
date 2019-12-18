<div class="card-box">
    <div class="col-12">
        <div class="p-20">
            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <th>No</th>
                    <th>Account Number</th>
                    <th>Account Name</th>
                    <th>Position</th>
                    <th>Amount</th>
                    <th>Notes</th>
                </thead>
                <tbody id="jurnal-list-body">
                    @php($i=1)
                    @foreach ($jurnals as $item)
                        <tr style="width:100%" id="trow{{$i}}">
                            <td>{{$i}}</td>
                            <td>{{$item->AccNo}}</td>
                            <td>{{$item->coa->AccName}}</td>
                            <td><input type="hidden" value="{{$item->AccPos}}" id="position{{$i}}">{{$item->AccPos}}</td>
                            <td><input type="hidden" value="{{$item->Amount}}" id="amount{{$i}}">Rp {{number_format($item->Amount,2,",",".")}}</td>
                            <td>{{$item->notes_item}}</td>
                        </tr>
                    @php($i++)
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<hr>
<div class="card-box">
    <div class="form-group row">
        <label class="col-2 col-form-label">Total Debet</label>
        <div class="col-10">
            <input type="text" readonly class="form-control" name="ttl_debet" id="ttl_debet" parsley-trigger="change" value="Rp @isset($ttl_debet){{number_format($ttl_debet,2,",",".")}}@else{{number_format(0,2,",",".")}}@endisset">
        </div>
    </div>
    <div class="form-group row">
            <label class="col-2 col-form-label">Total Credit</label>
            <div class="col-10">
                <input type="text" readonly class="form-control" name="ttl_credit" id="ttl_credit" parsley-trigger="change" value="Rp @isset($ttl_credit){{number_format($ttl_credit,2,",",".")}}@else{{number_format(0,2,",",".")}}@endisset">
            </div>
        </div>
    <div class="form-group row">
        <label class="col-2 col-form-label">Transaction Date</label>
        <div class="col-10">
            <div class="input-group">
                <input type="text" readonly class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="trx_date" id="trx_date"  data-date-format='yyyy-mm-dd' autocomplete="off" value="@isset($jurnals[0]->date){{$jurnals[0]->date}}@endisset">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="ti-calendar"></i></span>
                </div>
            </div><!-- input-group -->
        </div>
    </div>
    <div class="form-group row">
        <label class="col-2 col-form-label">Description</label>
        <div class="col-10">
            <textarea class="form-control" readonly rows="5" id="deskripsi" name="deskripsi">@isset($jurnals[0]->description){{$jurnals[0]->description}}@endisset</textarea>
        </div>
    </div>
</div>