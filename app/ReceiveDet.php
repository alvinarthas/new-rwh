<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Purchase;
use App\Product;

class ReceiveDet extends Model
{
    protected $table ='tblreceivedet';
    protected $fillable = [
        'trx_id','prod_id','qty','expired_date', 'gudang_id','creator', 'receive_date', 'id_jurnal', 'purchasedetail_id',
    ];

    public function prod(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }

    public function gudang(){
        return $this->belongsTo('App\Gudang');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }

    public function price(){
        return $this->belongsTo('App\PurchaseDetail', 'purchasedetail_id', 'id');
    }

    public static function getReceiveDet(Request $request){
        $start = $request->start_date;
        $end = $request->end_date;

        $draw = $request->draw;
        $row = $request->start;
        $rowperpage = $request->length; // Rows display per page
        $columnIndex = $request['order'][0]['column']; // Column index
        $columnName = $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $request['order'][0]['dir']; // asc or desc
        $searchValue = $request['search']['value']; // Search value

        $purchase = Purchase::join('tblpotrxdet','tblpotrx.id','tblpotrxdet.trx_id')->join('tblperusahaan', 'tblpotrx.supplier', 'tblperusahaan.id')->join('tblproduct', 'tblpotrxdet.prod_id', 'tblproduct.prod_id')->select('tblpotrx.id', 'tblpotrx.tgl', 'tblpotrxdet.prod_id', 'tblpotrx.jurnal_id', 'tblpotrxdet.trx_id', 'tblproduct.name AS prodname', 'tblperusahaan.nama AS supplier_name', 'tblpotrxdet.qty', 'tblpotrxdet.id AS purchasedetail_id', 'tblpotrxdet.unit', 'tblpotrxdet.price')->where('tblpotrx.approve', 1);

        if($start <> NULL && $end<> NULL){
            $purchase->whereBetween('tblpotrx.tgl',[$start,$end]);
        }

        $totalRecords = $purchase->count();

        if($searchValue != ''){
            $raw = ReceiveDet::select('trx_id')->where('id_jurnal', 'LIKE', '%'.$searchValue.'%')->get();
            // echo $raw;
            $purchase->where('tblpotrx.jurnal_id', 'LIKE', '%'.$searchValue.'%')->orWhere('tblperusahaan.nama', 'LIKE', '%'.$searchValue.'%')->orWhere('tblpotrxdet.prod_id', 'LIKE', '%'.$searchValue.'%')->orWhere('tblproduct.name', 'LIKE', '%'.$searchValue.'%')->orWhere('tblpotrxdet.qty', 'LIKE', '%'.$searchValue.'%')->orWhere('tblpotrxdet.unit', 'LIKE', '%'.$searchValue.'%')->orWhereIn('tblpotrx.id', $raw);
        }

        $totalRecordwithFilter = $purchase->count();

        if($columnName == "no"){
            $purchase->orderBy('tblpotrxdet.trx_id', $columnSortOrder);
        }elseif($columnName == "po_id"){
            $purchase->orderBy('tblpotrx.jurnal_id', $columnSortOrder);
        }elseif($columnName == "supplier"){
            $purchase->orderBy('supplier_name', $columnSortOrder);
        }elseif($columnName == "prod_name"){
            $purchase->orderBy('prodname', $columnSortOrder);
        }else{
            $purchase->orderBy($columnName, $columnSortOrder);
        }

        $purchase = $purchase->offset($row)->limit($rowperpage)->get();

        // echo "<pre>";
        // print_r($purchase);
        // die();

        $data = collect();
        $i = 1;

        foreach($purchase as $key){
            $detail = collect();

            $rp_id = "";

            $rp = ReceiveDet::where('trx_id',$key->trx_id)->where('prod_id',$key->prod_id)->select('id_jurnal')->get();
            foreach($rp as $r){
                $rp_id .= $r->id_jurnal." ";
            }

            $rp_qty = ReceiveDet::where('purchasedetail_id',$key->purchasedetail_id)->sum('qty');

            if($rp_qty == 0){
                $qtyrp = '<a href="javascrip:;" class="btn btn-danger btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">'.$rp_qty.'</a>';
            }elseif(($rp_qty-$key->qty) == 0){
                $qtyrp = '<a href="javascrip:;" class="btn btn-success btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">'.$rp_qty.'</a>';
            }elseif(($rp_qty-$key->qty) < 0){
                $qtyrp = '<a href="javascrip:;" class="btn btn-warning btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">'.$rp_qty.'</a>';
            }elseif(($rp_qty-$key->qty) > 0){
                $qtyrp = '<a href="javascrip:;" class="btn btn-custom btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">'.$rp_qty.'</a>';
            }else{
                $qtyrp = '<a href="javascrip:;" class="btn btn-info btn-rounded waves-effect w-xs waves-danger m-b-5 disabled">'.$rp_qty.'</a>';
            }

            $button = '<a href="receiveproduct/detail/'.$key->trx_id.'" class="btn btn-primary btn-trans waves-effect w-xs waves-danger m-b-5">'.$key->jurnal_id.'</a>';

            $prodname = $key->prodname." (".$key->price.")";

            $detail->put('no', $i++);
            $detail->put('po_id', $button);
            $detail->put('rp_id', $rp_id);
            $detail->put('supplier', $key->supplier_name);
            $detail->put('prod_id', $key->prod_id);
            $detail->put('prod_name', $key->prodname." - (Rp ".number_format($key->price, 2, ",", ".").")");
            $detail->put('qty', $key->qty);
            $detail->put('unit', $key->unit);
            $detail->put('qtyrp', $qtyrp);
            $data->push($detail);
        }

        // echo "<pre>";
        // print_r($data);
        // die();

        $response = array(
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecordwithFilter,
            'data' => $data,
        );

        return $response;
    }


    public static function listReceive($start,$end){
        $purchase = Purchase::join('tblpotrxdet','tblpotrx.id','=','tblpotrxdet.trx_id')->whereBetween('tblpotrx.tgl',[$start,$end])->where('tblpotrx.approve',1)->select('tblpotrx.id as trx_id','tblpotrx.supplier','tblpotrxdet.prod_id','tblpotrxdet.qty','tblpotrxdet.unit', 'tblpotrxdet.id as purdet_id','tblpotrxdet.price')->get();
        // $purchase = Purchase::whereBetween('tblpotrx.tgl',[$start,$end])->where('tblpotrx.approve',1)->get();

        $data = collect();
        foreach($purchase as $pur){
            $detail = collect($pur);
            $rp_id = "";
            $receive = ReceiveDet::where('trx_id', $pur->id)->where('prod_id', $pur->prod_id)->select('id_jurnal')->get();
            foreach($receive as $r){
                $rp_id .= $r->id_jurnal." ";
            }
            $qtyrec = ReceiveDet::where('purchasedetail_id',$pur->purdet_id)->sum('qty');
            $prodname = Product::where('prod_id',$pur->prod_id)->first()->name;

            $detail->put('rp_id', $rp_id);
            $detail->put('supplier', $pur->supplier()->first()->nama);
            $detail->put('qtyrec',$qtyrec);
            $detail->put('prod_name',$prodname." - (Rp ".number_format($pur->price, 2, ",", ".").")");
            $data->push($detail);
        }

        return $data;
    }

    public static function listReceiveAll(){
        $purchase = Purchase::join('tblpotrxdet','tblpotrx.id','=','tblpotrxdet.trx_id')->where('tblpotrx.approve',1)->select('tblpotrx.id as trx_id','tblpotrx.supplier','tblpotrxdet.prod_id','tblpotrxdet.qty','tblpotrxdet.unit', 'tblpotrxdet.id as purdet_id', 'tblpotrxdet.price')->get();

        $data = collect();
        foreach($purchase as $pur){
            $detail = collect($pur);
            $rp_id = "";
            $receive = ReceiveDet::where('trx_id', $pur->id)->where('prod_id', $pur->prod_id)->select('id_jurnal')->get();
            foreach($receive as $r){
                $rp_id .= $r->id_jurnal." ";
            }
            $qtyrec = ReceiveDet::where('purchasedetail_id',$pur->purdet_id)->sum('qty');
            $prodname = Product::where('prod_id',$pur->prod_id)->first()->name;

            $detail->put('rp_id', $rp_id);
            $detail->put('supplier', $pur->supplier()->first()->nama);
            $detail->put('qtyrec',$qtyrec);
            $detail->put('prod_name',$prodname." - (Rp ".number_format($pur->price, 2, ",", ".").")");
            $data->push($detail);
        }

        return $data;
    }

    public static function detailPurchase($trx){
        $purchase = Purchase::join('tblpotrxdet','tblpotrx.id','=','tblpotrxdet.trx_id')->select('tblpotrx.id as trx_id','tblpotrx.supplier','tblpotrxdet.prod_id','tblpotrxdet.qty','tblpotrxdet.unit','tblpotrxdet.id AS purchasedetail_id', 'tblpotrxdet.price')->where('tblpotrx.id',$trx)->get();

        $data = collect();
        foreach($purchase as $key){
            $key->supplier = $key->supplier()->first()->nama;
            $detail = collect($key);

            $qtyrec = ReceiveDet::where('purchasedetail_id',$key->purchasedetail_id)->sum('qty');
            $prodname = Product::where('prod_id',$key->prod_id)->first()->name;

            $detail->put('qtyrec',$qtyrec);
            $detail->put('prod_name',$prodname." - (Rp ".number_format($key->price, 2, ",", ".").")");
            $data->push($detail);
        }

        return $data;
    }

    public static function recycleRP($id,$purchasedetail_id=null){
        if ($purchasedetail_id){
            ReceiveDet::where('purchasedetail_id',$purchasedetail_id)->delete();
        }

        // Refesh and Loop
        foreach(ReceiveDet::where('trx_id', $id)->select('id_jurnal')->groupBy('id_jurnal')->get() as $key){
            $price = 0;
            foreach (ReceiveDet::where('id_jurnal', $key->id_jurnal)->get() as $data) {
                $pricedet = PurchaseDetail::where('id', $data->purchasedetail_id)->first();
                if(!$pricedet){
                    $pricedet = PurchaseDetail::where('prod_id',$data->prod_id)->where('trx_id',$data->trx_id)->first();
                }
                $price += $pricedet->price * $data->qty;
            }
            $debet = Jurnal::where('id_jurnal',$key->id_jurnal)->where('AccPos', 'Debet')->first();
            $debet->amount = $price;
            $debet->update();

            $credit = Jurnal::where('id_jurnal',$key->id_jurnal)->where('AccPos', 'Credit')->first();
            $credit->amount = $price;
            $credit->update();
        }
    }
}
