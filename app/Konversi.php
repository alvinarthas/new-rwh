<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Konversi extends Model
{
    protected $table ='tbl_konversi';
    protected $fillable = [
        'keterangan','creator','supplier'
    ];
    public function supplier(){
        return $this->belongsTo('App\Perusahaan','supplier','id');
    }

    public function creator(){
        return $this->belongsTo('App\Employee','creator','id');
    }
}
