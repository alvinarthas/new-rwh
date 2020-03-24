<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\MenuMapping;
use App\Task;
use App\TaskEmployee;
use App\Employee;
use App\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = MenuMapping::getMap(session('user_id'),"EMTA");
        $tasks = Task::all();
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

        return view('employee.task.index',compact('data', 'page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $jenis = "create";
        $employees = Employee::all();
        return view('employee.task.form',compact('employees', 'jenis'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'due_date' => 'required',
            'employee' => 'required|array',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $task = new Task(array(
                // Informasi Pribadi
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'creator' => session('user_id'),
            ));
            try {
                $task->save();

                foreach($request->employee as $emp){
                    $emtask = new TaskEmployee(array(
                        // Informasi Pribadi
                        'task_id' => $task->id,
                        'employee_id' => $emp,
                    ));

                    $emtask->save();
                }
                Log::setLog('EMTAC','Create Task: '.$task->id.' Title:'.$request->title);
                return redirect()->route('task.create')->with('status','Task berhasil ditambahkan');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        if ($request->ajax()) {
            $te = TaskEmployee::where('task_id', $request->id)->where('employee_id', $request->employee_id)->first();
            $te->read = 1;
            $te->update();

            $task = TaskEmployee::join('task', 'task_employee.task_id', 'task.id')->where('task_employee.task_id', $request->id)->get();

            return response()->json(view('employee.task.modal',compact('task'))->render());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $jenis = "edit";
        $employee_id = array();
        $employee = TaskEmployee::where('task_id', $id)->select('employee_id')->get();
        foreach($employee as $e){
            array_push($employee_id, $e->employee_id);
        }
        $employees = Employee::whereNotIn('id', $employee_id)->get();

        $task = TaskEmployee::join('task', 'task_employee.task_id', 'task.id')->where('task_employee.task_id',$id)->select('task.id', 'task.title', 'task.due_date', 'task.description', 'task_employee.employee_id')->get();
        return view('employee.task.form',compact('employees','task', 'jenis'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // echo "<pre>";
        // print_r($request->all());
        // die();
        // Validate
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'due_date' => 'required',
            'employee' => 'required|array',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $task = Task::where('id',$id)->first();

            $task->title = $request->title;
            $task->description = $request->description;
            $task->due_date = $request->due_date;
            $task->creator = session('user_id');

            try {
                $task->update();

                foreach($request->employee as $emp){
                    $emtsk = TaskEmployee::where('employee_id',$emp)->where('task_id',$task->id)->count();

                    if($emtsk == 0){
                        $emtask = new TaskEmployee(array(
                            // Informasi Pribadi
                            'task_id' => $task->id,
                            'employee_id' => $emp,
                        ));

                        $emtask->save();
                    }

                }

                Log::setLog('EMTAU','Update Task: '.$id.' Title:'.$request->title);
                return redirect()->route('task.index')->with('status','Task berhasil diupdate');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            Task::where('id',$id)->delete();
            Log::setLog('EMTAD','Delete Task:'.$id);
            return "true";
        // fail
        }catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function statusUpdate(Request $request, $id)
    {
        try{
            $data = TaskEmployee::where('task_id', $id)->where('employee_id', $request->employee_id)->first();
            // echo "<pre>";
            // print_r($data);
            // die();
            $data->status = 1;
            $data->update();

            Log::setLog('EMTAU','Update Task: '.$id.' Title:'.$request->title.' Employee : '.$request->employee_id.' Status : Done');
            if($request->page == "task"){
                return redirect()->route('task.index')->with('status','Task Selesai!');
            }else{
                return redirect()->route('getHome')->with('status','Task selesai!');
            }
        }catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
