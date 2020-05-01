<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KonversiDetail extends Model
{
    protected $table ='tbl_konversi';
    protected $fillable = [
        'konversi_id','product_id','status','qty'
    ];

    public function product(){
        return $this->belongsTo('App\Product','product_id','prod_id');
    }
}
