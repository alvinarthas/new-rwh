<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempPO extends Model
{
    protected $table ='temp_tblpotrx';
    protected $fillable = [
        'purchase_id','month','year','creator','supplier','notes','status','tgl','total_harga_modal','total_harga_dist','status'
    ];

    public function supplier(){
        return $this->belongsTo('App\Perusahaan','supplier');
    }  
}
