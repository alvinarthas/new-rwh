<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KonversiDetail extends Model
{
    protected $table ='tbl_konversi_detail';
    protected $fillable = [
        'konversi_id','product_id','status','qty'
    ];

    public function product(){
        return $this->belongsTo('App\Product','product_id','prod_id');
    }

    public static function getTotal($product){
        $total = 0;
        foreach(KonversiDetail::where('product_id',$product)->get() as $detail){
            if($detail->status == 0){
                $total-=$detail->qty;
            }else{
                $total+=$detail->qty;
            }
        }
        return $total;
    }
}
