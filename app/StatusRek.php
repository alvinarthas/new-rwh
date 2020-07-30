<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusRek extends Model
{
    protected $table ='statusrekening';
    protected $fillable = [
        'id', 'status', 'deskripsi',
    ];
}
