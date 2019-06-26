<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table ='tblbank';

    public static function bankMember(){
        return Bank::join('bankmember','tblbank.id','=','bankmember.bank_id')->groupBy('bankmember.bank_id')->orderBy('bankmember.bank_id','asc')->select('tblbank.id','tblbank.nama','tblbank.icon')->distinct()->get();
    }
}
