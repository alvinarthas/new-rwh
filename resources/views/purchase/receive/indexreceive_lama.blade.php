@php
    use App\ReceiveDet;
    use App\PurchaseDetail;
@endphp
<div class="card-box table-responsive">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a href="#sales" data-toggle="tab" aria-expanded="false" class="nav-link active">
                Per Purchase Order
            </a>
        </li>
        <li class="nav-item">
            <a href="#delivery" data-toggle="tab" aria-expanded="true" class="nav-link">
                Per Receive Item
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="sales">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <th>No</th>
                                <th>Purchase ID</th>
                                <th>Supplier</th>
                                <th>Total Harga Modal</th>
                                <th>Total Harga Distributor</th>
                                <th>Status Terima</th>
                                <th>Option</th>
                            </thead>
                            <tbody>
                                @csrf
                                @php($i=1)
                                @foreach ($purchases as $list)
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>PO.{{$list['trx_id']}}</td>
                                        <td>{{$list['supplier']}}</td>
                                        <td>Rp {{number_format($list['ttl_modal'],2,",",".") }}</td>
                                        <td>Rp {{number_format($list['ttl_dist'],2,",",".") }}</td>
                                        @if ($list['status_receive'] == 1)
                                            <td><a href="javascript:;" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Sudah selesai Terima Produk</a></td>
                                        @else
                                            <td><a href="javascript:;" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5">Belum selesai Terima Produk</a></td>
                                        @endif
                                        <td><a href="{{route('receiveProdDet',['trx_id'=>$list['trx_id']])}}" class="btn btn-primary btn-trans waves-effect w-xs waves-danger m-b-5 ">Atur</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="delivery">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">
                        <table id="responsive-datatable2" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                                <th>Transaction ID</th>
                                <th>RP ID</th>
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
                                        <td>{{$list->rp_id}}</td>
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
        </div>
    </div>
</div>

<script>
// Responsive Datatable
$('#responsive-datatable').DataTable();
$('#responsive-datatable2').DataTable({
    "columnDefs": [
        {
            "targets": [ 1 ],
            "visible": false,
        },
    ]
});

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
