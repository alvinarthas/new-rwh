<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table ='tblrole';
    protected $fillable = [
        'id','role_name', 'company_id','creator','gaji_pokok','tunjangan_jabatan'
    ];
}
