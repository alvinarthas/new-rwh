<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $table ='tblpotrxdet';
    protected $fillable = [
        'trx_id','prod_id','qty','unit','price','price_dist','creator'
    ];

    public function product(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }

    public function purchase(){
        return $this->belongsTo('App\Purchase','trx_id');
    }
}
