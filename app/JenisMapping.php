<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JenisMapping extends Model
{
    protected $table ='jenismapping';
    protected $fillable = [
        'mapping_id', 'jenis'
    ];  
}
