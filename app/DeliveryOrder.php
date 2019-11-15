<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    protected $table ='delivery_order';
    protected $fillable = [
        'sales_id','date','petugas'
    ];

    public function sales(){
        return $this->belongsTo('App\Sales');
    }

    public function petugas(){
        return $this->belongsTo('App\Employee','petugas','id');
    }
}
