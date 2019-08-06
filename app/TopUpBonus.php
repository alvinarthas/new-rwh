<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopUpBonus extends Model
{
    protected $table ='tbltopupbonus';
    protected $fillable = [
        'no_rek','tgl','bonus', 'creator', 'id_jurnal', 'bank_id'
    ];

    public $timestamps = false;
}
