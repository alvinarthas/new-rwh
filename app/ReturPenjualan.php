<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturPenjualan extends Model
{
    protected $table ='tblreturpj';
    protected $fillable = [
        'trx_id','tgl','customer','creator'
    ];

    public function customer(){
        return $this->belongsTo('App\Customer','customer');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }
}
