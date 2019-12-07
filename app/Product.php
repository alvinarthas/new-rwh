<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\PurchaseDetail;
use App\DeliveryDetail;
use App\ReceiveDet;

class Product extends Model
{
    protected $table ='tblproduct';
    protected $fillable = [
        'company_id','prod_id','name','category', 'stock', 'price', 'supplier', 'buy_price', 'prod_id_new'
    ];

    public function rolemapping(){
        return $this->belongsTo('App\RoleMapping','username','username');
    }

    public function supplier(){
        return $this->belongsTo('App\Perusahaan','supplier','id');
    }

    public static function getIndent($prod_id){
        $qty_purchase = PurchaseDetail::where('prod_id',$prod_id)->sum('qty');
        $qty_receive = ReceiveDet::where('prod_id',$prod_id)->sum('qty');

        return $qty_purchase-$qty_receive;
    }

    public static function getGudang($prod_id){
        $qty_receive = ReceiveDet::where('prod_id',$prod_id)->sum('qty');
        $qty_delivery = DeliveryDetail::where('product_id',$prod_id)->sum('qty');
        $stock_awal = Product::where('prod_id',$prod_id)->first()->stock;
        $gudang = $stock_awal+$qty_receive-$qty_delivery;

        return $gudang;
    }
}
