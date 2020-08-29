<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ecommerce extends Model
{
    protected $table ='tblecommerce';
    protected $fillable = [
        'nama','kode_trx','logo'
    ];

    public $timestamps = true;

    public static function getKode(){
        $data = collect();

        foreach(Ecommerce::select('id','kode_trx')->get() as $key){
            $data->put($key->id,$key->kode_trx);
        }
        $data->put(0,'SO');

        return $data->toArray();
    }
}
