<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\Storage;

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

class DeliveryController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $start = $request->start;
            $end = $request->end;
            $sales = Sales::checkDO($start,$end);
            return response()->json(view('sales.do.view',compact('sales'))->render());
        }else{
            return view('sales.do.index');
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
        $salesdets = SalesDet::where('trx_id',$id)->get();
        $dos = DeliveryOrder::where('sales_id',$id)->get();
        $page = MenuMapping::getMap(session('user_id'),"PSDO");
        $products = SalesDet::getProducts($id);
        return view('sales.do.form',compact('sales','salesdets','dos','page','products'));
    }

    public function addBrgDo(Request $request){
        $qty = $request->qty;
        $count = $request->count+1;
        $product = $request->select_product;

        $product = Product::where('prod_id',$product)->first();

        $append = '<tr style="width:100%" id="trow'.$count.'">
        <td><input type="hidden" name="prod_id[]" id="prod_id'.$count.'" value="'.$product->prod_id.'">'.$product->prod_id.'</td>
        <td><input type="hidden" name="prod_name[]" id="prod_name'.$count.'" value="'.$product->name.'">'.$product->name.'</td>
        <td><input type="hidden" name="qty[]" value="'.$qty.'" id="qty'.$count.'">'.$qty.'</td>
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
                for ($i=0; $i < $request->count ; $i++) {
                    $pricedet = SalesDet::where('trx_id',$request->sales_id)->where('prod_id',$request->prod_id[$i])->first()->price;

                    // $avcharga = PurchaseDetail::where('prod_id',$request->prod_id[$i])->where('created_at','<=',$sales->created_at)->avg('price');
                    $avcharga = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$request->prod_id[$i])->where('tblpotrx.tgl','<=',$sales->trx_date)->avg('tblpotrxdet.price');

                    $price = $avcharga * $request->qty[$i];
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
                       'qty' => $request->qty[$i]
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
