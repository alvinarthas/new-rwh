<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    protected $table ='tblretur';
    protected $fillable = [
        'tgl','customer', 'supplier', 'status', 'creator'
    ];

    public function customer(){
        return $this->belongsTo('App\Customer','customer');
    }

    public function supplier(){
        return $this->belongsTo('App\Perusahaan','supplier');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }

    public static function getTotal($product, $status){
        $total = 0;
        if($status == null){
            foreach(ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('tblreturdet.prod_id',$product)->get() as $detail){
                if($detail->status == 0){
                    $total-=$detail->qty;
                }else{
                    $total+=$detail->qty;
                }
            }
        }else{
            foreach(ReturDetail::join('tblretur', 'tblreturdet.trx_id', 'tblretur.id')->where('status', $status)->where('tblreturdet.prod_id',$product)->get() as $detail){
                $total+=$detail->qty;
            }
        }

        return $total;
    }
}
