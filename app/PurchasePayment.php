<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $table ='tblpopayment';
    protected $fillable = [
        'trx_id','payment_date','payment_amount','payment_method','payment_desc','due_date','deduct_category','deduct_amount','jurnal_id'
    ];

    public function payment(){
        return $this->belongsTo('App\Coa','payment_method','AccNo');
    }

    public function purchase(){
        return $this->belongsTo('App\Purchase','trx_id','id');
    }
}
