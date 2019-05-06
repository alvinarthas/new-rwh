@extends('layout.main')
@php
    use App\SubModul;
    use App\JenisMapping;
@endphp
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
    {{-- Current Menu Mapping --}}
    <form action="{{route('deleteMapping')}}" method="post">
        @csrf
        <input type="hidden" name="user_id" value="{{$id}}">
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">Current Mapping</h4>

                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Nama Modul</th>
                            <th>Nama Submodul</th>
                            <th>Action</th>
                        </thead>

                        <tbody>
                            @php($i = 1)
                            @foreach($currents as $current)
                            <tr>
                                <td>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="current[]" name="current[]" value="{{$current->id}}">
                                        <label class="form-check-label" for="autoSizingCheck">
                                            {{$i}}
                                        </label>
                                    </div>
                                </td>
                                <td>{{SubModul::where('submodul_id',$current->submodul_id)->first()->modul()->first()->modul_desc}}</td>
                                <td>{{$current->submodul->submodul_desc}}</td>
                                @php($jenismapping = JenisMapping::where('mapping_id',$current->id)->get())
                                <td>
                                    {{-- @foreach ($jenismapping as $jmap)
                                        @if ($jmap->jenis == "create" || $jmap->jenis == "update" || $jmap->jenis == "delete")
                                            <div class="checkbox checkbox-primary">
                                                <input id="checkbox2" type="checkbox">
                                                <label for="checkbox2">
                                                    Check me out !
                                                </label>
                                            </div>
                                        @endif
                                    @endforeach --}}
                                </td>
                            </tr>
                            @php($i++)
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- end row -->

        <div class="form-group text-right m-b-0">
            <button class="btn btn-primary waves-effect waves-light" type="submit">
                Submit
            </button>
        </div>
    </form>

    {{-- Rest of Menu Mapping --}}
    <form action="{{route('storeMapping')}}" method="post">
        @csrf
        <input type="hidden" name="user_id" value="{{$id}}">
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">List Modul</h4>

                    <table id="responsive-datatable2" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Nama Modul</th>
                            <th>Nama Submodul</th>
                            <th>Action</th>
                        </thead>

                        <tbody>
                            @php($i = 1)
                            @foreach($rests as $rest)
                            <tr>
                                <td>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="rest[]" name="rest[]" value="{{$rest->submodul_id}}">
                                        <label class="form-check-label" for="autoSizingCheck">
                                            {{$i}}
                                        </label>
                                    </div>
                                </td>
                                <td>{{SubModul::where('submodul_id',$rest->submodul_id)->first()->modul()->first()->modul_desc}}</td>
                                <td>{{$rest->submodul_desc}}</td>
                                <td>
                                    
                                </td>
                            </tr>
                            @php($i++)
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- end row -->

        <div class="form-group text-right m-b-0">
            <button class="btn btn-primary waves-effect waves-light" type="submit">
                Submit
            </button>
        </div>
    </form>
@endsection

@section('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
@endsection

@section('script-js')
<script type="text/javascript">

    $(document).ready(function () {

        // Responsive Datatable
        $('.table').DataTable();
    });
    
</script>
@endsection