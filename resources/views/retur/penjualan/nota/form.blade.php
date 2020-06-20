@extends('layout.main')

@section('css')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- form Uploads -->
    <link href="{{ asset('assets/plugins/fileuploads/css/dropify.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('judul')
Retur Detail
@endsection

@section('content')
@php
    use App\Perusahaan;
    use App\Product;
@endphp
<form class="form-horizontal" role="form" action="{{ route('returjual.update', ['id' => $trx_id]) }}" enctype="multipart/form-data" method="POST">
    {{ method_field('PUT') }}
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Data Sales yang akan di Retur</h4>
                <p class="text-muted m-b-30 font-14"></p>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Customer Name</label>
                    <div class="col-10">
                        <input type="text" class="form-control" name="customer" value="{{ $customer['apname'] }}" disabled>
                        <input type="hidden" class="form-control" name="customer" value="{{ $customer['id'] }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Transaction Date</label>
                    <div class="col-10">
                        <input type="text" class="form-control" name="trx_date" value="{{ $sales['trx_date'] }}" disabled>
                    </div>
                </div>
            </div>
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Retur Penjualan dari {{ $sales->jurnal_id }}</h4>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Qty SO</th>
                        <th>Unit</th>
                        <th>Harga</th>
                        <th>Qty Retur</th>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach($retur as $r)
                        <input type="hidden" name="ctr" id="ctr" value="{{ $i }}">
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{ $r['prod_id'] }}</td>
                            <td>{{ $r['product_name'] }}</td>
                            <td>{{ $r['qty'] }}</td>
                            <td>{{ $r['unit'] }}</td>
                            <td>Rp {{ number_format($r['harga'], 2, ",", ".") }}</td>
                            <td>{{ $r['qtyretur'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">

            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Create a Retur Sales Order Detail</h4>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Unit</th>
                        <th>Harga</th>
                        <th>Qty Retur</th>
                        <th>Alasan Retur</th>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach($salesdet as $s)
                        <input type="hidden" name="ctr" id="ctr" value="{{ $i }}">
                        <tr>
                            <td>{{$i++}}</td>
                            <td><input type="hidden" name="prod_id[]" value="{{ $s['prod_id'] }}" required>{{$s['prod_id'] }}</td>
                            <td>{{ $s->product->name }}</td>
                            <td><input type="hidden" name="unit[]" value="{{ $s['unit'] }}" required>{{ $s['unit'] }}</td>
                            <td><input type="hidden" name="harga[]" value="{{ $s['price'] }}" required>{{ number_format($s['price'], 2, ",", ".") }}</td>
                            <td><input type="text" class="form-control" min="0" max="{{ $s['qty'] }}" name="qtyretur[]" value="0"></td>
                            <td><input type="text" class="form-control" name="reason[]"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group text-right m-b-0">
        <button class="btn btn-primary waves-effect waves-light" type="submit">
            Submit
        </button>
    </div>
</form>
@endsection

@section('js')
    <!-- Plugin -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
    <!-- Validation js (Parsleyjs) -->
    <script type="text/javascript" src="{{ asset('assets/plugins/parsleyjs/dist/parsley.min.js') }}"></script>
    <!-- number-divider -->
    <script src="{{ asset('assets/plugins/number-divider/number-divider.min.js') }}"></script>

@endsection

@section('script-js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('form').parsley();
            $(".number").divide();
        });
    </script>

    <script>
        // Select2
        $(".select2").select2({
            templateResult: formatState,
            templateSelection: formatState
        });

        function formatState (opt) {
            if (!opt.id) {
                return opt.text.toUpperCase();
            }

            var optimage = $(opt.element).attr('data-image');
            console.log(optimage)
            if(!optimage){
            return opt.text.toUpperCase();
            } else {
                var $opt = $(
                '<span><img src="' + optimage + '" width="60px" /> ' + opt.text.toUpperCase() + '</span>'
                );
                return $opt;
            }
        };

    </script>
@endsection
