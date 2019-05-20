<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DemoFinger extends Model
{
    protected $table ='demo_finger';
    protected $fillable = [
        'user_id', 'finger_id','finger_data'
    ];
}
