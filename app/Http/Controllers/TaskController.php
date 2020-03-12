<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;
use App\TaskEmployee;
use App\Employee;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::all();
        return view('employee.task.index',compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employees = Employee::all();
        return view('employee.task.form',compact('employees'));
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
                return redirect()->route('role.index')->with('status','Role berhasil ditambahkan');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $taskemp = TaskEmployee::where('task_id',$id)->get();

        return $taskemp;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employees = Employee::all();
        $task = Task::where('id',$id)->first();
        return view('employee.task.form',compact('employees','task'));
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
            $task->due_date = session('user_id');

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
                return redirect()->route('role.index')->with('status','Role berhasil ditambahkan');

            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e);
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
            return redirect()->back()->withErrors($e->errorInfo);
        }
    }
}
