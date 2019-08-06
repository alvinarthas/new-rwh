<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    protected $table ='tblcoa';
    protected $fillable = [
        'id', 'AccNo', 'AccName', 'SaldoNormal', 'StatusAccount', 'SaldoAwal', 'company_id', 'grup', 'StatusAcc', 'AccParent'
    ];

    public $timestamps = false;
}
