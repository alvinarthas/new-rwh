<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    protected $table ='tblretur';
    protected $fillable = [
        'tgl','customer', 'supplier', 'status', 'creator'
    ];

    public function customer(){
        return $this->belongsTo('App\Customer','customer');
    }

    public function supplier(){
        return $this->belongsTo('App\Perusahaan','supplier');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }
}
