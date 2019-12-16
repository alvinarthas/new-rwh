@extends('layout.main')
@php
    use App\SubModul;
    use App\MenuMapping;
@endphp
@section('css')
@endsection

@section('content')
    {{-- Current Menu Mapping --}}
    <form id="formdelete" role="form" action="{{route('PurMapDelete')}}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="user_id" value="{{$id}}">
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">Current Mapping</h4>

                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>Action</th>
                        </thead>

                        <tbody>
                            @php($i = 1)
                            @foreach($currents as $current)
                            <tr>
                                <td>{{$i}}</td>
                                <td>{{$current->supplier->nama}}</td>
                                <td>
                                    <div>
                                        <input id="checkboxcurrent{{$current->supplier_id}}" name="current[]" type="checkbox" value="{{$current->supplier_id}}">
                                        <label for="checkboxcurrent{{$current->supplier_id}}"></label>
                                    </div>
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
    <form id="formstore" action="{{route('PurMapStore')}}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="user_id" value="{{$id}}">
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">List Supplier</h4>

                    <table id="responsive-datatable2" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                        <thead>
                            <th>No</th>
                            <th>Nama Supplier</th>
                            <th>Action</th>
                        </thead>

                        <tbody>
                            @php($i = 1)
                            @isset($rests)
                            @foreach($rests as $rest)
                            
                            <tr>
                                <td>
                                    {{$i}}
                                </td>
                                <td>{{$rest->nama}}</td>
                                <td>
                                    <div>
                                        <input id="checkboxrest{{$rest->id}}" name="rest[]" type="checkbox" value="{{$rest->id}}">
                                        <label for="checkboxrest{{$rest->id}}">
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            
                            @php($i++)
                            @endforeach
                            @else
                            
                            @endisset
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
<script>
    $('#responsive-datatable2').DataTable();
    $('#responsive-datatable').DataTable();
    
    $("#formstore").submit(function(e){
        count_rest = $('input[name="rest[]"]:checked').length;

        if(count_rest > 0){
            $( "#formstore" ).submit();
        }else{
            toastr.warning("Belum ada data yang di check, silahkan coba lagi", 'Warning!')
            e.preventDefault();
        }
    });

    $("#formdelete").submit(function(e){
        count_current = $('input[name="current[]"]:checked').length;

        if(count_current > 0){
            $( "#formDelete" ).submit();
        }else{
            toastr.warning("Belum ada data yang di check, silahkan coba lagi", 'Warning!')
            e.preventDefault();
        }
    });
</script>
@endsection