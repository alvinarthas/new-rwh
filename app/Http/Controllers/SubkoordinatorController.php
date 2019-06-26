<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Subkoordinator;

class SubkoordinatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subkoordinator = Subkoordinator::all();

        return view('subkoordinator.index', compact('subkoordinator'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenis = "create";
        return view('subkoordinator.form', compact('jenis'));
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
            $subkoordinator = new Subkoordinator(array(
                // Informasi Pribadi
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'telp' => $request->telp,
                'ktp' => $request->ktp,
                'memberid' => $request->memberid,
                'creator' => session('user_id'),
                'created' => now(),
            ));
            // success
            if($subkoordinator->save()){
                return redirect()->route('subkoordinator.index')->with('status', 'Data berhasil dibuat');
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
        $subkoordinator = Subkoordinator::find($id);
        $jenis = "edit";

        return view('subkoordinator.form', compact('jenis','subkoordinator'));
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
            $subkoordinator = Subkoordinator::find($id);
            $subkoordinator->nama = $request->nama;
            $subkoordinator->alamat = $request->alamat;
            $subkoordinator->telp = $request->telp;
            $subkoordinator->ktp = $request->ktp;
            $subkoordinator->memberid = $request->memberid;
            // success
            if($subkoordinator->update()){
                return redirect()->route('subkoordinator.index')->with('status', 'Data berhasil diupdate');
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
        $subkoordinator = Subkoordinator::find($id);
        if($subkoordinator->delete()){
            return redirect()->route('subkoordinator.index')->with('status', 'Data berhasil dihapus');
        // fail
        }else{
            return redirect()->back()->withErrors($e);
        }
    }
}
