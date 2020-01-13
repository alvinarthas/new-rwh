<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\BonusImport;
use App\Imports\BonusBayarImport;
use App\Imports\BonusTopupImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Excel;
use PDF;
use App\Exports\BonusGagalUploadExport1;
use App\Exports\BonusGagalUploadExport2;
use Carbon\Carbon;
use App\Bonus;
use App\BonusGagal;
use App\PurchaseDetail;
use App\Perusahaan;
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
        $bonus = Bonus::join('tblperusahaan', 'tblbonus.perusahaan_id', 'tblperusahaan.id')->groupBy('id_jurnal')->select('id_bonus','bulan', 'tahun', 'tgl', 'tblperusahaan.nama', DB::raw('SUM(bonus) as total_bonus'), 'id_jurnal')->orderBy('tgl', 'desc')->get();
        $bonusapa = "perhitungan";
        $jenis = "index";

        return view('bonus.index', compact('bonusapa', 'jenis', 'page', 'bonus'));
    }

    public function indexBayar(Request $request)
    {
        $page = MenuMapping::getMap(session('user_id'),"BMBB");
        $bonus = BonusBayar::join('tblcoa', 'tblbonusbayar.AccNo', 'tblcoa.AccNo')->groupBy('id_jurnal')->select('id_bonus','bulan', 'tahun', 'tgl', 'AccName', DB::raw('SUM(bonus) as total_bonus'), 'id_jurnal')->orderBy('tgl', 'desc')->get();
        $bonusapa = "pembayaran";
        $jenis = "index";
        return view('bonus.index', compact('bonusapa','jenis', 'page', 'bonus'));
    }

    public function indexTopup(Request $request)
    {
        $page = MenuMapping::getMap(session('user_id'),"BMTU");
        $bonus = TopUpBonus::join('tblcoa', 'tbltopupbonus.AccNo', 'tblcoa.AccNo')->groupBy('tgl')->select('id_bonus', 'tgl', 'AccName', DB::raw('SUM(bonus) as total_bonus'), 'id_jurnal')->orderBy('tgl', 'desc')->get();
        $bonusapa = "topup";
        $jenis = "index";
        return view('bonus.index', compact('bonusapa','jenis', 'page', 'bonus'));
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
        $bonusgagal = BonusGagal::all();

        return view('bonus.index', compact('bonusapa','jenis','perusahaans', 'page', 'bonusgagal'));
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
        $supplier = Perusahaan::all();
        $bonusapa = "topup";
        $jenis = "create";
        $page = MenuMapping::getMap(session('user_id'), "BMTU");
        return view('bonus.index', compact('rekening', 'jenis', 'bonusapa', 'supplier'));
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
            'tgl'   => 'required',
            'tahun' => 'required',
            'bulan' => 'required',
            'perusahaan_id' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            // success
            try{
                Carbon::setLocale('id');
                // $tgl = date('Y-m-d', strtotime(Carbon::today()));
                $tgl = $request->tgl;
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
                    if($selisih < 0){
                        $pos = "Debet";
                        $selisih = abs($selisih);
                    }else{
                        $pos = "Credit";
                    }

                    // credit selisih laba/rugi estimasi bonus
                    $credit2 = new Jurnal(array(
                        'id_jurnal'     => $id_jurnal,
                        'AccNo'         => "7.1",
                        'AccPos'        => $pos,
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

                for($i=0;$i<$ctr;$i++){
                    $noid = $request->noid[$i];
                    $bonus = $request->bonus[$i];

                    $bonusperhitungan = Bonus::where('noid', $noid)->where('tahun', $tahun)->where('bulan', $bulan)->select('id_bonus','id_jurnal')->count();
                    // $num = $bonusperhitungan->count();

                    if($bonus != 0){
                        if($bonusperhitungan==0){
                            // bonus
                            $data = new Bonus(array(
                                'noid'      => $noid,
                                'tgl'       => $tgl,
                                'bulan'     => $bulan,
                                'tahun'     => $tahun,
                                'bonus'     => $bonus,
                                'perusahaan_id' => $perusahaan_id,
                                'creator'   => session('user_id'),
                                'id_jurnal' => $id_jurnal,
                            ));
                            $data->save();
                        }else{
                            $bonushitung = Bonus::where('tgl', $tgl)->where('noid', $noid)->where('tahun', $tahun)->where('bulan', $bulan)->select('id_bonus','id_jurnal')->first();
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
                $ket = 'penerimaan bonus ke '.$AccNo.' - bulan '.$bulan.' '.$tahun;

                $id_jurnal = Jurnal::getJurnalID('BB');

                if($selisih!=0){
                    if($selisih < 0){
                        $pos = "Credit";
                        $selisih = abs($selisih);
                    }else{
                        $pos = "Debet";
                    }
                    // debet laba/rugi selisih pembayaran(penerimaan) bonus
                    $debet2 = new Jurnal(array(
                        'id_jurnal'     => $id_jurnal,
                        'AccNo'         => "7.2",
                        'AccPos'        => $pos,
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

                for($i=0;$i<$ctr;$i++){
                    $norek = $request->norekening[$i];
                    $bonus = $request->bonus[$i];
                    $bank = $request->namabank[$i];
                    $ket = 'penerimaan bonus ke '.$AccNo.' untuk '.$norek.' - bulan '.$bulan.' '.$tahun;

                    $bonusbayar = BonusBayar::where('no_rek', $norek)->where('tahun', $tahun)->where('bulan', $bulan)->where('tgl',$tgl)->where('AccNo',$AccNo)->select('id_bonus','id_jurnal')->get();
                    $num = $bonusbayar->count();

                    if($bonus != 0){
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
                    }
                }

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
        $bn = Bonus::where('id_bonus', $id)->select('bulan', 'tahun', 'perusahaan_id', 'tgl', 'id_jurnal', 'id_bonus')->first();
        $perusahaans = Perusahaan::all();
        $bonusapa = "perhitungan";
        $jenis = "edit";
        $page = MenuMapping::getMap(session('user_id'),"BMPB");
        $bonus = Bonus::join('perusahaanmember', 'tblbonus.noid', 'perusahaanmember.noid')->join('tblmember', 'perusahaanmember.ktp', 'tblmember.ktp')->where('tgl', $bn->tgl)->where('bulan', $bn->bulan)->where('tahun', $bn->tahun)->where('tblbonus.perusahaan_id', $bn->perusahaan_id)->where('id_jurnal', $bn->id_jurnal)->select('bonus', 'id_jurnal', 'perusahaanmember.ktp', 'tblbonus.noid', 'tblmember.nama', 'tblmember.member_id','id_bonus')->get();

        $purchase = PurchaseDetail::join('tblpotrx', 'tblpotrxdet.trx_id', 'tblpotrx.id')->where('tblpotrx.month',$bn->bulan)->where('tblpotrx.year',$bn->tahun)->where('tblpotrx.supplier',$bn->perusahaan_id)->select('qty', 'price', 'price_dist')->get();
        $estimasi_bonus = 0;
        foreach($purchase as $p){
            $estimasi_bonus = $estimasi_bonus + (($p['price_dist'] - $p['price']) * $p['qty']);
        }

        return view('bonus.index', compact('perusahaans', 'jenis', 'bonusapa','page', 'bonus' ,'bn', 'estimasi_bonus'));
    }

    public function editBayar($id)
    {
        $bn = BonusBayar::where('id_bonus', $id)->select('bulan', 'tahun', 'AccNo', 'supplier', 'tgl', 'id_jurnal', 'id_bonus')->first();
        $rekening = Coa::where('StatusAccount', "Detail")->select('AccNo', 'AccName')->orderBy('AccNo','asc')->get();
        $supplier = Perusahaan::all();
        $bonusapa = "pembayaran";
        $jenis = "edit";
        $page = MenuMapping::getMap(session('user_id'),"BMBB");
        $bonus = BonusBayar::join('bankmember', 'tblbonusbayar.no_rek', 'bankmember.norek')->join('tblmember', 'bankmember.ktp', 'tblmember.ktp')->join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('tgl', $bn->tgl)->where('bulan', $bn->bulan)->where('tahun', $bn->tahun)->where('AccNo', $bn->AccNo)->where('tblbonusbayar.id_jurnal', $bn->id_jurnal)->select('bonus', 'tblbonusbayar.id_jurnal', 'tblbank.nama AS namabank', 'tblbonusbayar.no_rek', 'tblmember.nama', 'id_bonus')->get();

        $perhitunganbonus = Bonus::where('bulan', $bn->bulan)->where('tahun',$bn->tahun)->select('id_jurnal')->get();
        $bonus_tertahan = 0;

        foreach($perhitunganbonus as $b){
            $jurnal  = Jurnal::where('id_jurnal', $b['id_jurnal'])->where('AccNo', "1.1.3.5")->select('Amount','AccPos')->first();
            if($jurnal['AccPos'] == "Debet"){
                $bonus_tertahan = $bonus_tertahan + $jurnal['Amount'];
            }elseif($jurnal['AccPos'] == "Credit"){
                $bonus_tertahan = $bonus_tertahan - $jurnal['Amount'];
            }
        }

        return view('bonus.index', compact('rekening', 'supplier', 'jenis', 'bonusapa', 'page', 'bonus' ,'bn', 'bonus_tertahan'));
    }

    public function editTopup($id)
    {
        $bn = TopUpBonus::where('id_bonus', $id)->select('AccNo', 'tgl', 'id_jurnal', 'id_bonus')->first();
        $rekening = Coa::where('StatusAccount', "Detail")->select('AccNo', 'AccName')->orderBy('AccNo','asc')->get();
        $supplier = Perusahaan::all();
        $bonusapa = "topup";
        $jenis = "edit";
        $page = MenuMapping::getMap(session('user_id'),"BMTU");
        $bonus = TopUpBonus::join('bankmember', 'tbltopupbonus.no_rek', 'bankmember.norek')->join('tblmember', 'bankmember.ktp', 'tblmember.ktp')->join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('tgl', $bn->tgl)->where('AccNo', $bn->AccNo)->select('bonus', 'tbltopupbonus.id_jurnal', 'tblbank.nama AS namabank', 'tbltopupbonus.no_rek', 'tblmember.nama', 'id_bonus')->get();

        return view('bonus.index', compact('rekening', 'supplier', 'jenis', 'bonusapa', 'page', 'bonus' ,'bn'));
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
        // echo "<pre>";
        // print_r($request->all());
        // die();
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
                // echo "<pre>";
                // print_r($request->all());
                // die();
                $tgl = $request->tgl_transaksi;
                $ctr = count($request->bonus);
                $bulan = $request->bulan;
                $tahun = $request->tahun;
                $perusahaan_id = $request->perusahaan;
                $perusahaan = Perusahaan::where('id',$perusahaan_id)->select('nama')->first();
                $ket = 'perhitungan bonus '.$perusahaan['nama'].' - bulan '.$bulan.' '.$tahun;
                $total_bonus = $request->total_bonus;
                $estimasi_bonus = $request->estimasi_bonus;
                $selisih = $request->selisih_bonus;
                $id_jurnal = Jurnal::getJurnalID('BP');
                $id_jurnal_lama = $request->id_jurnal_lama[0];

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
                    if($selisih < 0){
                        $pos = "Debet";
                        $selisih = abs($selisih);
                    }else{
                        $pos = "Credit";
                    }
                    // credit selisih laba/rugi estimasi bonus
                    $credit2 = new Jurnal(array(
                        'id_jurnal'     => $id_jurnal,
                        'AccNo'         => "7.1",
                        'AccPos'        => $pos,
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

                for($i=0;$i<$ctr;$i++){
                    $bonus = $request->bonus[$i];
                    $noid = $request->noid[$i];

                    if(isset($request->bonus_lama[$i])){
                        $bonus_lama = $request->bonus_lama[$i];
                        if($bonus_lama != $bonus){
                            $bonus = $bonus;
                        }else{
                            $bonus = $bonus_lama;
                        }
                        $id_bonus = $request->id_bonus[$i];
                        $data = Bonus::where('id_bonus', $id_bonus)->first();
                        $data->tgl = $tgl;
                        $data->bulan = $bulan;
                        $data->tahun = $tahun;
                        $data->bonus = $bonus;
                        $data->perusahaan_id = $perusahaan_id;
                        $data->id_jurnal = $id_jurnal;
                        $data->creator = session('user_id');
                        $data->update();
                    }else{
                    // elseif(empty(Bonus::where('noid', $noid)->where('tgl', $tgl)->where('bulan', $bulan)->where('tahun', $tahun)->where('perusahaan_id', $perusahaan_id)->first())){
                        // echo "new".$tgl." ".$id_bonus[$i]." ".$bonus;
                        // die();
                        $data = new Bonus(array(
                            'tgl' => $tgl,
                            'noid' => $noid,
                            'bulan' => $bulan,
                            'tahun' => $tahun,
                            'bonus' => $bonus,
                            'perusahaan_id' => $perusahaan_id,
                            'id_jurnal' => $id_jurnal,
                            'creator' => session('user_id'),
                        ));
                        $data->save();
                    }
                }
                $jurnal = Jurnal::where('id_jurnal', $id_jurnal_lama);
                $jurnal->delete();
                // die();

                return redirect()->route('bonus.index')->with('status', 'Data berhasil disimpan');
            }catch(\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }
    }

    public function updateBayar(Request $request, $id)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'tahun' => 'required',
            'bulan' => 'required',
            'tgl_transaksi' => 'required',
        ]);

        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                // echo "<pre>";
                // print_r($request->all());
                // die();
                $bulan = $request->bulan;
                $tahun = $request->tahun;
                $tgl = $request->tgl_transaksi;
                $ctr = count($request->norekening);
                $AccNo = $request->rekening;
                $selisih = $request->selisih_bonus;
                $bonus_tertahan = $request->bonus_tertahan;
                $ket = 'penerimaan bonus ke '.$AccNo.' - bulan '.$bulan.' '.$tahun;
                $id_jurnal_lama = $request->id_jurnal_lama[0];
                if($request->supplier != null){
                    $supplier = $request->supplier;
                }else{
                    $supplier = 0;
                }

                $id_jurnal = Jurnal::getJurnalID('BB');

                if($selisih!=0){
                    if($selisih < 0){
                        $pos = "Credit";
                        $selisih = abs($selisih);
                    }else{
                        $pos = "Debet";
                    }
                    // debet laba/rugi selisih pembayaran(penerimaan) bonus
                    $debet2 = new Jurnal(array(
                        'id_jurnal'     => $id_jurnal,
                        'AccNo'         => "7.2",
                        'AccPos'        => $pos,
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

                for($i=0;$i<$ctr;$i++){
                    $norek = $request->norekening[$i];
                    $bonus = $request->bonus[$i];
                    $bank = $request->namabank[$i];
                    $ket = 'penerimaan bonus ke'.$AccNo.' untuk '.$norek.' - bulan '.$bulan.' '.$tahun;

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

                    $bonusbayar = BonusBayar::where('no_rek', $norek)->where('tahun', $tahun)->where('bulan', $bulan)->where('tgl',$tgl)->where('AccNo',$AccNo)->select('id_bonus','id_jurnal')->get();
                    $num = $bonusbayar->count();

                    if(isset($request->bonus_lama[$i])){
                        $id_bonus = $request->id_bonus[$i];
                        $data = BonusBayar::where('id_bonus',$id_bonus)->first();


                        $data->tgl = $tgl;
                        $data->bulan = $bulan;
                        $data->tahun = $tahun;
                        $data->bonus = $bonus;
                        $data->AccNo = $AccNo;
                        $data->supplier = $supplier;
                        $data->id_jurnal = $id_jurnal;
                        $data->creator = session('user_id');
                        $data->update();
                    }else{
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
                    }
                }
                $jurnal_lama = Jurnal::where('id_jurnal', $id_jurnal_lama);
                $jurnal_lama->delete();

                return redirect()->route('bonus.penerimaan')->with('status', 'Data berhasil disimpan');
            }catch(\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
                    // return response()->json($e);
            }
        }
    }

    public function updateTopup(Request $request, $id)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'tgl_transaksi' => 'required',
        ]);

        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                // echo "<pre>";
                // print_r($request->all());
                // die();
                $ctr = count($request->norekening);
                $tgl = $request->tgl_transaksi;
                $AccNo = $request->rekening;

                for($i=0;$i<$ctr;$i++){
                    $id_jurnal_lama = $request->id_jurnal_lama[$i];
                    $norek = $request->norekening[$i];
                    $bonus = $request->bonus[$i];
                    $bank = $request->namabank[$i];
                    $ket = 'top up bonus '.$norek.' - '.$tgl;

                    $topup = TopUpBonus::where('no_rek', $norek)->where('tgl',$tgl)->where('AccNo',$AccNo)->select('id_bonus','id_jurnal')->get();
                    $num = $topup->count();

                    $id_jurnal = Jurnal::getJurnalID('BT');

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

                    if(isset($request->bonus_lama[$i])){
                        $id_bonus = $request->id_bonus[$i];
                        $data = TopUpBonus::where('id_bonus', $id_bonus)->first();
                        $data->tgl = $tgl;
                        $data->bonus = $bonus;
                        $data->AccNo = $AccNo;
                        $data->creator = session('user_id');
                        $data->id_jurnal = $id_jurnal;
                        $data->update();
                        $jurnal = Jurnal::where('id_jurnal', $id_jurnal_lama);
                        $jurnal->delete();
                    }else{
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            Bonus::where('id_jurnal', $id)->delete();
            Jurnal::where('id_jurnal', $id)->delete();
            return redirect()->route('bonus.index')->with('status', 'Data berhasil dihapus');
        }catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
            // return response()->json($e);
        }
    }

    public function destroyBayar($id)
    {
        try{
            BonusBayar::where('id_jurnal', $id)->delete();
            Jurnal::where('id_jurnal', $id)->delete();
            return redirect()->route('bonus.penerimaan')->with('status', 'Data berhasil dihapus');
        }catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
            // return response()->json($e);
        }
    }

    public function destroyTopup($id)
    {
        try{
            $data = TopUpBonus::where('tgl', $id)->select('id_jurnal')->get();
            foreach($data as $t){
                TopUpBonus::where('id_jurnal', $t->id_jurnal)->delete();
                Jurnal::where('id_jurnal', $t->id_jurnal)->delete();
            }

            return redirect()->route('bonus.topup')->with('status', 'Data berhasil dihapus');
        }catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
            // return response()->json($e);
        }
    }

    public function destroyGagalBonus($id)
    {
        try{
            $data = BonusGagal::where('id', $id)->first();
            $file = public_path('download/bonusgagal/').$data->file;
            // echo $file;
            // die();
            if (file_exists($file)){
                unlink($file);
            }
            $data->delete();
            return redirect()->route('bonus.bonusgagal')->with('status', 'Data berhasil dihapus');
        }catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
            // return response()->json($e);
        }
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
        $tgl = $request->tgl;
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
        return view('bonus.ajxCreateBonus', compact('perusahaanmember', 'bonus','tahun','bulan','tgl','perusahaan','bonusapa', 'estimasi_bonus'));
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
        $r = 1;
        $datas = array();

        for ($i=1; $i < $count ; $i++) {
            // echo $xls[0][$i][3];
            $noid = $xls[0][$i][1];
            $norek = $xls[0][$i][2];
            $nama = $xls[0][$i][3];
            $bonus = $xls[0][$i][4];

            if($norek <> ''){
                $num_member = PerusahaanMember::where('noid', $noid)->join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->join('bankmember', 'perusahaanmember.ktp', 'bankmember.ktp')->where('bankmember.norek', $norek)->where('tblmember.nama',"LIKE", $nama)->where('perusahaanmember.perusahaan_id',$perusahaan_id)->count();
                // $num_member= $perusahaan['perusahaanmember.noid']->count();
                if($num_member==0){
                    // $member = array(
                    //     'noid'  => $noid,
                    //     'norek' => $norek,
                    //     'nama'  => $nama,
                    //     'bonus' => $bonus
                    // );
                    // array_push($datas, $member);
                    $ktp = PerusahaanMember::where('perusahaanmember.noid', $noid)->select('perusahaanmember.ktp')->first();
                    $append = '<tr style="width:100%" id="trow'.$r.'" class="trow">
                    <td><input type="hidden" name="no[]" value="'.$r.'">'.$r.'</td>
                    <td><input type="hidden" name="nama[]" id="nama'.$r.'" value="'.$nama.'">'.$nama.'</td>
                    <td><input type="hidden" name="ktp[]" id="ktp'.$r.'" value="'.$ktp['ktp'].'">'.$ktp['ktp'].'</td>
                    <td><input type="hidden" name="noid[]" id="noid'.$r.'" value="'.$noid.'">'.$noid.'</td>
                    <td><input type="hidden" name="norekening[]" id="norekening'.$r.'" value="'.$norek.'">'.$norek.'</td>
                    <td><input type="hidden" name="bonus[]" id="bonus'.$r.'" value="'.$bonus.'">'.$bonus.'</td>
                    </tr>';

                    $data = array(
                        'append' => $append,
                        'count' => $r,
                        'jenis' => "bonus_gagal",
                    );

                    array_push($result, $data);
                    $r++;
                }else{
                    // echo "berhasil";
                    $ktp = PerusahaanMember::join('bankmember', 'perusahaanmember.ktp', 'bankmember.ktp')->where('perusahaanmember.noid', $noid)->where('norek', $norek)->where('perusahaan_id', $perusahaan_id)->select('perusahaanmember.ktp')->first();
                    $num_bonus = Bonus::where('noid', $noid)->where('tahun', $request->tahun)->where('bulan', $request->bulan)->count();
                    $total_bonus = $total_bonus + $bonus;
                    $append = '<tr style="width:100%" id="trow'.$row.'" class="trow">
                    <td>'.$row.'</td>
                    <td><input type="hidden" name="ktp[]" id="ktp'.$row.'" value="'.$ktp['ktp'].'">'.$ktp['ktp'].'</td>
                    <td><input type="hidden" name="noid[]" id="noid'.$row.'" value="'.$noid.'">'.$noid.'</td>
                    <td><input type="hidden" name="nama[]" id="nama'.$row.'" value="'.$nama.'">'.$nama.'</td>
                    <td><input type="text" class="form-control number" name="bonus[]" parsley-trigger="keyup" onkeyup="checkBonus()" id="bonus'.$row.'" value="'.$bonus.'"></td>
                    <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem('.$row.')" >Delete</a></td>
                    </tr>';

                    $data = array(
                        'append' => $append,
                        'count' => $row,
                        'jenis' => 'berhasil',
                    );
                    array_push($result, $data);
                    $row++;
                }
            }
        }
        // if(!empty($datas)){
        //     $pdf = PDF::loadview('bonus.pdfbonusgagal',['member'=>$datas, 'bulan'=>$request->bulan, 'tahun'=>$request->tahun, 'jenis'=>"perhitungan"])->setPaper('a4', 'potrait');
        //     $namafile = "gagal upload perhitungan bonus bulan $request->bulan $request->tahun.pdf";
        //     $pdf->save(public_path('download/'.$namafile));
        //     // return $pdf->download('gagal upload bonus bulan '.$request->bulan.' '.$request->tahun.'.pdf');
        //     response()->download(public_path('download/'.$namafile));
        // }

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

    public function deleteRowPerhitungan(Request $request)
    {
        try{
            $data = Bonus::where('id_bonus', $request->id)->first();
            $data->delete();
            return response()->json($data);
        }catch(\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
            // return response()->json($e);
        }
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
        $result_gagal = array();
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
        $r = 1;
        $datas = array();

        for ($i=1; $i < $count ; $i++) {
            $bank = $xls[0][$i][1];
            $norek = $xls[0][$i][2];
            $nama = $xls[0][$i][3];
            $bonus = $xls[0][$i][4];

            if($norek <> ''){
                $num_member = BankMember::join('tblmember', 'bankmember.ktp', 'tblmember.ktp')->join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('bankmember.norek',$norek)->where('tblmember.nama',"LIKE", $nama)->where('tblbank.nama', 'LIKE', $bank)->count();
                // $num_member= $perusahaan['perusahaanmember.noid']->count();
                if($num_member==0){
                    $member = BankMember::join('tblmember', 'bankmember.ktp', 'tblmember.ktp')->where('bankmember.norek', $norek)->select('tblmember.ktp AS ktp')->first();
                    $append = '<tr style="width:100%" id="trow'.$r.'" class="trow">
                    <td><input type="hidden" name="no[]" value="'.$r.'">'.$r.'</td>
                    <td><input type="hidden" name="nama[]" id="nama'.$r.'" value="'.$nama.'">'.$nama.'</td>
                    <td><input type="hidden" name="ktp[]" id="ktp'.$r.'" value="'.$member['ktp'].'">'.$member['ktp'].'</td>
                    <td><input type="hidden" name="norekening[]" id="norekening'.$r.'" value="'.$norek.'">'.$norek.'</td>
                    <td><input type="hidden" name="bonus[]" id="bonus'.$r.'" value="'.$bonus.'">'.$bonus.'</td>
                    </tr>';

                    $data = array(
                        'append' => $append,
                        'count' => $r,
                        'jenis' => "bonus_gagal",
                    );

                    array_push($result, $data);
                    $r++;
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
                        'jenis' => "berhasil",
                    );
                    array_push($result, $data);
                    $row++;
                }
            }
        }
        // if(!empty($datas)){
        //     $pdf = PDF::loadview('bonus.pdfbonusgagal',['member'=>$datas, 'bulan'=>$request->bulan, 'tahun'=>$request->tahun, 'jenis'=>"pembayaran"])->setPaper('a4', 'potrait');
        //     $namafile = "gagal upload penerimaan bonus bulan $request->bulan $request->tahun.pdf";
        //     $pdf->save(public_path('download/'.$namafile));
        //     // return $pdf->download('gagal upload bonus bulan '.$request->bulan.' '.$request->tahun.'.pdf');
        //     response()->download(public_path('download/'.$namafile));
        // }
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

    public function deleteRowPenerimaan(Request $request)
    {
        try{
            $data = BonusBayar::where('id_bonus', $request->id)->first();
            $data->delete();
            return response()->json($data);
        }catch(\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
            // return response()->json($e);
        }
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
        $r = 1;
        $datas = array();
        for ($i=1; $i < $count ; $i++) {
            $bank = $xls[0][$i][1];
            $norek = $xls[0][$i][2];
            $nama = $xls[0][$i][3];
            $bonus = $xls[0][$i][4];
            if($norek <> ''){
                $num_member = BankMember::join('tblmember', 'bankmember.ktp', 'tblmember.ktp')->join('tblbank', 'bankmember.bank_id', 'tblbank.id')->where('bankmember.norek',$norek)->where('tblmember.nama',"LIKE", $nama)->where('tblbank.nama', 'LIKE', $bank)->count();
                // $num_member= $perusahaan['perusahaanmember.noid']->count();
                if($num_member==0){
                    // $member = array(
                    //     'norek' => $norek,
                    //     'nama'  => $nama,
                    //     'bonus' => $bonus
                    // );
                    // array_push($datas, $member);
                    $member = BankMember::join('tblmember', 'bankmember.ktp', 'tblmember.ktp')->where('bankmember.norek', $norek)->select('tblmember.ktp AS ktp')->first();
                    $append = '<tr style="width:100%" id="trow'.$r.'" class="trow">
                    <td><input type="hidden" name="no[]" value="'.$r.'">'.$r.'</td>
                    <td><input type="hidden" name="nama[]" id="nama'.$r.'" value="'.$nama.'">'.$nama.'</td>
                    <td><input type="hidden" name="ktp[]" id="ktp'.$r.'" value="'.$member['ktp'].'">'.$member['ktp'].'</td>
                    <td><input type="hidden" name="norekening[]" id="norekening'.$r.'" value="'.$norek.'">'.$norek.'</td>
                    <td><input type="hidden" name="bonus[]" id="bonus'.$r.'" value="'.$bonus.'">'.$bonus.'</td>
                    </tr>';

                    $data = array(
                        'append' => $append,
                        'count' => $r,
                        'jenis' => "bonus_gagal",
                    );

                    array_push($result, $data);
                    $r++;
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
                        'jenis' => "berhasil",
                    );
                    array_push($result, $data);
                    $row++;
                }
            }
        }
        // if(!empty($datas)){
        //     $pdf = PDF::loadview('bonus.pdfbonusgagal',['member'=>$datas, 'tgl'=>$tgl, 'jenis'=>"topup"])->setPaper('a4', 'potrait');
        //     $namafile = "gagal upload top up bonus tanggal $tgl.pdf";
        //     $pdf->save(public_path('download/'.$namafile));
        //     // return $pdf->download('gagal upload bonus bulan '.$request->bulan.' '.$request->tahun.'.pdf');
        //     response()->download(public_path('download/'.$namafile));
        // }
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

    public function deleteRowTopup(Request $request)
    {
        try{
            $topup = TopUpBonus::where('id_bonus', $request->id)->first();
            $data = array(
                'norek' => $topup->no_rek,
                'tgl' => $topup->tgl,
                'bonus' => $topup->bonus,
            );

            $topup->delete();
            return response()->json($data);
        }catch(\Exception $e) {
            // return redirect()->back()->withErrors($e->getMessage());
            return response()->json($e->getMessage());
        }
    }

    public function showLaporanBonuslama(Request $request)
    {
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $member = Member::select('ktp','nama')->orderBy('nama','asc')->get();
        $bonusapa = "laporan";
        // $bonus = Bonus::where('tahun',$tahun)->where('bulan',$bulan)->get();
        // $bonusbayar = BonusBayar::where('tahun', $tahun)->where('bulan', $bulan)->get();

        return view('bonus.ajxShowBonus', compact('member', 'tahun','bulan','bonusapa'));
    }

    public function showLaporanBonus(Request $request)
    {
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $member = Member::select('ktp','nama')->orderBy('nama','asc')->get();
        $bonusapa = "laporan";
        $bonus = Bonus::where('tahun',$tahun)->where('bulan',$bulan)->get();
        // $bonusbayar = BonusBayar::where('tahun', $tahun)->where('bulan', $bulan)->get();

        return view('bonus.ajxShowBonus', compact('member', 'tahun','bulan','bonusapa', 'bonus'));
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
        $search = BankMember::join('tblmember','bankmember.ktp','=','tblmember.ktp')->join('tblbank', 'bankmember.bank_id', 'tblbank.id')->orWhere('tblmember.nama','LIKE', $key)->orWhere('norek','LIKE', $key)->orWhere('tblmember.ktp','LIKE', $key)->select('tblmember.nama', 'bankmember.norek', 'bankmember.id AS id', 'tblmember.ktp AS ktp', 'tblbank.nama AS namabank')->limit(5)->get();
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

    public function exportGagalBonus(Request $request)
    {
        // echo "<pre>";
        // print_r($request->all());
        // die();
        ini_set('max_execution_time', 3000);
        $bonusapa = $request->bonusapa3;
        if(($bonusapa=="perhitungan") OR ($bonusapa=="pembayaran")){
            $tahun = $request->tahun3;
            $bulan = $request->bulan3;
        }
        if($bonusapa=="perhitungan"){
            $perusahaan = Perusahaan::where('id', $request->perusahaan3)->select('nama')->first();
        }elseif(($bonusapa=="pembayaran") OR ($bonusapa=="topup")){
            $AccNo = Coa::where('AccNo', $request->AccNo3)->select('AccName')->first();
            $bank_id = $request->bank_id3;
        }
        $tgl = $request->tgl3;

        if($bonusapa=="perhitungan"){
            $filename = "Bonus Gagal Upload Perhitungan Bonus ".$perusahaan['nama']." bulan ".$bulan." ".$tahun."(".$tgl.")";
        }elseif($bonusapa=="pembayaran"){
            $filename = "Bonus Gagal Upload Penerimaan Bonus ".$AccNo['AccName']." bulan ".$bulan." ".$tahun."(".$tgl.")";
        }elseif($bonusapa=="topup"){
            $filename = "Bonus Gagal Upload Top Up Bonus dari ".$AccNo['AccName']."(".$tgl.")";
        }
        $exportTo = $request['xto'];
        $data = array();
        // $filename="Bonus Gagal Upload";

        $count = count($request->norekening);
        for($i=0; $i<$count; $i++){
            $nama = $request->nama[$i];
            $ktp = $request->ktp[$i];
            $norek = $request->norekening[$i];
            $bonus = $request->bonus[$i];
            $no = $i+1;
            if($bonusapa=="perhitungan"){
                $array = array(
                    // Data Member
                    'No' => $no,
                    'Nama' => $nama,
                    'No KTP' => $ktp,
                    'No ID' => $request->noid[$i],
                    'No Rekening' => $norek,
                    'Bonus' => $bonus,
                );
            }elseif(($bonusapa=="pembayaran") OR ($bonusapa=="topup")){
                $array = array(
                    // Data Member
                    'No' => $no,
                    'Nama' => $nama,
                    'No KTP' => $ktp,
                    'No Rekening' => $norek,
                    'Bonus' => $bonus,
                );
            }

            array_push($data, $array);
        }

        // Export To 0 == Export to Excel
        if($exportTo == 0){
            if($bonusapa=="perhitungan"){
                $export = new BonusGagalUploadExport1($data);
            }else{
                $export = new BonusGagalUploadExport2($data);
            }

            return Excel::download($export, $filename.'.xlsx');
        // Export To 1 == Export To PDF
        }elseif($exportTo == 1){
            if($bonusapa=="perhitungan"){

                $datas = ['member'=>$data, 'bulan'=>$bulan, 'tahun'=>$tahun, 'jenis'=>"perhitungan"];
            }elseif($bonusapa=="pembayaran"){
                $datas = ['member'=>$data, 'bulan'=>$bulan, 'tahun'=>$tahun, 'jenis'=>"pembayaran"];
            }elseif($bonusapa=="topup"){
                $datas = ['member'=>$data, 'tgl'=>$tgl, 'jenis'=>"topup"];
            }

            $pdf = PDF::loadview('bonus.pdfbonusgagal',$datas)->setPaper('a4', 'landscape');
            $data = new BonusGagal(array(
                'tgl' => $tgl,
                'jenis' => $bonusapa,
                'file'  => $filename.'.pdf',
                'creator' => session('user_id'),
            ));
            $data->save();

            $pdf->save(public_path('download/bonusgagal/'.$filename.'.pdf'));
            return $pdf->download($filename.'.pdf');
        }
    }

    public function checkEstimasiBonus(Request $request){
        $purchase = PurchaseDetail::join('tblpotrx', 'tblpotrxdet.trx_id', 'tblpotrx.id')->where('tblpotrx.month',$request->bulan)->where('tblpotrx.year',$request->tahun)->where('tblpotrx.supplier',$request->perusahaan_id)->select('qty', 'price', 'price_dist')->get();
        $estimasi_bonus = 0;
        foreach($purchase as $p){
            $estimasi_bonus = $estimasi_bonus + (($p['price_dist'] - $p['price']) * $p['qty']);
        }
        return response()->json($estimasi_bonus);
    }
}
