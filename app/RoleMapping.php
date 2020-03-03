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

    
    public function nonStaff(){
        $d = array_values(array_column(DB::select("SELECT e.id FROM tblemployee as e
        INNER JOIN tblemployeerole as rl on e.username = rl.username
        INNER JOIN tblrole as r ON r.id = rl.role_id
        WHERE r.role_name REGEXP 'Manager|General|Superadmin|Direktur|security'"),'id'));

        return $d;
    }
}
