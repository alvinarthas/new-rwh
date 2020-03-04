<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Perusahaan;
use App\MenuMapping;
use App\Log;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perusahaan = Perusahaan::all();
        $page = MenuMapping::getMap(session('user_id'),"MDPR");

        return view('perusahaan.index', compact('perusahaan','page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenis = "create";
        return view('perusahaan.form', compact('jenis'));
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
            'telepon' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $perusahaan = new Perusahaan(array(
                // Informasi Pribadi
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'telp' => $request->telepon,
                'cp1' => $request->cp1,
                'cp2' => $request->cp2,
                'cp3' => $request->cp3,
                'creator' => session('user_id'),
            ));
            // success
            if($perusahaan->save()){
                // Log::setLog('PRPDC','Create Product ID: '.$request->prod_id);
                return redirect()->route('perusahaan.index')->with('status', 'Data berhasil dibuat');
            // fail
            }else{
                return redirect()->back()->withErrors($e);
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
        $perusahaan = Perusahaan::find($id);
        $jenis = "edit";

        return view('perusahaan.form', compact('jenis','perusahaan'));
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
            'alamat' => 'required|string',
            'telepon' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $perusahaan = Perusahaan::where('id', $id)->first();
            $perusahaan->nama = $request->nama;
            $perusahaan->alamat = $request->alamat;
            $perusahaan->telp = $request->telepon;
            $perusahaan->cp1 = $request->cp1;
            $perusahaan->cp2 = $request->cp2;
            $perusahaan->cp3 = $request->cp3;
            $perusahaan->creator = "creator";
            // success
            if($perusahaan->update()){
                return redirect()->route('perusahaan.index')->with('status', 'Data berhasil diupdate');
            // fail
            }else{
                return redirect()->back()->withErrors($e);
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
        $perusahaan = Perusahaan::where('id', $id)->first();
        if($perusahaan->delete()){
            return redirect()->route('perusahaan.index')->with('status', 'Data berhasil dihapus');
        // fail
        }else{
            return redirect()->back()->withErrors($e);
        }
    }

    public function getSupplier(Request $request){
        $supplier = Perusahaan::where('id', $request->id)->first();
        $data = array(
            'id' => $supplier->id,
            'alamat' => $supplier->alamat,
            'telp' => $supplier->telp,
        );

        return response()->json($data);
    }
}
