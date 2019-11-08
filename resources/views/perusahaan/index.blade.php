@extends('layout.main')
@php
    use App\Perusahaan;
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
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Index Perusahaan</h4>
                <p class="text-muted font-14 m-b-30">
                    @if (array_search("MDPRC",$page))
                        <a href="{{ route('perusahaan.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah Perusahaan</a>
                    @endif
                </p>

                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Nama Perusahaan</th>
                        <th>Alamat</th>
                        <th>No Telp</th>
                        <th>CP 1</th>
                        <th>CP 2</th>
                        <th>CP 3</th>
                        <th>Actions</th>
                    </thead>

                    <tbody>
                        @php($i = 1)
                        @foreach($perusahaan as $prs)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$prs->nama}}</td>
                            <td>{{$prs->alamat}}</td>
                            <td>{{$prs->telp}}</td>
                            <td>{{$prs->cp1}}</td>
                            <td>{{$prs->cp2}}</td>
                            <td>{{$prs->cp3}}</td>
                            <td>
                                @if (array_search("MDPRU",$page))
                                <a href="{{ route('perusahaan.edit', ['id' => $prs->id]) }}" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5">Edit</a>
                                @endif
                                @if (array_search("MDPRD",$page))
                                <form class="" action="{{ route('perusahaan.destroy', ['id' => $prs->id]) }}" method="post">
                                    {{ csrf_field() }}
                                    {{ method_field('delete') }}
                                    <button type="submit" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5">Hapus </button>
                                </form>
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

    {{-- Fingerprint --}}
    <script src="{{ asset('assets/fingerprint/jquery.timer.js') }}"></script>
    <script src="{{ asset('assets/fingerprint/ajaxmask.js') }}"></script>
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
</script>
@endsection
