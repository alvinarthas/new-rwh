<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $table = "tbl_deposit";
    protected $fillable = ['supplier_id','status','amount','keterangan','creator','date','jurnal_id','AccNo'];

    public static function getSaldo($supplier){
        $plus = Deposit::where('supplier_id',$supplier)->where('status',1)->sum('amount');
        $minus = Deposit::where('supplier_id',$supplier)->where('status',0)->sum('amount');

        return $plus-$minus;
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator');
    }
}
