<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $table ='tbl_salary_detail';
    protected $fillable = [
        'salary_id', 'employee_id','bonus','gaji_pokok','tunjangan_jabatan','bonus_jabatan','take_home_pay'
    ];
}
