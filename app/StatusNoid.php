<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusNoid extends Model
{
    protected $table ='statusnoid';
    protected $fillable = [
        'id', 'status', 'deskripsi',
    ];
}
