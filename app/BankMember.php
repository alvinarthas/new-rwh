<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankMember extends Model
{
    protected $table ='bankmember';
    protected $fillable = [
        'ktp','bank_id','cabbank','norek','noatm','nobuku','creator','status','p_status','id_jurnal'
    ];

    public static function getData($ktp){
        return BankMember::where('ktp',$ktp)->select('norek','scantabungan','scanatm')->first();
    }
}
