<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesPayment extends Model
{
    protected $table ='tblsopayment';
    protected $fillable = [
        'trx_id','payment_date','payment_amount','payment_method','payment_desc','due_date','deduct_category','deduct_amount','jurnal_id'
    ];
}
