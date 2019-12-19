@extends('layout.main')
@php
    use App\Customer;
    use App\PriceDet;
@endphp

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Select2 -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

    <!--venobox lightbox-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/magnific-popup/dist/magnific-popup.css') }}"/>

    {{-- Fingerprint --}}
    <link href="{{ asset('assets/fingerprint/ajaxmask.css') }}" rel="stylesheet">

    <style>
    img.photo{
        display:block; width:50%; height:auto;
    }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-box">
            <h4 class="m-t-0 header-title">Updata Price & BV</h4>
            <div class="row">
                <div class="col-12">
                    <div class="p-20">
                        @if($jenis == "customer")
                            <p class="text-muted font-13">Informasi Customer</p>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Customer ID Number</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" readonly name="customer_id" id="customer_id" value="@isset($customer->cid){{$customer->cid}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nama</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" readonly name="name" id="name" value="@isset($customer->apname){{$customer->apname}}@endisset" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nomor Telepon</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" readonly name="phone" id="phone" value="@isset($customer->apphone){{$customer->apphone}}@endisset">
                                </div>
                            </div>
                        @elseif($jenis == "product")
                            <p class="text-muted font-13">Product Information</p>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Product ID</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" readonly name="prod_id" id="prod_id" value="@isset($product->prod_id){{$product->prod_id}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Product ID Perusahaan</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" readonly name="prod_id_new" id="prod_id_new" value="@isset($product->prod_id_new){{$product->prod_id_new}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Product Name</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" readonly name="name" id="name" value="@isset($product->name){{$product->name}}@endisset" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Product Brand</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" readonly name="category" id="category" value="@isset($product->category){{$product->category}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Supplier</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" readonly name="supplier" id="supplier" value="@isset($product->supplier){{$product->supplier}}@endisset">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($jenis == "customer")
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <p class="text-muted font-13">Informasi Perusahaan</p>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Nama Perusahaan</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" readonly name="cname" id="cname" value="@isset($customer->cicn){{$customer->cicn}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Alamat</label>
                                <div class="col-10">
                                    <textarea class="form-control" readonly name="cadd" id="cadd">@isset($customer->ciadd){{$customer->ciadd}}@endisset</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Kota</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" readonly name="ccity" id="ccity" value="@isset($customer->cicty){{$customer->cicty}}@endisset">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if($jenis == "customer")
    <form class="form-horizontal" role="form" action="{{ route('customer.updatepricebv',['id' => $customer->id]) }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">Product List Detail</h4>
                    @php
                        $i = 0;
                    @endphp
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Tambah Daftar Produk</label>
                            <div class="col-10">
                                <select class="form-control select2" id="search" name="search" parsley-trigger="change" onchange="addRowProduct(this.value)">
                                </select>
                            </div>
                        </div>
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Supplier</th>
                                <th width="10%">Product ID</th>
                                <th width="15%">Product Name</th>
                                <th width="10%">Product Brand</th>
                                <th width="20%">Price</th>
                                <th width="20%">BV</th>
                                <th width="5%">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            @foreach($product as $p)
                                @php
                                    $i++;
                                @endphp
                                <input type="hidden" name="counts" id="counts" value="0">
                                <input type="hidden" name="ctr" id="ctr" value="{{ $i }}">
                                <tr id="trpd{{ $p['pdid'] }}" class="trow">
                                    <td>{{ $i }}</td>
                                    <td>{{ $p->namasupplier }}</td>
                                    <td>{{ $p->prod_id }}</td>
                                    <input type="hidden" name="prod_id[]" id="prod_id{{ $i }}" value="{{ $p['prod_id'] }}">
                                    <td>{{ $p->name }}</td>
                                    <td>{{ $p->category }}</td>
                                    <td><input type="text" class="form-control" name="prod_price[]" id="prod_price{{ $i }}" value="{{ $p['price'] }}"></td>
                                    <input type="hidden" name="prod_price_lama[]" id="prod_price_lama{{ $i }}" value="{{ $p['price'] }}">
                                    <td><input type="text" class="form-control" name="prod_bv[]" id="prod_bv{{ $i }}" value="{{ $p['pv'] }}"></td>
                                    <input type="hidden" name="prod_bv_lama[]" id="prod_bv_lama{{ $i }}" value="{{ $p['pv'] }}">
                                    <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect waves-danger m-b-5" onclick="deleteRowProduct('{{ $p['pdid'] }}')" >x</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <input type="hidden" name="id_cust" id="id_cust" value="{{$customer->id}}">
                    <input type="hidden" name="opsi" id="opsi">
                </div>
            </div>
        </div>
        <div class="form-group text-right m-b-0">
            @if (array_search("PRMCU",$page))
            <button class="btn btn-primary waves-effect waves-light" type="submit" onclick="updatePriceBV()">
                Update Data
            </button>
            @endif
            @if (array_search("PRMCP",$page))
            <button class="btn btn-success waves-effect waves-light" type="submit" onclick="exportXls()">
                Cetak file Excel
            </button>
            @endif
        </div>
    </form>
@elseif($jenis=="product")
    <form class="form-horizontal" role="form" action="{{ route('updatebycustomer',['id' => $product->id]) }}" enctype="multipart/form-data" method="POST">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">Customer List</h4>
                    @php
                        $i = 0;
                    @endphp
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Customer ID</th>
                                <th width="10%">Customer's Name</th>
                                <th width="15%">Company's Name</th>
                                <th width="20%">Price</th>
                                <th width="20%">BV</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            @foreach($customer as $c)
                                @php
                                    $i++;
                                    $price = 0;
                                    $bv = 0;
                                    $pricedet = PriceDet::where('prod_id', $product['prod_id'])->where('customer_id', $c['id'])->select('price', 'pv')->first();
                                    if(!empty($pricedet)){
                                        $price = $pricedet['price'];
                                        $bv = $pricedet['pv'];
                                    }
                                @endphp
                                <input type="hidden" name="counts" id="counts" value="0">
                                <input type="hidden" name="ctr" id="ctr" value="{{ $i }}">
                                <tr id="trpd{{ $c['id'] }}" class="trow">
                                    <td>{{ $i }}</td>
                                    <td>{{ $c->cid }}</td>
                                    <td>{{ $c->apname }}</td>
                                    <input type="hidden" name="cust_id[]" id="cust_id{{ $i }}" value="{{ $c['id'] }}">
                                    <td>{{ $c->cicn }}</td>
                                    <td><input type="text" class="form-control" name="prod_price[]" id="prod_price{{ $i }}" value="{{ $price }}"></td>
                                    <input type="hidden" name="prod_price_lama[]" id="prod_price_lama{{ $i }}" value="{{ $price }}">
                                    <td><input type="text" class="form-control" name="prod_bv[]" id="prod_bv{{ $i }}" value="{{ $bv }}"></td>
                                    <input type="hidden" name="prod_bv_lama[]" id="prod_bv_lama{{ $i }}" value="{{ $bv }}">
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <input type="hidden" name="id" id="id" value="{{$product->id}}">
                    <input type="hidden" name="prod_id" id="prod_id" value="{{$product['prod_id']}}">
                    <input type="hidden" name="opsi" id="opsi">
                </div>
            </div>
        </div>
        <div class="form-group text-right m-b-0">
            @if (array_search("PRMPU",$page))
            <button class="btn btn-primary waves-effect waves-light" type="submit" onclick="updatePriceBV()">
                Update Data
            </button>
            @endif
            @if (array_search("PRMPP",$page))
            <button class="btn btn-success waves-effect waves-light" type="submit" onclick="exportXls()">
                Cetak file Excel
            </button>
            @endif
        </div>
    </form>
@endif
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

    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Magnific popup -->
    <script type="text/javascript" src="{{ asset('assets/plugins/magnific-popup/dist/jquery.magnific-popup.min.js') }}"></script>

    <!-- number-divider -->
    <script src="{{ asset('assets/plugins/number-divider/number-divider.min.js') }}"></script>
@endsection

@section('script-js')

<script type="text/javascript">

    $(document).ready(function () {
        ajx_product();

        // Responsive Datatable
        $('#responsive-datatable').DataTable({
            paging: false,
            scrollY: 400
        });

        $(".divide").divide();
    });

    function ajx_product(){
        $("#search").select2({
            placeholder:'Masukan Kata Kunci',
            ajax:{
                url: "{{route('ajxGetProduct')}}",
                dataType:'json',
                delay:250,
                data:function(params){
                    return{
                        keyword:params.term,
                    };
                },
                processResults:function(data){
                    var item = $.map(data, (value)=>{ //map buat ngemap object data kyk foreach
                        return { id: value.id, text: value.prod_id+" - "+value.nama+" (Supplier : "+value.supplier+")"};
                    });
                    return {
                        results: item
                    }
                },
                cache: false,
            },
            minimumInputLength: 3,
        });

    }

    function addRowProduct(id){
        var token = $("meta[name='csrf-token']").attr("content");
        var rows= $('#responsive-datatable tbody tr.trow').length;
        var cust = $("#id_cust").val();
        var cnt = $("#ctr").val();
        $.ajax({
            url : "{{route('ajxAddRowProduct')}}",
            type : "get",
            dataType: 'json',
            data:{
                prod_id : id,
                cust_id : cust,
                count : rows,
                _token : token,
            },
        }).done(function (data) {
            $('#table-body').append(data.append);
            var cnt = parseInt($('#ctr').val()) + 1;
            $('#ctr').val(cnt);
            resetall();
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function checkTotal(){
        var rows= $('#responsive-datatable tbody tr.trow').length;
        for(i=0;i<rows;i++){
            r = i+1;
            document.getElementById('no'+r).innerHTML = r;
        }
    }

    function resetall(){
        $('#search').empty().trigger('click')
    }

    function deleteRowProduct(id){
        var token = $("meta[name='csrf-token']").attr("content");
        // console.log(id);
        $.ajax({
            url : "/pricedetail/"+id+"/delete",
            type : "GET",
            dataType : "json",
            data:{
                id : id,
                _token : token,
            },success		:	function(){
                $('#trpd'+id).remove();
                var cnt = parseInt($('#ctr').val())+1;
                $('#ctr').val(cnt);
                checkTotal();
            },
            error       :   function(){
                alert('Gagal menampilkan data, silahkan refresh halaman.');
            }
        })
    }

    function deleteItem(id){
        $('#trow'+id).remove();
        var cnt = parseInt($('#ctr').val())+1;
        $('#ctr').val(cnt);
        checkTotal();
    }

    function updatePriceBV(){
        $('#opsi').val("0");
    }

    function exportXls(){
        $('#opsi').val("1");
    }
</script>
@endsection
