<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::all();
        return view('customer.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenis = "create";
        return view('customer.form', compact('jenis'));
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
            'customer_id' => 'required|string',
            'name' => 'required|string',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $customer = new Customer(array(
                    'cid' => $request->customer_id,
                    'apname' => $request->name,
                    'apphone' => $request->phone,
                    'apfax' => $request->fax,
                    'apemail' => $request->email,
                    'apadd' => $request->address,
                    'cicn' => $request->cname,
                    'ciadd' => $request->cadd,
                    'cicty' => $request->ccity,
                    'cizip' => $request->czipcode,
                    'cipro' => $request->cprovince,
                    'ciweb' => $request->cwebsite,
                    'ciemail' => $request->cemail,
                    'ciphone' => $request->cphone,
                    'cifax' => $request->cfax,
                    'creator' => session('user_id'),
                ));
                $customer->save();
                return redirect()->route('customer.index')->with('status','Data berhasil disimpan');
            }catch(\Exception $e){
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
        $customer = Customer::where('id', $id)->first();
        $jenis = "edit";
        return view('customer.form', compact('jenis', 'customer'));
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
            'customer_id' => 'required|string',
            'name' => 'required|string',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $customer = Customer::where('id', $id)->first();
                $customer->cid = $request->customer_id;
                $customer->apname = $request->name;
                $customer->apphone = $request->phone;
                $customer->apfax = $request->fax;
                $customer->apemail = $request->email;
                $customer->apadd = $request->address;
                $customer->cicn = $request->cname;
                $customer->ciadd = $request->cadd;
                $customer->cicty = $request->ccity;
                $customer->cizip = $request->czipcode;
                $customer->cipro = $request->cprovince;
                $customer->ciweb = $request->cwebsite;
                $customer->ciemail = $request->cemail;
                $customer->ciphone = $request->cphone;
                $customer->cifax = $request->cfax;
                $customer->creator = session('user_id');

                $customer->save();
                return redirect()->route('customer.index')->with('status','Perubahan data berhasil disimpan');
            }catch(\Exception $e){
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
    public function destroy(Request $request)
    {
        try{
            $customer = Customer::where('id', $request->id)->first();
            $customer->delete();
            return response()->json();
            // return redirect()->route('customer.index')->with('status','Data berhasil dihapus');
        }catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
