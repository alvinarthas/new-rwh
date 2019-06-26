<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubKoordinator extends Model
{
    protected $table ='tblsubkoordinator';
    protected $fillable = [
        'nama', 'alamat','ktp','telp','nama','memberid','creator','created'
    ];
}
