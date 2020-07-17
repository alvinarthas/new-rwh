<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
