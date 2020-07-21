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

<div class="card-box table-responsive">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a href="#sales" data-toggle="tab" aria-expanded="false" class="nav-link active">
                Per Sales Order
            </a>
        </li>
        <li class="nav-item">
            <a href="#delivery" data-toggle="tab" aria-expanded="true" class="nav-link">
                Per Delivery Order
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="sales">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <table id="responsive-datatable-sales" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <th>No</th>
                                <th>Sales ID</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th>Total Harga</th>
                                <th>Status Delivery</th>
                                <th>Option</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="delivery">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <table id="responsive-datatable-deliveries" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <th>No</th>
                                <th>SO ID</th>
                                <th>DO ID</th>
                                <th>Customer</th>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Qty DO</th>
                                <th>Option</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="start_date" value="{{$start_date}}">
<input type="hidden" id="end_date" value="{{$end_date}}">
<input type="hidden" id="customer" value="{{$customer}}">
<input type="hidden" id="prod_id" value="{{$prod_id}}">

<script>
    $(document).ready(function () {
        $('#responsive-datatable-sales').DataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                "url" : "{{ route('getDataSO') }}",
                "type" : "POST",
                "data" : {
                    "start_date" : $("#start_date").val(),
                    "end_date" : $("#end_date").val(),
                    "customer" : $("#customer").val(),
                    "prod_id" : $("#prod_id").val(),
                    "_token" : $("meta[name='csrf-token']").attr("content"),
                }
            },"columns" : [{data : "no", name : "no", searchable : false},
                    {data : "sales_id", name : "sales_id"},
                    {data : "trx_date", name : "trx_date"},
                    {data : "customer", name : "customer"},
                    {data : "total_harga", name : "total_harga", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' )},
                    {data : "status_do", name : "status_do", orderable : false, searchable :false},
                    {data : "option", name : "option", orderable : false, searchable : false}
            ],"columnDefs" : [
                {
                    targets: '_all',
                    type: 'natural'
                }
            ],oLanguage : {sProcessing: "<div id='loader'></div>"},
        });
        $('#responsive-datatable-deliveries').DataTable({
            "processing" : true,
                "serverSide" : true,
                "ajax" : {
                    "url" : "{{ route('getDataDO') }}",
                    "type" : "POST",
                    "data" : {
                        "start_date" : $("#start_date").val(),
                        "end_date" : $("#end_date").val(),
                        "customer" : $("#customer").val(),
                        "prod_id" : $("#prod_id").val(),
                        "_token" : $("meta[name='csrf-token']").attr("content"),
                    }
                },"columns" : [{data : "no", name : "no", searchable : false},
                    {data : "so_id", name : "so_id"},
                    {data : "do_id",name : "do_id"},
                    {data : "customer", name : "customer"},
                    {data : "prod_id", name : "prod_id"},
                    {data : "prod_name", name : "prod_name"},
                    {data : "qtydo", name : "qtydo", orderable : false, searchable : false},
                    {data : "option", name : "option", orderable : false, searchable : false}
                ],"columnDefs": [
                {
                    "targets": [ 2 ],
                    "visible": false,
                },
            ],oLanguage : {sProcessing: "<div id='loader'></div>"},

        });
    });
</script>
