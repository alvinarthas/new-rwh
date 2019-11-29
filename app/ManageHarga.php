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

    public function product(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }

    public static function showHarga($supplier,$month,$year){
        $data = collect();
        foreach (Product::select('prod_id','name')->where('supplier',$supplier)->get() as $key) {
            $getHarga = ManageHarga::where('month',$month)->where('year',$year)->where('prod_id',$key->prod_id)->select('harga_distributor','harga_modal')->first();

            $collect = collect();
            if(isset($getHarga)){
                $collect->put('prod_name',$key->name);
                $collect->put('prod_id',$key->prod_id);
                $collect->put('harga_distributor',$getHarga->harga_distributor);
                $collect->put('harga_modal',$getHarga->harga_modal);
            }else{
                $collect->put('prod_name',$key->name);
                $collect->put('prod_id',$key->prod_id);
                $collect->put('harga_distributor',0);
                $collect->put('harga_modal',0);
            }
            $data->push($collect);
        }

        return $data;
    }

    public static function showHargaEx($supplier,$month,$year){
        return ManageHarga::join('tblproduct','tblmanageharga.prod_id','=','tblproduct.prod_id')->where('tblproduct.supplier',$supplier)->where('tblmanageharga.month',$month)->where('tblmanageharga.year',$year)->select('tblmanageharga.harga_distributor','tblmanageharga.harga_modal','tblproduct.prod_id','tblproduct.name')->get();
    }

    public static function showProduct($product,$month,$year){
        $getHarga = ManageHarga::where('month',$month)->where('year',$year)->where('prod_id',$product)->select('harga_distributor','harga_modal','prod_id')->first();

        $collect = collect();
        if(isset($getHarga)){
            $collect->put('prod_name',$getHarga->product->name);
            $collect->put('prod_id',$product);
            $collect->put('harga_distributor',$getHarga->harga_distributor);
            $collect->put('harga_modal',$getHarga->harga_modal);
        }else{
            $prod_name = Product::where('prod_id',$product)->select('name')->first()->name;
            $collect->put('prod_name',$prod_name);
            $collect->put('prod_id',$product);
            $collect->put('harga_distributor',0);
            $collect->put('harga_modal',0);
        }

        return $collect;
    }
}
