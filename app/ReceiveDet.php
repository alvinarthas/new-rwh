<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Purchase;
use App\Product;

class ReceiveDet extends Model
{
    protected $table ='tblreceivedet';
    protected $fillable = [
        'trx_id','prod_id','qty','expired_date', 'creator', 'receive_date', 'id_jurnal','purchase_detail_id',
    ];

    public function prod(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }

    public static function listReceive($start,$end){
        // $purchase = Purchase::join('tblpotrxdet','tblpotrx.id','=','tblpotrxdet.trx_id')->whereBetween('tblpotrx.month',[$bulan_start,$bulan_end])->whereBetween('tblpotrx.year',[$tahun_start,$tahun_end])->where('tblpotrx.approve',1)->select('tblpotrx.id as trx_id','tblpotrx.supplier','tblpotrxdet.prod_id','tblpotrxdet.qty','tblpotrxdet.unit')->get();
        $receive = ReceiveDet::whereBetween('receive_date', [$start,$end] )->get();

        $data = collect();
        foreach($receive as $key){
            $detail = collect($key);

            $qtypur = PurchaseDetail::where('trx_id',$key->trx_id)->where('prod_id',$key->prod_id)->sum('qty');
            $unit = PurchaseDetail::where('trx_id',$key->trx_id)->where('prod_id',$key->prod_id)->first()->unit;
            $prodname = Product::where('prod_id',$key->prod_id)->first()->name;

            $detail->put('qtypur',$qtypur);
            $detail->put('unit',$unit);
            $detail->put('prod_name',$prodname);
            $data->push($detail);
        }

        return $data;
    }

    public static function listReceiveAll(){
        // $purchase = Purchase::join('tblpotrxdet','tblpotrx.id','=','tblpotrxdet.trx_id')->where('tblpotrx.approve',1)->select('tblpotrx.id as trx_id','tblpotrx.supplier','tblpotrxdet.prod_id','tblpotrxdet.qty','tblpotrxdet.unit')->get();
        $receive = ReceiveDet::all();

        $data = collect();
        foreach($receive as $key){
            // $key->supplier = $key->supplier()->first()->nama;
            $detail = collect($key);

            $qtypur = PurchaseDetail::where('trx_id',$key->trx_id)->where('prod_id',$key->prod_id)->sum('qty');
            $unit = PurchaseDetail::where('trx_id',$key->trx_id)->where('prod_id',$key->prod_id)->first()->unit;
            $prodname = Product::where('prod_id',$key->prod_id)->first()->name;

            $detail->put('qtypur',$qtypur);
            $detail->put('unit',$unit);
            $detail->put('prod_name',$prodname);
            $data->push($detail);
        }

        return $data;
    }

    public static function detailPurchase($trx){
        $purchase = Purchase::join('tblpotrxdet','tblpotrx.id','=','tblpotrxdet.trx_id')->select('tblpotrx.id as trx_id','tblpotrx.supplier','tblpotrxdet.prod_id','tblpotrxdet.qty','tblpotrxdet.unit')->where('tblpotrx.id',$trx)->get();

        $data = collect();
        foreach($purchase as $key){
            $key->supplier = $key->supplier()->first()->nama;
            $detail = collect($key);

            $qtyrec = ReceiveDet::where('trx_id',$key->trx_id)->where('prod_id',$key->prod_id)->sum('qty');
            $prodname = Product::where('prod_id',$key->prod_id)->first()->name;

            $detail->put('qtyrec',$qtyrec);
            $detail->put('prod_name',$prodname);
            $data->push($detail);
        }

        return $data;
    }
}
