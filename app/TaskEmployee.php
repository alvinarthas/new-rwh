<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskEmployee extends Model
{
    protected $table ='task_employee';
    protected $fillable = [
        'task_id', 'employee_id', 'status','read'
    ];

    public function task(){
        return $this->belongsTo('App\Task');
    }

    public function employee(){
        return $this->belongsTo('App\Employee');
    }
}
