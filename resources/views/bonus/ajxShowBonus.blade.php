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
    @endphp
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">Detail Bonus Member</h4>
                    @if($bonusapa=="laporan2")
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
                                @foreach($member as $m)
                                    @php
                                        $no = 1;
                                        $totalp = 0;
                                        $totalr = 0;
                                        $total_perhitungan = 0;
                                        $total_realisasi = 0;
                                        $prm = PerusahaanMember::join('tblperusahaan', 'perusahaanmember.perusahaan_id', 'tblperusahaan.id')->where('ktp',$m->ktp)->select('tblperusahaan.nama', 'noid')->get();
                                        foreach ($prm as $p) {
                                            $data_bonus = Bonus::where('bulan', $bulan)->where('tahun', $tahun)->where('noid', $p->noid)->sum('bonus');
                                            $totalp = $totalp + $data_bonus;
                                        }
                                        $total_perhitungan = $total_perhitungan + $totalp;
                                        $bm = BankMember::join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('ktp',$m->ktp)->select('norek', 'nama')->get();
                                        $no = 1;
                                        $totalr = 0;
                                        foreach ($bm as $b) {
                                            $d_bonus = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->where('no_rek',$b->norek)->sum('bonus');
                                            $totalr = $totalr + $d_bonus;
                                        }
                                        $total_realisasi = $total_realisasi + $totalr;
                                        $selisih = $total_perhitungan - $total_realisasi;
                                    @endphp

                                    @if($total_perhitungan!=0 OR $total_realisasi!=0)
                                        <tr>
                                            <td>{{$i}}</td>
                                            <td>{{$m->ktp}}</td>
                                            <td>{{$m->nama}}</td>
                                            <td>
                                                @foreach($prm as $p)
                                                    @php
                                                        $perusahaan = $p->nama;
                                                        $data_bonus = Bonus::where('bulan', $bulan)->where('tahun', $tahun)->where('noid', $p->noid)->sum('bonus');
                                                    @endphp
                                                        {{ $no }}. {{ $perusahaan }} {{ $p->noid}}<br><b>Bonus :{{ $data_bonus }}</b><br>
                                                    @php
                                                        $no++;
                                                    @endphp
                                                @endforeach

                                                <br><b>Total : {{ $total_perhitungan }}</b>
                                            </td>
                                            <td>
                                                @foreach ($bm as $b)
                                                    @php
                                                        $bb = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->where('no_rek',$b->norek)->select('tgl')->first();
                                                        $d_bonus = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->where('no_rek',$b->norek)->sum('bonus');
                                                        $d_tgl = $bb['tgl'];
                                                        $bank = $b->nama;
                                                    @endphp
                                                    {{ $no }}. {{ $bank }} {{ $b->norek }}<br><b>Bonus : {{ $d_bonus }}</b><br>Tgl : {{ $d_tgl }}<br>
                                                    @php
                                                        $no++;
                                                    @endphp
                                                @endforeach
                                                <br><b>Total : {{ $total_realisasi }}</b>
                                            </td>
                                            @if($selisih != 0)
                                                <td class="table-danger">
                                            @else
                                                <td class="table-success">
                                            @endif
                                                {{ $selisih }}
                                            </td>
                                            @php
                                                $i++;
                                            @endphp
                                        </tr>
                                    @endif
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
                                        $totalp = 0;
                                        $totalr = 0;
                                        $total_perhitungan = 0;
                                        $total_realisasi = 0;
                                        $member = Member::join('perusahaanmember', 'tblmember.ktp', 'perusahaanmember.ktp')->where('perusahaanmember.noid', $b->noid)->select('nama', 'tblmember.ktp')->first();
                                        $prm = PerusahaanMember::join('tblperusahaan', 'perusahaanmember.perusahaan_id', 'tblperusahaan.id')->where('ktp',$member->ktp)->select('tblperusahaan.nama', 'noid')->get();
                                        foreach ($prm as $p) {
                                            $data_bonus = Bonus::where('bulan', $bulan)->where('tahun', $tahun)->where('noid', $p->noid)->sum('bonus');
                                            $totalp = $totalp + $data_bonus;
                                        }
                                        $total_perhitungan = $total_perhitungan + $totalp;
                                        $bm = BankMember::join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('ktp',$member->ktp)->select('norek', 'nama')->get();
                                        $no = 1;
                                        $totalr = 0;
                                        foreach ($bm as $b) {
                                            $d_bonus = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->where('no_rek',$b->norek)->sum('bonus');
                                            $totalr = $totalr + $d_bonus;
                                        }
                                        $total_realisasi = $total_realisasi + $totalr;
                                        $selisih = $total_perhitungan - $total_realisasi;
                                    @endphp


                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{$member->ktp}}</td>
                                        <td>{{$member->nama}}</td>
                                        <td>
                                            @foreach($prm as $p)
                                                @php
                                                    $perusahaan = $p->nama;
                                                    $data_bonus = Bonus::where('bulan', $bulan)->where('tahun', $tahun)->where('noid', $p->noid)->sum('bonus');
                                                @endphp
                                                    {{ $no }}. {{ $perusahaan }} {{ $p->noid}}<br><b>Bonus :{{ $data_bonus }}</b><br>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach
                                            <br><b>Total : {{ $total_perhitungan }}</b>
                                        </td>
                                        <td>
                                            @foreach ($bm as $b)
                                                @php
                                                    $bb = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->where('no_rek',$b->norek)->select('tgl')->first();
                                                    $d_bonus = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->where('no_rek',$b->norek)->sum('bonus');
                                                    $d_tgl = $bb['tgl'];
                                                    $bank = $b->nama;
                                                @endphp
                                                {{ $no }}. {{ $bank }} {{ $b->norek }}<br><b>Bonus : {{ $d_bonus }}</b><br>Tgl : {{ $d_tgl }}<br>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach
                                            <br><b>Total : {{ $total_realisasi }}</b>
                                        </td>
                                        @if($selisih != 0)
                                            <td class="table-danger">
                                        @else
                                            <td class="table-success">
                                        @endif
                                            {{ $selisih }}
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
