<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bonus;
use App\BonusGagal;
use Illuminate\Support\Facades\DB;
use App\Perusahaan;
use Cyberduck\LaravelExcel\Contract\ParserInterface;
use Importer;
use Excel;
use App\Imports\BonusImport;

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
        $jenis = "index";
        return view('bonus.index', compact('perusahaans', 'jenis'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $perusahaans = Perusahaan::all();
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
        echo "<pre>";
        print_r("anjing");
        die();
        foreach($request->count as $i){
            $idm = "id_member$i";
            $id_member = $request->$idm;
            $bn = "bonus$i";
            $bonus = $request->$bn;
            $num = Bonus::where('id_member', $id_member)->where('tahun', $request->tahun2)->where('bulan', $request->bulan2)->count();

            echo "<pre>";
            print_r($bn);
            die();
            if($num==0){
                $data = new Bonus(array(
                    'id_member' => $id_member,
                    'bulan'     => $request->bulan2,
                    'tahun'     => $request->tahun2,
                    'bonus'     => $bonus,
                    'creator'   => session('user_id'),
                ));
                echo "<pre>";
                print_r($data);
                die();
                // $data->save();
            }else{
                $data = Bonus::where('id_member', $id_member)->where('tahun', $request->tahun)->where('bulan', $request->bulan)->get();
                $data->bonus = $bonus;
                $data->creator = session('user_id');
                echo "<pre>";
                print_r($data);
                die();
                // $data->update();
            }
        }
        // return redirect()->route('bonus.index')->with('status', 'Data berhasil disimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

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

    public function showBonus(Request $request)
    {
        $perusahaanmember = DB::table('perusahaanmember')->join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->where('perusahaanmember.perusahaan_id','LIKE',$request->perusahaan)->orderBy('tblmember.nama', 'asc')->get();
        $tahun = date('Y', strtotime($request->period));
        $bulan = date('m', strtotime($request->period));
        $bonus = Bonus::where('tahun',$tahun)->where('bulan',$bulan)->get();
        return view('bonus.ajxShowBonus', compact('perusahaanmember', 'bonus'));
    }

    public function createBonus(Request $request)
    {
        $perusahaan = $request->perusahaan;
        $perusahaanmember = DB::table('perusahaanmember')->join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->where('perusahaanmember.perusahaan_id','LIKE',$request->perusahaan)->orderBy('tblmember.nama', 'asc')->get();
        $tahun = date('Y', strtotime($request->period));
        $bulan = date('m', strtotime($request->period));
        $bonus = Bonus::where('tahun',$tahun)->where('bulan',$bulan)->get();
        return view('bonus.ajxCreateBonus', compact('perusahaanmember', 'bonus','tahun','bulan','perusahaan'));
    }

    public function uploadBonus(Request $request)
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

        // if($data->count() > 0){
        //     foreach($data->toArray() as $key => $value){
        //         foreach($value as $row){
        //             echo $row['1'];
        //             die;
        //             if(!empty($row['member_id'])){
        //                 $perusahaan = DB::table('perusahaanmember')->select('perusahaanmember.noid')->join('tblmember','perusahaanmember.ktp','=','tblmember.ktp')->where('perusahaanmember.noid','=',$row['member_id'])->where('tblmember.name','=',$row['nama']);
        //                 $num_member= $perusahaan['perusahaan.noid']->count();
        //                 if($num_member==0){
        //                     $bonusgagal = new BonusGagal;
        //                     $bonusgagal->ktp = $row['1'];
        //                     $bonusgagal->member_id = $row['2'];
        //                     $bonusgagal->nama = $row['3'];
        //                     $bonusgagal->tahun = $request->tahun;
        //                     $bonusgagal->bulan = $request->bulan;
        //                     $bonusgagal->bonus = $row['4'];
        //                     $bonusgagal->creator = session('user_id');
        //                     $bonusgagal->perusahaan = $request->perusahaan;
        //                     $bonusgagal->save();
        //                 }else{
        //                     $num_bonus = Bonus::where('id_member', $row['member_id'])->where('tahun', $request->tahun)->where('bulan', $request->bulan)->count();

        //                     if($num_bonus==0){
        //                         $bonus = new Bonus;
        //                         $bonus->id_member = $row['member_id'];
        //                         $bonus->tahun = $request->tahun;
        //                         $bonus->bulan = $request->bulan;
        //                         $bonus->bonus = $row['bonus'];
        //                         $bonus->creator = session('user_id');
        //                         $bonus->save();
        //                     }else{
        //                         $bonus = Bonus::where('id_member', $row['member_id'])->where('tahun',$request->tahun)->where('bulan',$request->bulan)->get();
        //                         $bonus->bonus;
        //                         $bonus->update();
        //                     }
        //                 }
        //             }
        //         }
        //     }
        // }
        // return redirect('/createBonus');
    }

    // public function transform($row, $header)
    // {
    //     $model = new BonusGagal;
    //     $model->ktp = $row[1];
    //     $model->member_id = $row[2];
    //     $model->nama = $row[3];
    //     $model->bonus = $row[4];
    //     $model->save();
    //     return back();
    // }
}
