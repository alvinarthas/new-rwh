<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PerusahaanMember_copy extends Model
{
    protected $table ='perusahaanmember_copy';
    protected $fillable = [
        'ktp','perusahaan_id','noid','passid','creator','posisi',
    ];

    public $timestamps = true;

    public function perusahaan(){
        return $this->belongsTo('App\Perusahaan');
    }
}
