<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturDetail extends Model
{
    protected $table ='tblreturdet';
    protected $fillable = [
        'trx_id','prod_id','qty','harga','reason', 'creator'
    ];

    public function product(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }
}
