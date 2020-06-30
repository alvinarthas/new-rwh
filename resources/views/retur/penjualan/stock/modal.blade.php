<div class="card-box">
    <div class="row">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="salesid" class="col-form-label">Receive Item ID</label>
                <input type="text" class="form-control" id="salesid" value="{{$delivery->id_jurnal}}" readonly>
            </div>
            <div class="form-group col-md-4">
                <label for="salesdate" class="col-form-label">Receive Date</label>
                <input type="text" class="form-control" id="salesdate" value="{{$delivery->date}}" readonly>
            </div>
            <div class="form-group col-md-4">
                <label for="salesdate" class="col-form-label">Creator</label>
                <input type="text" class="form-control" id="salesdate" value="{{$delivery->creator()->first()->name}}" readonly>
            </div>
        </div>
        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <th>No</th>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Qty</th>
                <th>Gudang</th>
            </thead>
            <tbody>
                @php($i=1)
                @foreach ($deliverys as $del)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{$del->prod_id}}</td>
                        <td>{{$del->prod->name}}</td>
                        <td>{{$del->qty}}</td>
                        <td>@isset($del->gudang->nama){{ $del->gudang->nama }}@endisset</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
