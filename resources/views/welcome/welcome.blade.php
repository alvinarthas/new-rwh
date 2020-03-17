@extends('layout.main')
@php
    use App\Coa;
    use App\TaskEmployee;
@endphp

@section('css')
<style>
    span.desc{
        font-size: 14px;
    }
</style>
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
@endsection

@section('judul')

@endsection

@section('content')
@php
    use Carbon\Carbon;
@endphp
{{-- <div class="card-box">
    <div class="row">
        <div class="col-md-12">
            <div class="home-fullscreen">
                <div class="full-screen">
                    <div class="home-wrapper home-wrapper-alt">
                        <div class="row">
                            <div class="form-group col-md-2">
                                <img src="{{ asset('assets/images/employee/foto/'.session('foto')) }}"  alt="user-img" title="{{ session('name') }}" class="rounded-circle img-thumbnail img-responsive photo">
                            </div>
                            <div class="form-group col-md-8">
                                <h1 class="text-dark">Hai {{ session('name') }},</h1>
                                @php
                                    Carbon::setLocale('id');
                                    $getTime = Carbon::now()->toTimeString();
                                    if($getTime < '12:00:00'){
                                        $time = "PAGI";
                                    }elseif(($getTime >='12:00:00') && ($getTime < '15:00:00')){
                                        $time = "SIANG";
                                    }elseif(($getTime >='15:00:00') && ($getTime < '18:00:00')){
                                        $time = "SORE";
                                    }elseif($getTime >='18:00:00'){
                                        $time = "MALAM";
                                    }
                                @endphp
                                <p class="text-dark">SELAMAT {{ $time }}, DAN SELAMAT BEKERJA!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}
{{-- <div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="form-group col-md-1">
            </div>
            <div class="form-group col-md-8">
                <h1 class="text-dark">Hai {{ session('name') }},</h1>
                @php
                    Carbon::setLocale('id');
                    $getTime = Carbon::now()->toTimeString();
                    if($getTime < '12:00:00'){
                        $time = "PAGI";
                    }elseif(($getTime >='12:00:00') && ($getTime < '15:00:00')){
                        $time = "SIANG";
                    }elseif(($getTime >='15:00:00') && ($getTime < '18:00:00')){
                        $time = "SORE";
                    }elseif($getTime >='18:00:00'){
                        $time = "MALAM";
                    }
                @endphp
                <p class="text-dark">SELAMAT {{ $time }}, DAN SELAMAT BEKERJA!</p>
            </div>
        </div>
    </div>
</div> --}}

<div class="card-box">
    <div class="row">
        <div class="form-group col-md-8">
            <h1 class="text-dark">Hai {{ session('name') }},</h1>
            @php
                Carbon::setLocale('id');
                $getTime = Carbon::now()->toTimeString();
                if($getTime < '12:00:00'){
                    $time = "PAGI";
                }elseif(($getTime >='12:00:00') && ($getTime < '15:00:00')){
                    $time = "SIANG";
                }elseif(($getTime >='15:00:00') && ($getTime < '18:00:00')){
                    $time = "SORE";
                }elseif($getTime >='18:00:00'){
                    $time = "MALAM";
                }
            @endphp
            <p class="text-dark"><strong>SELAMAT {{ $time }}, DAN SELAMAT BEKERJA!</strong></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <img src="{{ asset('assets/images/employee/foto/'.session('foto')) }}"  alt="user-img" title="{{ session('name') }}" class="rounded-circle img-thumbnail img-responsive photo">
        </div>
        <div class="form-group col-md-8" style="font-size:initial;">
            <div class="form-group row">
                <label class="col-2 col-form-label">Nama:</label>
                <label class="col-10 col-form-label">{{$user->name}}</label>
            </div>
            <div class="form-group row">
                <label class="col-2 col-form-label">NIP:</label>
                <label class="col-10 col-form-label">{{$user->nip}}</label>
            </div>
            @if(session('role') != 'Direktur Utama')
            <hr>
            <h4 style="color:mediumblue"><strong>Bonus dan Gaji Per Hari ini</strong></h4>
            <hr>
            <div class="row">
                <div class="col-sm-6">
                    <ul class="list-group m-b-0 user-list">
                        <li class="list-group-item">
                            <div class="user-list-item">
                                <div class="avatar">
                                    <img src="{{ asset('assets/images/flaticon/internal.png') }}" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name"><strong>Tugas Internal</strong></span>
                                    <span class="desc">Rp {{number_format($bonus['internal'],2,",",".")}}</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="user-list-item">
                                <div class="avatar">
                                    <img src="{{ asset('assets/images/flaticon/logistic.png') }}" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name"><strong>Tugas Logistik</strong></span>
                                    <span class="desc">Rp {{number_format($bonus['logistik'],2,",",".")}}</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="user-list-item">
                                <div class="avatar">
                                    <img src="{{ asset('assets/images/flaticon/company.png') }}" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name"><strong>Kendali Perusahaan</strong></span>
                                    <span class="desc">Rp {{number_format($bonus['kendali'],2,",",".")}}</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <ul class="list-group m-b-0 user-list">
                        <li class="list-group-item">
                            <div class="user-list-item">
                                <div class="avatar">
                                    <img src="{{ asset('assets/images/flaticon/discount.png') }}" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name"><strong>Gaji Pokok & Tunjangan Jabatan</strong></span>
                                    <span class="desc">Rp {{number_format($bonus['gaji_pokok'],2,",",".")}} - Rp {{number_format($bonus['tunjangan_jabatan'],2,",",".")}}</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="user-list-item">
                                <div class="avatar">
                                    <img src="{{ asset('assets/images/flaticon/bonus.png') }}" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name"><strong>Total Bonus sekarang</strong></span>
                                    <span class="desc">Rp {{number_format($bonus['total_bonus'],2,",",".")}}</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="user-list-item">
                                <div class="avatar">
                                    <img src="{{ asset('assets/images/flaticon/money.png') }}" alt="">
                                </div>
                                <div class="user-desc">
                                    <span class="name"><strong>Take Home Pay</strong></span>
                                    <span class="desc">Rp {{number_format($bonus['take_home_pay'],2,",",".")}}</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Task Employee</h4>
                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Dibuat pada</th>
                        <th>Deadline</th>
                        <th>Creator</th>
                        @if(session('role') == 'Superadmin' || session('role') == 'Direktur Utama' || session('role') == 'Manager IT' || session('role') == 'Manager Keuangan' || session('role') == 'Manager Operasional' || session('role') == 'General Manager')
                            <th>Status Dilihat</th>
                            <th>Status Dikerjakan</th>
                        @endif
                    </thead>

                    <tbody>
                        @php($i = 1)
                        @foreach($data as $task)
                            @if(TaskEmployee::where('employee_id',session('user_id'))->where('task_id', $task['id'])->count() == 1)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td><a href="javascript:;" onclick="getDescribe('{{ $task['id'] }}')" disabled="disabled">{{$task['title']}}</a></td>
                                    <input type="hidden" name="employee_id" id="employee_id" value="{{session('user_id')}}">
                                    <td>{{$task['created_at']}}</td>
                                    <td>{{$task['due_date']}}</td>
                                    <td>{{$task['creator']}}</td>
                                    @if(session('role') == 'Superadmin' || session('role') == 'Direktur Utama' || session('role') == 'Manager IT' || session('role') == 'Manager Keuangan' || session('role') == 'Manager Operasional' || session('role') == 'Direktur Utama')
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
                                    @endif
                                </tr>
                                @php($i++)
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- end row -->
    @if(session('role') == 'Superadmin' || session('role') == 'Direktur Utama')
        <div class="card-box table-responsive">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a href="#hutang" data-toggle="tab" aria-expanded="false" class="nav-link active">
                        Hutang
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#piutang" data-toggle="tab" aria-expanded="true" class="nav-link">
                        Piutang
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#kas" data-toggle="tab" aria-expanded="true" class="nav-link">
                        Kas/Bank
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade show active" id="hutang">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-box table-responsive">
                                <h4>SISA HUTANG SUPPLIER</h4>
                                <table id="datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Supplier</th>
                                            <th>Sisa Hutang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($i=1)
                                        @foreach ($hutang as $item)
                                            @if($item['sisa'] > 20000000)
                                                <tr>
                                                    <td>{{$i}}</td>
                                                    <td>{{$item['id']}}</td>
                                                    <td><strong>Rp {{number_format($item['sisa'],2,',','.')}}</strong></td>
                                                </tr>
                                                @php($i++)
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="piutang">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-box table-responsive">
                                <h4>SISA PIUTANG</h4>
                                <table id="datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Customer</th>
                                            <th>Sisa Piutang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($i=1)
                                        @foreach ($piutang as $item2)
                                            @if($item2['sisa'] > 20000000)
                                                <tr>
                                                    <td>{{$i}}</td>
                                                    <td>{{$item2['name']}}</td>
                                                    <td><strong>Rp {{number_format($item2['sisa'],2,',','.')}}</strong></td>
                                                </tr>
                                                @php($i++)
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="kas">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-box table-responsive">
                                <h4>KAS / Bank</h4>
                                <table id="datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Account</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php($coa = Coa::where('AccParent',"1.1.1")->where('AccNo','NOT LIKE',"1.1.1")->get())
                                        @php(Coa::kasBank($coa))
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
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

    <!-- Sweet Alert Js  -->
    <script src="{{ asset('assets/plugins/sweet-alert/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/pages/jquery.sweet-alert.init.js') }}"></script>

    <!-- Magnific popup -->
    <script type="text/javascript" src="{{ asset('assets/plugins/magnific-popup/dist/jquery.magnific-popup.min.js') }}"></script>
@endsection

@section('script-js')
<script>
    function getDescribe(id){
        var employee = $('#employee_id').val();
        var page = "dashboard";
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
</script>
@endsection
