@extends('layout.main')

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
            <h4 class="m-t-0 header-title">Index Laporan Sisa Hutang</h4>

            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <th>No</th>
                    <th>Nama Perusahaan</th>
                    <th>Sisa Hutang</th>
                    <th>Option</th>
                </thead>

                <tbody>
                    @php($i=1)
                    @foreach($suppliers as $sup)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$sup['nama']}}</td>
                            <td>{{$sup['sisa']}}</td>
                            <td><a href="javascript:;" type="button" class="btn btn-custom btn-round waves-effect waves-danger m-b-5" onclick="getDetail({{ $sup['id'] }})" >Detail</a></td>
                        </tr>
                        @php($i++)
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> <!-- end row -->

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" id="modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" id="do-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Detail Sisa Hutang</h4>
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
    <script src="{{ asset('assets/plugins/datatables/natural.js') }}"></script>

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
        $('#responsive-datatable').DataTable({
            "columns": [
                { "data": "no"},
                { "data": "name"},
                { "data": "sisa", render: $.fn.dataTable.render.number( '.', ',', 2, 'Rp ' ) },
                { "data": "detail"},
            ],
        });
    });

    function getDetail(id){
        $.ajax({
            url : "{{route('sisaHutangReport')}}",
            type : "get",
            dataType: 'json',
            data:{
                supplier:id,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modal').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }
</script>
@endsection
