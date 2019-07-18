<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bonus;
use App\BonusGagal;
use App\Perusahaan;
use App\Imports\BonusImport;
use Illuminate\Support\Facades\DB;
use Excel;


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
        $bank = Perusahaan::all();
        $bonusapa = "pembayaran";
        $jenis = "index";
        return view('bonus.index', compact('bank', 'bonusapa', 'jenis'));
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
        return view('bonus.index', compact('perusahaans', 'jenis'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach($request->count as $i){
            $idm = "id_member$i";
            $id_member = $request->$idm;
            $bn = "bonus$i";
            $bonus = $request->$bn;
            $bulan = $request->bulan2;
            $tahun = $request->tahun2;
            $num = Bonus::where('member_id', $id_member)->where('tahun', $tahun)->where('bulan', $bulan)->count();

            // echo "<pre>";
            // print_r($request->all());
            // die();
            if(empty($num)){
                $data = new Bonus(array(
                    'member_id' => $id_member,
                    'bulan'     => $bulan,
                    'tahun'     => $tahun,
                    'bonus'     => $bonus,
                    'creator'   => session('user_id'),
                ));

                $data->save();
            }else{
                $data = Bonus::where('member_id', $id_member)->where('tahun', $tahun)->where('bulan', $bulan)->get();
                $data->bonus = $bonus;
                $data->creator = session('user_id');
                $data->update();
            }

        }
        return redirect()->route('bonus.index')->with('status', 'Data berhasil disimpan');
    }

    public function storeBayar(Request $request)
    {
        foreach($request->count as $i){
            $idm = "id_member$i";
            $id_member = $request->$idm;
            $bn = "bonus$i";
            $bonus = $request->$bn;
            $bulan = $request->bulan2;
            $tahun = $request->tahun2;
            $num = Bonus::where('member_id', $id_member)->where('tahun', $tahun)->where('bulan', $bulan)->count();

            // echo "<pre>";
            // print_r($request->all());
            // die();
            if(empty($num)){
                $data = new Bonus(array(
                    'member_id' => $id_member,
                    'bulan'     => $bulan,
                    'tahun'     => $tahun,
                    'bonus'     => $bonus,
                    'creator'   => session('user_id'),
                ));

                $data->save();
            }else{
                $data = Bonus::where('member_id', $id_member)->where('tahun', $tahun)->where('bulan', $bulan)->get();
                $data->bonus = $bonus;
                $data->creator = session('user_id');
                $data->update();
            }

        }
        return redirect()->route('bonus.index')->with('status', 'Data berhasil disimpan');
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
        $perusahaanmember = DB::table('perusahaanmember')->join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->where('perusahaanmember.perusahaan_id','LIKE',$perusahaan)->orderBy('tblmember.nama', 'asc')->get();
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $bonus = Bonus::where('tahun',$tahun)->where('bulan',$bulan)->get();
        return view('bonus.ajxShowBonus', compact('perusahaanmember', 'bonus'));
    }

    public function createBonusPerhitungan(Request $request)
    {
        $perusahaan = $request->perusahaan;
        $perusahaanmember = DB::table('perusahaanmember')->join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->where('perusahaanmember.perusahaan_id','LIKE',$perusahaan)->orderBy('tblmember.nama', 'asc')->get();
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $bonusapa = "perhitungan";
        $bonus = Bonus::where('tahun',$tahun)->where('bulan',$bulan)->get();
        return view('bonus.ajxCreateBonus', compact('perusahaanmember', 'bonus','tahun','bulan','perusahaan','bonusapa'));
    }

    public function createBonusPembayaran(Request $request)
    {
        $bank = $request->bank;
        $bankmember = BankMember::where('namabank','LIKE', $bank)->get();
        $tahun = $request->tahun;
        $bulan = $request->bulan;
        $bonusapa = "pembayaran";
        $bonus = Bonus::where('tahun',$tahun)->where('bulan',$bulan)->get();
        return view('bonus.ajxCreateBonus', compact('bankmember', 'bonus','tahun','bulan','bonusapa'));
    }

    public function uploadBonusPerhitungan(Request $request)
    {
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
                $num_member = DB::table('perusahaanmember')->select('perusahaanmember.noid')->join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->where('perusahaanmember.noid',$xls[0][$i][2])->where('tblmember.nama',$xls[0][$i][3])->count();
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
                    $num_bonus = Bonus::where('id_member', $xls[0][$i][2])->where('tahun', $request->tahun)->where('bulan', $request->bulan)->count();

                    if($num_bonus==0){
                        $bonus = new Bonus;
                        $bonus->id_member = $xls[0][$i][2];
                        $bonus->tahun = $request->tahun;
                        $bonus->bulan = $request->bulan;
                        $bonus->bonus = $xls[0][$i][4];
                        $bonus->creator = session('user_id');
                        $bonus->save();
                    }else{
                        $bonus = Bonus::where('id_member', $xls[0][$i][2])->where('tahun',$request->tahun)->where('bulan',$request->bulan)->get();
                        $bonus->bonus = $xls[0][$i][4];
                        $bonus->creator = session('user_id');
                        $bonus->update();
                    }
                }
            }

        }
        return redirect()->route('bonus.index')->with('status', 'Data berhasil disimpan');
    }
}
