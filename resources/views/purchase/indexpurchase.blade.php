@php
    use App\TempPO;
    use App\TempPODet;
@endphp
<form class="form-horizontal" role="form" action="{{ route('exportPO') }}" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <div class="form-group text-right m-b-0">
                    <button class="btn btn-success btn-trans btn-rounded waves-effect waves-light w-xs m-b-5">
                        <span class="mdi mdi-file-excel">
                            Cetak Excel
                        </span>
                    </button>
                    <input type="hidden" name="bulan" value="{{$bulan}}">
                    <input type="hidden" name="tahun" value="{{$tahun}}">
                </div>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Transaction ID</th>
                        <th>Posting Period</th>
                        <th>Supplier</th>
                        <th>PO Date</th>
                        <th>Notes</th>
                        <th>Creator</th>
                        <th>Option</th>
                    </thead>
                    <tbody>
                        @csrf
                        @php($i=1)
                        @foreach ($purchases as $purchase)
                            <tr>
                                <td>{{$i}}</td>
                                <td><a href="javascript:;" onclick="getDetail({{$purchase->id}})" class="btn btn-primary btn-trans waves-effect w-md waves-danger m-b-5">PO.{{$purchase->id}}</a></td>
                                <td>{{date("F", mktime(0, 0, 0, $purchase->month, 10))}} {{$purchase->year}}</td>
                                <td>{{$purchase->supplier()->first()->nama}}</td>
                                <td>{{$purchase->tgl}}</td>
                                <td>{{$purchase->notes}}</td>
                                <td>{{$purchase->creator()->first()->name}}</td>
                                <td>
                                    @if (array_search("PUPUU",$page))
                                    <a href="{{route('purchase.edit',['id'=>$purchase->id])}}" class="btn btn-custom btn-trans waves-effect w-md waves-danger m-b-5">Edit</a>
                                    @endif
                                    @if (array_search("PUPUD",$page))
                                    <a href="javascript:;" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deletePurchase({{$purchase->id}})">Delete</a>
                                    @endif
                                    @if ($purchase->approve == 0)
                                    <?php
                                        $url_register		= base64_encode(route('purchaseApprove',['user_id'=>session('user_id'),'trx_id'=>$purchase->id,'role'=>session('role')]));
                                    ?>
                                        @if (array_search("PUPUA",$page))
                                        <a href="finspot:FingerspotVer;<?=$url_register?>" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Purchase</a>
                                        @endif
                                    @else
                                        <?php
                                            $count_temp = TempPO::where('purchase_id',$purchase->id)->count('purchase_id');
                                            $status_temp = TempPO::where('purchase_id',$purchase->id)->where('status',1)->count('purchase_id');
                                        ?>
                                        @if($count_temp > 0 && $status_temp == 1)
                                            <?php
                                                $url_register		= base64_encode(route('purchaseApprove',['user_id'=>session('user_id'),'trx_id'=>$purchase->id,'role'=>session('role')]));
                                            ?>
                                            @if (array_search("PUPUA",$page))
                                            <a href="finspot:FingerspotVer;<?=$url_register?>" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Purchase yang sudah diupdate</a>
                                            @endif
                                        @else
                                            <a class="btn btn-inverse btn-trans waves-effect w-md waves-danger m-b-5">Purchase sudah di approve</a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @php($i++)
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" id="modalLarge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" id="do-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Purchase Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
            </div>
            <div class="modal-body" id="modalView">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
// Responsive Datatable
$('#responsive-datatable').DataTable({
     columnDefs: [
       { type: 'natural', targets: '_all' }
     ]
  } );

function deletePurchase(id){
    var token = $("meta[name='csrf-token']").attr("content");

    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger m-l-10',
        buttonsStyling: false
    }).then(function () {
        $.ajax({
            url: "purchase/"+id,
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
            console.log("eh ga kehapus");
            swal(
                'Cancelled',
                'Your imaginary file is safe :)',
                'error'
            )
        }
    })
}

function getDetail(id){
    $.ajax({
        url : "{{route('purchase.show',['id'=>1])}}",
        type : "get",
        dataType: 'json',
        data:{
            id:id,
        },
    }).done(function (data) {
        $('#modalView').html(data);
        $('#modalLarge').modal("show");
    }).fail(function (msg) {
        alert('Gagal menampilkan data, silahkan refresh halaman.');
    });
}
</script>
