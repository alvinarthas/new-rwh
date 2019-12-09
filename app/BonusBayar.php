<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BonusBayar extends Model
{
    protected $table ='tblbonusbayar';
    protected $primaryKey = 'id_bonus';
    protected $fillable = [
        'no_rek','tgl','bulan','tahun','bonus', 'creator', 'id_jurnal', 'AccNo', 'supplier'
    ];



}
