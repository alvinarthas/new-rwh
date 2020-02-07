<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonusPegawai extends Model
{
    protected $table ='tbl_bonus_pegawai';
    protected $fillable = [
        'tugas_internal', 'logistik','kendali_perusahaan','top3','eom','total_bonus','month','year','employee_id','salary_det_id','bonus_divisi'
    ];

    public function employee(){
        return $this->belongsTo('App\Employee');
    }
}
