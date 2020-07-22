<style>
    #loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 50px;
        height: 50px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
        margin-left:10px;
        margin-right:10px;
        margin-top:10px;
    }
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
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
                    <input type="text" class="form-control" name="AccNo" id="AccNo" parsley-trigger="change" value="{{$coa->AccNo}}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card-box table-responsive">
    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <th>No</th>
            <th>Jurnal ID</th>
            <th>Transaction Date</th>
            <th>Notes</th>
            <th>Description</th>
            <th>Debet</th>
            <th>Credit</th>
            <th>Balance</th>
        </thead>
    </table>
</div>
<input type="hidden" id="start_date" value="{{$start_date}}">
<input type="hidden" id="end_date" value="{{$end_date}}">

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
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format(($jurnals['ttl_debet']-$jurnals['ttl_credit']),2,',','.')}}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Responsive Datatable
        $('#responsive-datatable').DataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                "url" : "{{ route('getDataGeneralLedger') }}",
                "type" : "POST",
                "data" : {
                    "start_date" : $("#start_date").val(),
                    "end_date" : $("#end_date").val(),
                    "coa" : $("#AccNo").val(),
                    "_token" : $("meta[name='csrf-token']").attr("content"),
                }
            },"columns" : [{data : 'no', name : 'no'},
                {data : 'id_jurnal', name : 'id_jurnal'},
                {data : "date",name : "date"},
                {data : "notes_item", name : "notes_item"},
                {data : "description", name : "description"},
                {data : "Debet", name : "Debet", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' )},
                {data : "Credit", name : "Credit", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' )},
                {data : "balance", name : "balance", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' )},
            ],"columnDefs" : [
                {
                    targets: '_all',
                    type: 'natural'
                }
            ],"order": [[ 2, "asc" ]
            ],oLanguage : {sProcessing: "<div id='loader'></div>"},
        });
    });
</script>
