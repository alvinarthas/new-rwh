<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankMember extends Model
{
    protected $table ='bankmember';
    protected $fillable = [
        'ktp','bank_id','cabbank','norek','noatm','nobuku','creator','status','p_status','id_jurnal','scanatm','scantabungan'
    ];

    public static function getData($ktp){
        $bankmember = BankMember::where('ktp',$ktp)->select('norek','scantabungan','scanatm','status');
        $countbm = $bankmember->count();
        $data = collect();

        if($countbm > 1){
            $data = $bankmember->where('status', 1)->first();
        }else{
            $data = $bankmember->first();
        }

        return $data;
    }

    public function bank(){
        return $this->belongsTo('App\Bank');
    }

    public function statusrek(){
        return $this->belongsTo('App\StatusRek', 'status', 'id');
    }
}
