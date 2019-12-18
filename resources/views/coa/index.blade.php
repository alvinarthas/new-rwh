@php
    use App\Coa;
@endphp
@extends('layout.main')

@section('css')
<!-- Treeview css -->
<link href="{{ asset('assets/plugins/jstree/style.css') }}" rel="stylesheet" type="text/css" />

<style>
    li.expand-font{
        font-size: medium;
    }
</style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <a href="{{ route('coaTable') }}" class="btn btn-success btn-rounded w-md waves-effect waves-light m-b-5">Show COA Table</a>
            <hr>
            <div class="card-box">
                <div id="basicTree">
                    <ul>
                        @foreach ($coas as $item1)
                            @php($count = Coa::checkChild($item1->AccNo))
                            @if ($count > 0)
                                <li class="expand-font" data-jstree='{"opened":true}'>{{$item1->AccNo}} -- {{$item1->AccName}}
                                    <ul>
                                        @foreach (Coa::getChild($item1->AccNo) as $get1)
                                            @php($count2 = Coa::checkChild($get1->AccNo))
                                            @if ($count2 > 0)
                                                <li data-jstree='{"opened":true}'>{{$get1->AccNo}} -- {{$get1->AccName}}
                                                    <ul>
                                                        @foreach (Coa::getChild($get1->AccNo) as $get2)
                                                            @php($count3 = Coa::checkChild($get2->AccNo))
                                                            @if ($count3 > 0)
                                                                <li data-jstree='{"opened":true}'>{{$get2->AccNo}} -- {{$get2->AccName}}
                                                                    <ul>
                                                                        @foreach (Coa::getChild($get2->AccNo) as $get3)
                                                                            @php($count4 = Coa::checkChild($get3->AccNo))
                                                                            @if ($count4 > 0)
                                                                                <li data-jstree='{"opened":true}'>{{$get3->AccNo}} -- {{$get3->AccName}}
                                                                                    <ul>
                                                                                        @foreach (Coa::getChild($get3->AccNo) as $get4)
                                                                                            @php($count5 = Coa::checkChild($get4->AccNo))
                                                                                            @if ($count5 > 0)
                                                                                                <li data-jstree='{"opened":true}'>{{$get4->AccNo}} -- {{$get4->AccName}}
                                                                                                    <ul>
                                                                                                        @foreach (Coa::getChild($get4->AccNo) as $get5)
                                                                                                            @php($count6 = Coa::checkChild($get5->AccNo))
                                                                                                            @if ($count6 > 0)
                                                                                                                <li data-jstree='{"opened":true}'>{{$get5->AccNo}} -- {{$get5->AccName}}</li>
                                                                                                            @else
                                                                                                                <li data-jstree='{"type":"file"}'>{{$get5->AccNo}} -- {{$get5->AccName}}</li>
                                                                                                            @endif
                                                                                                        @endforeach
                                                                                                    </ul>
                                                                                                </li>
                                                                                            @else
                                                                                                <li data-jstree='{"type":"file"}'>{{$get4->AccNo}} -- {{$get4->AccName}}</li>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </ul>
                                                                                </li>
                                                                            @else
                                                                                <li data-jstree='{"type":"file"}'>{{$get3->AccNo}} -- {{$get3->AccName}}</li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                </li>
                                                            @else
                                                                <li data-jstree='{"type":"file"}'>{{$get2->AccNo}} -- {{$get2->AccName}}</li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @else
                                                <li data-jstree='{"type":"file"}'>{{$get1->AccNo}} -- {{$get1->AccName}}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li data-jstree='{"type":"file"}'>{{$item1->AccNo}} -- {{$item1->AccName}}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div> <!-- end row -->
@endsection

@section('js')
<!-- Tree view js -->
<script src="{{ asset('assets/plugins/jstree/jstree.min.js') }}"></script>
<script src="{{ asset('assets/pages/jquery.tree.js') }}"></script>
@endsection

@section('script-js')
@endsection