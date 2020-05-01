<div class="card-box table-responsive">
    <div class="row">
        <div class="col-12">
            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <th>No</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th></th>
                    <th>Product Name</th>
                    <th>Qty</th>
                </thead>
                <tbody>
                    @php($a=1)
                    @for ($i = 0; $i < count($details);$i++)
                    <tr>
                        <td>{{$a}}</td>
                        <td>{{$details[$i]->product_id}} - {{$details[$i]->product()->first()->name}}</td>
                        <td>{{$details[$i]->qty}}</td>
                        @php($i++)
                        <td>=></td>
                        <td>{{$details[$i]->product_id}} - {{$details[$i]->product()->first()->name}}</td>
                        <td>{{$details[$i]->qty}}</td>
                    </tr>
                    @php($a++)
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
