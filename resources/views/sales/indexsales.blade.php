<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <th>No</th>
                    <th>Transaction ID</th>
                    <th>Transaction Date</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Option</th>
                </thead>
                <tbody>
                    @csrf
                    @php($i=1)
                    @foreach ($sales as $sale)
                        <tr>
                            <td>{{$i}}</td>
                            <td>SO.{{$sale->id}}</td>
                            <td>{{$sale->trx_date}}</td>
                            <td>{{$sale->customer->apname}}</td>
                            <td>{{$sale->ttl_harga+$sale->ongkir}}</td>
                            <td>
                                <a href="{{route('sales.edit',['id'=>$sale->id])}}" class="btn btn-custom btn-trans waves-effect w-md waves-danger m-b-5">Edit</a>
                                <a href="javascrip:;" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deletePurchase({{$sale->id}})">Delete</a>
                                @if ($sale->approve == 0)
                                <?php
                                    $url_register		= base64_encode(route('salesApprove',['user_id'=>session('user_id'),'trx_id'=>$sale->id]));
                                ?>
                                    <a href="finspot:FingerspotVer;<?=$url_register?>" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Approve Sales</a>
                                @else
                                    <a class="btn btn-inverse btn-trans waves-effect w-md waves-danger m-b-5" disabled>Sales sudah di approve</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Responsive Datatable
$('#responsive-datatable').DataTable();

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
            url: "sales/"+id,
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
</script>
