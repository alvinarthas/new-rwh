<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturPayment extends Model
{
    protected $table ='tblreturpay';
    protected $fillable = [
        'trx_id','amount', 'date', 'AccNo', 'description', 'status', 'creator', 'id_jurnal'
    ];

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }

    public function payment(){
        return $this->belongsTo('App\Coa','AccNo','AccNo');
    }
}
