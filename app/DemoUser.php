<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DemoUser extends Model
{
    protected $table ='demo_user';
    protected $fillable = [
        'user_id', 'user_name','employee_id'
    ];

    public function user(){
        return $this->belongsTo('App\Employee');
    }
}
