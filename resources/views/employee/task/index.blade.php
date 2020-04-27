@extends('layout.main')
@php
    use App\Employee;
@endphp

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!--venobox lightbox-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/magnific-popup/dist/magnific-popup.css') }}"/>
    <!-- Sweet Alert css -->
    <link href="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
    img.photo{
        display:block; width:50%; height:auto;
    }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Index Tugas Pegawai</h4>
                <p class="text-muted font-14 m-b-30">
                    @if (array_search("EMTAC",$page))
                        <a href="{{ route('task.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Tugas</a>
                    @endif
                </p>

                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Deadline</th>
                        <th>Creator</th>
                        <th>Status Dilihat</th>
                        <th>Status Dikerjakan</th>
                        <th>Status Tugas</th>
                        <th>Action</th>
                    </thead>

                    <tbody>
                        @php($i = 1)
                        @foreach($data as $task)
                        <tr>
                            <td>{{$i}}</td>
                            <td><a href="javascript:;" onclick="getDescribe('{{ $task['id'] }}')" disabled="disabled">{{$task['title']}}</a></td>
                            <input type="hidden" name="employee_id" id="employee_id" value="{{session('user_id')}}">
                            <td>{{$task['start_date']}}</td>
                            <td>{{$task['due_date']}}</td>
                            <td>{{$task['creator']}}</td>
                            <td class="text-center">
                                <a tabindex="0" class="{{$task['count_read']}}" data-toggle="popover" data-trigger="focus" title="" data-content="{{ $task['notyet_read'] }}" data-original-title="{{ $task['reader'] }}">
                                    <b>{{$task['read']}}</b>
                                </a>
                            </td>
                            <td class="text-center">
                                <a tabindex="0" class="{{$task['count_status']}}"data-toggle="popover" data-trigger="focus" title="" data-content="{{ $task['notyet_done'] }}" data-original-title="{{ $task['already'] }}">
                                    <b>{{$task['status']}}</b>
                                </a>
                            </td>
                            <td>
                                @if ($task['is_it_done'] == 1)
                                    <a href="javascript:;" disabled="disabled" class="btn btn-success btn-rounded waves-effect w-md waves-danger m-b-5" >Selesai</a>
                                @else
                                    <a href="javascript:;" disabled="disabled" class="btn btn-danger btn-rounded waves-effect w-md waves-danger m-b-5" >Belum</a>
                                @endif
                            </td>
                            <td>
                                @if (array_search("EMTAU",$page))
                                    <a href="{{route('task.edit',['id'=>$task['id']])}}" class="btn btn-custom btn-trans waves-effect w-md waves-danger m-b-5">Edit</a>
                                @endif
                                @if (array_search("EMTAD",$page))
                                    <a href="javascript:;" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteTask({{$task['id']}})">Delete</a>
                                @endif
                            </td>
                        </tr>
                        @php($i++)
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- end row -->
    <!--  Modal content for the above example -->
    <div class="modal fade bs-example-modal-lg" id="modalLarge" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg" id="do-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Detail Task</h4>
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

    <!-- Sweet Alert Js  -->
    <script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>

    <!-- Magnific popup -->
    <script type="text/javascript" src="{{ asset('assets/plugins/magnific-popup/dist/jquery.magnific-popup.min.js') }}"></script>
@endsection

@section('script-js')

<script type="text/javascript">
    $(document).ready(function () {
        // Responsive Datatable
        $('#responsive-datatable').DataTable();

        $('.image-popup').magnificPopup({
            type: 'image',
        });
    });

    function getDescribe(id){
        var employee = $('#employee_id').val();
        var page = "task";

        $.ajax({
            url : '{{route('task.show',['id'=>1])}}',
            type : "get",
            dataType: 'json',
            data:{
                id:id,
                employee_id:employee,
                page:page,
            },
        }).done(function (data) {
            $('#modalView').html(data);
            $('#modalLarge').modal("show");
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function deleteTask(id){
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
                url: "task/"+id,
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
</script>
@endsection
