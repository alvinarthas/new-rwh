<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $table ='tblpopayment';
    protected $fillable = [
        'trx_id','payment_date','payment_amount','payment_method','payment_desc','due_date','deduct_category','deduct_amount','id_jurnal'
    ];
}
