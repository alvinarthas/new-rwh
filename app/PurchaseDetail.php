<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $table ='tblpotrxdet';
    protected $fillable = [
        'trx_id','prdo_id','qty','unit','price','price_dist'
    ];
}
