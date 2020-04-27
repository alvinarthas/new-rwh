<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskGambar extends Model
{
    protected $table ='task_gambar';
    protected $fillable = [
        'task_id', 'source'
    ];

    public function task(){
        return $this->belongsTo('App\Task');
    }
}
