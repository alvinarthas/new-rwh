<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturPembelianDet extends Model
{
    protected $table ='tblreturpbdet';
    protected $fillable = [
        'trx_id','tgl','prod_id','qty', 'reason', 'creator', 'unit'
    ];
}
