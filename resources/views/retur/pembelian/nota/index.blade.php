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
                <h3 class="l-h-34">Retur Pembelian Barang Report</h3>
                @if (array_search("RBPOD",$page))
                <div class="form-group m-b-0">
                    <a href="{{ route("returbeli.create") }}" class="btn btn-primary waves-effect waves-light">
                        Buat Retur Pembelian
                    </a>
                </div>
                @endif
                <h4 class="m-t-0 header-title">Retur Pembelian Barang Detail</h4>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Transaction ID</th>
                        <th>Retur Date</th>
                        <th>Supplier Name</th>
                        <th>PO ID</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach($retur as $r)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td class="text-center"><a href="javascript:;" onclick="getDetailRB({{$r->id}})" class="btn btn-primary btn-trans waves-effect w-md waves-danger m-b-5">{{ $r['id_jurnal'] }}</td>
                                <td>{{ $r->tgl }}</td>
                                <td>{{ $r->supplier()->first()->nama }}</td>
                                <td>{{ $r->source_id }}</td>
                                <td>
                                    @if (array_search("RBPOD",$page))
                                        <a href="javascript:;" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteRetur({{$r->id}})">Delete</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--  Modal content for the above example -->
    <div class="modal fade bs-example-modal-lg" id="modalLarge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg" id="do-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Retur Purchase Order Detail</h4>
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
    <script src="{{ asset('assets/plugins/magnific-popup/dist/jquery.magnific-popup.min.js') }}" type="text/javascript"></script>

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

    function deleteRetur(id){
        var token = $("meta[name='csrf-token']").attr("content");
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
                url: "returbeli/"+id,
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
            url : "{{route('returbeli.show',['id'=>1])}}",
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
