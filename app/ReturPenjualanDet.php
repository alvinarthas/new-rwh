<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturPenjualanDet extends Model
{
    protected $table ='tblreturpjdet';
    protected $fillable = [
        'trx_id','tgl','prod_id','creator', 'qty', 'reason', 'unit'
    ];

    public function product(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }
}
