<div class="card-box">
    <div class="row">
        <div class="form-group col-sm-12">
            <h4 class="text-dark">Supplier: <strong>{{$supplier->nama}}</strong> <h4>
        </div>
    </div>
    <hr>
    <div class="row">
        <table id="datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Purchase Order</th>
                    <th>Sisa Hutang</th>
                </tr>
            </thead>
            <tbody>
                @php($i=1)
                @foreach ($detail as $item)
                    <tr>
                        <td>{{$i}}</td>
                        <td><a href="javascript:;" onclick="getDetailOrder({{$item['id']}})" class="btn btn-primary btn-trans waves-effect w-md waves-danger m-b-5">PO.{{$item['id']}}</a></td>
                        <td><strong>Rp {{number_format($item['sisa'],2,',','.')}}</strong></td>
                    </tr>
                    @php($i++)
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" id="modal2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" id="do-modal2">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel2">Purchase Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal2">×</button>
            </div>
            <div class="modal-body" id="modalView2">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    $(document).ready(function () {
        // Responsive Datatable
        $('#datatable').DataTable({
            columnDefs: [
            {targets: '_all', type: 'natural'}
            ]
        } );
    });

    function getDetailOrder(id2){
        $.ajax({
            url : "{{route('purchase.show',['id'=>1])}}",
            type : "get",
            dataType: 'json',
            data:{
                id:id2,
            },
        }).done(function (data) {
            $('#modalView2').html(data);
            $('#modal2').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
