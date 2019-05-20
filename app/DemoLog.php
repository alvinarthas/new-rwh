<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DemoLog extends Model
{
    protected $table ='demo_log';
    protected $fillable = [
        'log_time', 'user_name','data','keterangan'
    ];
}
