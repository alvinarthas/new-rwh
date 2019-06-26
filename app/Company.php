<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table ='tblcompany';
    protected $fillable = [
        'company_id','company_name','company_address', 'company_phone', 'company_email', 'company_est', 'company_ceo'
    ];

    public $timestamps = false;

    public function rolemapping(){
        return $this->belongsTo('App\RoleMapping','username','username');
    }
}
