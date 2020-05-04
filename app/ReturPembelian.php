<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturPembelian extends Model
{
    protected $table ='tblreturpb';
    protected $fillable = [
        'trx_id','tgl','supplier','username'
    ];

    public function supplier(){
        return $this->belongsTo('App\Perusahaan','supplier');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }
}
