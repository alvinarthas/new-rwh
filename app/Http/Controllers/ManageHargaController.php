<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\ManageHarga;

class ManageHargaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // echo "<pre>";
        // print_r($request->all());die();
        $validator = Validator::make($request->all(), [
            'month' => 'required',
            'year' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            // echo "<pre>";
            // print_r($request->all());
            // die();
            try{
                $month = $request->month;
                $year = $request->year;
                $count = count($request->pid);
                for($i=0; $i<$count; $i++){
                    $prod_id = $request->pid[$i];
                    $price_dis = $request->price_dis[$i];
                    $price_mod = $request->price_mod[$i];
                    if($price_dis!=0 AND $price_mod!=0){
                        if(empty(ManageHarga::where('prod_id', $prod_id)->where('month', $month)->where('year', $year)->first()) == 1){
                            $data = new ManageHarga(array(
                                'prod_id' => $prod_id,
                                'month' => $month,
                                'year' => $year,
                                'creator' => session('user_id'),
                                'harga_distributor' => $price_dis,
                                'harga_modal' => $price_mod,
                            ));
                            $data->save();
                        }else{
                            $data = ManageHarga::where('prod_id', $prod_id)->where('month', $month)->where('year', $year)->first();
                            $data->harga_distributor = $price_dis;
                            $data->harga_modal = $price_mod;
                            $data->creator = session('user_id');
                            $data->update();
                        }
                    }
                }
                return redirect()->back()->with('status', 'Berhasil diupdate');
            }catch(\Exception $e){
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
        //
    }
}
