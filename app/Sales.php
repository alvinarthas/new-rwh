<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $table ='tblproducttrx';
    protected $fillable = [
        'trx_date','creator','payment','ttl_harga','customer','jurnal_id','hpp_jurnal_id','ongkir','approve'
    ];

    public function customer(){
        return $this->belongsTo('App\Customer');
    }
}
