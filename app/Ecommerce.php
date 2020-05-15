<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ecommerce extends Model
{
    protected $table ='tblecommerce';
    protected $fillable = [
        'nama','kode_trx','logo'
    ];

    public $timestamps = true;
}
