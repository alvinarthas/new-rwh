<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $table ='tblsaldo';
    protected $fillable = [
        'customer_id','jenis','amount','keterangan','creator','tanggal'
    ];
}
