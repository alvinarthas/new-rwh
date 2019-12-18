<div class="card-box table-responsive">
    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
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
                    <td>SO.{{$sale['sales_id']}}</td>
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