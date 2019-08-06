<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table ='tblpotrx';
    protected $fillable = [
        'month','year','creator','supplier','notes','id_jurnal','tgl','approve','approve_by'
    ];

    public function supplier(){
        return $this->belongsTo('App\Perusahaan','supplier');
    }
}
