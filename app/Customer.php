<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table ='tblcustomer';
    protected $fillable = [
        'cid','apname','apbp','apbd','apjt','apphone','apfax','apidc','apidcn','apidce','apemail','apadd','cicn','cicg','cilob','ciadd','cicty','cizip','cipro','ciweb','ciemail','cinpwp','ciphone','cifax','creator'
    ];
    public $timestamps = false;

}
