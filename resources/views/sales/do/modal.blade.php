<div class="card-box">
    <div class="row">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="salesid" class="col-form-label">Delivery ID</label>
                <input type="text" class="form-control" id="salesid" value="{{$do->jurnal_id}}" readonly>
            </div>
            <div class="form-group col-md-4">
                <label for="salesdate" class="col-form-label">Delivery Date</label>
                <input type="text" class="form-control" id="salesdate" value="{{$do->date}}" readonly>
            </div>
            <div class="form-group col-md-4">
                <label for="salesdate" class="col-form-label">Creator</label>
                <input type="text" class="form-control" id="salesdate" value="{{$do->petugas()->first()->name}}" readonly>
            </div>
        </div>
        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Qty</th>
            </thead>
            <tbody>
                @foreach ($dodets as $dodet)
                    <tr>
                        <td>{{$dodet->product_id}}</td>
                        <td>{{$dodet->product->name}}</td>
                        <td>{{$dodet->qty}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>