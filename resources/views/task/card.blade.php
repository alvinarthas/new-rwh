@extends('layout.main')

@section('css')
    <link href="{{ asset('assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <!-- form Uploads -->
    <link href="{{ asset('assets/plugins/fileuploads/css/dropify.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Custom box (Modal) css -->
    <link href="{{ asset('assets/plugins/custombox/dist/custombox.min.css') }}" rel="stylesheet">
    <!-- Dragula (Drag and drop) css -->
    <link href="{{ asset('assets/plugins/dragula/dragula.min.css') }}" rel="stylesheet">
    <!--select2-->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('judul')
Task
@endsection

@section('content')
<div class="row">
    <div class="col-xl-4">
        <div class="card-box taskboard-box">
            <h4 class="header-title m-t-0 m-b-30 text-primary">Tugas Yang Harus Diselesaikan</h4>

            <ul class="list-unstyled task-list" id="drag-upcoming">
                <li>
                    <div class="card-box kanban-box">
                        <div class="kanban-detail">
                            <h4><a href="">Improve animation loader</a></h4>
                            <ul class="list-inline m-b-0">
                                <li class="list-inline-item">
                                    <img src="assets/images/users/avatar-2.jpg" alt="img" class="thumb-sm rounded-circle">
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="5 Tasks">
                                        <i class="mdi mdi-format-align-left"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="3 Comments">
                                        <i class="mdi mdi-comment-outline"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="card-box kanban-box">
                        <div class="kanban-detail">
                            <span class="badge badge-warning pull-right">High</span>
                            <h4><a href="">Write a release note for Ubol v1.5</a> </h4>
                            <ul class="list-inline m-b-0">
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="Username">
                                        <img src="assets/images/users/avatar-3.jpg" alt="img"
                                             class="thumb-sm rounded-circle">
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="5 Tasks">
                                        <i class="mdi mdi-format-align-left"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="3 Comments">
                                        <i class="mdi mdi-comment-outline"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="card-box kanban-box">
                        <div class="kanban-detail">
                            <h4><a href="">Invite user to a project</a> </h4>
                            <ul class="list-inline m-b-0">
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="Username">
                                        <img src="assets/images/users/avatar-4.jpg" alt="img"
                                             class="thumb-sm rounded-circle">
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="5 Tasks">
                                        <i class="mdi mdi-format-align-left"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="3 Comments">
                                        <i class="mdi mdi-comment-outline"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="card-box kanban-box">
                        <div class="kanban-detail">
                            <span class="badge badge-danger pull-right">Urgent</span>
                            <h4><a href="">Code HTML email template for welcome email</a> </h4>
                            <ul class="list-inline m-b-0">
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="Username">
                                        <img src="assets/images/users/avatar-5.jpg" alt="img"
                                             class="thumb-sm rounded-circle">
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="5 Tasks">
                                        <i class="mdi mdi-format-align-left"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="3 Comments">
                                        <i class="mdi mdi-comment-outline"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

            </ul>

            <div class="text-center">
                <a href="#custom-modal" class="btn btn-light btn-md waves-effect waves-light"
                   data-animation="fadein" data-plugin="custommodal" data-overlaySpeed="200" data-overlayColor="#36404a">
                    <i class="zmdi zmdi-plus"></i> Add New
                </a>
            </div>
        </div>
    </div><!-- end col -->

    <div class="col-xl-4">
        <div class="card-box taskboard-box">
            <h4 class="header-title m-t-0 m-b-30 text-success">Selesai</h4>

            <ul class="list-unstyled task-list" id="drag-complete">
                <li>
                    <div class="card-box kanban-box">
                        <div class="kanban-detail">
                            <span class="badge badge-danger pull-right">Urgent</span>
                            <h4><a href="">Improve animation loader</a> </h4>
                            <ul class="list-inline m-b-0">
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="Username">
                                        <img src="assets/images/users/avatar-9.jpg" alt="img"
                                             class="thumb-sm rounded-circle">
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="5 Tasks">
                                        <i class="mdi mdi-format-align-left"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="3 Comments">
                                        <i class="mdi mdi-comment-outline"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="card-box kanban-box">
                        <div class="kanban-detail">
                            <span class="badge badge-warning pull-right">High</span>
                            <h4><a href="">Write a release note for Ubol v1.5</a> </h4>
                            <ul class="list-inline m-b-0">
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="Username">
                                        <img src="assets/images/users/avatar-1.jpg" alt="img"
                                             class="thumb-sm rounded-circle">
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="5 Tasks">
                                        <i class="mdi mdi-format-align-left"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="3 Comments">
                                        <i class="mdi mdi-comment-outline"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

                <li>
                    <div class="card-box kanban-box">
                        <div class="kanban-detail">
                            <h4><a href="">Invite user to a project</a> </h4>
                            <ul class="list-inline m-b-0">
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="Username">
                                        <img src="assets/images/users/avatar-2.jpg" alt="img"
                                             class="thumb-sm rounded-circle">
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="5 Tasks">
                                        <i class="mdi mdi-format-align-left"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="3 Comments">
                                        <i class="mdi mdi-comment-outline"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>


                <li>
                    <div class="card-box kanban-box">
                        <div class="kanban-detail">
                            <span class="badge badge-danger pull-right">Urgent</span>
                            <h4><a href="">Code HTML email template for welcome email</a> </h4>
                            <ul class="list-inline m-b-0">
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="Username">
                                        <img src="assets/images/users/avatar-3.jpg" alt="img"
                                             class="thumb-sm rounded-circle">
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="5 Tasks">
                                        <i class="mdi mdi-format-align-left"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="" data-toggle="tooltip" data-placement="top"
                                       title="" data-original-title="3 Comments">
                                        <i class="mdi mdi-comment-outline"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>

            </ul>

            <div class="text-center">
                <a href="#custom-modal" class="btn btn-light btn-md waves-effect waves-light"
                   data-animation="fadein" data-plugin="custommodal" data-overlaySpeed="200" data-overlayColor="#36404a">
                    <i class="zmdi zmdi-plus"></i> Add New
                </a>
            </div>
        </div>
    </div><!-- end col -->

    <!-- Modal -->
    <div id="custom-modal" class="modal-demo">
        <button type="button" class="close" onclick="Custombox.close();">
            <span>&times;</span><span class="sr-only">Close</span>
        </button>
        <h4 class="custom-modal-title">Add New</h4>
        <div class="custom-modal-text text-left">
            <form role="form">
                <div class="form-group">
                    <label for="name">Task Name</label>
                    <input type="text" class="form-control" id="name" placeholder="">
                </div>

                <div class="form-group">
                    <label for="name">Ditujukan untuk</label>
                    <select class="select2 select2-multiple select2-hidden-accessible" multiple="multiple" data-placeholder="Choose ..." tabindex="-1" aria-hidden="true">
                        <option>Andri</option>
                        <option>Geni</option>
                        <option>Alvin</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Sdate">Start Date</label>
                            <input type="text" class="form-control" id="Sdate" placeholder="">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Ddate">Due Date</label>
                            <input type="text" class="form-control" id="Ddate" placeholder="">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success waves-effect waves-light">Save</button>
                <button type="button" class="btn btn-danger waves-effect waves-light m-l-5" onclick="Custombox.close();">Cancel</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
    <!-- drgula (Drag and drop) js -->
    <script src="{{ asset('assets/plugins/dragula/dragula.min.js') }}"></script>
    <!-- Modal-Effect -->
    <script src="{{ asset('assets/plugins/custombox/dist/custombox.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/custombox/dist/legacy.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
@endsection

@section('script-js')
<script type="text/javascript">
    // Select2
    $(".select2").select2({
        dropdownParent: $('#custom-modal'),
        closeOnSelect: false,
        allowClear: true,
    });

    dragula([document.querySelector('#drag-upcoming'), document.querySelector('#drag-inprogress'), document.querySelector('#drag-complete')], {
        isContainer: function (el) {
            return false; // only elements in drake.containers will be taken into account
        },
        moves: function (el, source, handle, sibling) {
            return true; // elements are always draggable by default
        },
        accepts: function (el, target, source, sibling) {
            return true; // elements can be dropped in any of the `containers` by default
        },
        invalid: function (el, handle) {
            return false; // don't prevent any drags from initiating by default
        },
        direction: 'vertical',             // Y axis is considered when determining where an element would be dropped
        copy: false,                       // elements are moved by default, not copied
        copySortSource: false,             // elements in copy-source containers can be reordered
        revertOnSpill: false,              // spilling will put the element back where it was dragged from, if this is true
        removeOnSpill: false,              // spilling will `.remove` the element, if this is true
        mirrorContainer: document.body,    // set the element that gets mirror elements appended
        ignoreInputTextSelection: true     // allows users to select input text, see details below
    });
</script>
@endsection
