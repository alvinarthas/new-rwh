<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempSales extends Model
{
    protected $table ='temp_tblproducttrx';
    protected $fillable = [
        'trx_date','creator','ttl_harga','customer_id','ongkir','status','trx_id','method','online_id'
    ];

    public function customer(){
        return $this->belongsTo('App\Customer');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }

    public function trx(){
        return $this->belongsTo('App\Sales');
    }

    public function online(){
        return $this->belongsTo('App\Ecommerce','method','id');
    }
}
