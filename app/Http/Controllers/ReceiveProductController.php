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
use App\Product;
use App\Gudang;

class ReceiveProductController extends Controller
{
    public function index(){
        return view('purchase.receive.index');
    }

    public function view(Request $request){
        if ($request->ajax()) {
            $jurnal_id = $request->ri_id;
            $receives = ReceiveDet::where('id_jurnal',$jurnal_id)->get();
            $receive = ReceiveDet::where('id_jurnal',$jurnal_id)->first();

            return response()->json(view('purchase.receive.modal',compact('receive', 'receives'))->render());
        }
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
        $producttrx = PurchaseDetail::where('trx_id',$request->trx_id)->select('id','prod_id', 'price')->get();
        $page = MenuMapping::getMap(session('user_id'),"PURP");
        $receives = ReceiveDet::where('trx_id',$request->trx_id)->groupBy('id_jurnal')->get();
        $gudangs = Gudang::all();

        return view('purchase.receive.form',compact('trx','details','producttrx','receives','page', 'gudangs'));
    }

    public function addBrgReceive(Request $request){
        $qty = $request->qty;
        $count = $request->count+1;
        $purdet_id = $request->select_product;
        $expired = $request->expired;
        $gudang_id = $request->gudang;

        $purdet = PurchaseDetail::where('id', $purdet_id)->select('prod_id')->first();

        $product = Product::where('prod_id',$purdet->prod_id)->first();

        $gudang = Gudang::where('id', $gudang_id)->first();

        $append = '<tr style="width:100%" id="trow'.$count.'">
        <td><input type="hidden" name="prod_id[]" id="prod_id'.$count.'" value="'.$product->prod_id.'">'.$product->prod_id.'</td>
        <td><input type="hidden" name="purdet_id[]" id="purdet_id'.$count.'" value="'.$purdet_id.'">'.$product->name.'</td>
        <td><input type="hidden" name="qty[]" value="'.$qty.'" id="qty'.$count.'">'.$qty.'</td>
        <td><input type="hidden" name="expired_date[]" value="'.$expired.'" id="expired_date'.$count.'">'.$expired.'</td>
        <td><input type="hidden" name="gudang[]" value="'.$gudang_id.'" id="gudang'.$count.'">'.$gudang->nama.'</td>
        <td><a href="javascript:;" type="button" class="btn btn-danger btn-trans waves-effect w-md waves-danger m-b-5" onclick="deleteItem('.$count.')" >Delete</a></td>
        </tr>';

        $data = array(
            'append' => $append,
            'count' => $count,
        );

        return response()->json($data);
    }

    public function store(Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'trx_id' => 'required',
            'prod_id' => 'required|array',
            'purdet_id' => 'required|array',
            'qty' => 'required|array',
            'receive_date' => 'required|date',
            'gudang' => 'required|array',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $id_jurnal = Jurnal::getJurnalID('RP');
                $price = 0;
                $count = count($request->prod_id);

                for($i=0; $i < $count; $i++){
                    $pricedet = PurchaseDetail::where('id',$request->purdet_id[$i])->first()->price;
                    $price += $pricedet * $request->qty[$i];
                }

                $desc = "Receive Barang PO.".$request->trx_id;

                // JURNAL
                //insert debet Persediaan Barang di Gudang
                Jurnal::addJurnal($id_jurnal,$price,$request->receive_date,$desc,'1.1.4.1.2','Debet');
                //insert credit Persediaan Barang Indent
                Jurnal::addJurnal($id_jurnal,$price,$request->receive_date,$desc,'1.1.4.1.1','Credit');

                for($i=0; $i < $count; $i++){
                    $receive = new ReceiveDet(array(
                        'trx_id' => $request->trx_id,
                        'prod_id' => $request->prod_id[$i],
                        'purchasedetail_id' => $request->purdet_id[$i],
                        'qty' => $request->qty[$i],
                        'expired_date' => $request->expired_date[$i],
                        'gudang_id' => $request->gudang[$i],
                        'creator' => session('user_id'),
                        'receive_date' => $request->receive_date,
                        'id_jurnal' => $id_jurnal,
                    ));

                    $receive->save();
                }

                Log::setLog('PURPC','Create Receive Product PO.'.$request->trx_id.' Jurnal ID: '.$id_jurnal);
                return redirect()->back()->with('status', 'Data berhasil dibuat');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function edit($id)
    {
        $receive = ReceiveDet::where('id_jurnal', $id)->get();
        $producttrx = PurchaseDetail::where('trx_id',$receive[0]->trx_id)->select('id', 'prod_id', 'price')->get();
        $gudangs = Gudang::all();

        return view('purchase.receive.form_update', compact('receive', 'producttrx', 'gudangs'));
    }

    public function update($id, Request $request){
        // Validate
        $validator = Validator::make($request->all(), [
            'receive_date' => 'required|date',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $receive = ReceiveDet::where('id_jurnal', $id)->get();

                // JURNAL
                //insert debet Persediaan Barang di Gudang
                $debet = Jurnal::where('id_jurnal', $id)->where('AccPos', 'Debet')->first();
                $debet->date = $request->receive_date;
                $debet->update();

                //insert credit Persediaan Barang Indent
                $credit = Jurnal::where('id_jurnal', $id)->where('AccPos', 'Credit')->first();
                $credit->date = $request->receive_date;
                $credit->update();

                foreach($receive as $rec){
                    $data = ReceiveDet::where('id', $rec->id)->first();
                    $data->receive_date = $request->receive_date;
                    $data->update();
                }

                Log::setLog('PURPU','Update Receive Product PO.'.$receive[0]->trx_id.' Jurnal ID: '.$id);
                return redirect()->route('receiveProdDet',['trx_id'=>$receive[0]['trx_id']])->with('status', 'Data berhasil diupdate');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function addProduct(Request $request){
        $validator = Validator::make($request->all(), [
            'trx_id' => 'required',
            'select_product' => 'required',
            'qty' => 'required',
            'receive_date' => 'required|date',
            'gudang' => 'required',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $product = PurchaseDetail::where('id', $request->select_product)->first();

                $receive = new ReceiveDet(array(
                    'trx_id' => $request->trx_id,
                    'prod_id' => $product->prod_id,
                    'purchasedetail_id' => $request->select_product,
                    'qty' => $request->qty,
                    'expired_date' => $request->expired_date,
                    'gudang_id' => $request->gudang,
                    'creator' => session('user_id'),
                    'receive_date' => $request->receive_date,
                    'id_jurnal' => $request->id_jurnal,
                ));

                $receive->save();
                $price = 0;

                $datas = ReceiveDet::where('id_jurnal', $request->id_jurnal)->get();

                foreach($datas as $data){
                    $pricedet = PurchaseDetail::where('id', $data->purchasedetail_id)->first()->price;
                    $price += $pricedet * $data->qty;
                }

                $debet = Jurnal::where('id_jurnal',$request->id_jurnal)->where('AccPos', 'Debet')->first();
                $debet->amount = $price;
                $debet->update();

                $credit = Jurnal::where('id_jurnal',$request->id_jurnal)->where('AccPos', 'Credit')->first();
                $credit->amount = $price;
                $credit->update();

                Log::setLog('PURPU','Update Receive Product Jurnal ID: '.$request->id_jurnal);

                return redirect()->back()->with('status', 'Data berhasil diupdate');
            }catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function delete(Request $request){
        // $receive = ReceiveDet::where('id_jurnal',$request->jurnal_id)->get();
        $id_jurnal = $request->jurnal_id;
        // $trx_id = $receive->trx_id;
        try {
            Jurnal::where('id_jurnal',$id_jurnal)->delete();
            Log::setLog('PURPD','Delete Receive Product Jurnal ID: '.$id_jurnal);
            return "true";
            // return redirect()->back()->with('status', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e);
        }
    }

    public function deleteProd(Request $request){
        $receive = ReceiveDet::where('id',$request->id)->first();
        $id_jurnal = $receive->id_jurnal;
        $idr = $request->id;
        $trx_id = $receive->trx_id;

        try {
            $receive->delete();

            $price = 0;
            $datas = ReceiveDet::where('id_jurnal', $id_jurnal)->get();

            if(!empty($datas)){
                ReceiveDet::recycleRP($trx_id);
            }else{
                Jurnal::where('id_jurnal', $receive->id_jurnal)->delete();
            }

            Log::setLog('PURPD','Delete Receive Product ID='.$idr.', trx_id :'.$trx_id.', Jurnal ID: '.$id_jurnal);

            return "true";
            // return redirect()->back()->with('status', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e);
        }
    }
}
