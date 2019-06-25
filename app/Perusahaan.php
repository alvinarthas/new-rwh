<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $table ='tblperusahaan';
    protected $fillable = [
        'nama','alamat','telp','creator'
    ];

    public $timestamps = false;

    public function rolemapping(){
        return $this->belongsTo('App\RoleMapping','username','username');
    }
}
