<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankMember_copy extends Model
{
    protected $table ='bankmember_copy';
    protected $fillable = [
        'ktp','bank_id','cabbank','norek','noatm','nobuku','creator','status','p_status','id_jurnal','scanatm','scantabungan'
    ];

    public static function getData($ktp){
        return BankMember::where('ktp',$ktp)->select('norek','scantabungan','scanatm')->first();
    }

    public function bank(){
        return $this->belongsTo('App\Bank');
    }
}
