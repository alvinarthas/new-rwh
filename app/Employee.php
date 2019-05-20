<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table ='tblemployee';
    protected $fillable = [
        'username', 'password','bck_pass','name','last_login','login_status','nip','address','phone','ktp','email','company_id','creator','tmpt_lhr','tgl_lhr','mulai_kerja','bank','norek','sima','simb','simc','npwp','bpjs','foto','scansima','scansimb','scansimc','scannpwp','scanbpjs','scanfoto','ktp','scanktp'
    ];

    protected $hidden = [
        'password', 'bck_pass',
    ];


    public function rolemapping(){
        return $this->belongsTo('App\RoleMapping','username','username');
    }
}
