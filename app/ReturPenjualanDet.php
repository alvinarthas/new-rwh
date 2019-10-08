<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturPenjualanDet extends Model
{
    protected $table ='tblreturpjdet';
    protected $fillable = [
        'trx_id','tgl','prod_id','username', 'qty', 'reason'
    ];
    public $timestamps = false;
}
