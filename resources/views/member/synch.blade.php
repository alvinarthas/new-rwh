@extends('layout.main')

@section('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Multi Item Selection examples -->
    <link href="{{ asset('assets/plugins/datatables/select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- Select2 --}}
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@php
    use App\Koordinator;
    use App\SubKoordinator;
@endphp
<form action="http://www.royalmarketinginternational.com/erp/synch_process.php" method="post" target="_blank">
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card-box" >
                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <h4 class="m-t-0 header-title">Synchronize Special Member RWH to RMI</h4>
                            <p class="text-muted font-14 m-b-30">
                                Detail Member
                            </p>
                            <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                                <thead>
                                    <th>No</th>
                                    <th>Synchronize</th>
                                    <th>Member ID</th>
                                    <th>Nama</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Koordinator</th>
                                    <th>Subkoordinator</th>
                                </thead>

                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach($member as $m)
                                    <tr class="trow">
                                        <td>{{$i}}</td>
                                        <td class="text-center"><input type='checkbox' name="cb{{ $i }}" id="cb{{ $i }}" value="{{ $m['member_id'] }}" parsley-trigger="change"></td>
                                        <td><input type="hidden" name="memberid{{ $i }}" id="memberid{{ $i }}" value="{{ $m['member_id'] }}">{{$m['member_id']}}</td>
                                        <td><input type="hidden" name="nama{{ $i }}" id="nama{{ $i }}" value="{{ $m['nama'] }}">{{ $m['nama']}}</td>
                                        @php
                                            $tgl_lahir = date("d-m-Y", strtotime($m['tgl_lahir']));
                                            $koor = Koordinator::where('id', $m['koordinator'])->select('nama')->first();
                                            $subkoor = SubKoordinator::where('id', $m['subkoor'])->select('nama')->first();
                                            $i++;
                                        @endphp
                                        <td><input type="hidden" name="tgllhr{{ $i }}" id="tgllhr{{ $i }}" value="{{ $tgl_lahir }}">{{$tgl_lahir}}</td>
                                        <td><input type="hidden" name="koordinator{{ $i }}" id="koordinator{{ $i }}" value="{{ $koor['nama'] }}">{{$koor['nama']}}</td>
                                        <td><input type="hidden" name="subkor{{ $i }}" id="subkor{{ $i }}" value="{{ $subkoor['nama'] }}">{{$subkoor['nama']}}</td>
                                    </tr>
                                    @endforeach
                                    <input type="hidden" name="ctr" id="ctr" value="{{ $i }}">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group text-right m-b-0">
                        <button class="btn btn-primary waves-effect waves-light" type="submit">
                            Synchronize Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
@endsection

@section('js')
    {{-- Select2 --}}
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>

    <!-- Required datatable js -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
@endsection

@section('script-js')
<script>
    $('#responsive-datatable').DataTable();
</script>
@endsection
