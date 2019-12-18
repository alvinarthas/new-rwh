@extends('layout.main')

@php
    use App\ManageHarga;
    use App\Perusahaan;
    use Illuminate\Support\Facades\DB;
@endphp

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!--select2-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
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
            <h4 class="m-t-0 header-title">Manage Harga Produk</h4>
            <p class="text-muted m-b-30 font-14">
            </p>

            <form action="{{ route('manageharga.store') }}" method="POST">
                @csrf
                <table id="responsive-datatable" class="table table-bordered dt-responsive wrap" cellspacing="0">
                    <thead>
                        <th style="width:5%">No</th>
                        <th style="width:5%">Product ID</th>
                        {{-- <th width="col-md-3">Prod ID Baru</th> --}}
                        <th width="10%">Product Name</th>
                        <th style="width:10%">Product Brand</th>
                        <th style="width:20%">Harga Distributor</th>
                        <th style="width:20%">Harga Modal</th>
                    </thead>
                    <tbody>
                        @foreach($prods as $prd)
                        <tr>
                            @php
                                $i++;
                            @endphp
                            <td>{{$i}}</td>
                            <input type="hidden" name="i" id="i" value="{{ $i }}"/>
                            <td>{{$prd->prod_id}}</td>
                            <input type="hidden" name="pid[]" id="pid{{ $i }}" value="{{ $prd->prod_id }}"/>
                            {{-- <td>{{$prd->prod_id_new}}</td> --}}
                            <td>{{$prd->name}}</td>
                            <td>{{$prd->category}}</td>
                            @php
                                if($prd->harga_distributor == null OR $prd->harga_distributor == ""){
                                    $harga_dis = 0;
                                }else{
                                    $harga_dis = $prd->harga_distributor;
                                }

                                if($prd->harga_modal == null OR $prd->harga_modal == ""){
                                    $harga_mod = 0;
                                }else{
                                    $harga_mod = $prd->harga_modal;
                                }
                            @endphp
                            <td>
                                <input class="form-control" name="price_dis[]" type="text" value="{{ $harga_dis }}">
                            </td>
                            <td>
                                <input class="form-control" name="price_mod[]" type="text" value="{{ $harga_mod }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="form-group text-right m-b-0">
                    <a href="{{ route('product.index') }}" class="btn btn-warning waves-effect waves-light">Kembali</a>
                    @if (array_search("PRMHU",$page))
                    <button class="btn btn-primary waves-effect waves-light" id="submit">
                        Update Harga
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
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
@endsection

@section('script-js')
<script type="text/javascript">
    $(document).ready(function () {
        // Responsive Datatable
        var table = $('#responsive-datatable').DataTable({
            searching : true,
            paging : false,
            scrollY: 400
        });

        $('form').submit(function() {
            table.search('').draw();
            $('#form').submit();
        })

        function formatState (opt) {
            if (!opt.id) {
                return opt.text.toUpperCase();
            }

            var optimage = $(opt.element).attr('data-image');
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
</script>
@endsection
