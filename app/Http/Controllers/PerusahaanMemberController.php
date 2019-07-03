<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;

use App\PerusahaanMember;
use App\Perusahaan;

class PerusahaanMemberController extends Controller
{
    public function create(Request $request){
        $ktp = $request->get('ktp');
        $jenis = "create";
        $perusahaan = Perusahaan::all();
        
        if ($request->ajax()) {
            return response()->json(view('member.perusahaanmember.form',compact('ktp','jenis','perusahaan'))->render());
        }
    }

    public function store(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'perusahaan' => 'required|string',
            'nomorid' => 'string',
            'password' => 'string',
            'posisi' => 'string'
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $perusahaan = new PerusahaanMember(array(
                // Informasi Pribadi
                'perusahaan_id' => $request->perusahaan,
                'noid' => $request->nomorid,
                'passid' => $request->password,
                'creator' => session('user_id'),
                'ktp' => $request->ktp,
                'posisi' => $request->posisi,
            ));
            // success
            try{
                $perusahaan->save();
                return redirect()->route('member.show',['id'=>$request->ktp])->with('status', 'Data berhasil dibuat');
            }catch(\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function edit(Request $request){
        $ktp = $request->get('ktp');
        $jenis = "edit";
        $perusahaan = Perusahaan::all();
        $perid = PerusahaanMember::where('id',$request->get('pid'))->first();

        if ($request->ajax()) {
            return response()->json(view('member.perusahaanmember.form',compact('ktp','jenis','perusahaan','perid'))->render());
        }
    }   

    public function update(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'perusahaan' => 'required|string',
            'nomorid' => 'string',
            'password' => 'string',
            'posisi' => 'string'
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $perusahaan = PerusahaanMember::where('id',$request->pid)->first();
            
            $perusahaan->perusahaan_id = $request->perusahaan;
            $perusahaan->noid = $request->nomorid;
            $perusahaan->passid = $request->password;
            $perusahaan->creator = session('user_id');
            $perusahaan->ktp = $request->ktp;
            $perusahaan->posisi = $request->posisi;
            try{
                $perusahaan->update();
                return redirect()->route('member.show',['id'=>$request->ktp])->with('status', 'Data berhasil diubah');
            }catch(\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function destroy(Request $request){
        $perusahaan = PerusahaanMember::where('id',$request->pid)->first();
        $perusahaan->delete();
        return "true";
    }
}
