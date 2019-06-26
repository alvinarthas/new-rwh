<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Koordinator extends Model
{
    protected $table ='tblkoordinator';
    protected $fillable = [
        'nama', 'alamat','ktp','telp','nama','memberid'
    ];
}
