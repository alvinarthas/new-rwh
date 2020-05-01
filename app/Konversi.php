<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Konversi extends Model
{
    protected $table ='tbl_konversi';
    protected $fillable = [
        'keterangan','creator','supplier'
    ];

    public function product(){
        return $this->belongsTo('App\Product','product_id','prod_id');
    }

    public function supplier(){
        return $this->belongsTo('App\Perusahaan','supplier','id');
    }
}
