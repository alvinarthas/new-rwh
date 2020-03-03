<div class="card-box table-responsive">
    <div class="form-group row">
        <label class="col-2 col-form-label">ID Jurnal</label>
        <div class="col-10">
            <input type="text" class="form-control" name="id_jurnal" id="id_jurnal" parsley-trigger="change" value="{{$id_jurnal}}" readonly>
        </div>
    </div>
    <div class="form-group row">
        @if($bonusapa=="perhitungan")
            <label class="col-2 col-form-label">Perusahaan</label>
            <div class="col-10">
                <input type="text" class="form-control" name="supplier" id="supplier" parsley-trigger="change" value="{{$supplier}}" readonly>
            </div>
        @elseif($bonusapa=="pembayaran")
            <label class="col-2 col-form-label">Rekening Tujuan</label>
            <div class="col-10">
                <input type="text" class="form-control" name="rekening" id="rekening" parsley-trigger="change" value="{{$rekening}}" readonly>
            </div>
        @endif

    </div>
    <div class="form-group row">
        <label class="col-2 col-form-label">Tanggal Transaksi</label>
        <div class="col-10">
            <input type="text" class="form-control" name="tgl_transaksi" id="tgl_transaksi" parsley-trigger="change" value="{{$tgl_transaksi}}" readonly>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12">
            <table id="datatable" class="table table-bordered table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                <thead>
                    <th>No</th>
                    @if($bonusapa=="perhitungan")
                        <th>KTP</th>
                        <th>No ID</th>
                    @elseif($bonusapa=="pembayaran" AND $details[0]->AccNo != "1.1.1.1.000003")
                        <th>Nama Bank</th>
                        <th>No Rekening</th>
                    @endif
                    <th>Nama</th>
                    <th>Bonus</th>
                </thead>
                <tbody>
                    @php($i=1)
                    @foreach ($details as $key)
                        <tr>
                            <td>{{$i}}</td>
                            @if($bonusapa=="perhitungan")
                                <td>{{$key->ktp}}</td>
                                <td>{{$key->noid}}</td>
                            @elseif($bonusapa=="pembayaran" AND $key->AccNo != "1.1.1.1.000003")
                                <td>{{$key->namabank}}</td>
                                <td>{{$key->no_rek}}</td>
                            @endif
                            <td>{{$key->nama}}</td>
                            <td>Rp {{number_format($key->bonus, 2, ",", ".")}}</td>
                        </tr>
                    @php($i++)
                    @endforeach
                </tbody>
            </table>
            <div class="form-group row">
                <label class="col-2 col-form-label">Total Bonus</label>
                <div class="col-10">
                    <input type="text" class="form-control" min="0" parsley-trigger="change" required name="total_bonus" id="total_bonus" value="Rp {{ number_format($total_bonus, 2, ",", ".") }}" readonly="readonly">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#datatable').DataTable();
</script>
