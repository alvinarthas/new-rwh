<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\Handler;
use App\Imports\BonusImport;
use App\Imports\BonusBayarImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Excel;
use App\Bonus;
use App\BonusGagal;
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

class BonusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perusahaans = Perusahaan::all();
        $bonusapa = "perhitungan";
        $jenis = "index";
        return view('bonus.index', compact('perusahaans', 'bonusapa', 'jenis'));
    }

    public function indexBayar(Request $request)
    {
        $rekening = CoaBayarBonus::orderBy('id','asc')->get();
        $bonusapa = "pembayaran";
        $jenis = "index";
        return view('bonus.index', compact('rekening', 'bonusapa','jenis'));
    }

    public function indexTopup(Request $request)
    {
        $rekening = CoaBayarBonus::orderBy('id','asc')->get();
        $bonusapa = "topup";
        $jenis = "index";
        return view('bonus.index', compact('rekening', 'bonusapa','jenis'));
    }

    public function indexLaporan(Request $request)
    {
        $bonusapa = "laporan";
        $jenis = "index";
        return view('bonus.index', compact('bonusapa','jenis'));
    }

    public function indexBonusGagal(Request $request)
    {
        $perusahaans = Perusahaan::all();
        $bonusapa = "bonusgagal";
        $jenis = "index";
        return view('bonus.index', compact('bonusapa','jenis','perusahaans'));
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
        return view('bonus.index', compact('perusahaans', 'jenis', 'bonusapa'));
    }

    public function createBayar()
    {
        $rekening = CoaBayarBonus::orderBy('id','asc')->get();
        $bonusapa = "pembayaran";
        $jenis = "create";
        return view('bonus.index', compact('rekening', 'jenis', 'bonusapa'));
    }

    public function createTopup()
    {
        $rekening = CoaBayarBonus::orderBy('id','asc')->get();
        $bonusapa = "topup";
        $jenis = "create";
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
            'tahun2' => 'required',
            'bulan2' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            // success
            try{
                P
                foreach($request->count as $i){
                    $idm = "id_member$i";
                    $id_member = $request->$idm;
                    $bn = "bonus$i";
                    $bonus = $request->$bn;
                    $bulan = $request->bulan2;
                    $tahun = $request->tahun2;
                    $ket = 'perhitungan bonus bulan '.$bulan.' '.$tahun;
                    $bonus = Bonus::where('member_id', $id_member)->where('tahun', $tahun)->where('bulan', $bulan)->select('id_jurnal')->get();
                    $num = $bonus->count();
                    // echo "<pre>";
                    // print_r($request->all());
                    // die();
                    if(empty($num)){
                        $id_jurnal = Jurnal::getJurnalID();

                        // debet piutang bonus
                        $data1 = new Jurnal(array(
                            'id_jurnal'     => $id_jurnal,
                            'AccNo'         => "4.2",
                            'AccPos'        => "Debet",
                            'Amount'        => $bonus,
                            'company_id'    => 1,
                            'description'   => $ket,
                            'creator'       => session('user_id')
                        ));
                        // credit estimasi bonus
                        $data2 = new Jurnal(array(
                            'id_jurnal'     => $id_jurnal,
                            'AccNo'         => "1.1.3.3",
                            'AccPos'        => "Credit",
                            'Amount'        => $bonus,
                            'company_id'    => 1,
                            'description'   => $ket,
                            'creator'       => session('user_id')
                        ));

                        $data3 = new Bonus(array(
                            'member_id' => $id_member,
                            'bulan'     => $bulan,
                            'tahun'     => $tahun,
                            'bonus'     => $bonus,
                            'creator'   => session('user_id'),
                        ));

                        $data1->save();
                        $data2->save();
                        $data3->save();
                    }else{
                        $id_jurnal = Jurnal::getJurnalID();
                        $data4 = Jurnal::where('id_jurnal', $bonus['id_jurnal'])->first();

                        // debet piutang bonus
                        $data5 = new Jurnal(array(
                            'id_jurnal'     => $id_jurnal,
                            'AccNo'         => "4.2",
                            'AccPos'        => "Debet",
                            'Amount'        => $bonus,
                            'company_id'    => 1,
                            'description'   => $ket,
                            'creator'       => session('user_id')
                        ));
                        // credit estimasi bonus
                        $data6 = new Jurnal(array(
                            'id_jurnal'     => $id_jurnal,
                            'AccNo'         => "1.1.3.3",
                            'AccPos'        => "Credit",
                            'Amount'        => $bonus,
                            'company_id'    => 1,
                            'description'   => $ket,
                            'creator'       => session('user_id')
                        ));

                        $data7 = Bonus::where('member_id', $id_member)->where('tahun', $tahun)->where('bulan', $bulan)->get();
                        $data7->bonus = $bonus;
                        $data7->creator = session('user_id');

                        $data4->delete();
                        $data5->save();
                        $data6->save();
                        $data7->update();
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
            'tahun2' => 'required',
            'bulan2' => 'required',
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
                    $bulan = $request->bulan2;
                    $tahun = $request->tahun2;
                    $tgl = $request->tgl;
                    $bank = $request->namabank[$i];
                    $AccNo = $request->AccNo;
                    $ket = 'pembayaran bonus '.$norek.' - bulan '.$bulan.' '.$tahun;

                    $bonusbayar = BonusBayar::where('no_rek', $norek)->where('tahun', $tahun)->where('bulan', $bulan)->where('tgl',$tgl)->where('bank_id',$bank)->select('id_bonus','id_jurnal')->get();
                    $num = $bonusbayar->count();

                    if($num==0){
                        if($bonus != 0){
                            $id_jurnal = Jurnal::getJurnalID();

                            // debet kas/bank
                            $data1 = new Jurnal(array(
                                'id_jurnal'     => $id_jurnal,
                                'AccNo'         => $AccNo,
                                'AccPos'        => "Debet",
                                'Amount'        => $bonus,
                                'company_id'    => 1,
                                'date'          => $tgl,
                                'description'   => $ket,
                                'creator'       => session('user_id')
                            ));
                            // credit piutang bonus tertahan
                            $data2 = new Jurnal(array(
                                'id_jurnal'     => $id_jurnal,
                                'AccNo'         => "4.2",
                                'AccPos'        => "Credit",
                                'Amount'        => $bonus,
                                'company_id'    => 1,
                                'date'          => $tgl,
                                'description'   => $ket,
                                'creator'       => session('user_id')
                            ));
                            // bonus bayar
                            $data3 = new BonusBayar(array(
                                'no_rek'    => $norek,
                                'tgl'       => $tgl,
                                'bulan'     => $bulan,
                                'tahun'     => $tahun,
                                'bonus'     => $bonus,
                                'creator'   => session('user_id'),
                                'bank_id'   => $bank,
                                'id_jurnal' => $id_jurnal,
                            ));
                            $data1->save();
                            $data2->save();
                            $data3->save();
                        }
                    }else{
                        $id_jurnal = Jurnal::getJurnalID();
                        $data4 = Jurnal::where('id_jurnal', $bonusbayar['id_jurnal'])->first();

                        $data5 = new Jurnal(array(
                            'id_jurnal'     => $id_jurnal,
                            'AccNo'         => $AccNo,
                            'AccPos'        => "Debet",
                            'Amount'        => $bonus,
                            'company_id'    => 1,
                            'date'          => $tgl,
                            'description'   => $ket,
                            'creator'       => session('user_id')
                        ));
                        // credit piutang bonus tertahan
                        $data6 = new Jurnal(array(
                            'id_jurnal'     => $id_jurnal,
                            'AccNo'         => "4.2",
                            'AccPos'        => "Credit",
                            'Amount'        => $bonus,
                            'company_id'    => 1,
                            'date'          => $tgl,
                            'description'   => $ket,
                            'creator'       => session('user_id')
                        ));
                        // bonus bayar
                        $data7 = BonusBayar::where('no_rek',$norek)->where('tahun',$tahun)->where('bulan',$bulan)->where('tgl',$tgl)->where('bank_id',$bank)->first();
                        $data7->bonus = $bonus;
                        $data7->id_jurnal = $id_jurnal;
                        $data7->creator = session('user_id');

                        $data4->delete();
                        $data5->save();
                        $data6->save();
                        $data7->update();
                    }
                }
                return redirect()->route('bonus.bayar')->with('status', 'Data berhasil disimpan');
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
                    $ket = 'top up bonus '.$norek;

                    $topup = TopUpBonus::where('no_rek', $norek)->where('tgl',$tgl)->where('bank_id',$bank)->select('id_bonus','id_jurnal')->get();
                    $num = $topup->count();

                    if($num==0){
                        if($bonus != 0){
                            $id_jurnal = Jurnal::getJurnalID();

                            // debet estimasi bonus
                            $data1 = new Jurnal(array(
                                'id_jurnal'     => $id_jurnal,
                                'AccNo'         => "1.1.3.3",
                                'AccPos'        => "Debet",
                                'Amount'        => $bonus,
                                'company_id'    => 1,
                                'date'          => $tgl,
                                'description'   => $ket,
                                'creator'       => session('user_id')
                            ));
                            // credit kas/bank
                            $data2 = new Jurnal(array(
                                'id_jurnal'     => $id_jurnal,
                                'AccNo'         => $AccNo,
                                'AccPos'        => "Credit",
                                'Amount'        => $bonus,
                                'company_id'    => 1,
                                'date'          => $tgl,
                                'description'   => $ket,
                                'creator'       => session('user_id')
                            ));
                            // Top Up Bonus
                            $data3 = new TopUpBonus(array(
                                'no_rek'    => $norek,
                                'tgl'       => $tgl,
                                'bonus'     => $bonus,
                                'creator'   => session('user_id'),
                                'bank_id'   => $bank,
                                'id_jurnal' => $id_jurnal,
                            ));
                            $data1->save();
                            $data2->save();
                            $data3->save();
                        }
                    }else{
                        $id_jurnal = Jurnal::getJurnalID();
                        $data4 = Jurnal::where('id_jurnal', $topup['id_jurnal'])->first();

                        // debet estimasi bonus
                        $data5 = new Jurnal(array(
                            'id_jurnal'     => $id_jurnal,
                            'AccNo'         => "1.1.3.3",
                            'AccPos'        => "Debet",
                            'Amount'        => $bonus,
                            'company_id'    => 1,
                            'date'          => $tgl,
                            'description'   => $ket,
                            'creator'       => session('user_id')
                        ));
                        // credit kas/bank
                        $data6 = new Jurnal(array(
                            'id_jurnal'     => $id_jurnal,
                            'AccNo'         => $AccNo,
                            'AccPos'        => "Credit",
                            'Amount'        => $bonus,
                            'company_id'    => 1,
                            'date'          => $tgl,
                            'description'   => $ket,
                            'creator'       => session('user_id')
                        ));
                        // topup
                        $data7 = TopUpBonus::where('no_rek',$norek)->where('tgl',$tgl)->where('bank_id',$bank)->first();
                        $data7->bonus = $bonus;
                        $data7->creator = session('user_id');
                        $data7->id_jurnal = $id_jurnal;

                        $data4->delete();
                        $data5->save();
                        $data6->save();
                        $data7->update();
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
        $perusahaanmember = PerusahaanMember::join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->where('perusahaanmember.perusahaan_id','=',$perusahaan)->select('tblmember.ktp', 'noid', 'nama')->orderBy('tblmember.nama', 'asc')->get();
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $bonusapa = "perhitungan";
        $bonus = Bonus::where('tahun',$tahun)->where('bulan',$bulan)->get();
        return view('bonus.ajxShowBonus', compact('perusahaanmember', 'bonus','bonusapa'));
    }

    public function createBonusPerhitungan(Request $request)
    {
        $perusahaan = $request->perusahaan;
        $perusahaanmember = DB::table('perusahaanmember')->join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->where('perusahaanmember.perusahaan_id',$perusahaan)->orderBy('tblmember.nama', 'asc')->get();
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $bonusapa = "perhitungan";
        $bonus = Bonus::where('tahun',$tahun)->where('bulan',$bulan)->get();
        $purchase = PurchaseDetail::join('tblpotrx', 'tblpotrxdet.trx_id', 'tblpotrx.id')->where('tblpotrx.bulan',$bulan)->where('tblpotrx.tahun',$tahun)->where('tblpotrx.supplier',$perusahaan)->select('prod_id', 'qty')->get();
        return view('bonus.ajxCreateBonus', compact('perusahaanmember', 'bonus','tahun','bulan','perusahaan','bonusapa'));
    }

    public function uploadBonusPerhitungan(Request $request)
    {
        $perusahaan = $request->perusahaan;
        $this->validate($request, ['file'  => 'required|mimes:xls,xlsx']);
        $path = $request->file('file')->getRealPath();
        $path = $request->file('file');
        // $data = Excel::load($path)->get();
        $array = Excel::toArray(new BonusImport, $path);
        // echo '<pre>';
        // print_r($array);
        $count = count($array[0]);
        $xls = array_chunk($array[0],$count);
        for ($i=1; $i < $count ; $i++) {
            if($xls[0][$i][2] <> ''){
                $num_member = PerusahaanMember::select('perusahaanmember.noid')->join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->where('tblmember.ktp',$xls[0][$i][1])->where('perusahaanmember.perusahaan_id',$perusahaan)->count();
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
                    $num_bonus = Bonus::where('member_id', $xls[0][$i][2])->where('tahun', $request->tahun)->where('bulan', $request->bulan)->count();

                    if($num_bonus==0){
                        $bonus = new Bonus;
                        $bonus->member_id = $xls[0][$i][2];
                        $bonus->tahun = $request->tahun;
                        $bonus->bulan = $request->bulan;
                        $bonus->bonus = $xls[0][$i][4];
                        $bonus->creator = session('user_id');
                        $bonus->save();
                    }else{
                        $bonus = Bonus::where('member_id', $xls[0][$i][2])->where('tahun',$request->tahun)->where('bulan',$request->bulan)->get();
                        $bonus->bonus = $xls[0][$i][4];
                        $bonus->creator = session('user_id');
                        $bonus->update();
                    }
                }
            }
        }
        return redirect()->route('bonus.index')->with('status', 'Data berhasil disimpan');
    }

    public function showBonusPembayaran(Request $request)
    {
        $tgl = $request->tgl;
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $AccNo = $request->rkng;
        $AccParent = Coa::where('AccNo', $AccNo)->first()->AccParent;
        $namabank = Coa::where('AccParent', $AccParent)->first()->AccName;
        $bank = Bank::where('nama','LIKE', $namabank)->first()->id;
        $bankmember = Bankmember::join('tblmember','bankmember.ktp','=','tblmember.ktp')->where('bank_id',$bank)->select('nama', 'norek')->orderBy('tblmember.nama', 'asc')->get();
        $bonusapa = "pembayaran";
        $bonus = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->where('tgl', $tgl)->get();
        return view('bonus.ajxShowBonus', compact('bankmember','bonus','tahun','bulan','bonusapa','namabank','AccNo', 'tgl'));
    }

    public function createBonusPembayaran(Request $request)
    {
        $AccNo = $request->rkng;
        $AccParent = Coa::where('AccNo', $AccNo)->first()->AccParent;
        $namabank = Coa::where('AccParent', $AccParent)->first()->AccName;
        $bank = Bank::where('nama','LIKE', $namabank)->first()->id;
        $tgl = $request->tgl;
        // $bankmember = Bankmember::where('bank_id',$bank)->get();

        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $bonusapa = "pembayaran";
        // $bonus = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->get();
        return view('bonus.ajxCreateBonus', compact('tahun','bulan','bonusapa','bank','AccNo', 'tgl'));
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

    public function uploadBonusPembayaran(Request $request)
    {
        $bank_id = $request->bank_id;
        $tgl = $request->tgl;
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $AccNo = $request->AccNo;
        $this->validate($request, ['file'  => 'required|mimes:xls,xlsx']);
        $path = $request->file('file')->getRealPath();
        $path = $request->file('file');
        // $data = Excel::load($path)->get();
        $array = Excel::toArray(new BonusBayarImport, $path);
        // echo '<pre>';
        // print_r($array);
        $count = count($array[0]);
        $xls = array_chunk($array[0],$count);
        try{
            for ($i=1; $i < $count ; $i++) {
                $ket='bonus '.$xls[0][$i][1];
                $bonusbayar = BonusBayar::where('no_rek',$xls[0][$i][1])->where('tahun', $request->tahun)->where('bulan',$request->bulan)->where('tgl',$request->tgl)->where('bank_id',$bank_id)->select('id_bonus','id_jurnal')->get();
                $num = $bonusbayar->count();

                if($xls[0][$i][2] <> ''){
                    if($num==0){
                        if($xls[0][$i][3] != 0){
                            $id_jurnal = Jurnal::getJurnalID();
                            // debet kas/bank
                            $data1 = new Jurnal(array(
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
                            $data2 = new Jurnal(array(
                                'id_jurnal'     => $id_jurnal,
                                'AccNo'         => '1-103005',
                                'AccPos'        => "Credit",
                                'Amount'        => $xls[0][$i][3],
                                'company_id'    => 1,
                                'date'          => $tgl,
                                'description'   => $ket,
                                'creator'       => session('user_id')
                            ));
                            // Pembayaran Bonus
                            $data3 = new BonusBayar(array(
                                'no_rek'    => $xls[0][$i][1],
                                'tgl'       => $tgl,
                                'bulan'     => $bulan,
                                'tahun'     => $tahun,
                                'bonus'     => $xls[0][$i][3],
                                'creator'   => session('user_id'),
                                'bank_id'   => $bank_id,
                                'id_jurnal' => $id_jurnal,
                            ));

                            $data1->save();
                            $data2->save();
                            $data3->save();
                        }
                    }else{
                        $id_jurnal = Jurnal::getJurnalID();
                        $data4 = Jurnal::where('id_jurnal', $bonusbayar['id_jurnal'])->first();

                        $data5 = new Jurnal(array(
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
                        $data6 = new Jurnal(array(
                            'id_jurnal'     => $id_jurnal,
                            'AccNo'         => '1-103005',
                            'AccPos'        => "Credit",
                            'Amount'        => $xls[0][$i][3],
                            'company_id'    => 1,
                            'date'          => $tgl,
                            'description'   => $ket,
                            'creator'       => session('user_id')
                        ));

                        $data7 = BonusBayar::where('no_rek',$xls[0][$i][1])->where('tahun', $request->tahun)->where('bulan',$request->bulan)->where('tgl',$request->tgl)->where('bank_id',$bank_id)->first();
                        $data7->bonus = $xls[0][$i][3];
                        $data7->id_jurnal = $id_jurnal;
                        $data7->creator = session('user_id');

                        $data4->delete();
                        $data5->save();
                        $data6->save();
                        $data7->update();
                    }
                }
            }
            return redirect()->route('bonus.bayar')->with('status', 'Data berhasil disimpan');
        }catch(\Exception $a){
            return redirect()->back()->withErrors($a->getMessage());
            // return response()->json($e);
        }
    }

    public function showBonusTopup(Request $request)
    {
        $tgl = $request->tgl;
        $AccNo = $request->rkng;
        $AccParent = Coa::where('AccNo', $AccNo)->first()->AccParent;
        $namabank = Coa::where('AccParent', $AccParent)->first()->AccName;
        $bank = Bank::where('nama','LIKE', $namabank)->first()->id;
        $bankmember = Bankmember::join('tblmember','bankmember.ktp','=','tblmember.ktp')->where('bank_id',$bank)->select('nama', 'norek')->orderBy('tblmember.nama', 'asc')->get();
        $bonusapa = "topup";
        $bonus = TopUpBonus::where('tgl',$tgl)->get();
        return view('bonus.ajxShowBonus', compact('bankmember','bonus','bonusapa','namabank','AccNo','tgl'));
    }

    public function createBonusTopup(Request $request)
    {
        $AccNo = $request->rkng;
        $AccParent = Coa::where('AccNo', $AccNo)->first()->AccParent;
        $namabank = Coa::where('AccParent', $AccParent)->first()->AccName;
        $bank = Bank::where('nama','LIKE', $namabank)->first()->id;
        $tgl = $request->tgl;
        // $bankmember = Bankmember::where('bank_id',$bank)->get();
        $bonusapa = "topup";
        // $bonus = BonusBayar::where('tahun',$tahun)->where('bulan',$bulan)->get();
        return view('bonus.ajxCreateBonus', compact('bonusapa','bank','AccNo', 'tgl'));
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
        $bankmember = BankMember::where('bank_id',$request->bankid);
        $search = $bankmember->join('tblmember','bankmember.ktp','=','tblmember.ktp')->where('norek','LIKE', $key)->orWhere('tblmember.nama','LIKE', $key)->orWhere('tblmember.ktp','LIKE', $key)->select('tblmember.nama', 'bankmember.norek', 'bankmember.id AS id', 'tblmember.ktp AS ktp')->limit(5)->get();
        // $s = BankMember::where('norek','LIKE', $norek.'%')->select('norek', 'id')->limit(5)->get();
        $data = array();
        $array = json_decode( json_encode($search), true);
        foreach ($array as $key) {
            $arrayName = array('id' =>$key['id'],'norek' => $key['norek'], 'nama' => $key['nama'], 'ktp' => $key['ktp']);
            // $arrayName = array('id' => $key['id'],'text' => $key['norek']);
            array_push($data,$arrayName);
        }
        echo json_encode($data, JSON_FORCE_OBJECT);
    }
}
