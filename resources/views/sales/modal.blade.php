<div class="card-box table-responsive">
    <ul class="nav nav-tabs">
        @isset($temp_sales)
        <li class="nav-item">
            <a href="#edited" data-toggle="tab" aria-expanded="true" class="nav-link active">
                Edited
            </a>
        </li>
        <li class="nav-item">
            <a href="#original" data-toggle="tab" aria-expanded="true" class="nav-link">
                Original
            </a>
        </li>
        @endisset
    </ul>
    <div class="tab-content">
        @isset($temp_sales)
        <div role="tabpanel" class="tab-pane fade show active" id="edited">
            <h4>Sales Order #{{$sales->id}}</h4>
            @if($sales->method <> 0)
            <h4>{{$sales->online->nama}} #{{$sales->online_id}}</h4>
            @endif
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="salesid" class="col-form-label">Nama</label>
                    <input type="text" class="form-control" id="salesid" value="{{$temp_sales->customer->apname}}" readonly>
                </div>
                <div class="form-group col-md-6">
                    <label for="salesdate" class="col-form-label">Telepon</label>
                    <input type="text" class="form-control" id="salesdate" value="{{$temp_sales->customer->apphone}}" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="salesid" class="col-form-label">Perusahaan</label>
                    <input type="text" class="form-control" id="salesid" value="{{$temp_sales->customer->cicn}}" readonly>
                </div>
                <div class="form-group col-md-6">
                    <label for="salesdate" class="col-form-label">Kontak Perusahaan</label>
                    <input type="text" class="form-control" id="salesdate" value="{{$temp_sales->customer->ciphone}}" readonly>
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
                        @foreach ($temp_salesdet as $detail)
                            @isset($detail->product->name)
                            <tr style="width:100%">
                                <td>{{$i}}</td>
                                <td>{{$detail->prod_id}}</td>
                                <td>{{$detail->product->name}}</td>
                                <td>Rp {{number_format($detail->price,2,",",".")}}</td>
                                <td>{{$detail->qty}}</td>
                                <td>{{$detail->unit}}</td>
                                <td>Rp {{number_format($detail->sub_ttl,2,",",".")}}</td>
                                <td>Rp {{number_format($detail->pv,2,",",".")}}</td>
                                <td>Rp {{number_format($detail->sub_ttl_pv,2,",",".")}}</td>
                            </tr>
                            @php($i++)
                            @endisset
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="form-group-row">
                <div class="form-group col-md-12">
                    <label for="salesid" class="col-form-label">Total Transaksi</label>
                    <input type="text" class="form-control" id="ongkir" value="Rp {{number_format($temp_totalprice + $temp_sales->ongkir, 2, ",", ".")}}" readonly>
                </div>
            </div>
            <div class="form-group-row">
                <div class="form-group col-md-12">
                    <label for="salesid" class="col-form-label">Ongkir</label>
                    <input type="text" class="form-control" id="ongkir" value="Rp {{number_format($temp_sales->ongkir, 2, ",", ".")}}" readonly>
                </div>
            </div>
            <div class="form-group-row">
                <div class="form-group col-md-12">
                    <label for="salesid" class="col-form-label">Total yang sudah dibayar</label>
                    <input type="text" class="form-control" id="ongkir" value="Rp {{number_format($salespay, 2, ",", ".")}}" readonly>
                </div>
            </div>
        </div>
        @endisset
        <div role="tabpanel" class="tab-pane fade show active" id="original">
            <h4>Sales Order #{{$sales->id}}</h4>
            @if($sales->method <> 0)
            <h4>{{$sales->online->nama}} #{{$sales->online_id}}</h4>
            @endif
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
                            @isset($detail->product->name)
                            <tr style="width:100%">
                                <td>{{$i}}</td>
                                <td>{{$detail->prod_id}}</td>
                                <td>{{$detail->product->name}}</td>
                                <td>Rp {{number_format($detail->price,2,",",".")}}</td>
                                <td>{{$detail->qty}}</td>
                                <td>{{$detail->unit}}</td>
                                <td>Rp {{number_format($detail->price*$detail->qty,2,",",".")}}</td>
                                <td>Rp {{number_format($detail->pv,2,",",".")}}</td>
                                <td>Rp {{number_format($detail->sub_ttl_pv,2,",",".")}}</td>
                            </tr>
                            @php($i++)
                            @endisset
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="form-group-row">
                <div class="form-group col-md-12">
                    <label for="salesid" class="col-form-label">Total Transaksi</label>
                    <input type="text" class="form-control" id="ongkir" value="Rp {{number_format($totalprice+$sales->ongkir,2, ",", ".")}}" readonly>
                </div>
            </div>
            <div class="form-group-row">
                <div class="form-group col-md-12">
                    <label for="salesid" class="col-form-label">Ongkir</label>
                    <input type="text" class="form-control" id="ongkir" value="Rp {{number_format($sales->ongkir, 2,",",".")}}" readonly>
                </div>
            </div>
            <div class="form-group-row">
                <div class="form-group col-md-12">
                    <label for="salesid" class="col-form-label">Total yang sudah dibayar</label>
                    <input type="text" class="form-control" id="ongkir" value="Rp {{number_format($salespay,2,",",".")}}" readonly>
                </div>
            </div>
            {{-- </div> --}}
        </div>
    </div>
</div>
