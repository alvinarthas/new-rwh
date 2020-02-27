    @php
        use Illuminate\Support\Facades\DB;
        use App\Member;
        use App\BankMember;
        use App\Bank;
        use App\PerusahaanMember;
        use App\Perusahaan;
        use App\Bonus;
        use App\BonusBayar;
        use App\TopUpBonus;
        use App\PurchaseDetail;
    @endphp
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">Detail Bonus Member</h4>
                    @if($bonusapa=="estimasi")
                        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                            <thead>
                                <th>No</th>
                                <th>Nama Perusahaan</th>
                                <th>Estimasi Bonus</th>
                                <th>Perhitungan Bonus</th>
                                <th>Selisih</th>
                            </thead>

                            <tbody>
                                @php
                                    $i = 1;
                                    $selisih = 0;
                                @endphp
                                @foreach($data as $d)
                                    @php
                                        $estimasi = 0;
                                        $purchasedetail = PurchaseDetail::join('tblpotrx', 'tblpotrxdet.trx_id', 'tblpotrx.id')->where('tblpotrx.month', $bulan)->where('tblpotrx.year', $tahun)->where('tblpotrx.supplier', $d->supplier)->select(DB::raw('(tblpotrxdet.price_dist - tblpotrxdet.price) * tblpotrxdet.qty AS bonus'))->get();
                                        foreach($purchasedetail as $pd){
                                            $estimasi += $pd['bonus'];
                                        }
                                        $bonus = Bonus::where('bulan', $bulan)->where('tahun', $tahun)->where('perusahaan_id', $d->supplier)->sum('bonus');
                                        $selisih = $estimasi - $bonus;
                                    @endphp
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{$d->nama}}</td>
                                        <td>Rp {{ number_format($estimasi, 2, ",", ".") }}</td>
                                        <td>Rp {{ number_format($bonus, 2, ",", ".") }}</td>
                                        @if($selisih != 0)
                                            <td class="table-danger">
                                        @else
                                            <td class="table-success">
                                        @endif
                                            Rp {{ number_format($selisih, 2, ",", ".") }}
                                        </td>
                                        @php
                                            $i++;
                                        @endphp
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @elseif($bonusapa=="laporan")
                        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                            <thead>
                                <th>No</th>
                                <th>KTP</th>
                                <th>Nama</th>
                                <th>Perhitungan Bonus</th>
                                <th>Detail Realisasi Bonus</th>
                                <th>Selisih</th>
                            </thead>

                            <tbody>
                                @php
                                    $i = 1;
                                    $total_bonus = 0;
                                    $selisih = 0;
                                @endphp
                                @foreach($bonus as $b)
                                    @php
                                        $no = 1;
                                        $total_perhitungan = 0;
                                        $total_realisasi = 0;
                                        $selisih = 0;
                                        // $member = Member::join('perusahaanmember', 'tblmember.ktp', 'perusahaanmember.ktp')->where('perusahaanmember.noid', $b->noid)->select('nama', 'tblmember.ktp')->first();
                                        $prm = PerusahaanMember::join('tblperusahaan', 'perusahaanmember.perusahaan_id', 'tblperusahaan.id')->where('ktp',$b->ktp)->select('tblperusahaan.nama', 'noid')->get();
                                        $bm = BankMember::join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('ktp',$b->ktp)->select('norek', 'nama')->get();
                                    @endphp
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{$b->ktp}}</td>
                                        <td>{{$b->nama}}</td>
                                        <td>
                                            @foreach($prm as $p)
                                                @php
                                                    $perusahaan = $p->nama;
                                                    $data_bonus = Bonus::where('bulan', $bulan)->where('tahun', $tahun)->where('noid', $p->noid)->sum('bonus');
                                                @endphp
                                                    {{ $no }}. {{ $perusahaan }} {{ $p->noid}}<br><b>Bonus : Rp {{ number_format($data_bonus, 2, ",", ".") }}</b><br>
                                                @php
                                                    $no++;
                                                    $total_perhitungan = $total_perhitungan + $data_bonus;
                                                @endphp
                                            @endforeach
                                            <br><b>Total : Rp {{ number_format($total_perhitungan, 2, ",", ".") }}</b>
                                        </td>
                                        <td>
                                            @php
                                                $no = 1;
                                            @endphp
                                            @foreach ($bm as $m)
                                                @php
                                                    $bb = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->where('no_rek',$m->norek)->select('tgl')->first();
                                                    $d_bonus = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->where('no_rek',$m->norek)->sum('bonus');
                                                    $d_tgl = $bb['tgl'];
                                                    $bank = $m->nama;
                                                @endphp
                                                {{ $no }}. {{ $bank }} {{ $b->norek }}<br><b>Bonus : Rp {{ number_format($d_bonus, 2, ",", ".") }}</b><br>Tgl : {{ $d_tgl }}<br>
                                                @php
                                                    $no++;
                                                    $total_realisasi += $d_bonus;
                                                @endphp
                                            @endforeach
                                            <br><b>Total : Rp {{ number_format($total_realisasi, 2, ",", ".") }}</b>
                                        </td>
                                        @php
                                            $selisih = $total_perhitungan - $total_realisasi;
                                        @endphp
                                        @if($selisih != 0)
                                            <td class="table-danger">
                                        @else
                                            <td class="table-success">
                                        @endif
                                            Rp {{ number_format($selisih, 2, ",", ".") }}
                                        </td>
                                        @php
                                            $i++;
                                        @endphp
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    @if($bonusapa=="perhitungan" OR $bonusapa=="pembayaran" OR $bonusapa=="topup" OR $bonusapa=="bonusgagal")
                        <div class="form-group row">
                            <label class="col-2 col-form-label">Total Bonus</label>
                            <div class="col-10">
                                <input type="text" class="form-control number" parsley-trigger="change" required name="total_bonus" id="total_bonus" value="@isset($total_bonus){{ $total_bonus }}@endisset" readonly="readonly">
                            </div>
                        </div>
                        @if($bonusapa=="perhitungan")
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Estimasi Bonus</label>
                                <div class="col-10">
                                    <input type="text" class="form-control number" parsley-trigger="change" required name="estimasi_bonus" id="estimasi_bonus" value="@isset($estimasi_bonus){{ $estimasi_bonus }}@endisset" readonly="readonly">
                                </div>
                            </div>
                        @elseif($bonusapa=="pembayaran")
                            <div class="form-group row">
                                <label class="col-2 col-form-label">Piutang Bonus Tertahan</label>
                                <div class="col-10">
                                    <input type="text" class="form-control number" min="0" parsley-trigger="change" required name="bonus_tertahan" id="bonus_tertahan" value="{{ $bonus_tertahan }}" readonly="readonly">
                                </div>
                            </div>
                        @endif
                    @endif
                    <input type="hidden" name="ctr" value="{{ $i }}"/>
                </div>
            </div>
        </div>

        <script>
            $(".number").divide();

            $(document).ready(function () {
                // Responsive Datatable
                $('#responsive-datatable').DataTable();
            });
        </script>
