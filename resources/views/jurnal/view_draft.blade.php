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
    <table id="datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
        <thead>
            <th>No</th>
            <th>Jurnal ID</th>
            <th>Transaction Date</th>
            <th>Account Number</th>
            <th>Account Name</th>
            <th>Debet</th>
            <th>Credit</th>
            <th>Notes</th>
            <th>Description</th>
            @if($param == "umum")
            <th>Option</th>
            @endif
        </thead>
    </table>
</div>
<input type="hidden" id="start_date" value="{{$start_date}}">
<input type="hidden" id="end_date" value="{{$end_date}}">
<input type="hidden" id="coa" value="{{$coa}}">
<input type="hidden" id="position" value="{{$position}}">
<input type="hidden" id="param" value="{{$param}}">

<div class="card-box">
    <h4 class="m-t-0 header-title">Total Jurnal</h4>
    <div class="col-12">
        <div class="p-20">
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Debet</label>
                <div class="col-10">
                    @if ($total['ttl_debet'] < 0)
                        <input type="text" class="form-control" parsley-trigger="change" value="Rp ({{number_format($total['ttl_debet'],2,',','.')}})" readonly>
                    @else
                        <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($total['ttl_debet'],2,',','.')}}" readonly>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Credit</label>
                <div class="col-10">
                    @if ($total['ttl_credit'] < 0)
                        <input type="text" class="form-control" parsley-trigger="change" value="Rp ({{number_format($total['ttl_credit'],2,',','.')}})" readonly>
                    @else
                        <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($total['ttl_credit'],2,',','.')}}" readonly>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var param = $('#param').val();
        if(param == "umum"){
            var column = [{data : 'no', name : 'no', searchable : false},
            {data : 'id_jurnal', name : 'id_jurnal'},
            {data : "date",name : "date"},
            {data : "AccNo", name : "AccNo"},
            {data : "AccName", name : "AccName"},
            {data : "Debet", name : "Debet", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' )},
            {data : "Credit", name : "Credit", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' )},
            {data : "notes_item", name : "notes_item"},
            {data : "description", name : "description"},
            {data : "option", name : "option", orderable : false, searchable : false}];
        }else{
            var column = [{data : 'no', name : 'no', searchable : false},
            {data : 'id_jurnal', name : 'id_jurnal'},
            {data : "date",name : "date"},
            {data : "AccNo", name : "AccNo"},
            {data : "AccName", name : "AccName"},
            {data : "Debet", name : "Debet", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' )},
            {data : "Credit", name : "Credit", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' )},
            {data : "notes_item", name : "notes_item"},
            {data : "description", name : "description"}];
        }
        $('#datatable').DataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                "url" : "{{ route('getDataJurnal') }}",
                "type" : "POST",
                "data" : {
                    "start_date" : $("#start_date").val(),
                    "end_date" : $("#end_date").val(),
                    "position" : $("#position").val(),
                    "coa" : $("#coa").val(),
                    "param" : $("#param").val(),
                    "_token" : $("meta[name='csrf-token']").attr("content"),
                }
            },"columns" : column,"columnDefs" : [
                {
                    targets: '_all',
                    type: 'natural'
                }
            ],oLanguage : {sProcessing: "<div id='loader'></div>"},
        });
    });

    $(document).on('click', '.delete', function(){
        var id = $(this).attr("id");
        jurnalDelete(id);
    })

function jurnalDelete(id){
    var token = $("meta[name='csrf-token']").attr("content");
    console.log(id);

    swal({
        title: 'Apa kamu yakin akan menghapus semua data '+id+'?',
        text: "Kamu tidak akan dapat mengembalikan data yang sudah terhapus!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Tidak, Batalkan!',
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger m-l-10',
        buttonsStyling: false
    }).then(function () {
        $.ajax({
            url: "jurnal/"+id,
            type: 'DELETE',
            data: {
                "id": id,
                "_token": token,
            },
        }).done(function (data) {
            swal(
                'Deleted!',
                'Your file has been deleted.',
                'success'
            )
            location.reload();
        }).fail(function (msg) {
            swal(
                'Failed',
                'Your imaginary file is safe :)',
                'error'
            )
        });

    }, function (dismiss) {
        // dismiss can be 'cancel', 'overlay',
        // 'close', and 'timer'
        if (dismiss === 'cancel') {
            swal(
                'Cancelled',
                'Your imaginary file is safe :)',
                'error'
            )
        }
    })
}
</script>
