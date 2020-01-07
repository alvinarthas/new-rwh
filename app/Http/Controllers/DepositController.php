<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;

use App\Deposit;
use App\Perusahaan;
use App\MenuMapping;
use App\Coa;
use App\Jurnal;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Perusahaan::all();
        $data = collect();
        $page = MenuMapping::getMap(session('user_id'),"PUDP");
        foreach($suppliers as $supplier){
            $deposit = collect();
            $saldo = Deposit::getSaldo($supplier->id);

            $deposit->put('name',$supplier->nama);
            $deposit->put('id',$supplier->id);
            $deposit->put('saldo',$saldo);
            $data->push($deposit);
        }

        return view('purchase.deposit.index',compact('data','page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Perusahaan::all();
        $coas = Coa::where('AccNo','LIKE','1.1.1.2%')->where('StatusAccount','Detail')->orderBy('AccName','asc')->get();

        return view('purchase.deposit.form',compact('suppliers','coas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier' => 'required',
            'amount' => 'required|integer',
            'tanggal' => 'required|date',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $id_jurnal = Jurnal::getJurnalID('DS');

            $data = new Deposit(array(
                'supplier_id' => $request->supplier,
                'status' => 1,
                'amount' => $request->amount,
                'keterangan' => $request->keterangan,
                'creator' => session('user_id'),
                'date' => $request->tanggal,
                'jurnal_id' => $id_jurnal,
                'AccNo' => $request->method,
            ));

            try{
                // Jurnal Debet Deposit Pembelian
                Jurnal::addJurnal($id_jurnal,$request->amount,$request->tanggal,$desc,'1.1.3.3','Debet');

                // Jurnal Kredit Cash/Bank
                Jurnal::addJurnal($id_jurnal,$request->amount,$request->tanggal,$desc,$request->method,'Credit');

                $data->save();

                $desc = "Top Up Deposit Pembelian, id=".$data->id;

               

                return redirect()->route('deposit.index')->with('status', 'Data berhasil ditambah');
            }catch(\Exception $e) {
                return redirect()->back()->withErrors($e->errorInfo);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $saldo = Deposit::getSaldo($request->id);
        $name = $request->name;
        $details = Deposit::where('supplier_id',$request->id)->orderBy('date','desc')->get();
        $page = MenuMapping::getMap(session('user_id'),"PUDP");
        return response()->json(view('purchase.deposit.modal',compact('saldo','name','details','page'))->render());
        
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
        $deposit = Deposit::where('id',$id)->first();
        try{
            $deposit->delete();
            return "true";
        // fail
        }catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
