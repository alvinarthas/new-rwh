<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $table ='tblperusahaan';
    protected $fillable = [
        'nama','alamat','telp','creator','cp1', 'cp2', 'cp3'
    ];

    public $timestamps = true;
}
