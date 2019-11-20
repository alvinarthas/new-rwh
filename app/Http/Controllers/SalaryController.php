<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;

use App\Employee;
use App\GajiPokok;
use App\RecordPoin;
use App\MenuMapping;
use App\Salary;
use App\SalaryDet;
use App\Purchase;
use App\BonusPegawai;
use App\BonusPegawaiDet;

use Carbon\Carbon;

class SalaryController extends Controller
{
    // Gaji Pokok
    public function indexGajiEmp(Request $request){
        $employees = GajiPokok::all();
        $page = MenuMapping::getMap(session('user_id'),"EMES");
        return view('salary.gajipokok.index',compact('employees','page'));
    }

    public function formGajiEmp($jenis, $id=null, Request $request){
        if($jenis == "create"){
            $employees = Employee::select('id','username')->get();
            return view('salary.gajipokok.form',compact('jenis','employees'));
        }else{  
            $employees = Employee::select('id','username')->get();
            $employee = GajiPokok::where('employee_id',$id)->first();
            return view('salary.gajipokok.form',compact('jenis','employee','employees'));
        }
    }
    
    public function storeGajiEmp($jenis, $id=null, Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'employee' => 'required',
            'gaji_pokok' => 'required|integer',
            'tunjangan_jabatan' => 'required|integer',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            if($jenis == "store"){
                $gaji = new GajiPokok(array(
                    'employee_id' => $request->employee,
                    'gaji_pokok' => $request->gaji_pokok,
                    'tunjangan_jabatan' => $request->tunjangan_jabatan,
                ));
            }elseif ($jenis == "update") {
                $gaji = GajiPokok::where('id',$id)->first();

                $gaji->gaji_pokok = $request->gaji_pokok;
                $gaji->tunjangan_jabatan = $request->tunjangan_jabatan;
            }
            $gaji->save();

            return redirect()->route('indexGajiEmp')->with('status', 'Data Gaji berhasil dibuat');
        }
    }

    public function delGajiEmp(Request $request){
        $gaji = GajiPokok::where('id',$request->id)->first();
        try{
            $gaji->delete();
            return "true";
        // fail
        }catch (\Exception $e) {
            return redirect()->back()->withErrors($e->errorInfo);
        }
    }

    // Record Poin
    public function indexPoin(Request $request){
        if($request->ajax()){
            $start = $request->start_date;
            $end = $request->end_date;
            $jenis = $request->jenis;
            $employees = Employee::select('id','username')->join('tblemployeerole as er','er.username','=','tblemployee.username')->join('tblrole as r','r.id','er.role_id')->where('r.role_name','LIKE','Staff%')->select('tblemployee.id','tblemployee.username')->get();
            $datas = collect();

            foreach ($employees as $employee) {
                $kolek = collect();
                $poin = RecordPoin::sumPoin($employee->id,$start,$end,$jenis);

                $kolek->put('id',$employee->id);
                $kolek->put('username',$employee->username);
                $kolek->put('poin',$poin);

                $datas->push($kolek);
            }
            return response()->json(view('salary.poin.view',compact('datas','start','end'))->render());
        }else{
            $page = MenuMapping::getMap(session('user_id'),"EMEP");
            return view('salary.poin.index',compact('page'));
        }
        
    }

    public function detailPoin(Request $request){
        $start = $request->start;
        $end = $request->end;
        $employee = $request->employee_id;
        $nama = Employee::where('id',$employee)->select('username')->first()->username;
        $detpoin = RecordPoin::where('employee_id',$employee)->whereBetween('date',[$start,$end])->select('id','poin','date','creator')->get();
        $datas = collect();

        foreach ($detpoin as $det) {
            $poin = collect();
            $poin->put('date',$det->date);
            $poin->put('id',$det->id);
            $poin->put('poin',$det->poin);
            $poin->put('creator',Employee::where('id',$det->creator)->select('username')->first()->username);

            $datas->push($poin);
        }

        $page = MenuMapping::getMap(session('user_id'),"EMEP");
        return response()->json(view('salary.poin.detail',compact('datas','start','end','nama','page'))->render());
    }

    public function formPoin(Request $request){
        $employees = Employee::select('id','username')->join('tblemployeerole as er','er.username','=','tblemployee.username')->join('tblrole as r','r.id','er.role_id')->where('r.role_name','LIKE','Staff%')->select('tblemployee.id','tblemployee.username','tblemployee.scanfoto')->get();
        return view('salary.poin.form',compact('employees'));
    }

    public function storePoin(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'employee' => 'required',
            'poin' => 'required',
            'date' => 'required|date',
            'jenis' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $date = Carbon::createFromFormat('Y-m-d H:i:s',$request->date.'00:00:00');
            $today = Carbon::now();
            
            $interval = date_diff($today, $date);

            if($interval->days > 2){
                return redirect()->back()->with("warning","Maaf anda tidak bisa melakukan input poin, karena sudah melebihi 2 hari");
            }else{
                try {
                    $poin = new RecordPoin(array(
                        'employee_id' => $request->employee,
                        'poin' => $request->poin,
                        'date' => $request->date,
                        'jenis' => $request->jenis,
                        'creator' => session('user_id'),
                    ));
                    
                    $poin->save();
        
                    return redirect()->route('indexPoin')->with('status', 'Data Poin berhasil dibuat');
                } catch (\Throwable $th) {
                    return redirect()->back()->withErrors($e->getMessage());
                }
            }
            
        }
    }

    public function delPoin(Request $request){
        try{
            RecordPoin::where('id',$request->id)->delete();
            return "true";
        // fail
        }catch (\Exception $e) {
            return redirect()->back()->withErrors($e->errorInfo);
        }
    }


    // Perhitungan Gaji
    public function indexPerhitunganGaji(Request $request){
        if($request->ajax()){
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            $salary = Salary::where('month',$bulan)->where('year',$tahun)->first();
            $salaries = SalaryDet::join('tbl_salary as sd','sd.id','=','tbl_salary_detail.salary_id')->where('sd.month',$bulan)->where('sd.year',$tahun)->select('tbl_salary_detail.*')->get();
            $page = MenuMapping::getMap(session('user_id'),"EMPG");

            return response()->json(view('salary.perhitungan.show',compact('salaries','bulan','tahun','salary','page'))->render());
        }else{
            $page = MenuMapping::getMap(session('user_id'),"EMPG");
            return view('salary.perhitungan.index',compact('page'));
        }
    }

    public function detGajiPegawai(Request $request){
        $saldet = SalaryDet::join('tbl_salary as sd','sd.id','=','tbl_salary_detail.salary_id')->where('sd.month',$request->bulan)->where('sd.year',$request->tahun)->where('employee_id',$request->employee)->select('tbl_salary_detail.*')->first();
        $bonpeg = BonusPegawai::where('month',$request->bulan)->where('year',$request->tahun)->where('employee_id',$request->employee)->first();
        $bonpegdet = BonusPegawaiDet::where('bonus_pegawai_id',$bonpeg->id)->first();

        if ($request->ajax()) {
            return response()->json(view('salary.perhitungan.detail',compact('bonpeg','bonpegdet','saldet'))->render());
        }
    }

    public function createPerhitunganGaji(Request $request){
        return view('salary.perhitungan.form');
    }

    public function storePerhitunganGaji(Request $request){
        // Detail Information
        $month = $request->bulan;
        $year = $request->tahun;
        $bv = $request->bv;
        $hari_kerja = $request->hari_kerja;
        $collectionEom = collect();

        $checkJurnal = Salary::where('month',$month)->where('year',$year)->count();

        if($checkJurnal == 0){
            // Store Salary to Db
            $salary = new Salary(array(
                'month' => $month,
                'year' => $year,
                'bv' => $bv,
                'hari_kerja' => $hari_kerja,
                'creator' => session('user_id'),
            ));

            $salary->save();

            // Total Poin
            $ttl_poin_internal = RecordPoin::totalPoin($month,$year,1);
            $ttl_poin_logistik = RecordPoin::totalPoin($month,$year,0);
            $ttl_poin_kendali_perusahaan = 0;
            $ttl_poin_top3 = 3;

            // Anggaran Bonus
            $anggaran_internal = $bv*0.35;
            $anggaran_logistik = $bv*0.15;
            $anggaran_kendali_perusahaan = $bv*0.1;
            $anggaran_top3 = $bv*0.05;
            $anggaran_eom = $bv*0.1;

            $arr_top3 = Purchase::getTop3($month,$year);

            // Value per poin
            if($ttl_poin_internal == 0){
                $value_share_internal = 0;
            }else{
                $value_share_internal = $anggaran_internal/$ttl_poin_internal;
            }

            if($ttl_poin_logistik == 0){
                $value_share_logistik = 0;
            }else{
                $value_share_logistik = $anggaran_logistik/$ttl_poin_logistik;
            }

            if($ttl_poin_kendali_perusahaan == 0){
                $value_share_kendali_perusahaan = 0;
            }else{
                $value_share_kendali_perusahaan = $anggaran_kendali_perusahaan/$ttl_poin_kendali_perusahaan;
            }

            if($ttl_poin_top3 == 0){
                $value_share_top3 = 0;
            }else{
                $value_share_top3 = $anggaran_top3/$ttl_poin_top3;
            }

            // Hitung Bonus tiap pegawai
            foreach (GajiPokok::all() as $peg) {
                $eomcollect = collect();
                // Share Internal
                $poin_internal = RecordPoin::sumPoin2($peg->employee_id,$month,$year,1);
                if($ttl_poin_internal == 0){
                    $persen_internal = 0;
                }else{
                    $persen_internal = ($poin_internal/$ttl_poin_internal)*100;
                }
                
                $value_internal = $poin_internal*$value_share_internal;

                // Share Logistik
                $poin_logistik = RecordPoin::sumPoin2($peg->employee_id,$month,$year,0);
                if($ttl_poin_logistik == 0){
                    $persen_logistik = 0;
                }else{
                    $persen_logistik = ($poin_logistik/$ttl_poin_logistik)*100;
                }
                $value_logistik = $poin_logistik*$value_share_logistik;

                // Share Kendali Perusahaan
                $poin_kendali_perusahaan = Purchase::sharePost($month,$year,$peg->employee_id);
                if($ttl_poin_kendali_perusahaan == 0){
                    $persen_kendali_perusahaan = 0;
                }else{
                    $persen_kendali_perusahaan = (($poin_kendali_perusahaan/$ttl_poin_kendali_perusahaan)*100)/2;
                }
                $value_kendali_perusahaan = $poin_kendali_perusahaan*$value_share_kendali_perusahaan;
                
                // Share Top 3

                if (in_array($peg->employee_id, (array) $arr_top3)){
                    $poin_top3 = 1;
                }else{
                    $poin_top3 = 0;
                }
                
                if($ttl_poin_top3 == 0){
                    $persen_top3 = 0;
                }else{
                    $persen_top3 = (($poin_top3/$ttl_poin_top3)*100)/2;
                }
                
                $value_top3 = $poin_top3*$value_share_top3;

                // Employee of The Month Sementara
                $eom = 0;

                // Tunjangan Persentase
                if($value_share_internal == 0 || $ttl_poin_internal == 0){
                    $tunjangan_persentase = 0;
                }else{
                    $tunjangan_persentase = ((($peg->tunjangan_jabatan/$value_share_internal)/$ttl_poin_internal)*100)/2;
                }

                // Bonus Jabatan Sementara
                $bonus_jabatan = 0;

                // Total Bonus dan Persen
                $total_persen_all = $persen_internal+$persen_logistik+$persen_kendali_perusahaan+$persen_top3+$tunjangan_persentase;
                $total_bonus = $value_internal+$value_logistik+$value_kendali_perusahaan+$value_top3+$eom;

                // Take Home Pay
                $take_home_pay = $peg->gaji_pokok+$bonus_jabatan+$total_bonus;

                // Store to Collection EOM
                $eomcollect->put('id',$peg->employee_id);
                $eomcollect->put('value',$total_persen_all);
                $collectionEom->push($eomcollect);

                // Store to Salary Detail
                $salarydet = new SalaryDet(array(
                    'salary_id' => $salary->id,
                    'bonus' => $total_bonus,
                    'gaji_pokok' => $peg->gaji_pokok,
                    'bonus_jabatan' => $bonus_jabatan,
                    'take_home_pay' => $take_home_pay,
                    'employee_id' => $peg->employee_id,
                    'tunjangan_jabatan' => $peg->tunjangan_jabatan,
                ));
                $salarydet->save();

                // Store into Bonus Pegawai
                $bonus_pegawai = new BonusPegawai(array(
                    'salary_det_id' => $salarydet->id,
                    'employee_id' => $peg->employee_id,
                    'month' => $month,
                    'year' => $year,
                    'tugas_internal' => $value_internal,
                    'logistik' => $value_logistik,
                    'kendali_perusahaan' => $value_kendali_perusahaan,
                    'top3' => $value_top3,
                    'eom' => $eom,
                    'total_bonus' => $total_bonus,
                ));
                $bonus_pegawai->save();

                // Store into Bonus Pegawai Detail
                $bonus_pegawai_detail = new BonusPegawaiDet(array(
                    'bonus_pegawai_id' => $bonus_pegawai->id,
                    'poin_internal' => $poin_internal,
                    'persen_internal' => $persen_internal,
                    'poin_logistik' => $poin_logistik,
                    'persen_logistik' => $persen_logistik,
                    'poin_kendali' => $poin_kendali_perusahaan,
                    'persen_kendali' => $persen_kendali_perusahaan,
                    'poin_top3' => $poin_top3,
                    'persen_top3' => $persen_top3,
                    'tunjangan_persen' => $tunjangan_persentase,
                    'total_persen' => $total_persen_all
                ));
                $bonus_pegawai_detail->save();
            }

            // Tentukan EOM
            $sorted = $collectionEom->sortByDesc('value');
            
            if($bv >= 15000000){
                // get pegawai yg dapat eom
                $choseneom = $sorted->values()->all();
                for ($i=0; $i < 2 ; $i++) {
                    if($i == 0){
                        $value_eom = $bv*0.07;
                    }else{
                        $value_eom = $bv*0.03;
                    }
                    
                    if(isset($choseneom[$i]['id'])){
                        // Insert to DB Bonus Pegawai
                        $bonpeg = BonusPegawai::where('employee_id',$choseneom[$i]['id'])->where('month',$month)->where('year',$year)->latest()->first();
                        $totbon = $bonpeg->total_bonus + $value_eom;

                        $bonpeg->eom = $value_eom;
                        $bonpeg->total_bonus = $totbon;
                        $bonpeg->save();

                        // Insert to Salary Det
                        $saldet = SalaryDet::where('id',$bonpeg->salary_det_id)->first();

                        $tot_takehome = $totbon+$saldet->take_home_pay;
                        $saldet->bonus = $totbon;
                        $saldet->take_home_pay = $tot_takehome;
                    }
                    
                }
            }else{
                // get pegawai yg dapat eom
                
                // 5.1
                $choseneom = $sorted->values()->first();

                $value_eom = $bv*0.1;

                // Insert to DB Bonus Pegawai
                $bonpeg = BonusPegawai::where('employee_id',$choseneom['id'])->where('month',$month)->where('year',$year)->first();
                $totbon = $bonpeg->total_bonus + $value_eom;

                $bonpeg->eom = $value_eom;
                $bonpeg->total_bonus = $totbon;
                $bonpeg->save();

                // Insert to Salary Det
                $saldet = SalaryDet::where('id',$bonpeg->salary_det_id)->first();
                
                $tot_takehome = $totbon+$saldet->take_home_pay;

                $saldet->bonus = $totbon;
                $saldet->take_home_pay = $tot_takehome;
                $saldet->save();
            }
                // Check Bonus Manager
                // count gaji yg > 5.6jt
                // if diatas 3 orang, semua manager dapet 10% bv
                return redirect()->route('indexPerhitunganGaji')->with('status', 'Data Gaji berhasil dibuat');
        }else{
                return redirect()->back()->withErrors('Data Jurnal sudah data, silahkan hapus dulu jika ingin membuat di periode yang sama');
        }
    }

    public function deletePerhitunganGaji(Request $request){
        try {
            Salary::where('id',$request->id)->delete();
            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }
}
