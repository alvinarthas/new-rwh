<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\PurchaseDetail;
use App\SalesDet;

class ReturDetail extends Model
{
    protected $table ='tblreturdet';
    protected $fillable = [
        'trx_id','prod_id','qty','harga','reason', 'creator'
    ];

    public function product(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }

    public static function getRetured($jenis, $jurnal_id, $id){
        if($jenis == 0){
            $detail = PurchaseDetail::join('tblpotrx', 'tblpotrxdet.trx_id', 'tblpotrx.id')->where('tblpotrxdet.trx_id', $id)->select('prod_id', 'price_dist', 'price', 'qty', 'unit')->get();
        }elseif($jenis == 1){
            $detail = SalesDet::join('tblproducttrx', 'tblproducttrxdet.trx_id', 'tblproducttrx.id')->where('tblproducttrxdet.trx_id', $id)->select('prod_id', 'price', 'qty', 'unit')->get();
        }

        $data = collect();
        foreach($detail as $key){
            $retur = collect();

            if($jenis == 0){
                $source = PurchaseDetail::where('trx_id', $id)->where('prod_id', $key->prod_id)->where('price_dist', $key->harga_dist)->first();
            }elseif($jenis == 1){
                $source = ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('id_jurnal', $jurnal_id)->where('prod_id', $key->prod_id)->where('harga', $key->price)->first();
            }

            $qtyretur = $detail = ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblretur.source_id', $jurnal_id)->where('status', $jenis)->where('prod_id', $key->prod_id)->sum('qty');

            $retur->put('prod_id', $key->prod_id);
            $retur->put('product_name', $key->product->name);
            $retur->put('qty',$key->qty);
            $retur->put('unit', $key->unit);
            if($jenis == 0){
                $retur->put('harga_dist', $key->price_dist);
            }
            $retur->put('harga', $key->price);
            $retur->put('qtyretur', $qtyretur);
            $data->push($retur);
        }

        return $data;
    }
}
