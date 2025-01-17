<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PerusahaanMember extends Model
{
    protected $table ='perusahaanmember';
    protected $fillable = [
        'ktp','perusahaan_id','noid','passid','creator','posisi','status',
    ];

    public $timestamps = true;

    public function perusahaan(){
        return $this->belongsTo('App\Perusahaan','perusahaan_id', 'id');
    }

    public function statusnoid(){
        return $this->belongsTo('App\StatusNoid', 'status', 'id');
    }
}
