<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Koordinator;
use App\MenuMapping;

class KoordinatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $koordinator = Koordinator::all();
        $page = MenuMapping::getMap(session('user_id'),"MBKM");
        return view('koordinator.index', compact('koordinator','page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenis = "create";
        return view('koordinator.form', compact('jenis'));
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
            'telp' => 'required',
            'ktp' => 'required',
            'memberid' => 'required|string',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $koordinator = new Koordinator(array(
                // Informasi Pribadi
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'telp' => $request->telp,
                'ktp' => $request->ktp,
                'memberid' => $request->memberid,
            ));
            // success
            if($koordinator->save()){
                return redirect()->route('koordinator.index')->with('status', 'Data berhasil dibuat');
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
        $koordinator = Koordinator::find($id);
        $jenis = "edit";

        return view('koordinator.form', compact('jenis','koordinator'));
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
            'telp' => 'required',
            'ktp' => 'required',
            'memberid' => 'required|string',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $koordinator = Koordinator::find($id);
            $koordinator->nama = $request->nama;
            $koordinator->alamat = $request->alamat;
            $koordinator->telp = $request->telp;
            $koordinator->ktp = $request->ktp;
            $koordinator->memberid = $request->memberid;
            // success
            if($koordinator->update()){
                return redirect()->route('koordinator.index')->with('status', 'Data berhasil diupdate');
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
        $koordinator = Koordinator::find($id);
        if($koordinator->delete()){
            return redirect()->route('koordinator.index')->with('status', 'Data berhasil dihapus');
        // fail
        }else{
            return redirect()->back()->withErrors($e);
        }
    }
}
