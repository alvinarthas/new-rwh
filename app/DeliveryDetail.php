<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\DeliveryOrder;
use App\Purchase;
use App\Jurnal;

class DeliveryDetail extends Model
{
    protected $table ='delivery_detail';
    protected $fillable = [
        'do_id','qty','product_id','sales_id', 'gudang_id',
    ];

    public function sales(){
        return $this->belongsTo('App\Sales');
    }

    public function gudang(){
        return $this->belongsTo('App\Gudang');
    }

    public function product(){
        return $this->belongsTo('App\Product','product_id','prod_id');
    }

    public static function recycleDO($do_id,$det_id,$date){
        DeliveryDetail::where('id',$det_id)->delete();
        $do = DeliveryOrder::where('id',$do_id)->first();

        $price = 0;
        $count = 0;
        foreach(DeliveryDetail::where('do_id',$do_id)->get() as $key) {
            $sumprice = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key->product_id)->where('tblpotrx.tgl','<=',$date)->sum(DB::raw('tblpotrxdet.price*tblpotrxdet.qty'));

            $sumqty = Purchase::join('tblpotrxdet','tblpotrxdet.trx_id','=','tblpotrx.id')->where('tblpotrxdet.prod_id',$key->product_id)->where('tblpotrx.tgl','<=',$date)->sum('tblpotrxdet.qty');

            if($sumprice <> 0 && $sumqty <> 0){
                $avcharga = $sumprice/$sumqty;
            }else{
                $avcharga = 0;
            }

            $price += $avcharga * $key->qty;
            $count++;
        }

        if ($count > 0){
            $debet = Jurnal::where('id_jurnal',$do->jurnal_id)->where('AccPos', 'Debet')->first();
            $debet->amount = $price;
            $debet->update();

            $credit = Jurnal::where('id_jurnal',$do->jurnal_id)->where('AccPos', 'Credit')->first();
            $credit->amount = $price;
            $credit->update();
        }else{
            $jurnal = Jurnal::where('id_jurnal',$do->jurnal_id)->delete();
        }
    }
}
