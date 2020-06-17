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
    @if($jenisretur == "pembelian")
        <form class="form-horizontal" role="form" action="{{ route('returbeli.update', ['id' => $trx_id]) }}" enctype="multipart/form-data" method="POST">
        {{ method_field('PUT') }}
    @elseif($jenisretur == "penjualan")
        <form class="form-horizontal" role="form" action="{{ route('returjual.update', ['id' => $trx_id]) }}" enctype="multipart/form-data" method="POST">
        {{ method_field('PUT') }}
    @endif

    @csrf

    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Purchase Order Detail</h4>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Qty PO</th>
                        <th>Unit</th>
                        <th>Qty Retur</th>
                        <th>Alasan Retur</th>
                    </thead>
                    @if($jenisretur=="pembelian")
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach($purchasedet as $p)
                            <input type="hidden" name="ctr" id="ctr" value="{{ $i }}">
                            <tr>
                                <td>{{$i++}}</td>
                                <td><input type="hidden" class="form-control" min="0" name="prod_id[]" value="{{ $p['prod_id'] }}" required>{{$p['prod_id'] }}</td>
                                <td>{{ Product::where('prod_id', $p['prod_id'])->first()->name }}</td>
                                <td>{{ $p['qty'] }}</td>
                                <td>{{ $p['unit'] }}</td>
                                <td><input type="text" class="form-control" min="0" max="{{ $p['qty'] }}" name="qtyretur[]" value="0"></td>
                                <td><input type="text" class="form-control" name="reason[]"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    @elseif($jenisretur=="penjualan")
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach($salesdet as $s)
                            <input type="hidden" name="ctr" id="ctr" value="{{ $i }}">
                            <tr>
                                <td>{{$i++}}</td>
                                <td><input type="hidden" class="form-control" min="0" name="prod_id[]" value="{{ $s['prod_id'] }}" required>{{$s['prod_id'] }}</td>
                                <td>{{ Product::where('prod_id', $s['prod_id'])->first()->name }}</td>
                                <td>{{ $s['qty'] }}</td>
                                <td>{{ $s['unit'] }}</td>
                                <td><input type="text" class="form-control" min="0" max="{{ $s['qty'] }}" name="qtyretur[]" value="0"></td>
                                <td><input type="text" class="form-control" name="reason[]"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    @endif
                </table>
            </div>
        </div>
    </div>
    {{-- Perusahaan --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Total Transaction</h4>
                <p class="text-muted m-b-30 font-14">
                </p>
                @if($jenisretur=="pembelian")
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Supplier</label>
                        <div class="col-10">
                            <select class="form-control select2" parsley-trigger="change" name="supplier" disabled="true">
                                @isset($purchase['supplier'])
                                    <option value="{{$purchase['supplier']}}" selected>{{ Perusahaan::where('id', $purchase['supplier'])->first()->nama }}</option>
                                @endif
                            </select>
                            <input type="hidden" name="supplier" value="{{ $purchase['supplier'] }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Posting Period</label>
                        <div class="col-5">
                            <select class="form-control select2" parsley-trigger="change" name="bulan" id="bulan"  disabled required>
                                <option value="#" disabled>Pilih Bulan</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    @if($purchase['month'] == $i)
                                        <option value="{{$i}}" selected>{{date("F", mktime(0, 0, 0, $i, 10))}}</option>
                                    @else
                                        <option value="{{$i}}">{{date("F", mktime(0, 0, 0, $i, 10))}}</option>
                                    @endif
                                @endfor
                            </select>
                        </div>
                        <div class="col-5">
                            <select class="form-control select2" parsley-trigger="change" name="tahun" id="tahun"  disabled required>
                                <option value="#" selected disabled>Pilih Tahun</option>
                                @for ($i = 2018; $i <= date('Y'); $i++)
                                    @if($purchase['year'] == $i)
                                        <option value="{{$i}}" selected>{{$i}}</option>
                                    @else
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endif
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Notes</label>
                        <div class="col-10">
                            <textarea name="notes" cols="99" rows="4" disabled="disabled" id="notes">{{ $purchase['notes'] }}</textarea>
                        </div>
                    </div>
                @elseif($jenisretur=="penjualan")
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
                @endif
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
