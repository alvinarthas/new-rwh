<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonusPegawaiDet extends Model
{
    protected $table ='tbl_bonus_pegawai_detail';
    protected $fillable = [
        'bonus_pegawai_id', 'poin_internal','persen_internal','poin_logistik','persen_logistik','poin_kendali','persen_kendali','poin_top3','persen_top3','tunjangan_persen','total_persen'
    ];
}
