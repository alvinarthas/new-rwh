<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempPODet extends Model
{
    protected $table ='temp_tblpotrxdet';
    protected $fillable = [
        'temp_id','prod_id','qty','unit','price','price_dist','creator','purchasedetail_id'
    ];

    public function product(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }

    public function purchase(){
        return $this->belongsTo('App\Purchase','trx_id');
    }
}
