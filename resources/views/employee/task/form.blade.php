@extends('layout.main')

@section('css')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- form Uploads -->
    <link href="{{ asset('assets/plugins/fileuploads/css/dropify.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datepicker-->
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
    <!--datetimepicker-->
    <link href="{{ asset('assets/plugins/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
    <!-- Sweet Alert css -->
    <link href="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!--Token-->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('judul')
Tambah Task Pegawai
@endsection

@section('content')
    @if($jenis == "create")
        <form class="form-horizontal" role="form" action="{{ route('task.store') }}" enctype="multipart/form-data" method="POST">
    @elseif($jenis == "edit")
        <form class="form-horizontal" role="form" action="{{ route('task.update', ['id' => $task[0]->id]) }}" enctype="multipart/form-data" method="POST">
            {{ method_field('PUT') }}
    @endif

    @csrf
    {{-- Informasi Pribadi --}}
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <h4 class="m-t-0 header-title">Tambah Task Pegawai</h4>
                <p class="text-muted m-b-30 font-14">
                </p>

                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Judul Tugas</label>
                                <div class="col-10">
                                    <input type="text" class="form-control" parsley-trigger="change" placeholder="Judul" required name="title" id="title" value="@isset($task[0]->title){{$task[0]->title}}@endisset">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Deskripsi Tugas</label>
                                <div class="col-10">
                                    <textarea class="form-control" name="description" id="description" placeholder="Deskripsi dan detail tugas yang diberikan" rows="4" cols="100">@isset($task[0]->description){{ $task[0]->description }}@endisset</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Tanggal</label>
                                <div class="col-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="start_date" id="start_date" value="@isset($task[0]->start_date){{$task[0]->start_date}}@endisset" data-date-format="yyyy-mm-dd" autocomplete="off">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="ti-calendar"></i></span>
                                        </div>
                                    </div><!-- input-group -->
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Deadline Pengerjaan</label>
                                <div class="col-10">
                                    <div class="input-group">
                                        <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="due_date" id="due_date" value="@isset($task[0]->due_date){{$task[0]->due_date}}@endisset" data-date-format="yyyy-mm-dd" autocomplete="off">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="ti-calendar"></i></span>
                                        </div>
                                    </div><!-- input-group -->
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Ditujukan kepada</label>
                                <div class="col-10">
                                    <select class="select2 select2-multiple select2-hidden-accessible" name="employee[]" id="employee" multiple="multiple" data-placeholder="Pilih" tabindex="-1" aria-hidden="true">
                                        @isset($task[0]->employee_id)
                                            @foreach($task as $t)
                                                <option value="{{$t->employee->id}}" selected>{{$t->employee->name}}</option>
                                            @endforeach
                                            @foreach($employees as $e)
                                                <option value="{{$e->id}}">{{$e->name}}</option>
                                            @endforeach
                                        @else
                                            @foreach($employees as $e)
                                                <option value="{{$e->id}}">{{$e->name}}</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Upload Gambar Petunjuk</label>
                                <div class="col-10 increment">
                                    <input type="file" class="dropify" data-height="100" name="gambar[]" id="gambar" multiple/>
                                </div>
                                @isset($source)
                                    @if($count_source!=0)
                                        @foreach($source as $gambar)
                                            <div class="col-2"></div>
                                            <div class="col-10">
                                                <a href="{{ asset('assets/images/task/'.$gambar['source']) }}" class="image-popup" title="{{$gambar['source']}}">
                                                    <img src="{{ asset('assets/images/task/'.$gambar['source']) }}"  alt="user-img" title="{{ $gambar['source'] }}" width="200px" class="img-thumbnail img-responsive photo">
                                                </a>
                                                <a href="javascript:;" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteImage({{$gambar['id']}})">Delete</a>
                                            </div>
                                        @endforeach
                                    @endif
                                @endisset
                            </div>
                        </div>
                    </div>
                </div>
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
<!-- file uploads js -->
<script src="{{ asset('assets/plugins/fileuploads/js/dropify.min.js') }}"></script>
<!-- Validation js (Parsleyjs) -->
<script type="text/javascript" src="{{ asset('assets/plugins/parsleyjs/dist/parsley.min.js') }}"></script>
<!-- Datepicker -->
<script src="{{ asset('assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<!-- Timepicker -->
<script src="{{ asset('assets/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
<!-- Datetimepicker -->
<script src="{{ asset('assets/plugins/moment/moment2.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js') }}"></script>
<!-- Sweet Alert Js  -->
<script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>
@endsection

@section('script-js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('form').parsley();

            $(".btn-success").click(function(){
                var html = $(".clone").html();
                document.getElementById("file").style.display = 'block';
                $(".increment").after(html);
            });

            $("body").on("click",".btn-danger",function(){
                $(this).parents(".increment").remove();
            });

        });

        $('#start_date').datepicker();
        $('#due_date').datepicker();

        // $(function () {
        //     $('#due_date').datetimepicker();
        // });

        // $('#due_time').timepicker({
        //     showMeridian : false,
        //     defaultTIme : false,
        //     minuteStep : 30,
        //     icons: {
        //         up: 'mdi mdi-chevron-up',
        //         down: 'mdi mdi-chevron-down'
        //     }
        // });

        // Select2
        $(".select2").select2({
            templateResult: formatState,
            templateSelection: formatState,
            closeOnSelect: false,
            allowClear: true,
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

        function deleteImage(id){
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
                    url: "/task/image/"+id,
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

    <script type="text/javascript">
        $('.dropify').dropify({
            messages: {
                'default': 'Drag and drop a file here or click',
                'replace': 'Drag and drop or click to replace',
                'remove': 'Remove',
                'error': 'Ooops, something wrong appended.'
            },
            error: {
                'fileSize': 'The file size is too big (1M max).'
            }
        });
    </script>
@endsection
