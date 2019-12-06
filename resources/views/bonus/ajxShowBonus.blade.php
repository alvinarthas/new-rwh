    @php
        use Illuminate\Support\Facades\DB;
        use App\Member;
        use App\BankMember;
        use App\Bank;
        use App\PerusahaanMember;
        use App\Perusahaan;
        use App\BonusBayar;
        use App\TopUpBonus;
    @endphp
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <h4 class="m-t-0 header-title">Detail Bonus Member</h4>
                    @if($bonusapa=="perhitungan")
                        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                            <thead>
                                <th>No</th>
                                <th>KTP</th>
                                <th>No ID</th>
                                <th>Nama</th>
                                <th>Bonus</th>
                            </thead>

                            <tbody>
                                @php
                                    $i = 1;
                                    $total_bonus = 0;
                                @endphp
                                @foreach($perusahaanmember as $prm)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$prm->ktp}}</td>
                                    <td>{{$prm->noid}}</td>
                                    <td>{{$prm->nama}}</td>

                                    {{-- tampil semua data member perusahaan --}}
                                    @php
                                        $data_bonus = $bonus->where('noid', $prm->noid)->first();
                                    @endphp

                                    <td>
                                        {{-- tampil semua data member perusahaan --}}
                                        <input class="form-control number" value="{{ number_format($data_bonus['bonus'],0) }}" type="text" name="bonus{{ $i }}" readonly>

                                        {{-- hanya yang bonus !=0 --}}
                                        {{-- <input class="form-control number" value="{{ number_format($prm['bonus'],0) }}" type="text" name="bonus{{ $i }}"> --}}
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
                        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                            <thead>
                                <th>No</th>
                                <th>Nama Bank</th>
                                <th>No Rekening</th>
                                <th>Nama</th>
                                <th>Bonus</th>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                    $total_bonus = 0;
                                @endphp
                                @foreach($bankmember as $bm)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$namabank['AccName']}}</td>
                                    <td>{{$bm->norek}}</td>
                                    <td>{{ $bm->nama }}</td>
                                    @php
                                        if($bonusapa=="pembayaran"){
                                            $data_bonus = BonusBayar::where('no_rek', $bm->norek)->where('bulan', $bulan)->where('tahun', $tahun)->where('tgl', $tgl)->where('AccNo', $AccNo)->select('bonus')->first();
                                        }elseif($bonusapa=="topup"){
                                            $data_bonus = TopUpBonus::where('no_rek', $bm->norek)->where('tgl', $tgl)->where('AccNo', $AccNo)->select('bonus')->first();
                                        }

                                    @endphp
                                    <td>
                                        <input class="form-control number" value="{{ number_format($data_bonus['bonus'],0) }}" type="text" name="bonus{{ $i }}" readonly>
                                    </td>
                                </tr>
                                @php
                                    $i++;
                                    $total_bonus = $total_bonus + $data_bonus['bonus'];
                                @endphp
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
                                @foreach($member as $m)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$m->ktp}}</td>
                                    <td>{{$m->nama}}</td>
                                    <td>
                                        @php
                                            $no = 1;
                                            $totalp = 0;
                                            $totalr = 0;
                                            $total_perhitungan = 0;
                                            $total_realisasi = 0;
                                            $prm = PerusahaanMember::where('ktp',$m->ktp)->get();
                                        @endphp

                                        @foreach($prm as $p)
                                        @php
                                            $perusahaan = Perusahaan::where('id', $p->perusahaan_id)->select('nama')->first();
                                            $data_bonus = $bonus->where('member_id', $p->noid)->sum('bonus');
                                        @endphp

                                            {{ $no}}. {{ $perusahaan['nama'] }} {{ $p->noid}}<br><b>Bonus :{{ $data_bonus}}</b><br>
                                        @php
                                            $no++;
                                            $totalp = $totalp;
                                        @endphp
                                        @endforeach
                                        @php
                                            $total_perhitungan = $total_perhitungan + $totalp;
                                        @endphp

                                        <br><b>Total : {{ $total_perhitungan }}</b>
                                    </td>
                                    <td>
                                        @php
                                            $bm = BankMember::where('ktp',$m->ktp)->get();
                                            $no = 1;
                                            $totalr = 0;
                                        @endphp
                                        @foreach ($bm as $b)
                                        @php
                                            $bb = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->where('no_rek',$b->norek)->select('tgl')->first();
                                            $d_bonus = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->where('no_rek',$b->norek)->sum('bonus');
                                            $d_tgl = $bb['tgl'];
                                            $bank = Bank::where('id',$b->bank_id)->first()->nama;
                                        @endphp
                                        {{ $no }}. {{ $bank }} {{ $b->norek }}<br><b>Bonus : {{ $d_bonus }}</b><br>Tgl : {{ $d_tgl }}<br>
                                        @php
                                            $no++;
                                            $totalr = $totalr + $d_bonus;
                                        @endphp
                                        @endforeach

                                        @php
                                            $total_realisasi = $total_realisasi + $totalr;
                                            $selisih = $total_perhitungan - $total_realisasi;
                                        @endphp

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
                    @elseif($bonusapa=="bonusgagal")
                        <table id="responsive-datatable" class="table table-bordered table-bordered dt-responsive wrap" cellspacing="0" width="100%">
                            <thead>
                                <th>No</th>
                                <th>KTP</th>
                                <th>No ID</th>
                                <th>Nama</th>
                                <th>Bonus</th>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                    $total_bonus = 0;
                                @endphp
                                @foreach($bonusgagal as $bg)
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$bg->ktp}}</td>
                                    <td>{{$bg->id_member}}</td>
                                    <td>{{$bg->nama}}<td>
                                    <td>{{$bg->bonus}}</td>
                                    @php
                                        $i++;
                                        $total_bonus = $total_bonus + $bg->bonus;
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
