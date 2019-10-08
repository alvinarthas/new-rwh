<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReturPenjualan extends Model
{
    protected $table ='tblreturpj';
    protected $fillable = [
        'trx_id','tgl','customer','username'
    ];
    public $timestamps = false;
}
