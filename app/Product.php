<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table ='tblproduct';
    protected $fillable = [
        'company_id','prod_id','name','category', 'stock', 'price', 'supplier', 'buy_price', 'prod_id_new'
    ];

    public function rolemapping(){
        return $this->belongsTo('App\RoleMapping','username','username');
    }
}
