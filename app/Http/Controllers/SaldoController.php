<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Saldo;
use App\Customer;
use App\Coa;
use App\Jurnal;
use App\MenuMapping;

class SaldoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = MenuMapping::getMap(session('user_id'),"PSDC");
        $jenis = "topup";
        $saldo = Saldo::join('tblcustomer', 'tblsaldo.customer_id', 'tblcustomer.id')->select('tblcustomer.apname', 'accNo', 'amount', 'keterangan', 'tblsaldo.creator AS creator', 'tanggal','tblsaldo.id AS sid')->orderBy('tblsaldo.tanggal', 'desc')->get();
        return view('customer.index', compact('saldo', 'jenis', 'page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenis = "topup";
        $customers = Customer::select('apname','cicn', 'id')->orderBy('apname', 'asc')->get();
        return view('customer.form', compact('jenis', 'customers'));
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
            'customer_id' => 'required',
            'nominal' => 'required|integer',
            'tanggal' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $id_jurnal = Jurnal::getJurnalID('SD');
                $saldo = new Saldo;
                $saldo->customer_id = $request->customer_id;
                $saldo->amount = $request->nominal;
                $saldo->accNo = $request->search;
                $saldo->status = 1;
                $saldo->tanggal = $request->tanggal;
                $namacust = Customer::where('id', $request->customer_id)->select('apname')->first();
                $namarek = Coa::where('AccNo', $request->search)->select('AccName')->first();
                if($request->buktitf <> NULL|| $request->buktitf <> ''){
                    $buktitf = $namacust['apname'].'.'.$request->tanggal.'.'.$namarek['AccName'].'.'.$request->buktitf->getClientOriginalExtension();
                    $request->buktitf->move(public_path('assets/images/saldo/topup/'),$buktitf);
                    $saldo->buktitf = $buktitf;
                }

                $saldo->keterangan = $request->keterangan;
                $saldo->id_jurnal = $id_jurnal;
                $saldo->creator = session('user_id');

                // Pembuatan Jurnal
                $ket = 'Deposit dari '.$namacust['apname'].'('.$request->tanggal.')';

                // debet Cash/Bank
                $debet = new Jurnal(array(
                    'id_jurnal'     => $id_jurnal,
                    'AccNo'         => $request->search,
                    'AccPos'        => "Debet",
                    'Amount'        => $request->nominal,
                    'company_id'    => 1,
                    'date'          => $request->tanggal,
                    'description'   => $ket,
                    'creator'       => session('user_id')
                ));
                // credit Deposit dari Customer
                $credit = new Jurnal(array(
                    'id_jurnal'     => $id_jurnal,
                    'AccNo'         => "2.1.2",
                    'AccPos'        => "Credit",
                    'Amount'        => $request->nominal,
                    'company_id'    => 1,
                    'date'          => $request->tanggal,
                    'description'   => $ket,
                    'creator'       => session('user_id')
                ));

                $saldo->save();
                $debet->save();
                $credit->save();
                return redirect()->route('saldo.index')->with('status','Data berhasil disimpan');
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
        $jenis = "edittopup";
        $saldo = Saldo::where('id', $id)->select('customer_id', 'tanggal', 'accNo', 'amount', 'buktitf', 'keterangan', 'id')->first();
        $customers = Customer::select('apname','cicn', 'id')->orderBy('apname', 'asc')->get();
        return view('customer.form', compact('jenis', 'customers', 'saldo'));
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
            'customer_id' => 'required',
            'nominal' => 'required|integer',
            'tanggal' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                // Pembuatan Jurnal
                $id_jurnal = Jurnal::getJurnalID('SD');
                $saldo = Saldo::where('id', $id)->first();
                $jurnal = Jurnal::where('id_jurnal', $saldo['id_jurnal'])->first();

                $saldo->customer_id = $request->customer_id;
                $saldo->amount = $request->nominal;
                $saldo->accNo = $request->search;
                $saldo->status = 1;
                $saldo->tanggal = $request->tanggal;
                $namacust = Customer::where('id', $request->customer_id)->select('apname')->first();
                $namarek = Coa::where('AccNo', $request->search)->select('AccName')->first();

                if($request->buktitf <> NULL|| $request->buktitf <> ''){
                    if ((file_exists(public_path('assets/images/saldo/topup/').$saldo->buktitf)) AND ($saldo->buktitf <> NULL)) {
                        unlink(public_path('assets/images/saldo/topup/').$saldo->buktitf);
                    }

                    $buktitf = $namacust['apname'].'.'.$request->tanggal.'.'.$namarek['AccName'].'.'.$request->buktitf->getClientOriginalExtension();
                    $request->buktitf->move(public_path('assets/images/saldo/topup/'),$buktitf);
                }else{
                    $buktitf = "";
                }

                $saldo->buktitf = $buktitf;
                $saldo->keterangan = $request->keterangan;
                $saldo->id_jurnal = $id_jurnal;
                $saldo->creator = session('user_id');

                $ket = 'Deposit dari '.$namacust['apname'].'('.$request->tanggal.')';

                // debet Cash/Bank
                $debet = new Jurnal(array(
                    'id_jurnal'     => $id_jurnal,
                    'AccNo'         => $request->search,
                    'AccPos'        => "Debet",
                    'Amount'        => $request->nominal,
                    'company_id'    => 1,
                    'date'          => $request->tanggal,
                    'description'   => $ket,
                    'creator'       => session('user_id')
                ));
                // credit Deposit dari Customer
                $credit = new Jurnal(array(
                    'id_jurnal'     => $id_jurnal,
                    'AccNo'         => "2.1.2",
                    'AccPos'        => "Credit",
                    'Amount'        => $request->nominal,
                    'company_id'    => 1,
                    'date'          => $request->tanggal,
                    'description'   => $ket,
                    'creator'       => session('user_id')
                ));

                $saldo->save();
                $jurnal->delete();
                $debet->save();
                $credit->save();

                return redirect()->route('saldo.index')->with('status','Data berhasil diupdate');
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
        // echo "<pre>";
        // print_r("cek");
        // die();
        try{
            $saldo = Saldo::where('id', $id)->first();
            if ((file_exists(public_path('assets/images/saldo/topup/').$saldo->buktitf)) AND ($saldo->buktitf <> NULL)) {
                unlink(public_path('assets/images/saldo/topup/').$saldo->buktitf);
            }
            Jurnal::where('id_jurnal', $saldo['id_jurnal'])->delete();
            $saldo->delete();
            return redirect()->back()->with('status','Data berhasil dihapus');
        }catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function ajxCoaOrder(Request $request)
    {
        $keyword = strip_tags(trim($request->keyword));
        $key = '%'.$keyword.'%';
        $datas = Coa::where('StatusAccount', "Detail")->where('tblcoa.AccName','LIKE',$key)->orderBy('tblcoa.AccName')->limit(10)->get();
        $data = array();
        $array = json_decode( json_encode($datas), true);
        foreach ($array as $a) {
            $arrayName = array('id' =>$a['AccNo'],'AccName' => $a['AccName']);
            array_push($data,$arrayName);
        }
        echo json_encode($data, JSON_FORCE_OBJECT);
    }
}
