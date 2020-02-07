<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;
use Illuminate\Http\Request;

use App\Jurnal;
use App\Coa;
use App\MenuMapping;

class JurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jurnals = Jurnal::viewJurnal($request->start_date,$request->end_date,$request->coa,$request->position,$request->param);
            $param = $request->param;
            $page = MenuMapping::getMap(session('user_id'),"FIJU");
            return response()->json(view('jurnal.view',compact('jurnals','page','param'))->render());
        }else{
            $coas = Coa::where('StatusAccount','Detail')->orderBy('AccNo','asc')->get();
            $page = MenuMapping::getMap(session('user_id'),"FIJU");
            return view('jurnal.index',compact('coas','page'));
        }
    }

    public function addJurnal(Request $request){
        $coa = $request->coa;
        $position = $request->position;
        $amount = $request->amount;
        $count = $request->count+1;
        $notes = $request->notes;

        $accname = Coa::where('AccNo',$coa)->first()->AccName;
        $ttl_debet = 0;
        $ttl_credit = 0;

        if($position == "Debet"){
            $ttl_debet+=$amount;
        }else {
            $ttl_credit+=$amount;
        }

        $append = '<tr style="width:100%" id="trow'.$count.'">
        <td>'.$count.'</td>
        <td><input type="hidden" name="accno[]" id="accno'.$count.'" value="'.$coa.'">'.$coa.'</td>
        <td><input type="hidden" name="accname[]" id="accname'.$count.'" value="'.$accname.'">'.$accname.'</td>
        <td><input type="hidden" name="position[]" value="'.$position.'" id="position'.$count.'">'.$position.'</td>
        <td><input type="hidden" name="amount[]" value="'.$amount.'" id="amount'.$count.'">Rp. '.number_format($amount).'</td>
        <td><input type="hidden" name="notes[]" value="'.$notes.'" id="notes'.$count.'">'.$notes.'</td>
        <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem('.$count.')" >Delete</a></td>
        </tr>';

        $data = array(
            'append' => $append,
            'position' => $position,
            'count' => $count,
            'ttl_debet' => $ttl_debet,
            'ttl_credit' => $ttl_credit,
        );

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $coas = Coa::where('StatusAccount','Detail')->orderBy('AccNo','asc')->get();
        $jenis = "create";
        $page = MenuMapping::getMap(session('user_id'),"FIJU");
        return view('jurnal.form',compact('coas','jenis','page'));
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
            'ttl_debet' => 'required',
            'trx_date' => 'required|date',
            'count' => 'required|integer',
            'ttl_credit' => 'required',
            'accno' => 'required|array',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $id_jurnal = Jurnal::getJurnalID('JN');
            try{
                for ($i=0; $i < $request->count ; $i++) {
                    $jurnal = new Jurnal(array(
                        'id_jurnal' => $id_jurnal,
                        'AccNo' => $request->accno[$i],
                        'AccPos' => $request->position[$i],
                        'Amount' => $request->amount[$i],
                        'company_id' => 1,
                        'date' => $request->trx_date,
                        'notes_item' => $request->notes[$i],
                        'description' => $request->deskripsi,
                        'creator' => session('user_id'),
                    ));
                    $jurnal->save();
                }
                return redirect()->route('jurnal.index')->with('status', 'Data berhasil dibuat');
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
    public function show(Request $request,$id)
    {
        if ($request->ajax()) {
            $jurnals = Jurnal::where('id_jurnal',$request->id)->get();
            $ttl_debet = Jurnal::where('id_jurnal',$request->id)->where('AccPos','Debet')->sum('Amount');
            $ttl_credit = Jurnal::where('id_jurnal',$request->id)->where('AccPos','Credit')->sum('Amount');
            return response()->json(view('jurnal.modal',compact('jurnals','ttl_debet','ttl_credit'))->render());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $jurnals = Jurnal::where('id_jurnal',$id)->get();
        $count_edit = $jurnals->count();
        $ttl_debet = Jurnal::where('id_jurnal',$id)->where('AccPos','Debet')->sum('Amount');
        $ttl_credit = Jurnal::where('id_jurnal',$id)->where('AccPos','Credit')->sum('Amount');
        $coas = Coa::where('StatusAccount','Detail')->orderBy('AccNo','asc')->get();
        $jenis = "edit";
        return view('jurnal.form',compact('coas','jurnals','jenis','id','ttl_debet','ttl_credit','count_edit'));
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
            'ttl_debet' => 'required',
            'trx_date' => 'required|date',
            'count' => 'required|integer',
            'ttl_credit' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                for ($i=0; $i < $request->count ; $i++) {
                    if(isset($request->accno[$i])){
                        $jurnal = new Jurnal(array(
                            'id_jurnal' => $id,
                            'AccNo' => $request->accno[$i],
                            'AccPos' => $request->position[$i],
                            'Amount' => $request->amount[$i],
                            'company_id' => 1,
                            'date' => $request->trx_date,
                            'notes_item' => $request->notes[$i],
                            'description' => $request->deskripsi,
                            'creator' => session('user_id'),
                        ));
                        $jurnal->save();
                    }
                }

                $raw_update = Jurnal::where('id_jurnal',$id)->update(['date' => $request->trx_date]);
                return redirect()->back()->with('status', 'Data berhasil diubah');
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
            $jurnals = Jurnal::where('id_jurnal',$id)->delete();
            return "true";
        // fail
        }catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function detailJuralDestroy(Request $request){
        $jurnal = Jurnal::where('id',$request->id)->first();
        try {
            $jurnal->delete();
            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }
}
