<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\PiutangKaryawan;
use App\Employee;
use App\MenuMapping;
use App\Coa;
use App\Jurnal;
use App\Log;

class PiutangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = MenuMapping::getMap(session('user_id'),"FIPK");

        $data = PiutangKaryawan::getData();

        return view('employee.piutangkaryawan.index',compact('data','page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::where('id', '!=', 1)->get();
        $coas = Coa::where(function ($query) {
            $query->where('AccNo','LIKE','1.1.1.2.%')->orWhere('AccNo', 'LIKE', '2.5%')->orWhere('AccNo', 'LIKE', '1.10.1')->orWhere('AccNo', 'LIKE', '1.1.1.1.%');
        })->where('StatusAccount','Detail')->orderBy('AccName','asc')->get();

        return view('employee.piutangkaryawan.form',compact('employees', 'coas'));
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
            'employee' => 'required',
            'amount' => 'required|integer',
            'tanggal' => 'required|date',
            'status' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $id_jurnal = Jurnal::getJurnalID('EP');

                $desc = "Piutang Karyawan ".$id_jurnal;

                if($request->status == 0){
                    // Jurnal Debet Piutang Karyawan
                    Jurnal::addJurnal($id_jurnal,$request->amount,$request->tanggal,$desc,'1.1.3.6','Debet');

                    // Jurnal Kredit Cash/Bank
                    Jurnal::addJurnal($id_jurnal,$request->amount,$request->tanggal,$desc,$request->method,'Credit');
                }elseif($request->status == 1){
                    // Jurnal Debet Cash/Bank
                    Jurnal::addJurnal($id_jurnal,$request->amount,$request->tanggal,$desc,$request->method,'Debet');

                    // Jurnal Kredit Piutang Karyawan
                    Jurnal::addJurnal($id_jurnal,$request->amount,$request->tanggal,$desc,'1.1.3.6','Credit');
                }

                $data = new PiutangKaryawan(array(
                    'employee_id' => $request->employee,
                    'status' => $request->status,
                    'amount' => $request->amount,
                    'description' => $request->keterangan,
                    'creator' => session('user_id'),
                    'date' => $request->tanggal,
                    'id_jurnal' => $id_jurnal,
                    'AccNo' => $request->method,
                ));

                $data->save();

                Log::setLog('FIPKC','Create Piutang Karyawan: '.$request->employee.' Jurnal ID: '.$id_jurnal);

                return redirect()->route('piutang.index')->with('status', 'Data berhasil ditambah');
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
    public function show(Request $request, $id)
    {
        $piutang = PiutangKaryawan::getPiutang($request->id);
        $name = $request->name;
        $details = PiutangKaryawan::where('employee_id',$request->id)->orderBy('date','desc')->get();
        $page = MenuMapping::getMap(session('user_id'),"FIPK");
        return response()->json(view('employee.piutangkaryawan.modal',compact('piutang','name','details','page'))->render());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $piutang = PiutangKaryawan::where('id', $id)->first();
        $employees = Employee::where('id', '!=', 1)->get();
        $coas = Coa::where(function ($query) {
            $query->where('AccNo','LIKE','1.1.1.2.%')->orWhere('AccNo', 'LIKE', '2.5%')->orWhere('AccNo', 'LIKE', '1.10.1')->orWhere('AccNo', 'LIKE', '1.1.1.1.%');
        })->where('StatusAccount','Detail')->orderBy('AccName','asc')->get();

        return view('employee.piutangkaryawan.form',compact('employees','coas', 'piutang'));
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
        $validator = Validator::make($request->all(), [
            'employee' => 'required',
            'amount' => 'required|integer',
            'tanggal' => 'required|date',
            'status' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $data = PiutangKaryawan::where('id', $id)->first();
            $AccNo_lama = $data->AccNo;
            $jurnal_id = $data->id_jurnal;

            $data->employee_id = $request->employee;
            $data->amount = $request->amount;
            $data->status = $request->status;
            $data->description = $request->keterangan;
            $data->creator = session('user_id');
            $data->date = $request->tanggal;
            $data->AccNo = $request->method;

            try{
                $desc = "Piutang Karyawan, id=".$data->id." (Edit)";

                if($request->status == 0){
                    $db = '1.1.3.6';
                    $cr = $request->method;
                }else{
                    $db = $request->method;
                    $cr = '1.1.3.6';
                }

                // Jurnal Debet Deposit Pembelian
                $debet = Jurnal::where('id_jurnal', $jurnal_id)->where('AccPos', 'Debet')->first();
                $debet->Amount = $request->amount;
                $debet->date = $request->tanggal;
                $debet->description = $desc;
                $debet->AccNo = $db;
                $debet->creator = session('user_id');

                // Jurnal Kredit Cash/Bank
                $credit = Jurnal::where('id_jurnal', $jurnal_id)->where('AccPos', 'Credit')->first();
                $credit->Amount = $request->amount;
                $credit->date = $request->tanggal;
                $credit->description = $desc;
                $credit->AccNo = $cr;
                $credit->creator = session('user_id');

                $data->update();
                $debet->update();
                $credit->update();

                Log::setLog('FIPKU','Update Piutang Karyawan: '.$request->employee.' Jurnal ID: '.$jurnal_id);

                return redirect()->route('piutang.index')->with('status', 'Data berhasil ditambah');
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
        $piutang = PiutangKaryawan::where('id',$id)->first();
        $id_jurnal = $piutang->id_jurnal;
        $employee = $piutang->employee_id;
        try{
            $jurnal = Jurnal::where('id_jurnal',$id_jurnal)->delete();
            Log::setLog('FIPKD','Delete Piutang Karyawan, Employee: '.$employee.' Jurnal ID: '.$id_jurnal);
            return "true";
        // fail
        }catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
