<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaldoHistory extends Model
{
    protected $table ='tblsaldohistory';
    protected $fillable = [
        'saldo_id', 'customer_id','jenis','amount','keterangan','creator','input_date'
    ];
}
