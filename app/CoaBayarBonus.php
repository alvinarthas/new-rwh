<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoaBayarBonus extends Model
{
    protected $table ='tblcoabonusbayar';
    protected $fillable = [
        'id_coa'
    ];

    public $timestamps = false;
}
