<div class="card-box">
    <div class="row">
        <div class="form-group col-sm-12">
            <h4 class="text-dark">Customer: <strong>{{$customer->apname}}</strong> <h4>
        </div>
    </div>
    <hr>
    <div class="row">
        <table id="datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Sales Order</th>
                    <th>Product</th>
                    <th>Sisa Barang</th>
                </tr>
            </thead>
            <tbody>
                @php($i=1)
                @foreach ($stocks as $item)
                    <tr>
                        <td>{{$i}}</td>
                        <td>{{$item['id']}}</td>
                        <td>
                            @foreach ($item['data'] as $item2)
                                {{$item2['product_id']}} - {{$item2['product']}} <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($item['data'] as $item2)
                                {{$item2['selisih']}}<br>
                            @endforeach
                        </td>
                    </tr>
                    @php($i++)
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // Responsive Datatable
        $('#datatable').DataTable();
    });
</script>
