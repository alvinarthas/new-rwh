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

    public static function showHarga($supplier,$month,$year){
        return ManageHarga::join('tblproduct','tblmanageharga.prod_id','=','tblproduct.prod_id')->where('tblproduct.supplier',$supplier)->where('tblmanageharga.month',$month)->where('tblmanageharga.year',$year)->select('tblmanageharga.harga_distributor','tblmanageharga.harga_modal','tblproduct.prod_id','tblproduct.name')->get();
    }

    public static function showProduct($product,$month,$year){
        return ManageHarga::join('tblproduct','tblmanageharga.prod_id','=','tblproduct.prod_id')->where('tblproduct.prod_id',$product)->where('tblmanageharga.month',$month)->where('tblmanageharga.year',$year)->select('tblmanageharga.harga_distributor','tblmanageharga.harga_modal','tblproduct.prod_id','tblproduct.name')->first();
    }
}
