@extends('layout.main')
@php
    use App\Perusahaan;
    use App\Product;
    use App\PurchaseDetail;
    use App\Purchase;
@endphp

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box table-responsive">
            <h4 class="m-t-0 header-title">Index Product</h4>

            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:5%">No</th>
                        <th rowspan="2">Product ID</th>
                        <th rowspan="2">Product Name</th>
                        <th colspan="3">Indent</th>
                        <th colspan="3">di Gudang</th>
                        <th colspan="3">milik Customer</th>
                    </tr>
                    <tr>
                        <th>Qty</th>
                        <th>Avg Cost</th>
                        <th>Total</th>
                        <th>Qty</th>
                        <th>Avg Cost</th>
                        <th>Total</th>
                        <th>Qty</th>
                        <th>Avg Cost</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $i = 1;
                        $ttl_gudang= 0;
                        $ttl_indent= 0;
                        $ttl_customer= 0;
                    @endphp
                    @foreach($products as $prd)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$prd->prod_id}}</td>
                            <td>{{$prd->name}}</td>
                                @php
                                    $i++;
                                    $indent = Product::getIndent($prd->prod_id);
                                    $gudang = Product::getGudang($prd->prod_id);
                                    $brgcust = Product::getBrgCust($prd->prod_id);
                                    
                                    $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$prd->prod_id)->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));

                                    $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$prd->prod_id)->sum('tblpotrxdet.qty');
                                    
                                    if($sumprice <> 0 && $sumqty <> 0){
                                        $avcost = $sumprice/$sumqty;
                                    }else{
                                        $avcost = 0;
                                    }
                                    $ttl_gudang+=($avcost*$gudang);
                                    $ttl_indent+=($avcost*$indent);
                                    $ttl_customer+=($avcost*$brgcust);
                                @endphp
                            {{-- Indent --}}
                            <td><a href="javascript:;" onclick="getIndent('{{ $prd->id }}')" disabled="disabled"><strong>{{ $indent }}</strong></a></td>
                            <td>{{number_format($avcost,2,",",".")}}</td>
                            <td><strong>{{number_format($avcost*$indent,2,",",".")}}</strong></td>
                            {{-- Gudang --}}
                            <td><a href="javascript:;" onclick="getGudang('{{ $prd->id }}')" disabled="disabled"><strong>{{ $gudang }}</strong></a></td>
                            <td>{{number_format($avcost,2,",",".")}}</td>
                            <td><strong>{{number_format($avcost*$gudang,2,",",".")}}</strong></td>
                            {{-- Customer --}}
                            <td><a href="javascript:;" onclick="getBrgCust('{{ $prd->id }}')" disabled="disabled"><strong>{{ $brgcust }}</strong></a></td>
                            <td>{{number_format($avcost,2,",",".")}}</td>
                            <td><strong>{{number_format($avcost*$brgcust,2,",",".")}}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <input type="hidden" name="xto" id="xto">
        </div>
    </div>
</div> <!-- end row -->

<div class="card-box">
    <h4 class="m-t-0 header-title">Total Jurnal</h4>
    <div class="col-12">
        <div class="p-20">
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Barang Indent</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($ttl_indent,2,',','.')}}" readonly>
                    
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Barang Digudang</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($ttl_gudang,2,',','.')}}" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Barang Customer</label>
                <div class="col-10">
                    <input type="text" class="form-control" parsley-trigger="change" value="Rp {{number_format($ttl_customer,2,',','.')}}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" id="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" id="do-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Mutasi Product</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
            </div>
            <div class="modal-body" id="modalView">
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@section('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

    <!-- Modal-Effect -->
    <script src="{{ asset('assets/plugins/custombox/dist/custombox.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/custombox/dist/legacy.min.js') }}"></script>
@endsection

@section('script-js')

<script type="text/javascript">

    $(document).ready(function () {
        // Responsive Datatable
        $('#responsive-datatable').DataTable();
    });

    function getDetail(id){
        $.ajax({
            url : "{{route('stockControlling')}}",
            type : "get",
            dataType: 'json',
            data:{
                prod_id:id,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modal').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function getIndent(id){
        $.ajax({
            url : '/stockcontrolling/'+id+'/mutasi/brgindent',
            type : "get",
            dataType: 'json',
            data:{
                id:id,
            },
        }).done(function (data) {
            $('h4.modal-title').text('Mutasi Barang Indent');
            $('#modalView').html(data);
            $('#modal').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function getGudang(id){
        $.ajax({
            url : '/stockcontrolling/'+id+'/mutasi/brggudang',
            type : "get",
            dataType: 'json',
            data:{
                id:id,
            },
        }).done(function (data) {
            $('h4.modal-title').text('Mutasi Barang di Gudang');
            $('#modalView').html(data);
            $('#modal').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function getBrgCust(id){
        $.ajax({
            url : '/stockcontrolling/'+id+'/mutasi/brgcustomer',
            type : "get",
            dataType: 'json',
            data:{
                id:id,
            },
        }).done(function (data) {
            $('h4.modal-title').text('Mutasi Barang milik Customer');
            $('#modalView').html(data);
            $('#modal').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection
