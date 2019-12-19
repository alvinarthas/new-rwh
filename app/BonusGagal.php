<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonusGagal extends Model
{
    protected $table ='tblbonusgagal';
    protected $fillable = [
        'tgl','jenis','file','creator'
    ];

    public $timestamps = true;

}
