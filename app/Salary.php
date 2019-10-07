<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $table ='tbl_salary';
    protected $fillable = [
        'month', 'year','bv','hari_kerja','creator'
    ];
}
