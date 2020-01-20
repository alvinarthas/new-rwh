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
                        <td>{{$item['id']}}</td>
                        <td>Rp {{number_format($item['sisa'],2,',','.')}}</td>
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
