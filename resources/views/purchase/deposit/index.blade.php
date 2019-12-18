@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Sweet Alert css -->
    <link href="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('judul')
Index Purchasing
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Deposit Pembelian</h4>
                @if (array_search("PUDPC",$page))
                <a href="{{route('deposit.create')}}" class="btn btn-success btn-rounded waves-effect waves-light w-md m-b-5">Add Deposit</a>
                @endif
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Supplier Name</th>
                        <th>Saldo</th>
                        <th>Option</th>
                    </thead>
                    <tbody>
                        @php($i=1)
                        @foreach ($data as $key)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$key['name']}}</td>
                                <td>Rp {{number_format($key['saldo'],2,",",".")}}</td>
                                <td>
                                    @if (array_search("PUDPS",$page))
                                        <a href="javascript:;" onclick="getDetail({{$key['id']}},`{{$key['name']}}`)" class="btn btn-custom btn-trans waves-effect w-md waves-danger m-b-5">Detail</a>
                                    @endif
                                </td>
                            </tr>
                        @php($i++)
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
                    <h4 class="modal-title" id="myLargeModalLabel">Detail Deposit Pembelian</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closemodal">×</button>
                </div>
                <div class="modal-body" id="modalView">
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('js')
<!-- Sweet Alert Js  -->
<script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>

<!-- Required datatable js -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
@endsection

@section('script-js')
<script>
    function getDetail(id,name){
        $.ajax({
            url : "{{route('deposit.show',['id'=>1])}}",
            type : "get",
            dataType: 'json',
            data:{
                id:id,
                name:name
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