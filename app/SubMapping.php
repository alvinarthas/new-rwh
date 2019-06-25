<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubMapping extends Model
{
    protected $table ='sub_mapping';
    protected $fillable = [
        'submodul_id', 'jenis_id'
    ];  
}
