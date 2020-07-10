@php
    use App\DeliveryDetail;
    use App\SalesDet;
@endphp
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
                                <th>Customer</th>
                                <th>Total Harga</th>
                                <th>Status Delivery</th>
                                <th>Option</th>
                            </thead>
                            <tbody>
                                @php($i=1)
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{$sale['jurnal_id']}}</td>
                                        <td>{{$sale['customer']}}</td>
                                        <td>Rp {{number_format($sale['ttl'],2,",",".")}}</td>
                                        @if ($sale['status_do'] == 1)
                                            <td><a href="javascrip:;" class="btn btn-success btn-trans waves-effect w-md waves-danger m-b-5">Sudah selesai melakukan Delivery</a></td>
                                        @else
                                            <td><a href="javascrip:;" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5">Belum selesai melakukan Delivery</a></td>
                                        @endif
                                        <td><a href="{{route('showDo',['id'=>$sale['sales_id']])}}" class="btn btn-primary btn-rounded waves-effect w-md waves-danger m-b-5">Atur</a></td>
                                    </tr>
                                @php($i++)
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
                            <tbody>
                                @php($x=1)
                                @foreach ($deliveries as $delivery)
                                    <tr>
                                        <td>{{$x}}</td>
                                        <td>{{$delivery->so_id}}</td>
                                        <td>{{ $delivery->do_id }}</td>
                                        <td>{{$delivery->customer}}</td>
                                        <td>{{$delivery->prod_id}}</td>
                                        <td>{{$delivery->product_name}}</td>
                                        {{-- @if ($delivery->qty == $delivery->do_qty)
                                            <td><a href="javascrip:;" class="btn btn-success btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$delivery->qty}}</a></td>
                                        @elseif ($delivery->qty > $delivery->do_qty)
                                            <td><a href="javascrip:;" class="btn btn-warning btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$delivery->qty}}</a></td>
                                        @else
                                            <td><a href="javascrip:;" class="btn btn-danger btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$delivery->qty}}</a></td>
                                        @endif --}}
                                        @if ($delivery->do_qty == 0)
                                            <td><a href="javascrip:;" class="btn btn-danger btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$delivery->do_qty}}</a></td>
                                        @elseif(($delivery->do_qty-$delivery->qty) == 0 )
                                            <td><a href="javascrip:;" class="btn btn-success btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$delivery->do_qty}}</a></td>
                                        @elseif(($delivery->do_qty-$delivery->qty) < 0 )
                                            <td><a href="javascrip:;" class="btn btn-warning btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$delivery->do_qty}}</a></td>
                                        @elseif(($delivery->do_qty-$delivery->qty) > 0 )
                                            <td><a href="javascrip:;" class="btn btn-custom btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$delivery->do_qty}}</a></td>
                                        @else
                                            <td><a href="javascrip:;" class="btn btn-info btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$delivery->do_qty}}</a></td>
                                        @endif

                                        <td><a href="{{route('showDo',['id'=>$delivery->trx_id])}}" class="btn btn-primary btn-rounded waves-effect w-md waves-danger m-b-5">Atur DO</a></td>
                                    </tr>
                                @php($x++)
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
    $('#responsive-datatable-sales').DataTable();
    $('#responsive-datatable-deliveries').DataTable({
        "columnDefs": [
            {
                "targets": [ 2 ],
                "visible": false,
            },
        ]
    });
</script>
