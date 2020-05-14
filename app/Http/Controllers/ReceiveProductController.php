<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;

use App\Perusahaan;
use App\ReceiveDet;
use App\Purchase;
use App\PurchaseDetail;
use App\Jurnal;
use App\MenuMapping;
use App\Log;

class ReceiveProductController extends Controller
{
    public function index(){
        return view('purchase.receive.index');
    }

    public function ajx(Request $request){
        if($request->jenis == "all"){
            $lists = json_decode (json_encode (ReceiveDet::listReceiveAll()), FALSE);
        }else{
            // $bulan_start = date('m',strtotime($request->start));
            // $bulan_end = date('m',strtotime($request->end));

            // $tahun_start = date('Y',strtotime($request->start));
            // $tahun_end = date('Y',strtotime($request->end));

            $lists = json_decode (json_encode (ReceiveDet::listReceive($request->start,$request->end)), FALSE);
        }


        if ($request->ajax()) {
            $purchases = Purchase::checkReceive($request->start, $request->end);
            $page = MenuMapping::getMap(session('user_id'),"PURP");
            return response()->json(view('purchase.receive.indexreceive',compact('purchases', 'lists','page'))->render());
        }
    }

    public function detail(Request $request){
        $trx = Purchase::where('id',$request->trx_id)->first();
        $details = json_decode (json_encode (ReceiveDet::detailPurchase($request->trx_id)), FALSE);
        $producttrx = PurchaseDetail::where('trx_id',$request->trx_id)->select('prod_id')->get();
        $page = MenuMapping::getMap(session('user_id'),"PURP");
        $receives = ReceiveDet::where('trx_id',$request->trx_id)->get();

        return view('purchase.receive.form',compact('trx','details','producttrx','receives','page'));
    }

    public function store(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'trx_id' => 'required',
            'product' => 'required',
            'qty' => 'required|integer',
            'receive_date' => 'required|date',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $id_jurnal = Jurnal::getJurnalID('RP');

            $receive = new ReceiveDet(array(
                'trx_id' => $request->trx_id,
                'prod_id' => $request->product,
                'qty' => $request->qty,
                'expired_date' => $request->expired_date,
                'creator' => session('user_id'),
                'receive_date' => $request->receive_date,
                'id_jurnal' => $id_jurnal,
            ));

            $desc = "Receive Barang Product_id: ".$request->product." PO.".$request->trx_id;
            $pricedet = PurchaseDetail::where('trx_id',$request->trx_id)->where('prod_id',$request->product)->first()->price;
            $price = $pricedet * $request->qty;

            try{
                // JURNAL
                //insert debet Persediaan Barang di Gudang
                Jurnal::addJurnal($id_jurnal,$price,$request->receive_date,$desc,'1.1.4.1.2','Debet');
                //insert credit Persediaan Barang Indent
                Jurnal::addJurnal($id_jurnal,$price,$request->receive_date,$desc,'1.1.4.1.1','Credit');

                $receive->save();
                Log::setLog('PURPC','Create Receive Product PO.'.$request->trx_id.' Jurnal ID: '.$id_jurnal);
                return redirect()->back()->with('status', 'Data berhasil dibuat');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function delete(Request $request){
        $receive = ReceiveDet::where('id',$request->id)->first();
        $id_jurnal = $receive->id_jurnal;
        $trx_id = $receive->trx_id;
        try {
            $jurnal = Jurnal::where('id_jurnal',$receive->id_jurnal)->delete();
            Log::setLog('PURPD','Delete Receive Product ID:'.$request->id.' PO.'.$trx_id.' Jurnal ID: '.$id_jurnal);
            return redirect()->back()->with('status', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e);
        }
    }
}
