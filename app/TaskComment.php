<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskComment extends Model
{
    protected $table ='task_comment';
    protected $fillable = [
        'task_id', 'employee_id', 'comment'
    ];

    public function task(){
        return $this->belongsTo('App\Task');
    }

    public function employee(){
        return $this->belongsTo('App\Employee');
    }
}
