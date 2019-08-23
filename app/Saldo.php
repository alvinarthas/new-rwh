<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $table ='tblsaldo';
    protected $fillable = [
        'customer_id', 'saldo_awal','saldo_skrng'
    ];
}
