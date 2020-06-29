<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\MenuMapping;
use App\Gudang;
use App\Log;

class GudangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gudang = Gudang::all();
        $page = MenuMapping::getMap(session('user_id'),"MDGD");

        return view('gudang.index', compact('gudang', 'page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('gudang.form');
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
            'alamat' => 'required|string',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $data = new Gudang(array(
                    'nama' => $request->nama,
                    'alamat' => $request->alamat,
                    'creator' => session('user_id'),
                ));
                $data->save();
                Log::setLog('MDGDC','Create Gudang: '.$request->nama);
                return redirect()->route('gudang.index')->with('status', 'Data berhasil dibuat');
            }catch (\Exception $e) {
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
        $gudang = Gudang::where('id', $id)->first();
        return view('gudang.form', compact('gudang'));
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
        // Validate
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string',
            'alamat' => 'required|string',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $data = Gudang::where('id', $id)->first();
                $data->nama = $request->nama;
                $data->alamat = $request->alamat;
                $data->creator = session('user_id');
                // success
                $data->save();
                Log::setLog('MDGDU','Update Data Gudang : '.$request->nama);
                return redirect()->route('gudang.index')->with('status', 'Data berhasil dibuat');
            }catch (\Exception $e) {
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
        try{
            $data = Gudang::where('id', $id)->first();
            $nama = $data->nama;
            $data->delete();

            Log::setLog('MDGDD','Delete Gudang: '.$nama);
            return "true";
        }catch (\Exception $e) {
            return response()->json($e);
        }
    }
}
