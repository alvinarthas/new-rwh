<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonusBayar extends Model
{
    protected $table ='tblbonusbayar';
    protected $fillable = [
        'no_rek','tgl','bulan','tahun','bonus', 'creator', 'id_jurnal', 'AccNo'
    ];

}
