<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturPembelian extends Model
{
    protected $table ='tblreturpb';
    protected $fillable = [
        'trx_id','tgl','supplier','username'
    ];
    public $timestamps = false;
}
