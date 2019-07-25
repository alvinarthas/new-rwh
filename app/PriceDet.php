<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceDet extends Model
{
    protected $table ='tblpricedetail';

    public function prod(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }
}
