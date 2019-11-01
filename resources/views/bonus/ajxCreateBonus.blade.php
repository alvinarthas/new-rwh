@if($bonusapa=="perhitungan" OR $bonusapa=="pembayaran")
    @if($bonusapa=="perhitungan")
        <form method="post" action="{{ route('uploadBonusPerhitungan') }}" enctype="multipart/form-data">
    @elseif($bonusapa=="pembayaran")
        <form method="post" action="{{ route('uploadBonusPembayaran') }}" enctype="multipart/form-data">
    @endif
    {{ csrf_field() }}
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                @if($bonusapa=="perhitungan")
                    <h4 class="m-t-0 header-title">Masukkan File Excel Untuk Perhitungan Bonus <a href="{{ asset('excel/UploadBonusMember.xls') }}" target="_blank">&lt; Download Template Excel Bonus&gt;</a></h4>
                @elseif($bonusapa=="pembayaran")
                    <h4 class="m-t-0 header-title">Masukkan File Excel Untuk Pembayaran Bonus <a href="{{ asset('excel/UploadPembayaranBonus.xls') }}" target="_blank">&lt; Download Template Excel Bonus&gt;</a></h4>
                @endif
                <div class="form-row">
                    <label for="file" class="col-form-label">Import File (.xlsx)</label>
                    <input type="file" class="form-control-file" name="file">
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    @if($bonusapa=="perhitungan")
                        <input type="hidden" name="perusahaan" value="{{ $perusahaan }}">
                    @elseif($bonusapa=="pembayaran")
                        <input type="hidden" name="AccNo" id="AccNo" value="{{ $AccNo }}">
                        <input type="hidden" name="bank_id" id="bank_id" value="{{ $bank }}">
                        <input type="hidden" name="tgl" id="tgl" value="{{ $tgl }}">
                    @endif
                </div>
                <div class="form-row pull-right m-b-0">
                    <div class="form-group">
                        <button type="submit" class="btn btn-danger">Upload Excel Bonus</button>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>
</form>
@endif

@if($bonusapa=="perhitungan")
    <form class="form-horizontal" role="form" action="{{ route('bonus.store') }}" enctype="multipart/form-data" method="POST">
@elseif($bonusapa=="pembayaran")
    <form class="form-horizontal" role="form" action="{{ route('bonus.storeBayar') }}" enctype="multipart/form-data" method="POST">
@elseif($bonusapa=="topup")
    <form class="form-horizontal" role="form" action="{{ route('bonus.storetopup') }}" enctype="multipart/form-data" method="POST">
@endif
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="card-box table-responsive">
                <h4 class="m-t-0 header-title">Detail Bonus Member</h4>

                    @php
                        $i = 1;
                        $total_bonus = 0;
                    @endphp
                    @if($bonusapa=="perhitungan")
                    <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                        <thead>
                            <th>Tandai</th>
                            <th>No</th>
                            <th>KTP</th>
                            <th>No ID</th>
                            <th>Nama</th>
                            <th>Bonus</th>
                        </thead>
                        <tbody>
                            @foreach($perusahaanmember as $prm)
                                <tr class="trow">
                                    <td><input type='checkbox' name="count[]" id="count{{ $i }}" value="{{ $i }}" parsley-trigger="change" onchange="check(this.value)"></td>
                                    <td>{{$i}}</td>
                                    <td>{{$prm->ktp}}</td>
                                    <td>{{$prm->noid}}</td>
                                    <td>{{$prm->nama}}</td>
                                    @php
                                        $data_bonus = $bonus->where('member_id', $prm->noid)->first();
                                    @endphp
                                    <td>
                                        <input class="form-control" value="{{ number_format($data_bonus['bonus'],0) }}" type="text" name="bonus{{ $i }}" id="bonus{{ $i }}">
                                        <input type="hidden" name="id_member{{ $i }}" value="{{ $prm->noid }}">
                                    </td>
                                </tr>
                                @php
                                    $i++;
                                    $total_bonus = $total_bonus + $data_bonus['bonus'];
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                    @elseif($bonusapa=="pembayaran" OR $bonusapa=="topup")
                    <table id="table" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Tambah Daftar Member</label>
                            <div class="col-10">
                                @if($bonusapa=="pembayaran")
                                    <select class="form-control select2" id="search" name="search" parsley-trigger="change" onchange="addRowPembayaran(this.value)">
                                    </select>
                                @elseif($bonusapa=="topup")
                                    <select class="form-control select2" id="search" name="search" parsley-trigger="change" onchange="addRowTopUp(this.value)">
                                    </select>
                                @endif
                            </div>
                        </div>
                        <thead>
                            <tr>
                            <th width="7%">No</th>
                            <th width="13%">Nama Bank</th>
                            <th width="13%">No Rekening</th>
                            <th width="17%">Nama</th>
                            <th width="27%">Bonus</th>
                            <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <input type="hidden" name="counts" id="counts" value="0">

                        </tbody>
                    </table>
                    @endif

                <div class="form-group row">
                    <label class="col-2 col-form-label">Total Bonus</label>
                    <div class="col-10">
                        <input type="text" class="form-control number" min="0" parsley-trigger="change" required name="total_bonus" id="total_bonus" value="{{ $total_bonus }}" readonly="readonly">
                    </div>
                </div>
                @if($bonusapa=="perhitungan")
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Estimasi Bonus</label>
                        <div class="col-10">
                            <input type="text" class="form-control number" min="0" parsley-trigger="change" required name="estimasi_bonus" id="estimasi_bonus" value="{{ $estimasi_bonus }}" readonly="readonly">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-2 col-form-label">Selisih (Laba/Rugi)</label>
                        <div class="col-10">
                            <input type="text" class="form-control number" min="0" parsley-trigger="change" required name="selisih_bonus" id="selisih_bonus" value="{{ $selisih_bonus }}" readonly="readonly">
                        </div>
                    </div>
                @elseif($bonusapa=="pembayaran" OR $bonusapa=="topup")
                    <input type="hidden" name="AccNo" id="AccNo" value="{{ $AccNo }}">
                    <input type="hidden" name="tgl" id="tgl" value="{{ $tgl }}">
                    <input type="hidden" name="bank_id" id="bank_id" value="{{ $bank }}">
                @endif

                @if($bonusapa=="perhitungan" OR $bonusapa=="pembayaran")
                    <input type="hidden" name="bulan2" value="{{ $bulan }}">
                    <input type="hidden" name="tahun2" value="{{ $tahun }}">
                @endif
                <input type="hidden" name="ctr" id="ctr" value="{{ $i }}">
            </div>
        </div>
    </div>
    <div class="form-group text-right m-b-0">
        <button class="btn btn-primary waves-effect waves-light" type="submit">
            Save Bonus
        </button>
    </div>
</form>

<script>

    $(document).ready(function () {
        ajx_member()
        // Responsive Datatable
        $('#responsive-datatable').DataTable();

        $(".number").divide();
    });

    function ajx_member(){
        var bid = $("#bank_id").val()
        $("#search").select2({
            placeholder:'Masukan Kata Kunci',
            ajax:{
                url: "{{route('ajxBonusOrder')}}",
                dataType:'json',
                delay:250,
                data:function(params){
                    return{
                        keyword:params.term,
                        bankid:bid,
                    };
                },
                processResults:function(data){
                    var item = $.map(data, (value)=>{ //map buat ngemap object data kyk foreach
                        return { id: value.id, text: value.norek+" - "+value.nama+" (KTP : "+value.ktp+")"};
                    });
                    return {
                        results: item
                    }
                },
                cache: false,
            },
            minimumInputLength: 3,
            // templateResult: formatRepo,
            // templateSelection: formatRepoSelection
        });

    }

    function addRowPembayaran(id){
        var token = $("meta[name='csrf-token']").attr("content");
        var thn = $("#tahun").val();
        var bln = $("#bulan").val();
        var cnt = $("#ctr").val();
        $.ajax({
            url : "{{route('ajxAddRowPembayaran')}}",
            type : "post",
            dataType: 'json',
            data:{
                id_member : id,
                tahun : thn,
                bulan : bln,
                count : cnt,
                _token : token,
            },
        }).done(function (data) {
            $('#table-body').append(data.append);
            var cnt = parseInt($('#ctr').val()) + 1;
            $('#ctr').val(cnt);
            resetall();
            // changeTotalHarga(data.sub_ttl);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function addRowTopUp(id){
        var token = $("meta[name='csrf-token']").attr("content");
        var tanggal = $("#tgl").val();
        var cnt = $("#ctr").val();
        $.ajax({
            url : "{{route('ajxAddRowTopup')}}",
            type : "post",
            dataType: 'json',
            data:{
                id_member : id,
                tgl : tanggal,
                count : cnt,
                _token : token,
            },
        }).done(function (data) {
            $('#table-body').append(data.append);
            var cnt = parseInt($('#ctr').val()) + 1;
            $('#ctr').val(cnt);
            resetall();
            changeTotalHarga(data.sub_ttl);
        }).fail(function (msg) {
            alert('Gagal menampilkan data, silahkan refresh halaman.');
        });
    }

    function changeTotalHarga(sub_ttl){
        temp_bonus = parseInt($('#total_bonus').val());
        new_total = parseInt(temp_bonus) + parseInt(sub_ttl);
        $('#total_bonus').val(new_total);
    }

    function minusTotalHarga(sub_ttl){
        temp_bonus = parseInt($('#total_bonus').val());
        new_total = parseInt(temp_bonus) - parseInt(sub_ttl);
        $('#total_bonus').val(new_total);
    }

    function decreaseTotalHarga(id){
        bonus = $('#bonus'+id).val();
        minusTotalHarga(bonus);
    }

    function resetall(){
        $('#search').empty().trigger('click')
    }

    function deleteItem(id){
        count = parseInt($('#ctr').val());
        decreaseTotalHarga(id);
        $('#trow'+id).remove();
        $('#ctr').val(count);
    }

    function check(id){
        var count = "count"+id
        var check_count = document.getElementById(count)
        var bonus = "#bonus"+id
        var amount = parseInt($(bonus).val())
        if(check_count.checked==true){
            changeTotalHarga(amount)
        }else{
            minusTotalHarga(amount)
        }
    }

    function checkBonus(id){
        bonus = $('#bonus'+id).val();

        if(bonus == NaN || bonus == null || bonus == ""){
            bonus=0;
        }else{
            bonus = bonus;
            // console.log(subharga);
            $('#bonus'+id).val(bonus);
        }
        checkTotal();
    }

    function checkTotal(){
        var rows= $('#table tbody tr.trow').length;
        var totalharga = 0;
        var bonus = $("input[name='bonus[]']").map(function(){return $(this).val();}).get();
        // console.log(rows)
        for(i=0;i<rows;i++){
            b = bonus[i];
            totalharga = totalharga + parseInt(b);
        }
        // console.log(totalharga)
        $('#total_bonus').val(totalharga);
    }
</script>
