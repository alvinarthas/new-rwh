<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\PurchaseDetail;
use App\DeliveryDetail;
use App\ReceiveDet;
use App\SalesDet;
use App\KonversiDetail;
use App\TransitProductDetail;

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
        $qty_purchase = PurchaseDetail::join('tblpotrx', 'tblpotrxdet.trx_id', 'tblpotrx.id')->where('prod_id',$prod_id)->where('jurnal_id', '!=', '0')->sum('qty');
        $qty_receive = ReceiveDet::where('prod_id',$prod_id)->sum('qty');

        $indent = $qty_purchase-$qty_receive;

        return $indent;
    }

    public static function getGudang($prod_id,$gudang_id=null){
        if ($gudang_id){
            $qty_receive = ReceiveDet::where('prod_id',$prod_id)->where('gudang_id',$gudang_id)->sum('qty');
            $qty_delivered = DeliveryDetail::where('product_id',$prod_id)->where('gudang_id',$gudang_id)->sum('qty');
            $konversi = KonversiDetail::getTotalGudang($gudang_id,$prod_id);
            $retur = 0;
            // Pindah Gudang
            $gudangKeluar = TransitProductDetail::where('product_id',$prod_id)->where('gudang_awal',$gudang_id)->sum('qty');
            $gudangMasuk = TransitProductDetail::where('product_id',$prod_id)->where('gudang_akhir',$gudang_id)->sum('qty');
            $sumGudang = $gudangMasuk-$gudangKeluar;

            $gudang = ($qty_receive-$qty_delivered)+$konversi+$retur+$sumGudang;
        }else{
            $qty_receive = ReceiveDet::where('prod_id',$prod_id)->sum('qty');
            $qty_delivered = DeliveryDetail::where('product_id',$prod_id)->sum('qty');
            $konversi = KonversiDetail::getTotal($prod_id);
            $retur = Retur::getTotal($prod_id, null);

            $gudang = ($qty_receive-$qty_delivered)+$konversi+$retur;
        }

        return $gudang;
    }

    public static function getBrgCust($prod_id){
        $sales = SalesDet::join('tblproducttrx', 'tblproducttrxdet.trx_id', 'tblproducttrx.id')->where('prod_id',$prod_id)->where('jurnal_id','!=','0')->sum('qty');
        $delivery = DeliveryDetail::where('product_id',$prod_id)->sum('qty');
        $retur = Retur::getTotal($prod_id, 1);

        // $brgcust = $sales-$delivery-$retur;
        $brgcust = $sales-$delivery;

        return $brgcust;
    }
}
