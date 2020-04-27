<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TaskEmployee;
use App\Employee;

class Task extends Model
{
    protected $table ='task';
    protected $fillable = [
        'title', 'description', 'start_date', 'due_date', 'is_it_done','creator'
    ];

    public static function getTask($page){
        if($page=="task"){
            $tasks = Task::all();
        }else{
            $tasks = Task::where('is_it_done', 0)->get();
        }

        $data = array();

        foreach($tasks as $t){
            $taskemployee = TaskEmployee::where('task_id', $t->id)->get();
            $read = 0;
            $reader = "Sudah : ";
            $notyet_read ="Belum : ";
            $status = 0;
            $count_read = 0;
            $count_status = 0;
            $already = "Sudah : ";
            $notyet_done = "Belum : ";

            foreach($taskemployee as $te){
                if($te->read == 1){
                    $read += 1;
                    $reader .= $te->employee->name.", ";
                }else{
                    $notyet_read .= $te->employee->name.", ";
                }

                if($te->status == 1){
                    $status += 1;
                    $already .= $te->employee->name.", ";
                }else{
                    $notyet_done .= $te->employee->name.", ";
                }
            }

            $count = TaskEmployee::where('task_id', $t->id)->count();

            if($read == $count){
                $count_read = "text-success";
            }else{
                $count_read = "text-danger";
            }

            if($status == $count){
                $count_status = "text-success";
            }else{
                $count_status = "text-danger";
            }

            $task = array(
                'id'          => $t->id,
                'title'       => $t->title,
                'description' => $t->description,
                'created_at'  => $t->created_at,
                'start_date'  => $t->start_date,
                'due_date'    => $t->due_date,
                'creator'     => Employee::where('id', $t->creator)->first()->name,
                'read'        => $read,
                'count_read'  => $count_read,
                'reader'      => $reader,
                'notyet_read' => $notyet_read,
                'status'      => $status,
                'count_status'=> $count_status,
                'already'     => $already,
                'notyet_done' => $notyet_done,
            );
            array_push($data, $task);
        }

        return $data;
    }


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
