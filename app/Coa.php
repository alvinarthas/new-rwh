<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    protected $table ='tblcoa';
    protected $fillable = [
        'AccNo', 'AccName', 'SaldoNormal', 'StatusAccount', 'SaldoAwal', 'company_id', 'grup_id', 'StatusAcc', 'AccParent'
    ];

    public function grup(){
        return $this->belongsTo('App\CoaGrup');
    }
}
