<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Ecommerce;
use App\MenuMapping;
use App\Log;

class EcommerceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ecommerce = Ecommerce::all();
        $page = MenuMapping::getMap(session('user_id'),"MDEC");

        return view('ecommerce.index', compact('ecommerce','page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenis = "create";
        return view('ecommerce.form', compact('jenis'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'kode_trx' => 'required|string',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $logo = "noimage.jpg";
                if($request->logo <> NULL|| $request->logo <> ''){
                    $logo = $request->nama.'.'.$request->logo->getClientOriginalExtension();
                    $request->logo->move(public_path('assets/images/ecommerce/'),$logo);
                }

                $data = new Ecommerce(array(
                    // Informasi Pribadi
                    'nama' => $request->nama,
                    'kode_trx' => $request->kode_trx,
                    'logo' => $logo,
                    'creator' => session('user_id'),
                ));
                // success
                $data->save();
                Log::setLog('MDECC','Create Ecommerce: '.$request->nama);
                return redirect()->route('ecommerce.index')->with('status', 'Data berhasil dibuat');
            }catch (\Exception $e) {
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
        $ecommerce = Ecommerce::find($id);
        $jenis = "edit";

        return view('ecommerce.form', compact('jenis','ecommerce'));
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
            'nama' => 'required|string',
            'kode_trx' => 'required|string',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $data = Ecommerce::where('id', $id)->first();

                if($request->logo <> NULL|| $request->logo <> ''){

                    if (file_exists(public_path('assets/images/ecommerce/').$data->logo) && $data->logo != "noimage.jpg") {
                        unlink(public_path('assets/images/ecommerce/').$data->logo);
                    }

                    $logo = $request->nama.'.'.$request->logo->getClientOriginalExtension();
                    $request->logo->move(public_path('assets/images/ecommerce/'),$logo);
                }else{
                    $logo = $data->logo;
                }

                $data->nama = $request->nama;
                $data->kode_trx = $request->kode_trx;
                $data->logo = $logo;
                $data->creator = session('user_id');

                $data->save();

                Log::setLog('MDECU','Update Ecommerce: '.$request->nama);
                return redirect()->route('ecommerce.index')->with('status', 'Data berhasil diedit');
            }catch (\Exception $e) {
                return redirect()->back()->withErrors($e->errorInfo);
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
        try{
            $data = Ecommerce::where('id', $id)->first();
            $data->delete();

            Log::setLog('MDECD','Delete Ecommerce: '.$request->nama);
            return "true";
        }catch (\Exception $e) {
            return response()->json($e);
        }
    }
}
