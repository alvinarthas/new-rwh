<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <th>Transaction ID</th>
                    <th>Supplier</th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Qty Receive</th>
                </thead>
                <tbody>
                    @csrf
                    @php($i=1)
                    @foreach ($lists as $list)
                        <tr>
                            <td><a href="{{route('receiveProdDet',['trx_id'=>$list->trx_id])}}" class="btn btn-primary btn-trans waves-effect w-xs waves-danger m-b-5 ">PO.{{$list->trx_id}}</a></td>
                            <td>{{$list->supplier}}</td>
                            <td>{{$list->prod_id}}</td>
                            <td>{{$list->prod_name}}</td>
                            <td>{{$list->qty}}</td>
                            <td>{{$list->unit}}</td>
                            @if ($list->qtyrec == 0)
                                <td><a href="javascrip:;" class="btn btn-danger btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$list->qtyrec}}</a></td>
                            @elseif(($list->qtyrec-$list->qty) == 0 )
                                <td><a href="javascrip:;" class="btn btn-success btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$list->qtyrec}}</a></td>
                            @elseif(($list->qtyrec-$list->qty) < 0 )
                                <td><a href="javascrip:;" class="btn btn-warning btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$list->qtyrec}}</a></td>
                            @elseif(($list->qtyrec-$list->qty) > 0 )
                                <td><a href="javascrip:;" class="btn btn-custom btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$list->qtyrec}}</a></td>
                            @else
                                <td><a href="javascrip:;" class="btn btn-info btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$list->qtyrec}}</a></td>
                            @endif
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
</script>
