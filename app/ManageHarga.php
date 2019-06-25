<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManageHarga extends Model
{
    protected $table ='tblmanageharga';
    protected $fillable = [
        'month','year','harga_modal','harga_distributor','creator', 'prod_id'
    ];

    public $timestamps = false;

    public function rolemapping(){
        return $this->belongsTo('App\RoleMapping','username','username');
    }
}
