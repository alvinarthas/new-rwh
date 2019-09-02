<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Coa;
use App\CoaGrup;
use App\Company;

class CoaController extends Controller
{
    public function index()
    {
        $coa = Coa::all();
        return view('coa.index', compact('coa'));
    }

    public function create()
    {
        $coagrup = CoaGrup::all();
        $company = Company::all();
        $parents = Coa::where('StatusAccount','Grup')->get();
        $jenis = "create";
        return view('coa.form', compact('coagrup','company','jenis','parents'));
    }

    public function store(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'account_grup' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
            'saldo_normal' => 'required',
            'status_account' => 'required',
            'saldo_awal' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            if($request->status_account == "Detail"){
                $accparent = $request->account_parent;
            }else{
                $accparent = $request->account_number;
            }

            $coa = new Coa(array(
                // Informasi Pribadi
                'AccNo' => $request->account_number,
                'AccName' => $request->account_name,
                'SaldoNormal' => $request->saldo_normal,
                'StatusAccount' => $request->status_account,
                'SaldoAwal' => $request->saldo_awal,
                'company_id' => $request->company,
                'grup_id' => $request->grup,
                'AccParent' => $accparent,
            ));
            // success
            if($coa->save()){
                return redirect()->route('coa.index')->with('status', 'Data berhasil dibuat');
            // fail
            }else{
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function edit($id)
    {
        $coagrup = CoaGrup::all();
        $company = Company::first();
        $jenis = "edit";
        $parents = Coa::where('StatusAccount','Grup')->get();
        $coa = Coa::where('id',$id)->first();
        return view('coa.form', compact('coagrup','company','jenis', 'coa','parents'));
    }

    public function update(Request $request, $id)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'account_grup' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
            'saldo_normal' => 'required',
            'status_account' => 'required',
            'saldo_awal' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $coa = Coa::where('id',$id)->first();

            $coa->AccNo = $request->account_number;
            $coa->AccName = $request->account_name;
            $coa->SaldoNormal = $request->saldo_normal;
            $coa->StatusAccount = $request->status_account;
            $coa->SaldoAwal = $request->saldo_awal;
            $coa->company_id = $request->company;
            $coa->grup_id = $request->grup;
            $coa->AccParent = $accparent;

            // success
            if($coa->save()){
                return redirect()->route('coa.index')->with('status', 'Data berhasil diiubah');
            // fail
            }else{
                return redirect()->back()->withErrors($e->errorInfo);
            }
        }
    }

    public function destroy($id)
    {
        $coa = Coa::find($id);
        if($coa->delete()){
            return redirect()->route('coa.index')->with('status', 'Data berhasil dihapus');
        // fail
        }else{
            return redirect()->back()->withErrors($e);
        }
    }
}
