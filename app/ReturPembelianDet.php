<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturPembelianDet extends Model
{
    protected $table ='tblreturpbdet';
    protected $fillable = [
        'trx_id','tgl','prod_id','qty', 'reason', 'creator', 'unit'
    ];

    public function product(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }
}
