@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css"/>
    <!--venobox lightbox-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/magnific-popup/dist/magnific-popup.css') }}"/>
    <!--select2-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
    <!--datepicker-->
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- Sweet Alert css -->
    <link href="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!--Token-->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
@php
    use App\Perusahaan;
    use App\Customer;
    use App\Product;
    use App\ReturPembelianDet;
    use App\ReturPenjualanDet;
@endphp
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                @if($jenis=="create")
                    @if($jenisretur=="pembelian")
                        <h2>Manage Retur Pembelian Barang</h2>
                        <h3 class="m-t-0 header-title">Please Choose Purchase Order</h3>
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Posting Period</label>
                            <div class="col-5">
                                <select class="form-control select2" parsley-trigger="change" name="bulan" id="bulan" required>
                                    <option value="#" selected disabled>Pilih Bulan</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{$i}}">{{date("F", mktime(0, 0, 0, $i, 10))}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-5">
                                <select class="form-control select2" parsley-trigger="change" name="tahun" id="tahun" required>
                                    <option value="#" selected disabled>Pilih Tahun</option>
                                    @for ($i = 2018; $i <= date('Y'); $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    @elseif($jenisretur=="penjualan")
                        <h2>Manage Retur Penjualan Barang</h2>
                        <h3 class="m-t-0 header-title">Please Choose Customer</h3>
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Customer Name</label>
                            <div class="col-10">
                                <select class="form-control select2" parsley-trigger="change" name="customer" id="customer">
                                    <option value="#" selected disabled>Pilih Customer</option>
                                    @foreach($customer as $c)
                                        <option value="{{$c['id']}}">{{ $c['apname'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                @elseif($jenis=="report")
                    @if($jenisretur=="penjualan")
                        <h3 class="l-h-34">Retur Penjualan Barang Report</h3>
                        <div class="form-group m-b-0">
                            <a href="{{ route("retur.createpj") }}"><button class="btn btn-primary waves-effect waves-light" type="submit">
                                Buat Retur Penjualan Barang
                            </button></a>
                        </div>
                        <h4 class="m-t-0 header-title">Retur Penjualan Barang Detail</h4>
                        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                            <thead>
                                <th>No</th>
                                <th>Transaction ID</th>
                                <th>Retur Date</th>
                                <th>Customer Name</th>
                                <th>SO ID</th>
                                <th>Action</th>
                                {{-- <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Qty</th>
                                <th>Qty Retur</th>
                                <th>Alasan Retur</th>
                                <th>Tgl Retur</th> --}}
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @foreach($retur as $r)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td class="text-center"><a href="javascript:;" onclick="getDetailRJ({{$r->id}})" class="btn btn-primary btn-trans waves-effect w-md waves-danger m-b-5">{{ $r['id_jurnal'] }}</td>
                                        <td>{{ $r->tgl }}</td>
                                        <td>{{ $r->customer()->first()->apname }}</td>
                                        <td>{{ $r->source_id }}</td>
                                        <td>
                                            {{-- @if (array_search("REPJU",$page))
                                            <a href="{{route('retur.edit',['id'=>$r->trx_id])}}" class="btn btn-custom btn-trans waves-effect w-md waves-danger m-b-5">Edit</a>
                                            @endif --}}
                                            @if (array_search("REPJD",$page))
                                            <a href="javascript:;" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteRetur({{$r->id}})">Delete</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                {{-- @foreach($sales as $s)
                                    @php
                                        $dataretur = ReturPenjualanDet::join('tblreturpj', 'tblreturpjdet.trx_id', 'tblreturpj.trx_id')->where('tblreturpj.trx_id', $s['trx_id'])->where('prod_id', $s['prod_id'])->first();
                                    @endphp
                                    @if($dataretur['qty']!=0)
                                        <tr>

                                            <td class="text-center">{{ $dataretur['id_jurnal']}}</td>
                                            <td>{{ $s['trx_date'] }}</td>
                                            <td>{{ Customer::where('id', $s['customer_id'])->first()->apname }}</td>
                                            <td>{{ $s['prod_id'] }}</td>
                                            <td>{{ Product::where('prod_id', $s['prod_id'])->first()->name }}</td>
                                            <td>{{ $s['qty'] }} {{ $s['unit'] }}</td>
                                            <td style="background-color:yellow">{{ $dataretur['qty'] }}</td>
                                            <td style="background-color:yellow">{{ $dataretur['reason'] }}</td>
                                            <td style="background-color:yellow">{{ $dataretur['tgl'] }}</td>
                                        </tr>
                                    @endif
                                @endforeach --}}
                            </tbody>
                        </table>
                    @elseif($jenisretur=="pembelian")
                        <h3 class="l-h-34">Retur Pembelian Barang Report</h3>
                        <div class="form-group m-b-0">
                            <a href="{{ route("retur.create") }}"><button class="btn btn-primary waves-effect waves-light" type="submit">
                                Buat Retur Pembelian Barang
                            </button></a>
                        </div>
                        <h4 class="m-t-0 header-title">Retur Pembelian Barang Detail</h4>
                        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                            <thead>
                                <th>No</th>
                                <th>Transaction ID</th>
                                <th>Retur Date</th>
                                <th>Supplier Name</th>
                                <th>PO ID</th>
                                <th>Action</th>
                                {{-- <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Qty</th>
                                <th>Qty Retur</th>
                                <th>Alasan Retur</th>
                                <th>Tgl Retur</th> --}}
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                {{-- @foreach($purchase as $p)
                                    @php
                                        $dataretur = ReturPembelianDet::join('tblreturpb', 'tblreturpbdet.trx_id', 'tblreturpb.trx_id')->where('tblreturpb.trx_id', $p['trx_id'])->where('prod_id', $p['prod_id'])->first();
                                    @endphp
                                    @if($dataretur['qty']!=0)
                                        <tr>
                                            <td class="text-center">{{ $dataretur['id_jurnal']}}</td>
                                            <td>{{ date("F",strtotime("2017-".$p['month']."-01"))." ".$p['year'] }}</td>
                                            <td>{{ Perusahaan::where('id', $p['supplier'])->first()->nama }}</td>
                                            <td>{{ $p['prod_id'] }}</td>
                                            <td>{{ Product::where('prod_id', $p['prod_id'])->first()->name }}</td>
                                            <td>{{ $p['qty'] }} {{ $p['unit'] }}</td>
                                            <td style="background-color:yellow">{{ $dataretur['qty'] }}</td>
                                            <td style="background-color:yellow">{{ $dataretur['reason'] }}</td>
                                            <td style="background-color:yellow">{{ $dataretur['tgl'] }}</td>
                                        </tr>
                                    @endif
                                @endforeach --}}
                                @foreach($retur as $r)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td class="text-center"><a href="javascript:;" onclick="getDetailRB({{$r->id}})" class="btn btn-primary btn-trans waves-effect w-md waves-danger m-b-5">{{ $r['id_jurnal'] }}</td>
                                        <td>{{ $r->tgl }}</td>
                                        <td>{{ $r->supplier()->first()->nama }}</td>
                                        <td>{{ $r->source_id }}</td>
                                        <td>
                                            {{-- @if (array_search("REPBU",$page))
                                            <a href="{{route('retur.edit',['id'=>$r->trx_id])}}" class="btn btn-custom btn-trans waves-effect w-md waves-danger m-b-5">Edit</a>
                                            @endif --}}
                                            @if (array_search("REPBD",$page))
                                            <a href="javascript:;" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteRetur({{$r->id}})">Delete</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @if($jenis=="create")
        @if($jenisretur=="pembelian")
            <div class="form-group text-right m-b-0">
                <button class="btn btn-primary waves-effect waves-light" onclick="showReturPembelian()" type="submit">
                    Show Purchase Order
                </button>
            </div>
        @elseif($jenisretur=="penjualan")
            <div class="form-group text-right m-b-0">
                <button class="btn btn-primary waves-effect waves-light" onclick="showReturPenjualan()" type="submit">
                    Show SO
                </button>
            </div>
        @endif
    @endif

    <div id="tabelPO">

    </div> <!-- end row -->

    <!--  Modal content for the above example -->
    <div class="modal fade bs-example-modal-lg" id="modalLarge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg" id="do-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Retur Order Detail</h4>
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

    <!-- Magnific popup -->
    <script type="text/javascript" src="{{ asset('assets/plugins/magnific-popup/dist/jquery.magnific-popup.min.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- number-divider -->
    <script src="{{ asset('assets/plugins/number-divider/number-divider.min.js') }}"></script>

    <!-- Datepicker -->
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>

    <!-- Sweet Alert Js  -->
    <script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('script-js')

<script type="text/javascript">

    $(document).ready(function () {

        // Responsive Datatable
        $('#responsive-datatable').DataTable();

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

    });

    function showReturPembelian(){
        var bln = $("#bulan").val()
        var thn = $("#tahun").val()
        console.log(bln,thn)
        $.ajax({
            url         :   "{{route('showReturPembelian')}}",
            data        :   {
                tahun : thn,
                bulan : bln,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tabelPO").html(data);
                console.log(data)
            },
            error       :   function(data){
                document.getElementById('tahun').value = '2018';
            }
        });
    }

    function showReturPenjualan(){
        var cust = $("#customer").val()
        $.ajax({
            url         :   "{{route('showReturPenjualan')}}",
            data        :   {
                customer : cust,
            },
            type		:	"GET",
            dataType    :   "html",
            success		:	function(data){
                $("#tabelPO").html(data);
                console.log(data)
            },
            error       :   function(data){
                document.getElementById('customer').value = '#';
            }
        });
    }

    function deleteRetur(id){
        var token = $("meta[name='csrf-token']").attr("content");
        console.log(id)

        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger m-l-10',
            buttonsStyling: false
        }).then(function () {
            $.ajax({
                url: "/retur/"+id,
                type: 'DELETE',
                data: {
                    "id": id,
                    "_token": token,
                },
            }).done(function (data) {
                swal(
                    'Deleted!',
                    'Your file has been deleted.',
                    'success'
                )
                location.reload();
            }).fail(function (msg) {
                swal(
                    'Failed',
                    'Your imaginary file is safe :)',
                    'error'
                )
            });

        }, function (dismiss) {
            // dismiss can be 'cancel', 'overlay',
            // 'close', and 'timer'
            if (dismiss === 'cancel') {
                console.log("eh ga kehapus");
                swal(
                    'Cancelled',
                    'Your imaginary file is safe :)',
                    'error'
                )
            }
        })
    }

    function getDetailRB(id){
        $.ajax({
            url : "{{route('showReturPb',['id'=>1])}}",
            type : "get",
            dataType: 'json',
            data:{
                id:id,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modalLarge').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function getDetailRJ(id){
        $.ajax({
            url : "{{route('showReturPj',['id'=>1])}}",
            type : "get",
            dataType: 'json',
            data:{
                id:id,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modalLarge').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection
