<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DemoDevice extends Model
{
    protected $table ='demo_device';
    protected $fillable = [
        'device_name', 'sn','vc','ac','vkey'
    ];
}
