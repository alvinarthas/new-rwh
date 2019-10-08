@extends('layout.main')
@php
    use App\Perusahaan;
@endphp

@section('css')
    <!--venobox lightbox-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/magnific-popup/dist/magnific-popup.css') }}"/>

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
                <h3 class="l-h-34 card-title">Security</h3>
                <div class="form-group row">
                    <label class="col-2 col-form-label">Masukkan No. ATM</label>
                    <div class="col-5">
                        <input type="text" class="form-control" parsley-trigger="change" required name="noatm" id="noatm">
                    </div>
                    <div class="col-5">
                        <button class="btn btn-primary waves-effect waves-light" onclick="getATM()" type="submit">
                            Cek Kartu ATM
                        </button>
                    </div>
                </div>
                <div id="atm"></div>
            </div>
        </div>
    </div> <!-- end row -->
@endsection

@section('js')
    <!-- Modal-Effect -->
    <script src="{{ asset('assets/plugins/custombox/dist/custombox.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/custombox/dist/legacy.min.js') }}"></script>

    <!-- Magnific popup -->
    <script type="text/javascript" src="{{ asset('assets/plugins/magnific-popup/dist/jquery.magnific-popup.min.js') }}"></script>
@endsection

@section('script-js')

<script type="text/javascript">
    function getATM(){
        var noatm = $("#noatm").val();
        $.ajax({
            url : "{{route('security.getatm')}}",
            type : "get",
            dataType: 'html',
            data:{
                noatm : noatm,
            },
        }).done(function (data) {
            $("#atm").html(data);

        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

</script>
@endsection
