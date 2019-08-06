@extends('layout.main')

@section('css')
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Date Picker --}}
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
@endsection

@section('judul')
Form Receive Item
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">PO Details</h4>
                <div class="form-group row">
                    <label class="col-2 col-form-label">PO Number</label>
                    <div class="col-10">
                        <input type="text" class="form-control"disabled value="{{$trx->id}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Posting Period</label>
                    <div class="col-10">
                            <input type="text" class="form-control"disabled value="{{date("F", mktime(0, 0, 0, $trx->month, 10))}} {{$trx->year}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Supplier Name</label>
                    <div class="col-10">
                        <input type="text" class="form-control"disabled value="{{$trx->supplier()->first()->nama}}">
                    </div>
                </div>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Qty Receive</th>
                    </thead>
                    <tbody>
                        @php($i=1)
                        @foreach ($details as $detail)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$detail->prod_id}}</td>
                                <td>{{$detail->prod_name}}</td>
                                <td>{{$detail->qty}}</td>
                                <td>{{$detail->unit}}</td>
                                @if ($detail->qtyrec == 0)
                                    <td><a href="javascrip:;" class="btn btn-danger btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$detail->qtyrec}}</a></td>
                                @elseif(($detail->qtyrec-$detail->qty) == 0 )
                                    <td><a href="javascrip:;" class="btn btn-success btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$detail->qtyrec}}</a></td>
                                @elseif(($detail->qtyrec-$detail->qty) < 0 )
                                    <td><a href="javascrip:;" class="btn btn-warning btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$detail->qtyrec}}</a></td>
                                @else
                                    <td><a href="javascrip:;" class="btn btn-info btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">{{$detail->qtyrec}}</a></td>
                                @endif
                            </tr>
                        @php($i++)
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-box">
                <form class="form-horizontal" role="form" action="{{ route('receiveProdStr') }}" enctype="multipart/form-data" method="POST">
                @csrf
                <input type="hidden" name="trx_id" value="{{$trx->id}}">
                <h4 class="m-t-0 header-title">Insert Item</h4>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Choose Product Name</label>
                    <div class="col-10">
                        <select class="form-control select2" parsley-trigger="change" name="product" id="product" required>
                            <option value="#" disabled selected>Pilih Product</option>
                            @foreach ($producttrx as $product)
                                <option value="{{$product->prod_id}}">{{$product->prod_id}} - {{$product->product->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Quantity Receive</label>
                    <div class="col-10">
                        <input type="number" class="form-control" name="qty" id="qty" parsley-trigger="change" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Receive Date</label>
                    <div class="col-10">
                        <div class="input-group">
                            <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="receive_date" id="receive_date" data-date-format='yyyy-mm-dd' autocomplete="off">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="ti-calendar"></i></span>
                            </div>
                        </div><!-- input-group -->
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Expired Date</label>
                    <div class="col-10">
                        <div class="input-group">
                            <input type="text" class="form-control" parsley-trigger="change" placeholder="yyyy/mm/dd" name="expired_date" id="expired_date" data-date-format='yyyy-mm-dd' autocomplete="off">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="ti-calendar"></i></span>
                            </div>
                        </div><!-- input-group -->
                    </div>
                </div>
                <div class="form-group text-right m-b-0">
                    <button class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5">Add Item</a>
                </div>
                </form>
            </div>
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Receiving Item Details</h4>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Qty Receive</th>
                        <th>Expired Date</th>
                        <th>Received Date</th>
                        <th>Option</th>
                    </thead>
                    <tbody>
                        @php($i=1)
                        @foreach ($receives as $receive)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$receive->prod_id}}</td>
                                <td>{{$receive->prod->name}}</td>
                                <td>{{$receive->qty}}</td>
                                <td>{{$receive->expired_date}}</td>
                                <td>{{$receive->receive_date}}</td>
                                <td><a href="{{route('receiveProdDel',['id' => $receive->id])}}" class="btn btn-danger btn-trans waves-effect w-xs waves-danger m-b-5" onclick="confirm('Apakah Anda Yakin ingin menghapus ?')">Delete</a></td>
                            </tr>
                        @php($i++)
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    {{-- Date Picker --}}
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
@endsection

@section('script-js')
<script>
jQuery('#receive_date').datepicker();
jQuery('#expired_date').datepicker();
</script>
@endsection