<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleMapping extends Model
{
    protected $table ='tblemployeerole';
    protected $fillable = [
        'username', 'company_id','role_id'
    ];

    public function role(){
        return $this->belongsTo('App\Role');
    }
}
