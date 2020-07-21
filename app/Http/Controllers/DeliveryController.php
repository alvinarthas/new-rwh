<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use PDF;

use App\Customer;
use App\Product;
use App\Jurnal;
use App\PriceDet;
use App\Sales;
use App\SalesDet;
use App\MenuMapping;
use App\DeliveryOrder;
use App\DeliveryDetail;
use App\PurchaseDetail;
use App\Purchase;
use App\Log;
use App\Gudang;

class DeliveryController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $customer = $request->customer;
            $prod_id = $request->prod_id;
            // $sales = Sales::checkDO($start_date,$end_date);
            // $deliveries = json_decode (json_encode (DeliveryOrder::checkDO($request)),FALSE);
            return response()->json(view('sales.do.view', compact('start_date', 'end_date', 'customer', 'prod_id'))->render());
        }else{
            $customers = Customer::select('id', 'apname')->orderBy('apname', 'asc')->get();
            $products = Product::select('prod_id', 'name')->orderBy('name', 'asc')->get();
            return view('sales.do.index', compact('customers', 'products'));
        }
    }

    public function getDataDO(Request $request)
    {
        // echo "<pre>";
        // print_r($request->all());
        // die();
        if($request->ajax()){
            $datas = DeliveryOrder::checkDO($request);
            echo json_encode($datas);
        }
    }

    public function getDataSO(Request $request)
    {
        // echo "<pre>";
        // print_r($request->all());
        // die();
        if($request->ajax()){
            $datas = Sales::checkDO($request);
            echo json_encode($datas);
        }
    }

    public function view(Request $request){
        if ($request->ajax()) {
            $do_id = $request->do_id;
            $dodets = DeliveryDetail::where('do_id',$do_id)->get();
            $do = DeliveryOrder::where('id',$do_id)->first();

            return response()->json(view('sales.do.modal',compact('do','dodets'))->render());
        }
    }

    public function show($id){
        $sales = Sales::where('id',$id)->first();
        $salesdets = SalesDet::where('trx_id',$id)->select('*',DB::Raw('SUM(qty) as sum_qty'))->groupBy('prod_id')->get();
        $dos = DeliveryOrder::where('sales_id',$id)->get();
        $page = MenuMapping::getMap(session('user_id'),"PSDO");
        $products = SalesDet::getProducts($id);
        $gudangs = Gudang::all();

        return view('sales.do.form',compact('sales','salesdets','dos','page','products', 'gudangs'));
    }

    public function addBrgDo(Request $request){
        $qty = $request->qty;
        $count = $request->count+1;
        $product = $request->select_product;
        $gudang_id = $request->gudang;

        $product = Product::where('prod_id',$product)->first();

        $gudang = Gudang::where('id', $gudang_id)->first();

        $append = '<tr style="width:100%" id="trow'.$count.'">
        <td><input type="hidden" name="prod_id[]" id="prod_id'.$count.'" value="'.$product->prod_id.'">'.$product->prod_id.'</td>
        <td><input type="hidden" name="prod_name[]" id="prod_name'.$count.'" value="'.$product->name.'">'.$product->name.'</td>
        <td><input type="hidden" name="qty[]" value="'.$qty.'" id="qty'.$count.'">'.$qty.'</td>
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
            'sales_id' => 'required|integer',
            'count' => 'required',
            'prod_id' => 'required|array',
            'qty' => 'required|array',
            'gudang' => 'required|array',
            'do_date' => 'required|date',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $id_jurnal = Jurnal::getJurnalID('DO');
            $sales = Sales::where('id',$request->sales_id)->first();

            $do = new DeliveryOrder(array(
                'sales_id' => $request->sales_id,
                'date' => $request->do_date,
                'petugas' => session('user_id'),
                'jurnal_id' => $id_jurnal,
            ));

            try {
                $price = 0;

                for ($i=0; $i < $request->count ; $i++) {
                    $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$request->prod_id[$i])->where('tblpotrx.tgl','<=',$sales->trx_date)->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));

                    $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$request->prod_id[$i])->where('tblpotrx.tgl','<=',$sales->trx_date)->sum('tblpotrxdet.qty');

                    if($sumprice <> 0 && $sumqty <> 0){
                        $avcharga = $sumprice/$sumqty;
                    }else{
                        $avcharga = 0;
                    }

                    $price += $avcharga * $request->qty[$i];
                }

                $desc = "Delivery Order SO.".$request->sales_id;
                // JURNAL
                    //insert debet Persediaan Barang milik Customer
                    Jurnal::addJurnal($id_jurnal,$price,$request->do_date,$desc,'2.1.3','Debet');
                    //insert credit Persediaan Barang digudang
                    Jurnal::addJurnal($id_jurnal,$price,$request->do_date,$desc,'1.1.4.1.2','Credit');

                $do->save();

                $desc = "Delivery Order ID=".$do->id." SO.".$request->sales_id;
                foreach(Jurnal::where('id_jurnal',$id_jurnal)->get() as $key){
                    $key->description = $desc;
                    $key->save();
                }

                for ($i=0; $i < $request->count ; $i++) {
                    $dodet = new DeliveryDetail(array(
                       'do_id' => $do->id,
                       'sales_id' => $request->sales_id,
                       'product_id' => $request->prod_id[$i],
                       'qty' => $request->qty[$i],
                       'gudang_id' => $request->gudang[$i],
                    ));

                    $dodet->save();
                }
                Log::setLog('PSDOC','Create Delivery Order SO.'.$request->sales_id.' Jurnal ID: '.$id_jurnal);
                return redirect()->back()->with('status', 'Data DO berhasil dibuat');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->errorInfo);
            }
        }
    }

    public function edit($id)
    {
        $do = DeliveryOrder::where('id', $id)->first();
        $details = DeliveryDetail::where('do_id', $id)->get();
        $producttrx = SalesDet::where('trx_id', $do->sales_id)->select('prod_id')->get();
        $gudangs = Gudang::all();

        return view('sales.do.form_update', compact('do', 'details', 'producttrx', 'gudangs'));
    }

    public function update($id, Request $request){
        // echo "<pre>";
        // print_r($request->all());
        // die();

        // Validate
        $validator = Validator::make($request->all(), [
            'delivery_date' => 'required|date',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            try{
                $delivery = DeliveryOrder::where('id', $id)->first();

                // JURNAL
                //insert debet Persediaan Barang di Gudang
                $debet = Jurnal::where('id_jurnal', $delivery->jurnal_id)->where('AccPos', 'Debet')->first();
                $debet->date = $request->delivery_date;
                $debet->update();

                //insert credit Persediaan Barang Indent
                $credit = Jurnal::where('id_jurnal', $delivery->jurnal_id)->where('AccPos', 'Credit')->first();
                $credit->date = $request->delivery_date;
                $credit->update();

                $delivery->date = $request->delivery_date;
                $delivery->update();

                Log::setLog('PSDOU','Update Delivery Order SO.'.$delivery->sales_id.' Jurnal ID: '.$id);
                return redirect()->route('showDo',['id'=>$delivery->sales_id])->with('status', 'Data berhasil diupdate');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function addProduct(Request $request){
        $validator = Validator::make($request->all(), [
            'sales_id' => 'required',
            'do_id' => 'required',
            'id_jurnal' => 'required',
            'select_product' => 'required',
            'qty' => 'required|integer',
            'gudang' => 'required',
            'delivery_date' => 'required|date',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $sales = Sales::where('id',$request->sales_id)->first();
            $do = new DeliveryDetail(array(
                'do_id' => $request->do_id,
                'sales_id' => $request->sales_id,
                'qty' => $request->qty,
                'product_id' => $request->select_product,
                'gudang_id' => $request->gudang,
            ));

            try{

                $do->save();
                $price = 0;

                $details = DeliveryDetail::where('do_id', $request->do_id)->get();
                $count = $details->count();

                for ($i=0; $i < $count ; $i++) {
                    $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$details[$i]->product_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));

                    $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$details[$i]->product_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum('tblpotrxdet.qty');

                    if($sumprice <> 0 && $sumqty <> 0){
                        $avcharga = $sumprice/$sumqty;
                    }else{
                        $avcharga = 0;
                    }
                    // echo $sumprice;

                    $price += $avcharga * $details[$i]->qty;
                }

                $debet = Jurnal::where('id_jurnal',$request->id_jurnal)->where('AccPos', 'Debet')->first();
                $debet->amount = $price;
                $debet->update();

                $credit = Jurnal::where('id_jurnal',$request->id_jurnal)->where('AccPos', 'Credit')->first();
                $credit->amount = $price;
                $credit->update();

                Log::setLog('PSDOU','Update Receive Product Jurnal ID: '.$request->id_jurnal);

                return redirect()->back()->with('status', 'Data berhasil diupdate');
            }catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    public function delete(Request $request){
        $do_id = $request->id;
        $do = DeliveryOrder::where('id',$do_id)->select('jurnal_id','sales_id')->first();
        $sales_id = $do->sales_id;
        $id_jurnal = $do->jurnal_id;
        try {
            $jurnal = Jurnal::where('id_jurnal',$do->jurnal_id)->delete();
            Log::setLog('PSDOD','Delete Delivery Order SO.'.$sales_id.' Jurnal ID: '.$id_jurnal);
            return "true";
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }

    public function deleteProd(Request $request){
        // echo "<pre>";
        // print_r($request->all());
        // die();
        $id = $request->id;
        $do_id = $request->do_id;

        $do = DeliveryOrder::where('id',$do_id)->first();
        $sales = Sales::where('id',$do->sales_id)->first();
        $jurnal_id = $do->jurnal_id;

        try {
            $price = 0;
            $datas = DeliveryDetail::where('do_id', $do_id)->where('id', '!=', $id)->get();
            $count = $datas->count();

            // echo "<pre>";
            // print_r($datas);
            // die();

            if(!empty($datas)){
                for ($i=0; $i < $count ; $i++) {
                    $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$datas[$i]->product_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));

                    $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$datas[$i]->product_id)->where('tblpotrx.tgl','<=',$sales->trx_date)->sum('tblpotrxdet.qty');

                    if($sumprice <> 0 && $sumqty <> 0){
                        $avcharga = $sumprice/$sumqty;
                    }else{
                        $avcharga = 0;
                    }
                    // echo $sumprice;

                    $price += $avcharga * $datas[$i]->qty;
                }
                $debet = Jurnal::where('id_jurnal',$jurnal_id)->where('AccPos', 'Debet')->first();
                $debet->amount = $price;
                $debet->update();

                $credit = Jurnal::where('id_jurnal',$jurnal_id)->where('AccPos', 'Credit')->first();
                $credit->amount = $price;
                $credit->update();

                DeliveryDetail::where('id', $id)->delete();

                Log::setLog('PSDOD','Delete Delivery Detail ID='.$id.', Jurnal ID: '.$jurnal_id);
            }else{
                Jurnal::where('id_jurnal', $jurnal_id)->delete();
                Log::setLog('PSDOD','Delete Delivery Detail Jurnal ID: '.$jurnal_id);
            }

            return "true";
            // return redirect()->back()->with('status', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e);
        }
    }

    public function print(Request $request){
        try{
            $delivery = DeliveryOrder::where('id',$request->id)->select('sales_id','date')->first();

            $data = collect();
            $datdet = collect();
            $filename = "DO-".$request->id;
            $data->put('do_id',$request->id);
            $data->put('trx_id',$delivery->sales->id);
            $data->put('date',$delivery->date);
            $data->put('customer',$delivery->sales->customer->apname);
            $no = 1;
            foreach(DeliveryDetail::where('do_id',$request->id)->get() as $key){
                $unit = SalesDet::where('trx_id',$delivery->sales_id)->where('prod_id',$key->product_id)->select('unit')->first()->unit;

                $det = collect();
                $det->put('product',$key->product->name);
                $det->put('unit',$unit);
                $det->put('qty',$key->qty);
                $det->put('no',$no);

                $no++;
                $datdet->push($det);
            }

            $data->put('data',$datdet);
            $pdf = PDF::loadview('sales.do.pdf',$data)->setPaper('a4','portrait');
            $pdf->save(public_path('download/'.$filename.'.pdf'));
            return $pdf->download($filename.'.pdf');
        }catch(\Exception $e){
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
