<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;

use App\Bank;
use App\BankMember;

class BankMemberController extends Controller
{
    public function create(Request $request){
        $ktp = $request->get('ktp');
        $jenis = "create";
        $banks = Bank::all();
        
        if ($request->ajax()) {
            return response()->json(view('member.bankmember.form',compact('ktp','jenis','banks'))->render());
        }
    }

    public function store(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'bank' => 'string',
            'cabang' => 'string',
            'rekening' => 'string',
            'atm' => 'required',
            'tabungan' => 'required',
            'status' => 'string',
            'p_status' => 'string',
            'scanatm' => 'image',
            'scantabungan' => 'image',

        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            // Upload KTP
            $scanatm = "noimage.jpg";

            if($request->scanatm <> NULL|| $request->scanatm <> ''){
                $scanatm = $request->ktp.'.'.$request->scanatm->getClientOriginalExtension();
                $request->scanatm->move(public_path('assets/images/member/atm/'),$scanatm);
            }

            // Upload KTP
            $scantabungan = "noimage.jpg";

            if($request->scantabungan <> NULL|| $request->scantabungan <> ''){
                $scantabungan = $request->ktp.'.'.$request->scantabungan->getClientOriginalExtension();
                $request->scantabungan->move(public_path('assets/images/member/tabungan/'),$scantabungan);
            }
            $bank = new BankMember(array(
                // Informasi Pribadi
                'bank_id' => $request->bank,
                'cabbank' => $request->cabang,
                'norek' => $request->rekening,
                'noatm' => $request->atm,
                'nobuku' => $request->tabungan,
                'creator' => session('user_id'),
                'ktp' => $request->ktp,
                'status' => $request->status_rek,
                'p_status' => $request->primary,
                'scanatm' => $scanatm,
                'scantabungan' => $scantabungan,
            ));
            
            // success
            try{
                $bank->save();
                return redirect()->route('member.show',['id'=>$request->ktp])->with('status', 'Data berhasil dibuat');
            }catch(\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function edit(Request $request){
        $ktp = $request->get('ktp');
        $jenis = "edit";
        $banks = Bank::all();
        $banm = BankMember::where('id',$request->get('bid'))->first();

        if ($request->ajax()) {
            return response()->json(view('member.bankmember.form',compact('ktp','jenis','banks','banm'))->render());
        }
    }   

    public function update(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'bank' => 'string',
            'cabang' => 'string',
            'rekening' => 'string',
            'status' => 'string',
            'p_status' => 'string',
            'scanatm' => 'image',
            'scantabungan' => 'image',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $bank = BankMember::where('id',$request->bid)->first();

            // Upload ATM
            if($request->scanatm <> NULL|| $request->scanatm <> ''){

                if (file_exists(public_path('assets/images/member/atm/').$bank->scanatm)) {
                    unlink(public_path('assets/images/member/atm/').$bank->scanatm);
                }

                $scanatm = $request->ktp.'.'.$request->scanatm->getClientOriginalExtension();
                $request->scanatm->move(public_path('assets/images/member/ktp/'),$scanatm);
            }else{
                $scanatm = $bank->scanatm;
            }

            // Upload Tabungan
            if($request->scantabungan <> NULL|| $request->scantabungan <> ''){

                if (file_exists(public_path('assets/images/member/tabungan/').$bank->scantabungan)) {
                    unlink(public_path('assets/images/member/tabungan/').$bank->scantabungan);
                }

                $scantabungan = $request->ktp.'.'.$request->scantabungan->getClientOriginalExtension();
                $request->scantabungan->move(public_path('assets/images/member/ktp/'),$scantabungan);
            }else{
                $scantabungan = $bank->scantabungan;
            }
            
            $bank->bank_id = $request->bank;
            $bank->cabbank = $request->cabang;
            $bank->norek = $request->rekening;
            $bank->noatm = $request->atm;
            $bank->nobuku = $request->tabungan;
            $bank->creator = session('user_id');
            $bank->ktp = $request->ktp;
            $bank->status = $request->status_rek;
            $bank->p_status = $request->primary;
            $bank->scanatm = $scanatm;
            $bank->scantabungan = $scantabungan;

            try{
                $bank->update();
                return redirect()->route('member.show',['id'=>$request->ktp])->with('status', 'Data berhasil diubah');
            }catch(\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function destroy(Request $request){
        $bank = BankMember::where('id',$request->bid)->first();
        $bank->delete();
        return "true";
    }
}
