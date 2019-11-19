<div class="card-box">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="salesid" class="col-form-label">Nama</label>
            <input type="text" class="form-control" id="salesid" value="{{$sales->customer->apname}}" readonly>
        </div>
        <div class="form-group col-md-6">
            <label for="salesdate" class="col-form-label">Telepon</label>
            <input type="text" class="form-control" id="salesdate" value="{{$sales->customer->apphone}}" readonly>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="salesid" class="col-form-label">Perusahaan</label>
            <input type="text" class="form-control" id="salesid" value="{{$sales->customer->cicn}}" readonly>
        </div>
        <div class="form-group col-md-6">
            <label for="salesdate" class="col-form-label">Kontak Perusahaan</label>
            <input type="text" class="form-control" id="salesdate" value="{{$sales->customer->ciphone}}" readonly>
        </div>
    </div>
    <div class="row">
        <hr>
        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
                <th>No</th>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Sub Total Price</th>
                <th>BV/ Unit</th>
                <th>Sub Total BV</th>
            </thead>
            <tbody>
                @php($i=1)
                @foreach ($salesdet as $detail)
                    {{-- @isset($detail->product->name) --}}
                    <tr style="width:100%">
                        <td>{{$i}}</td>
                        <td>{{$detail->prod_id}}</td>
                        <td>{{$detail->product->name}}</td>
                        <td>Rp. {{number_format($detail->price)}}</td>
                        <td>{{$detail->qty}}</td>
                        <td>{{$detail->unit}}</td>
                        <td>Rp. {{number_format($detail->sub_ttl)}}</td>
                        <td>Rp. {{number_format($detail->pv)}}</td>
                        <td>Rp. {{number_format($detail->sub_ttl_bv)}}</td>
                    </tr>
                    @php($i++)
                    {{-- @endisset --}}
                @endforeach
            </tbody>
        </table>
    </div>
</div>