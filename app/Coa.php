<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    protected $table ='tblcoa';
    protected $fillable = [
        'AccNo', 'AccName', 'SaldoNormal', 'StatusAccount', 'SaldoAwal', 'company_id', 'AccParent'
    ];

    public static function checkChild($id){
        return Coa::where('AccParent',$id)->where('AccNo','NOT LIKE',$id)->count();
    }

    public static function getChild($id){
        return Coa::where('AccParent',$id)->where('AccNo','NOT LIKE',$id)->get();
    }
}
