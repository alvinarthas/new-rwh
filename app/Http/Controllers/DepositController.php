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
use App\Log;

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

        $data = $data->sortByDesc('saldo');

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
        $coas = Coa::where(function ($query) {
            $query->where('AccNo','LIKE','1.1.1.2.%')->orWhere('AccNo', 'LIKE', '2.5%')->orWhere('AccNo', 'LIKE', '1.10.1')->orWhere('AccNo', 'LIKE', '1.1.1.1.%');
        })->where('StatusAccount','Detail')->orderBy('AccName','asc')->get();

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
                $desc = "Top Up Deposit Pembelian, id=".$data->id;
                // Jurnal Debet Deposit Pembelian
                Jurnal::addJurnal($id_jurnal,$request->amount,$request->tanggal,$desc,'1.1.3.3','Debet');

                // Jurnal Kredit Cash/Bank
                Jurnal::addJurnal($id_jurnal,$request->amount,$request->tanggal,$desc,$request->method,'Credit');

                $data->save();


                Log::setLog('PUDPC','Create Deposit Pembelian Supplier: '.$request->supplier.' Jurnal ID: '.$id_jurnal);

                return redirect()->route('deposit.index')->with('status', 'Data berhasil ditambah');
            }catch(\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
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
        $deposit = Deposit::where('id', $id)->first();
        $suppliers = Perusahaan::all();
        $coas = Coa::where(function ($query) {
            $query->where('AccNo','LIKE','1.1.1.2.%')->orWhere('AccNo', 'LIKE', '2.5%')->orWhere('AccNo', 'LIKE', '1.10.1')->orWhere('AccNo', 'LIKE', '1.1.1.1.%');
        })->where('StatusAccount','Detail')->orderBy('AccName','asc')->get();

        return view('purchase.deposit.form',compact('suppliers','coas', 'deposit'));
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
            $data = Deposit::where('id', $id)->first();
            $AccNo_lama = $data->AccNo;
            $jurnal_id = $data->jurnal_id;

            $data->supplier_id = $request->supplier;
            $data->amount = $request->amount;
            $data->keterangan = $request->keterangan;
            $data->creator = session('user_id');
            $data->date = $request->tanggal;
            $data->AccNo = $request->method;

            try{
                $desc = "Top Up Deposit Pembelian, id=".$data->id." (Edit)";
                // Jurnal Debet Deposit Pembelian
                $debet = Jurnal::where('id_jurnal', $jurnal_id)->where('AccPos', 'Debet')->where('AccNo', '1.1.3.3')->first();
                $debet->Amount = $request->amount;
                $debet->date = $request->tanggal;
                $debet->description = $desc;
                $debet->creator = session('user_id');

                // Jurnal Kredit Cash/Bank
                $credit = Jurnal::where('id_jurnal', $jurnal_id)->where('AccPos', 'Credit')->where('AccNo', $AccNo_lama)->first();
                $credit->Amount = $request->amount;
                $credit->date = $request->tanggal;
                $credit->description = $desc;
                $credit->AccNo = $request->method;
                $credit->creator = session('user_id');

                $data->update();
                $debet->update();
                $credit->update();

                Log::setLog('PUDPC','Update Deposit Pembelian Supplier: '.$request->supplier.' Jurnal ID: '.$jurnal_id);

                return redirect()->route('deposit.index')->with('status', 'Data berhasil ditambah');
            }catch(\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
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
        $deposit = Deposit::where('id',$id)->first();
        $id_jurnal = $deposit->jurnal_id;
        $supplier = $deposit->supplier_id;
        try{
            $jurnal = Jurnal::where('id_jurnal',$deposit->jurnal_id)->delete();
            Log::setLog('PUDPD','Delete Deposit Pembelian Supplier: '.$supplier.' Jurnal ID: '.$id_jurnal);
            return "true";
        // fail
        }catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
