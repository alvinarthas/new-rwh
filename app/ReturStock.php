<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Retur;
use App\Product;

class ReturStock extends Model
{
    protected $table ='tblreturstock';
    protected $fillable = [
        'trx_id','prod_id', 'qty', 'date', 'id_jurnal', 'status', 'creator'
    ];

    public function prod(){
        return $this->belongsTo('App\Product','prod_id','prod_id');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }

    public static function detailRetur($trx, $jenis){
        $retur = Retur::join('tblreturdet','tblretur.id','=','tblreturdet.trx_id')->select('tblretur.id as trx_id','tblretur.supplier', 'tblretur.customer', 'tblreturdet.prod_id','tblreturdet.qty','tblreturdet.unit')->where('tblretur.id',$trx)->where('tblretur.status', $jenis)->get();

        $data = collect();
        foreach($retur as $key){
            if($jenis == 0){
                $key->supplier = $key->supplier()->first()->nama;
            }elseif($jenis == 1){
                $key->customer = $key->customer()->first()->apname;
            }

            $detail = collect($key);

            $qtyrec = ReturStock::where('trx_id',$key->trx_id)->where('prod_id',$key->prod_id)->sum('qty');
            $prodname = Product::where('prod_id',$key->prod_id)->first()->name;

            $detail->put('qtyrec',$qtyrec);
            $detail->put('prod_name',$prodname);
            $data->push($detail);
        }

        return $data;
    }
}
