<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonusGagal extends Model
{
    protected $table ='tblbonusgagal';
    protected $fillable = [
        'ktp','member_id','nama','bulan','tahun','bonus','creator','perusahaan'
    ];

    public $timestamps = false;

}
