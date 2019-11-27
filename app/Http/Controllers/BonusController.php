<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\Handler;
use App\Imports\BonusImport;
use App\Imports\BonusBayarImport;
use App\Imports\BonusTopupImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Excel;
use PDF;
use Carbon\Carbon;
use App\Bonus;
use App\BonusGagal;
use App\PurchaseDetail;
use App\Perusahaan;
use App\CoaBayarBonus;
use App\Coa;
use App\BonusBayar;
use App\Bank;
use App\BankMember;
use App\Member;
use App\TopUpBonus;
use App\PerusahaanMember;
use App\Jurnal;
use App\MenuMapping;

class BonusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = MenuMapping::getMap(session('user_id'),"BMPB");
        $perusahaans = Perusahaan::all();
        $bonusapa = "perhitungan";
        $jenis = "index";
        return view('bonus.index', compact('perusahaans', 'bonusapa', 'jenis', 'page'));
    }

    public function indexBayar(Request $request)
    {
        $page = MenuMapping::getMap(session('user_id'),"BMBB");
        $supplier = Perusahaan::all();
        $rekening = Coa::where('StatusAccount', "Detail")->select('AccNo', 'AccName')->orderBy('AccNo','asc')->get();
        $bonusapa = "pembayaran";
        $jenis = "index";
        return view('bonus.index', compact('rekening', 'bonusapa','jenis', 'page', 'supplier'));
    }

    public function indexTopup(Request $request)
    {
        $page = MenuMapping::getMap(session('user_id'),"BMTU");
        $supplier = Perusahaan::all();
        $rekening = Coa::where('StatusAccount', "Detail")->select('AccNo', 'AccName')->orderBy('AccNo','asc')->get();
        $bonusapa = "topup";
        $jenis = "index";
        return view('bonus.index', compact('rekening', 'bonusapa','jenis', 'page', 'supplier'));
    }

    public function indexLaporan(Request $request)
    {
        $page = MenuMapping::getMap(session('user_id'),"BMRU");
        $bonusapa = "laporan";
        $jenis = "index";
        return view('bonus.index', compact('bonusapa','jenis', 'page'));
    }

    public function indexBonusGagal(Request $request)
    {
        $page = MenuMapping::getMap(session('user_id'),"BMGU");
        $perusahaans = Perusahaan::all();
        $bonusapa = "bonusgagal";
        $jenis = "index";
        return view('bonus.index', compact('bonusapa','jenis','perusahaans', 'page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $perusahaans = Perusahaan::all();
        $bonusapa = "perhitungan";
        $jenis = "create";
        $page = MenuMapping::getMap(session('user_id'),"BMPB");
        return view('bonus.index', compact('perusahaans', 'jenis', 'bonusapa','page'));
    }

    public function createBayar()
    {
        $rekening = Coa::where('StatusAccount', "Detail")->select('AccNo', 'AccName')->orderBy('AccNo','asc')->get();
        $supplier = Perusahaan::all();
        $bonusapa = "pembayaran";
        $jenis = "create";
        $page = MenuMapping::getMap(session('user_id'), "BMBB");
        return view('bonus.index', compact('rekening', 'jenis', 'bonusapa', 'supplier'));
    }

    public function createTopup()
    {
        $rekening = Coa::where('StatusAccount', "Detail")->select('AccNo', 'AccName')->orderBy('AccNo','asc')->get();
        $bonusapa = "topup";
        $jenis = "create";
        $page = MenuMapping::getMap(session('user_id'), "BMTU");
        return view('bonus.index', compact('rekening', 'jenis', 'bonusapa'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'tahun' => 'required',
            'bulan' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            // success
            try{
                Carbon::setLocale('id');
                $tgl = date('Y-m-d', strtotime(Carbon::today()));
                $ctr = count($request->ktp);
                $bulan = $request->bulan;
                $tahun = $request->tahun;
                $perusahaan_id = $request->perusahaan_id;
                $perusahaan = Perusahaan::where('id',$perusahaan_id)->select('nama')->first();
                $ket = 'perhitungan bonus '.$perusahaan['nama'].' - bulan '.$bulan.' '.$tahun;
                $total_bonus = $request->total_bonus;
                $estimasi_bonus = $request->estimasi_bonus;
                $selisih = $request->selisih_bonus;
                $id_jurnal = Jurnal::getJurnalID('BP');

                for($i=0;$i<$ctr;$i++){
                    $noid = $request->noid[$i];
                    $bonus = $request->bonus[$i];

                    $bonusperhitungan = Bonus::where('noid', $noid)->where('tahun', $tahun)->where('bulan', $bulan)->select('id_bonus','id_jurnal')->count();
                    // $num = $bonusperhitungan->count();

                    if($bonus != 0){
                        if($bonusperhitungan==0){
                            // bonus
                            $data = new Bonus(array(
                                'noid'    => $noid,
                                'bulan'     => $bulan,
                                'tahun'     => $tahun,
                                'bonus'     => $bonus,
                                'creator'   => session('user_id'),
                                'id_jurnal' => $id_jurnal,
                            ));
                            $data->save();
                        }else{
                            $bonushitung = Bonus::where('noid', $noid)->where('tahun', $tahun)->where('bulan', $bulan)->select('id_bonus','id_jurnal')->first();
                            $jurnal = Jurnal::where('id_jurnal', $bonushitung['id_jurnal']);

                            // bonus hitung
                            $data = Bonus::where('id_bonus', $bonushitung['id_bonus'])->first();
                            $data->bonus = $bonus;
                            $data->id_jurnal = $id_jurnal;
                            $data->creator = session('user_id');

                            $jurnal->delete();
                            $data->update();
                        }
                    }
                }

                // debet Piutang Bonus
                $debet = new Jurnal(array(
                    'id_jurnal'     => $id_jurnal,
                    'AccNo'         => "1.1.3.5",
                    'AccPos'        => "Debet",
                    'Amount'        => $total_bonus,
                    'company_id'    => 1,
                    'date'          => $tgl,
                    'description'   => $ket,
                    'creator'       => session('user_id')
                ));
                // credit Estimasi Bonus
                $credit = new Jurnal(array(
                    'id_jurnal'     => $id_jurnal,
                    'AccNo'         => "1.1.3.4",
                    'AccPos'        => "Credit",
                    'Amount'        => $estimasi_bonus,
                    'company_id'    => 1,
                    'date'          => $tgl,
                    'description'   => $ket,
                    'creator'       => session('user_id')
                ));

                if($selisih!=0){
                    // credit selisih laba/rugi estimasi bonus
                    $credit2 = new Jurnal(array(
                        'id_jurnal'     => $id_jurnal,
                        'AccNo'         => "7.1",
                        'AccPos'        => "Credit",
                        'Amount'        => $selisih,
                        'company_id'    => 1,
                        'date'          => $tgl,
                        'description'   => "selisih ".$ket,
                        'creator'       => session('user_id')
                    ));

                    $credit2->save();
                }

                $debet->save();
                $credit->save();

                return redirect()->route('bonus.index')->with('status', 'Data berhasil disimpan');
            }catch(\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }
    }

    public function storeBayar(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'tahun' => 'required',
            'bulan' => 'required',
            'tgl' => 'required',
        ]);

        // echo "<pre>";
        // print_r($request->all());
        // die();

        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $bulan = $request->bulan;
                $tahun = $request->tahun;
                $tgl = $request->tgl;
                $ctr = count($request->norekening);
                $AccNo = $request->AccNo;
                $supplier = $request->supplier;
                $selisih = $request->selisih_bonus;
                $bonus_tertahan = $request->bonus_tertahan;

                $id_jurnal = Jurnal::getJurnalID('BB');

                for($i=0;$i<$ctr;$i++){
                    $norek = $request->norekening[$i];
                    $bonus = $request->bonus[$i];
                    $bank = $request->namabank[$i];
                    $ket = 'penerimaan bonus '.$norek.' - bulan '.$bulan.' '.$tahun;

                    $bonusbayar = BonusBayar::where('no_rek', $norek)->where('tahun', $tahun)->where('bulan', $bulan)->where('tgl',$tgl)->where('AccNo',$AccNo)->select('id_bonus','id_jurnal')->get();
                    $num = $bonusbayar->count();

                    if($bonus != 0){
                        if($num==0){
                            // bonus bayar
                            $data = new BonusBayar(array(
                                'no_rek'    => $norek,
                                'tgl'       => $tgl,
                                'bulan'     => $bulan,
                                'tahun'     => $tahun,
                                'bonus'     => $bonus,
                                'creator'   => session('user_id'),
                                'AccNo'     => $AccNo,
                                'supplier'  => $supplier,
                                'id_jurnal' => $id_jurnal,
                            ));

                            $data->save();
                        }else{
                            $jurnal_lama = Jurnal::where('id_jurnal', $bonusbayar['id_jurnal']);

                            // bonus bayar
                            $data = BonusBayar::where('no_rek',$norek)->where('tahun',$tahun)->where('bulan',$bulan)->where('tgl',$tgl)->where('AccNo',$AccNo)->first();
                            $data->bonus = $bonus;
                            $data->id_jurnal = $id_jurnal;
                            $data->creator = session('user_id');

                            $jurnal_lama->delete();
                            $data->update();
                        }
                        // debet kas/bank
                        $debet = new Jurnal(array(
                            'id_jurnal'     => $id_jurnal,
                            'AccNo'         => $AccNo,
                            'AccPos'        => "Debet",
                            'Amount'        => $bonus,
                            'company_id'    => 1,
                            'date'          => $tgl,
                            'description'   => $ket,
                            'creator'       => session('user_id')
                        ));

                        $debet->save();
                    }
                }
                if($selisih!=0){
                    // debet laba/rugi selisih pembayaran(penerimaan) bonus
                    $debet2 = new Jurnal(array(
                        'id_jurnal'     => $id_jurnal,
                        'AccNo'         => "7.2",
                        'AccPos'        => "Debet",
                        'Amount'        => $selisih,
                        'company_id'    => 1,
                        'date'          => $tgl,
                        'description'   => $ket,
                        'creator'       => session('user_id')
                    ));
                    $debet2->save();
                }

                // credit piutang bonus tertahan
                $credit = new Jurnal(array(
                    'id_jurnal'     => $id_jurnal,
                    'AccNo'         => "1.1.3.5",
                    'AccPos'        => "Credit",
                    'Amount'        => $bonus_tertahan,
                    'company_id'    => 1,
                    'date'          => $tgl,
                    'description'   => $ket,
                    'creator'       => session('user_id')
                ));

                $credit->save();

                return redirect()->route('bonus.penerimaan')->with('status', 'Data berhasil disimpan');
            }catch(\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
                    // return response()->json($e);
            }
        }
    }

    public function storeTopup(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'tgl' => 'required',
        ]);

        // echo "<pre>";
        // print_r($request->all());
        // die();

        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $ctr = count($request->norekening);
                for($i=0;$i<$ctr;$i++){
                    $norek = $request->norekening[$i];
                    $bonus = $request->bonus[$i];
                    $tgl = $request->tgl;
                    $bank = $request->namabank[$i];
                    $AccNo = $request->AccNo;
                    $ket = 'top up bonus '.$norek.' - '.$tgl;

                    $topup = TopUpBonus::where('no_rek', $norek)->where('tgl',$tgl)->where('AccNo',$AccNo)->select('id_bonus','id_jurnal')->get();
                    $num = $topup->count();

                    $id_jurnal = Jurnal::getJurnalID('BT');

                    if($bonus != 0){
                        if($num==0){
                            // Top Up Bonus
                            $data = new TopUpBonus(array(
                                'no_rek'    => $norek,
                                'tgl'       => $tgl,
                                'bonus'     => $bonus,
                                'creator'   => session('user_id'),
                                'AccNo'   => $AccNo,
                                'id_jurnal' => $id_jurnal,
                            ));
                            $data->save();
                        }else{
                            $jurnal = Jurnal::where('id_jurnal', $topup['id_jurnal']);

                            // topup
                            $data = TopUpBonus::where('no_rek',$norek)->where('tgl',$tgl)->where('AccNo',$AccNo)->first();
                            $data->bonus = $bonus;
                            $data->creator = session('user_id');
                            $data->id_jurnal = $id_jurnal;

                            $jurnal->delete();
                            $data->update();
                        }
                        // debet estimasi bonus
                        $debet = new Jurnal(array(
                            'id_jurnal'     => $id_jurnal,
                            'AccNo'         => "1.1.3.4",
                            'AccPos'        => "Debet",
                            'Amount'        => $bonus,
                            'company_id'    => 1,
                            'date'          => $tgl,
                            'description'   => $ket,
                            'creator'       => session('user_id')
                        ));
                        // credit kas/bank
                        $credit = new Jurnal(array(
                            'id_jurnal'     => $id_jurnal,
                            'AccNo'         => $AccNo,
                            'AccPos'        => "Credit",
                            'Amount'        => $bonus,
                            'company_id'    => 1,
                            'date'          => $tgl,
                            'description'   => $ket,
                            'creator'       => session('user_id')
                        ));

                        $debet->save();
                        $credit->save();
                    }
                }
                return redirect()->route('bonus.topup')->with('status', 'Data berhasil disimpan');
            }catch(\Exception $e){
                return redirect()->back()->withErrors($e->getMessage());
                // return response()->json($e);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function showBonusPerhitungan(Request $request)
    {
        $perusahaan = $request->perusahaan;
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        // tampil semua data member perusahaan
        $perusahaanmember = PerusahaanMember::join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->where('perusahaanmember.perusahaan_id','=',$perusahaan)->select('tblmember.ktp', 'noid', 'nama')->orderBy('tblmember.nama', 'asc')->get();
        $bonus = Bonus::where('tahun',$tahun)->where('bulan',$bulan)->get();

        // hanya yang bonus !=0
        // $perusahaanmember = PerusahaanMember::join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->join('tblbonus', 'perusahaanmember.noid', 'tblbonus.noid')->where('perusahaanmember.perusahaan_id','=',$perusahaan)->select('tblmember.ktp', 'perusahaanmember.noid', 'nama', 'bonus')->orderBy('tblmember.nama', 'asc')->get();
        $bonusapa = "perhitungan";

        $purchase = PurchaseDetail::join('tblpotrx', 'tblpotrxdet.trx_id', 'tblpotrx.id')->where('tblpotrx.month',$bulan)->where('tblpotrx.year',$tahun)->where('tblpotrx.supplier',$perusahaan)->select('qty', 'price', 'price_dist')->get();
        $estimasi_bonus = 0;
        foreach($purchase as $p){
            $estimasi_bonus = $estimasi_bonus + (($p['price_dist'] - $p['price']) * $p['qty']);
        }
        return view('bonus.ajxShowBonus', compact('perusahaanmember', 'bonus','bonusapa', 'estimasi_bonus'));
    }

    public function createBonusPerhitungan(Request $request)
    {
        $perusahaan = $request->perusahaan;
        $perusahaanmember = DB::table('perusahaanmember')->join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->where('perusahaanmember.perusahaan_id',$perusahaan)->orderBy('tblmember.nama', 'asc')->get();
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $bonusapa = "perhitungan";
        $bonus = Bonus::where('tahun',$tahun)->where('bulan',$bulan)->get();
        $purchase = PurchaseDetail::join('tblpotrx', 'tblpotrxdet.trx_id', 'tblpotrx.id')->where('tblpotrx.month',$bulan)->where('tblpotrx.year',$tahun)->where('tblpotrx.supplier',$perusahaan)->select('qty', 'price', 'price_dist')->get();
        $estimasi_bonus = 0;
        foreach($purchase as $p){
            $estimasi_bonus = $estimasi_bonus + (($p['price_dist'] - $p['price']) * $p['qty']);
        }
        return view('bonus.ajxCreateBonus', compact('perusahaanmember', 'bonus','tahun','bulan','perusahaan','bonusapa', 'estimasi_bonus'));
    }

    public function ajxAddRowPerhitungan(Request $request){
        $id = $request->id;
        $perusahaan = $request->perusahaan;
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $count = $request->count;

        $perusahaanmember = PerusahaanMember::where('id',$id)->select('ktp', 'noid')->first();
        $member = Member::where('ktp', $perusahaanmember['ktp'])->select('nama','member_id')->first();
        $bonus = Bonus::where('noid',$perusahaanmember['noid'])->where('tahun', $tahun)->where('bulan',$bulan)->select('bonus')->first();
        if (!$bonus){
            $bonus = 0;
        }else{
            $bonus = $bonus['bonus'];
        }

        $sub_ttl = $bonus;

        $append = '<tr style="width:100%" id="trow'.$count.'" class="trow">
        <td>'.$count.'</td>
        <td><input type="hidden" name="ktp[]" id="ktp'.$count.'" value="'.$perusahaanmember->ktp.'">'.$perusahaanmember->ktp.'</td>
        <td><input type="hidden" name="noid[]" id="noid'.$count.'" value="'.$perusahaanmember->noid.'">'.$perusahaanmember->noid.'</td>
        <td><input type="hidden" name="nama[]" id="nama'.$count.'" value="'.$member['member_id'].'">'.$member['nama'].'</td>
        <td><input type="text" class="form-control number" name="bonus[]" parsley-trigger="keyup" onkeyup="checkBonus()" id="bonus'.$count.'" value="'.$bonus.'"></td>
        <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem('.$count.')" >Delete</a></td>
        </tr>';

        $data = array(
            'append' => $append,
            'count' => $count,
            'sub_ttl' => $sub_ttl,
        );

        return response()->json($data);
    }

    // upload EXCEL yang baru
    public function uploadBonusPerhitungan2(Request $request)
    {
        $perusahaan_id = $request->perusahaan;
        $this->validate($request, ['file'  => 'required|mimes:xls,xlsx']);
        $path = $request->file('file')->getRealPath();
        $path = $request->file('file');
        // $path = $request->file('file');
        // $data = Excel::load($path)->get();
        $array = Excel::toArray(new BonusImport, $path);
        $result = array();
        Carbon::setLocale('id');
        $tgl = date('Y-m-d', strtotime(Carbon::today()));
        $perusahaan = Perusahaan::where('id',$perusahaan_id)->select('nama')->first();
        $ket = 'perhitungan bonus via upload excel'.$perusahaan['nama'].' - bulan '.$request->bulan.' '.$request->tahun;
        $total_bonus = 0;
        $estimasi_bonus = $request->estimasi_bonus;
        $id_jurnal = Jurnal::getJurnalID('BP');
        $count = count($array[0]);
        $xls = array_chunk($array[0],$count);
        $row = 1;
        $datas = array();

        for ($i=1; $i < $count ; $i++) {
            // echo $xls[0][$i][2];
            $noid = $xls[0][$i][1];
            $norek = $xls[0][$i][2];
            $nama = $xls[0][$i][3];
            $bonus = $xls[0][$i][4];

            if($norek <> ''){
                $num_member = PerusahaanMember::where('noid', $noid)->join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->join('bankmember', 'perusahaanmember.ktp', 'bankmember.ktp')->where('bankmember.norek', $norek)->where('tblmember.nama',"LIKE", $nama)->where('perusahaanmember.perusahaan_id',$perusahaan_id)->count();
                // $num_member= $perusahaan['perusahaanmember.noid']->count();
                if($num_member==0){
                    $member = array(
                        'noid'  => $noid,
                        'norek' => $norek,
                        'nama'  => $nama,
                        'bonus' => $bonus
                    );
                    array_push($datas, $member);
                }else{
                    // echo "berhasil";
                    $pm = PerusahaanMember::join('bankmember', 'perusahaanmember.ktp', 'bankmember.ktp')->where('perusahaanmember.noid', $noid)->where('norek', $norek)->where('perusahaan_id', $perusahaan_id)->select('perusahaanmember.ktp')->first();
                    $num_bonus = Bonus::where('noid', $noid)->where('tahun', $request->tahun)->where('bulan', $request->bulan)->count();
                    $total_bonus = $total_bonus + $bonus;
                    $append = '<tr style="width:100%" id="trow'.$row.'" class="trow">
                    <td>'.$row.'</td>
                    <td><input type="hidden" name="ktp[]" id="ktp'.$row.'" value="'.$pm->ktp.'">'.$pm->ktp.'</td>
                    <td><input type="hidden" name="noid[]" id="noid'.$row.'" value="'.$noid.'">'.$noid.'</td>
                    <td><input type="hidden" name="nama[]" id="nama'.$row.'" value="'.$nama.'">'.$nama.'</td>
                    <td><input type="text" class="form-control number" name="bonus[]" parsley-trigger="keyup" onkeyup="checkBonus()" id="bonus'.$row.'" value="'.$bonus.'"></td>
                    <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem('.$row.')" >Delete</a></td>
                    </tr>';

                    $data = array(
                        'append' => $append,
                        'count' => $row,
                    );
                    array_push($result, $data);
                    $row++;
                }
            }
        }
        if(!empty($datas)){
            $pdf = PDF::loadview('bonus.pdfbonusgagal',['member'=>$datas, 'bulan'=>$request->bulan, 'tahun'=>$request->tahun, 'jenis'=>"perhitungan"])->setPaper('a4', 'potrait');
            $namafile = "gagal upload perhitungan bonus bulan $request->bulan $request->tahun.pdf";
            $pdf->save(public_path('download/'.$namafile));
            // return $pdf->download('gagal upload bonus bulan '.$request->bulan.' '.$request->tahun.'.pdf');
            response()->download(public_path('download/'.$namafile));
        }

        return response()->json($result);
    }

    // upload EXCEL lama (tidak dipakai)
    public function uploadBonusPerhitungan(Request $request)
    {
        $perusahaan_id = $request->perusahaan;
        $this->validate($request, ['file'  => 'required|mimes:xls,xlsx']);
        $path = $request->file('file')->getRealPath();
        // $path = $request->file('file');
        // $data = Excel::load($path)->get();
        $array = Excel::toArray(new BonusImport, $path);
        // echo '<pre>';
        // print_r($array);
        Carbon::setLocale('id');
        $tgl = date('Y-m-d', strtotime(Carbon::today()));
        $perusahaan = Perusahaan::where('id',$perusahaan_id)->select('nama')->first();
        $ket = 'perhitungan bonus via upload excel'.$perusahaan['nama'].' - bulan '.$request->bulan.' '.$request->tahun;
        $total_bonus = 0;
        $estimasi_bonus = $request->estimasi_bonus;
        $id_jurnal = Jurnal::getJurnalID('BP');

        $count = count($array[0]);
        $xls = array_chunk($array[0],$count);
        for ($i=1; $i < $count ; $i++) {
            if($xls[0][$i][2] <> ''){
                $num_member = PerusahaanMember::select('perusahaanmember.noid')->join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->where('tblmember.ktp',$xls[0][$i][1])->where('tblmember.nama',"LIKE", $xls[0][$i][3])->where('perusahaanmember.perusahaan_id',$perusahaan_id)->count();
                // $num_member= $perusahaan['perusahaanmember.noid']->count();
                if($num_member==0){
                    $bonusgagal = new BonusGagal;
                    $bonusgagal->ktp = $xls[0][$i][1];
                    $bonusgagal->member_id = $xls[0][$i][2];
                    $bonusgagal->nama = $xls[0][$i][3];
                    $bonusgagal->tahun = $request->tahun;
                    $bonusgagal->bulan = $request->bulan;
                    $bonusgagal->bonus = $xls[0][$i][4];
                    $bonusgagal->creator = session('user_id');
                    $bonusgagal->perusahaan = $request->perusahaan;
                    $bonusgagal->save();
                }else{
                    $pm = PerusahaanMember::where('ktp', $xls[0][$i][1])->where('perusahaan_id', $perusahaan_id)->select('noid')->first();
                    $num_bonus = Bonus::where('member_id', $pm['noid'])->where('tahun', $request->tahun)->where('bulan', $request->bulan)->count();
                    $total_bonus = $total_bonus + $xls[0][$i][4];

                    if($num_bonus==0){
                        $bonus = new Bonus;
                        $bonus->member_id = $pm['noid'];
                        $bonus->tahun = $request->tahun;
                        $bonus->bulan = $request->bulan;
                        $bonus->bonus = $xls[0][$i][4];
                        $bonus->id_jurnal = $id_jurnal;
                        $bonus->creator = session('user_id');
                        $bonus->save();
                    }else{
                        $bonus = Bonus::where('member_id', $pm['noid'])->where('tahun',$request->tahun)->where('bulan',$request->bulan)->select('bonus','id_jurnal','creator')->first();
                        $jurnal = Jurnal::where('id_jurnal', $bonus['id_jurnal']);
                        $bonus->bonus = $xls[0][$i][4];
                        $bonus->id_jurnal = $id_jurnal;
                        $bonus->creator = session('user_id');
                        $jurnal->delete();
                        $bonus->update();
                    }
                }
            }
        }

        // debet Piutang Bonus
        $debet = new Jurnal(array(
            'id_jurnal'     => $id_jurnal,
            'AccNo'         => "1.1.3.5",
            'AccPos'        => "Debet",
            'Amount'        => $total_bonus,
            'company_id'    => 1,
            'date'          => $tgl,
            'description'   => $ket,
            'creator'       => session('user_id')
        ));
        // credit Estimasi Bonus
        $credit = new Jurnal(array(
            'id_jurnal'     => $id_jurnal,
            'AccNo'         => "1.1.3.4",
            'AccPos'        => "Credit",
            'Amount'        => $estimasi_bonus,
            'company_id'    => 1,
            'date'          => $tgl,
            'description'   => $ket,
            'creator'       => session('user_id')
        ));

        $debet->save();
        $credit->save();

        return redirect()->route('bonus.index')->with('status', 'Data berhasil disimpan');
    }

    public function showBonusPembayaran(Request $request)
    {
        $tgl = $request->tgl;
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $AccNo = $request->rkng;
        $supplier = $request->splr;
        $AccParent = Coa::where('AccNo', $AccNo)->select('AccParent')->first();
        $namabank = Coa::where('AccNo', $AccParent['AccParent'])->select('AccName')->first();
        $bank = Bank::where('nama','LIKE', $namabank['AccName'])->select('id')->first();
        $bankmember = Bankmember::join('tblmember','bankmember.ktp','=','tblmember.ktp')->where('bank_id','LIKE', $bank['id'])->select('nama', 'norek')->orderBy('tblmember.nama', 'asc')->get();
        $bonusapa = "pembayaran";
        $bonus = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->where('tgl', $tgl)->where('AccNo', $AccNo)->where('supplier', $supplier)->select('bonus')->get();
        $perhitunganbonus = Bonus::where('bulan', $bulan)->where('tahun',$tahun)->select('id_jurnal')->get();
        $bonus_tertahan = 0;

        foreach($perhitunganbonus as $b){
            $jurnal  = Jurnal::where('id_jurnal', $b['id_jurnal'])->where('AccNo', "1.1.3.5")->select('Amount','AccPos')->first();
            if($jurnal['AccPos'] == "Debet"){
                $bonus_tertahan = $bonus_tertahan + $jurnal['Amount'];
            }elseif($jurnal['AccPos'] == "Credit"){
                $bonus_tertahan = $bonus_tertahan - $jurnal['Amount'];
            }
        }

        return view('bonus.ajxShowBonus', compact('bankmember','bonus','tahun','bulan','bonusapa','namabank','AccNo', 'tgl', 'bonus_tertahan'));
    }

    public function createBonusPembayaran(Request $request)
    {
        $AccNo = $request->rkng;
        $supplier = $request->splr;
        $AccParent = Coa::where('AccNo', $AccNo)->select('AccParent')->first();
        $namabank = Coa::where('AccNo', $AccParent['AccParent'])->select('AccName')->first();
        $bank = Bank::where('nama','LIKE', $namabank['AccName'])->select('id')->first();
        $tgl = $request->tgl;
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $bonusapa = "pembayaran";
        $perhitunganbonus = Bonus::where('bulan', $bulan)->where('tahun',$tahun)->select('id_jurnal')->get();
        $bonus_tertahan = 0;

        foreach($perhitunganbonus as $b){
            $jurnal  = Jurnal::where('id_jurnal', $b['id_jurnal'])->where('AccNo', "1.1.3.5")->select('Amount','AccPos')->first();
            if($jurnal['AccPos'] == "Debet"){
                $bonus_tertahan = $bonus_tertahan + $jurnal['Amount'];
            }elseif($jurnal['AccPos'] == "Credit"){
                $bonus_tertahan = $bonus_tertahan - $jurnal['Amount'];
            }
        }

        return view('bonus.ajxCreateBonus', compact('tahun','bulan','bonusapa','bank','AccNo', 'tgl', 'bonus_tertahan', 'supplier'));
    }

    public function ajxAddRowPembayaran(Request $request){
        $id_member = $request->id_member;
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $count = $request->count;

        $bankmember = BankMember::where('id',$id_member)->first();
        $norek = $bankmember->norek;
        $namabank = Bank::where('id', $bankmember->bank_id)->first()->nama;
        $nama = Member::where('ktp', $bankmember->ktp)->first()->nama;
        $bonus = BonusBayar::where('no_rek',$norek)->where('tahun', $tahun)->where('bulan',$bulan)->first();
        if (!$bonus){
            $bonus = 0;
        }else{
            $bonus = $bonus->bonus;
        }

        $sub_ttl = $bonus;

        $append = '<tr style="width:100%" id="trow'.$count.'" class="trow">
        <td>'.$count.'</td>
        <td><input type="hidden" name="namabank[]" id="namabank'.$count.'" value="'.$bankmember->bank_id.'">'.$namabank.'</td>
        <td><input type="hidden" name="norekening[]" id="norekening'.$count.'" value="'.$norek.'">'.$norek.'</td>
        <td><input type="hidden" name="nama[]" id="nama'.$count.'" value="'.$nama.'">'.$nama.'</td>
        <td><input type="text" class="form-control number" name="bonus[]" parsley-trigger="keyup" onkeyup="checkBonus()" id="bonus'.$count.'" value="'.$bonus.'"></td>
        <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem('.$count.')" >Delete</a></td>
        </tr>';

        $data = array(
            'append' => $append,
            'count' => $count,
            'sub_ttl' => $sub_ttl,
        );

        return response()->json($data);
    }

    // upload EXCEL pembayaran/penerimaan bonus yang baru
    public function uploadBonusPenerimaan(Request $request)
    {
        // echo "<pre>";
        // print_r($request->all());
        // die();

        $bank_id = $request->bank_id;
        $tgl = $request->tgl;
        $AccNo = $request->AccNo;
        $this->validate($request, ['file'  => 'required|mimes:xls,xlsx']);
        $path = $request->file('file')->getRealPath();
        $path = $request->file('file');
        $array = Excel::toArray(new BonusBayarImport, $path);
        $result = array();
        Carbon::setLocale('id');
        $tgl = date('Y-m-d', strtotime(Carbon::today()));
        $account = Coa::where('AccNo',$AccNo)->select('AccName')->first();
        $ket = 'penerimaan bonus via upload excel ('.$tgl.') '.$account['AccName'].' - bulan '.$request->bulan.' '.$request->tahun;
        $total_bonus = 0;
        $bonus_tertahan = $request->bonus_tertahan;
        $id_jurnal = Jurnal::getJurnalID('BB');
        $count = count($array[0]);
        $xls = array_chunk($array[0],$count);
        $row = 1;
        $datas = array();

        for ($i=1; $i < $count ; $i++) {
            $norek = $xls[0][$i][1];
            $nama = $xls[0][$i][2];
            $bonus = $xls[0][$i][3];

            if($norek <> ''){
                $num_member = BankMember::join('tblmember', 'bankmember.ktp', 'tblmember.ktp')->where('bankmember.norek',$norek)->where('tblmember.nama',"LIKE", $nama)->where('bankmember.bank_id',$bank_id)->count();
                // $num_member= $perusahaan['perusahaanmember.noid']->count();
                if($num_member==0){
                    $member = array(
                        'norek' => $norek,
                        'nama'  => $nama,
                        'bonus' => $bonus
                    );
                    array_push($datas, $member);
                }else{
                    // echo "berhasil";
                    $namabank = BankMember::join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('norek', $norek)->select('nama')->first();
                    $total_bonus = $total_bonus + $bonus;
                    $append = '<tr style="width:100%" id="trow'.$row.'" class="trow">
                    <td>'.$row.'</td>
                    <td><input type="hidden" name="namabank[]" id="namabank'.$row.'" value="'.$namabank['nama'].'">'.$namabank['nama'].'</td>
                    <td><input type="hidden" name="norekening[]" id="norekening'.$row.'" value="'.$norek.'">'.$norek.'</td>
                    <td><input type="hidden" name="nama[]" id="nama'.$row.'" value="'.$nama.'">'.$nama.'</td>
                    <td><input type="text" class="form-control number" name="bonus[]" parsley-trigger="keyup" onkeyup="checkBonus()" id="bonus'.$row.'" value="'.$bonus.'"></td>
                    <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem('.$row.')" >Delete</a></td>
                    </tr>';

                    $data = array(
                        'append' => $append,
                        'count' => $row,
                    );
                    array_push($result, $data);
                    $row++;
                }
            }
        }
        if(!empty($datas)){
            $pdf = PDF::loadview('bonus.pdfbonusgagal',['member'=>$datas, 'bulan'=>$request->bulan, 'tahun'=>$request->tahun, 'jenis'=>"pembayaran"])->setPaper('a4', 'potrait');
            $namafile = "gagal upload penerimaan bonus bulan $request->bulan $request->tahun.pdf";
            $pdf->save(public_path('download/'.$namafile));
            // return $pdf->download('gagal upload bonus bulan '.$request->bulan.' '.$request->tahun.'.pdf');
            response()->download(public_path('download/'.$namafile));
        }
        return response()->json($result);
    }

    // upload EXCEL pembayaran bonus lama (tidak dipakai)
    public function uploadBonusPembayaran(Request $request)
    {
        $tgl = $request->tgl2;
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $AccNo = $request->AccNo2;
        $this->validate($request, ['file'  => 'required|mimes:xls,xlsx']);
        $path = $request->file('file')->getRealPath();
        $path = $request->file('file');
        echo "<pre>";
        print_r($path);
        die();
        // $data = Excel::load($path)->get();
        $array = Excel::toArray(new BonusBayarImport, $path);
        // echo '<pre>';
        // print_r($array);
        $count = count($array[0]);
        $xls = array_chunk($array[0],$count);
        for ($i=1; $i < $count ; $i++) {
            $ket='pembayaran bonus via upload excel '.$xls[0][$i][1].' - bulan'.$bulan.' '.$tahun;
            $bonusbayar = BonusBayar::where('no_rek',$xls[0][$i][1])->where('tahun', $request->tahun)->where('bulan',$request->bulan)->where('tgl',$request->tgl)->where('AccNo',$AccNo)->select('id_bonus','id_jurnal')->get();
            $num = $bonusbayar->count();
            $id_jurnal = Jurnal::getJurnalID('BB');
            if($xls[0][$i][2] <> ''){
                if($num==0){
                    if($xls[0][$i][3] != 0){
                        // Pembayaran Bonus
                        $data = new BonusBayar(array(
                            'no_rek'    => $xls[0][$i][1],
                            'tgl'       => $tgl,
                            'bulan'     => $bulan,
                            'tahun'     => $tahun,
                            'bonus'     => $xls[0][$i][3],
                            'creator'   => session('user_id'),
                            'AccNo'   => $AccNo,
                            'id_jurnal' => $id_jurnal,
                        ));

                        $data->save();
                    }
                }else{
                    $jurnal_lama = Jurnal::where('id_jurnal', $bonusbayar['id_jurnal']);

                    $data = BonusBayar::where('no_rek',$xls[0][$i][1])->where('tahun', $request->tahun)->where('bulan',$request->bulan)->where('tgl',$request->tgl)->where('AccNo',$AccNo)->first();
                    $data->bonus = $xls[0][$i][3];
                    $data->id_jurnal = $id_jurnal;
                    $data->creator = session('user_id');

                    $jurnal_lama->delete();
                    $data->update();
                }

                // debet kas/bank
                $debet = new Jurnal(array(
                    'id_jurnal'     => $id_jurnal,
                    'AccNo'         => $AccNo,
                    'AccPos'        => "Debet",
                    'Amount'        => $xls[0][$i][3],
                    'company_id'    => 1,
                    'date'          => $tgl,
                    'description'   => $ket,
                    'creator'       => session('user_id')
                ));
                // credit piutang bonus tertahan
                $credit = new Jurnal(array(
                    'id_jurnal'     => $id_jurnal,
                    'AccNo'         => "1.1.3.5",
                    'AccPos'        => "Credit",
                    'Amount'        => $xls[0][$i][3],
                    'company_id'    => 1,
                    'date'          => $tgl,
                    'description'   => $ket,
                    'creator'       => session('user_id')
                ));

                $debet->save();
                $credit->save();
            }
        }
        return redirect()->route('bonus.penerimaan')->with('status', 'Data berhasil disimpan');

    }

    public function showBonusTopup(Request $request)
    {
        $tgl = $request->tgl;
        $AccNo = $request->rkng;
        $AccParent = Coa::where('AccNo', $AccNo)->select('AccParent')->first();
        $namabank = Coa::where('AccNo', $AccParent['AccParent'])->select('AccName')->first();
        $bank = Bank::where('nama','LIKE', $namabank['AccName'])->select('id')->first();
        $bankmember = Bankmember::join('tblmember','bankmember.ktp','=','tblmember.ktp')->where('bank_id',$bank['id'])->select('nama', 'norek')->orderBy('tblmember.nama', 'asc')->get();
        $bonusapa = "topup";
        // $bonus = TopUpBonus::where('tgl',$tgl)->get();
        return view('bonus.ajxShowBonus', compact('bankmember','bonusapa','namabank','AccNo','tgl'));
    }

    public function createBonusTopup(Request $request)
    {
        $AccNo = $request->rkng;
        $AccParent = Coa::where('AccNo', $AccNo)->select('AccParent')->first();
        $namabank = Coa::where('AccNo', $AccParent['AccParent'])->select('AccName')->first();
        $bank = Bank::where('nama','LIKE', $namabank['AccName'])->select('id')->first();
        $tgl = $request->tgl;
        // $bankmember = Bankmember::where('bank_id',$bank)->get();
        $bonusapa = "topup";
        return view('bonus.ajxCreateBonus', compact('bonusapa','bank','AccNo', 'tgl'));
    }

        // upload EXCEL pembayaran/penerimaan bonus yang baru
        public function uploadBonusTopup(Request $request)
        {
            // echo "<pre>";
            // print_r($request->all());
            // die();

            $bank_id = $request->bank_id;
            $tgl = $request->tgl;
            $AccNo = $request->AccNo;
            $this->validate($request, ['file'  => 'required|mimes:xls,xlsx']);
            $path = $request->file('file')->getRealPath();
            $path = $request->file('file');
            $array = Excel::toArray(new BonusTopupImport, $path);
            $result = array();
            Carbon::setLocale('id');
            $tgl = date('Y-m-d', strtotime(Carbon::today()));
            $account = Coa::where('AccNo',$AccNo)->select('AccName')->first();
            $ket = 'topup bonus via upload excel ('.$tgl.') '.$account['AccName'].' - bulan '.$request->bulan.' '.$request->tahun;
            $total_bonus = 0;
            $bonus_tertahan = $request->bonus_tertahan;
            $id_jurnal = Jurnal::getJurnalID('BT');
            $count = count($array[0]);
            $xls = array_chunk($array[0],$count);
            $row = 1;
            $datas = array();

            for ($i=1; $i < $count ; $i++) {
                $norek = $xls[0][$i][1];
                $nama = $xls[0][$i][2];
                $bonus = $xls[0][$i][3];

                if($norek <> ''){
                    $num_member = BankMember::join('tblmember', 'bankmember.ktp', 'tblmember.ktp')->where('bankmember.norek',$norek)->where('tblmember.nama',"LIKE", $nama)->where('bankmember.bank_id',$bank_id)->count();
                    // $num_member= $perusahaan['perusahaanmember.noid']->count();
                    if($num_member==0){
                        $member = array(
                            'norek' => $norek,
                            'nama'  => $nama,
                            'bonus' => $bonus
                        );
                        array_push($datas, $member);
                    }else{
                        // echo "berhasil";
                        $namabank = BankMember::join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('norek', $norek)->select('nama')->first();
                        $total_bonus = $total_bonus + $bonus;
                        $append = '<tr style="width:100%" id="trow'.$row.'" class="trow">
                        <td>'.$row.'</td>
                        <td><input type="hidden" name="namabank[]" id="namabank'.$row.'" value="'.$namabank['nama'].'">'.$namabank['nama'].'</td>
                        <td><input type="hidden" name="norekening[]" id="norekening'.$row.'" value="'.$norek.'">'.$norek.'</td>
                        <td><input type="hidden" name="nama[]" id="nama'.$row.'" value="'.$nama.'">'.$nama.'</td>
                        <td><input type="text" class="form-control number" name="bonus[]" parsley-trigger="keyup" onkeyup="checkBonus()" id="bonus'.$row.'" value="'.$bonus.'"></td>
                        <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem('.$row.')" >Delete</a></td>
                        </tr>';

                        $data = array(
                            'append' => $append,
                            'count' => $row,
                        );
                        array_push($result, $data);
                        $row++;
                    }
                }
            }
            if(!empty($datas)){
                $pdf = PDF::loadview('bonus.pdfbonusgagal',['member'=>$datas, 'tgl'=>$tgl, 'jenis'=>"topup"])->setPaper('a4', 'potrait');
                $namafile = "gagal upload top up bonus tanggal $tgl.pdf";
                $pdf->save(public_path('download/'.$namafile));
                // return $pdf->download('gagal upload bonus bulan '.$request->bulan.' '.$request->tahun.'.pdf');
                response()->download(public_path('download/'.$namafile));
            }
            return response()->json($result);
        }

    public function ajxAddRowTopup(Request $request){
        $id_member = $request->id_member;
        $count = $request->count;
        $tgl = $request->tgl;

        $bankmember = BankMember::where('id',$id_member)->first();
        $norek = $bankmember->norek;
        $namabank = Bank::where('id', $bankmember->bank_id)->first()->nama;
        $nama = Member::where('ktp', $bankmember->ktp)->first()->nama;
        $bonus = TopUpBonus::where('no_rek',$norek)->where('tgl',$tgl)->first();
        if (!$bonus){
            $bonus = 0;
        }else{
            $bonus = $bonus->bonus;
        }

        $sub_ttl = $bonus;

        $append = '<tr style="width:100%" id="trow'.$count.'" class="trow">
        <td>'.$count.'</td>
        <td><input type="hidden" name="namabank[]" id="namabank'.$count.'" value="'.$bankmember->bank_id.'">'.$namabank.'</td>
        <td><input type="hidden" name="norekening[]" id="norekening'.$count.'" value="'.$norek.'">'.$norek.'</td>
        <td><input type="hidden" name="nama[]" id="nama'.$count.'" value="'.$nama.'">'.$nama.'</td>
        <td><input type="text" class="form-control number" name="bonus[]" parsley-trigger="keyup" onkeyup="checkBonus()" id="bonus'.$count.'" value="'.$bonus.'"></td>
        <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem('.$count.')" >Delete</a></td>
        </tr>';

        $data = array(
            'append' => $append,
            'count' => $count,
            'sub_ttl' => $sub_ttl,
        );

        return response()->json($data);
    }

    public function showLaporanBonus(Request $request)
    {
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $member = Member::orderBy('nama','asc')->get();
        $bonusapa = "laporan";
        $bonus = Bonus::where('tahun',$tahun)->where('bulan',$bulan)->get();

        return view('bonus.ajxShowBonus', compact('member','bonus','tahun','bulan','bonusapa'));
    }

    public function showLaporanBonusGagal(Request $request)
    {
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $perusahaan = $request->prshn;
        $bonusapa = "bonusgagal";
        $bonusgagal = BonusGagal::where('tahun',$tahun)->where('bulan',$bulan)->where('perusahaan',$perusahaan)->orderBy('nama','asc')->get();
        return view('bonus.ajxShowBonus', compact('bonusgagal','tahun','bulan','bonusapa'));
    }

    public function ajxBonusOrder(Request $request){
        $keyword = strip_tags(trim($request->keyword));
        $key = $keyword.'%';
        // $bankmember = BankMember::where('bank_id',$request->bankid);
        // $search = $bankmember->join('tblmember','bankmember.ktp','=','tblmember.ktp')->orWhere('norek','LIKE', $key)->orWhere('tblmember.nama','LIKE', $key)->orWhere('tblmember.ktp','LIKE', $key)->select('tblmember.nama', 'bankmember.norek', 'bankmember.id AS id', 'tblmember.ktp AS ktp')->limit(5)->get();

        // $bankmember = BankMember::where('bank_id',$request->bankid);
        $search = BankMember::join('tblmember','bankmember.ktp','=','tblmember.ktp')->where('bank_id', $request->bankid)->join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('tblmember.nama','LIKE', $key)->orWhere('norek','LIKE', $key)->orWhere('tblmember.ktp','LIKE', $key)->select('tblmember.nama', 'bankmember.norek', 'bankmember.id AS id', 'tblmember.ktp AS ktp', 'tblbank.nama AS namabank')->limit(5)->get();
        // $search = DB::raw("SELECT m.nama, b.norek, b.id AS id, m.ktp AS ktp FROM bankmember b INNER JOIN tblmember m ON b.ktp = m.ktp WHERE b.bank_id = $request->bankid AND b.norek LIKE $key OR m.nama LIKE $key OR m.ktp LIKE $key limit 5");
        // $search = BankMember::join('tblmember','bankmember.ktp','=','tblmember.ktp')->where('bank_id', $request->bankid)->where('tblmember.nama','LIKE', $key)->orWhere(function($q, $key){ $q->where('bankmember.norek','LIKE', $key)->orWhere('tblmember.ktp','LIKE', $key);})->select('tblmember.nama', 'bankmember.norek', 'bankmember.id AS id', 'tblmember.ktp AS ktp')->limit(5)->get();
        // echo $search;
        $data = array();
        $array = json_decode( json_encode($search), true);
        foreach ($array as $key) {
            $arrayName = array('id' =>$key['id'],'norek' => $key['norek'], 'nama' => $key['nama'], 'ktp' => $key['ktp'], 'namabank' => $key['namabank']);
            // $arrayName = array('id' => $key['id'],'text' => $key['norek']);
            array_push($data,$arrayName);
        }
        echo json_encode($data, JSON_FORCE_OBJECT);
    }

    public function ajxBonusOrderPerhitungan(Request $request){
        $keyword = strip_tags(trim($request->keyword));
        $key = $keyword.'%';
        $prsmember = PerusahaanMember::where('perusahaan_id',$request->perusahaanid);
        $search = $prsmember->join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->where('tblmember.nama','LIKE', $key)->orWhere('tblmember.ktp','LIKE', $key)->select('tblmember.nama', 'perusahaanmember.noid', 'perusahaanmember.id AS id', 'tblmember.ktp AS ktp')->limit(5)->get();

        $data = array();
        $array = json_decode( json_encode($search), true);
        foreach ($array as $key) {
            $arrayName = array('id' =>$key['id'],'noid' => $key['noid'], 'nama' => $key['nama'], 'ktp' => $key['ktp']);
            // $arrayName = array('id' => $key['id'],'text' => $key['norek']);
            array_push($data,$arrayName);
        }
        echo json_encode($data, JSON_FORCE_OBJECT);
    }
}
