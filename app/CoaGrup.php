<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoaGrup extends Model
{
    protected $table ='tblcoagrup';
    protected $fillable = [
        'grup'
    ];

    public $timestamps = false;
}
