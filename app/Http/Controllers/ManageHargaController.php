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
            'i' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            // CODE LAMA
            // $n = $request->i;
            // for($i=1; $i<=$n; $i++){
            //     $mh = new ManageHarga;
            //     $price_dis = "price_dis".$i;
            //     $mh->harga_distributor = $request->$price_dis;
            //     $price_mod = "price_mod".$i;
            //     $mh->harga_modal = $request->$price_mod;
            //     $pid = "pid".$i;
            //     $mh->prod_id = $request->$pid;
            //     $mh->month = $request->month;
            //     $mh->year = $request->year;
            //     $mh->creator = session('user_id');
            //     $mh->save();
            // }

            $count = count($request->pid);
            for($i=0; $i<$count; $i++){
                $price_dis = $request->price_dis[$i];
                $price_mod = $request->price_mod[$i];
                if(($price_dis!=0 AND $price_mod!=0) OR ($price_dis!="" AND $price_mod!="")){
                    $data = new ManageHarga(array(
                        'prod_id' => $request->pid[$i],
                        'month' => $request->month,
                        'year' => $request->year,
                        'creator' => session('user_id'),
                        'harga_distributor' => $price_dis,
                        'harga_modal' => $price_mod,
                    ));
                    $data->save();
                }
            }
            return redirect()->back();
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
