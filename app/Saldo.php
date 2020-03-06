<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $table ='tblsaldo';
    protected $fillable = [
        'customer_id','status','amount','keterangan','creator','tanggal', 'accNo', 'buktitf','id_jurnal'
    ];

    public static function getSaldo($customer){
        $plus = Saldo::where('customer_id',$customer)->where('status',1)->sum('amount');
        $minus = Saldo::where('customer_id',$customer)->where('status',0)->sum('amount');

        return $plus-$minus;
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator');
    }
}
