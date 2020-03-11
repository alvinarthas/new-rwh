<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table ='task';
    protected $fillable = [
        'title', 'description', 'due_date','creator'
    ];


    public static function checkPegawai($id){
        $count_task = TaskEmployee::where('task_id',$id)->count();
        $count_done = TaskEmployee::where('task_id',$id)->where('status',1)->count();
        $count_read = TaskEmployee::where('task_id',$id)->where('read',1)->count();

        if($count_done < $count_task){
            $status = "Belum Selesai";
        }else{
            $status = "Selesai";
        }

        if($count_read == 0){
            $read = "Belum di Baca";
        }elseif($count_read < $count_task){
            $read = "Belum Semua di Baca";
        }else{
            $read = "Sudah di Baca";
        }

        $data = array(
            'total' => $count_task,
            'done' => $status,
            'read' => $read,
        );

        return $data;
    }
}
