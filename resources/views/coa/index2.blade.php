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
                <h4 class="m-t-0 header-title">Index COA</h4>
                <p class="text-muted font-14 m-b-30">
                    @if (array_search("FICOC",$page))
                        <a href="{{ route('coa.create') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Tambah COA</a>
                    @endif
                    <a href="{{ route('coa.index') }}" class="btn btn-primary btn-rounded w-md waves-effect waves-light m-b-5">Show Full COA</a>
                </p>

                <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                    <thead>
                        <th>No</th>
                        <th>Account Number</th>
                        <th>Account Name</th>
                        <th>Account Parent</th>
                        <th>Posisi Saldo Normal</th>
                        <th>Status Account</th>
                        <th>Action</th>
                    </thead>

                    <tbody>
                        @php($i = 1)
                        @foreach($coa as $item)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{{$item->AccNo}}</td>
                            <td>{{$item->AccName}}</td>
                            <td>{{$item->AccParent}}</td>
                            <td>{{$item->SaldoNormal}}</td>
                            <td>{{$item->StatusAccount}}</td>
                            <td>
                                @if (array_search("FICOU",$page))
                                    <a href="{{route('coa.edit',['id'=>$item->id])}}" class="btn btn-custom btn-rounded waves-effect waves-light w-md m-b-5">Edit</a>
                                @endif
                                @if (array_search("FICOD",$page))
                                    <form class="" action="{{ route('coa.destroy', ['id' => $item->id]) }}" method="post">
                                        {{ csrf_field() }}
                                        {{ method_field('delete') }}
                                        <button type="submit" class="btn btn-danger btn-rounded waves-effect waves-light w-md m-b-5">Hapus </button></a>
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
@endsection

@section('script-js')
<script type="text/javascript">

    $(document).ready(function () {

        // Responsive Datatable
        $('#responsive-datatable').DataTable();
    });
    
</script>
@endsection