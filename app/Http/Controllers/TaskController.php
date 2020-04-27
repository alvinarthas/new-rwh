<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\MenuMapping;
use App\Task;
use App\TaskEmployee;
use App\TaskGambar;
use App\TaskComment;
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
        $data = Task::getTask("task");
        $page = MenuMapping::getMap(session('user_id'),"EMTA");

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
        // echo "<pre>";
        // print_r($request->all());
        // die();
        // Validate
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string',
            'description' => 'required|string',
            'start_date'  => 'required',
            'due_date'    => 'required',
            'employee'    => 'required|array',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $task = new Task(array(
                // Informasi Pribadi
                'title'       => $request->title,
                'description' => $request->description,
                'start_date'  => $request->start_date,
                'due_date'    => $request->due_date,
                'is_it_done'  => 0,
                'creator'     => session('user_id'),
            ));

            try {
                if($task->save()){
                    $no=0;
                    foreach($request->gambar as $gmbr){
                        // Upload Foto
                        if($gmbr <> NULL|| $gmbr <> ''){
                            $gambar = ++$no.' '.$request->title.'.'.$gmbr->getClientOriginalExtension();
                            $gmbr->move(public_path('assets/images/task/'),$gambar);

                            $petunjuk = new TaskGambar(array(
                                'task_id' => $task->id,
                                'source'  => $gambar,
                            ));
                            $petunjuk->save();
                        }
                    }

                    foreach($request->employee as $emp){
                        $emtask = new TaskEmployee(array(
                            // Informasi Pribadi
                            'task_id' => $task->id,
                            'employee_id' => $emp,
                        ));
                        $emtask->save();
                    }
                    Log::setLog('EMTAC','Create Task: '.$task->id.' Title:'.$request->title);
                    return redirect()->route('task.index')->with('status','Task berhasil ditambahkan');
                }
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
        $page = $request->page;
        if ($request->ajax()) {
            if(TaskEmployee::where('task_id', $request->id)->where('employee_id', $request->employee_id)->first()){
                // echo session('role');
                // die();
                $te = TaskEmployee::where('task_id', $request->id)->where('employee_id', $request->employee_id)->first();
                $te->read = 1;
                $te->update();
            }

            $task = TaskEmployee::join('task', 'task_employee.task_id', 'task.id')->where('task_employee.task_id', $request->id)->get();
            $comment = TaskComment::where('task_id', $request->id)->get();

            if(!empty($comment)){
                $kendala = array();
                foreach($comment as $c){
                    $data = array(
                        'employee'   => $c->employee->name,
                        'comment'    => $c->comment,
                        'created_at' => date("Y-m-d, H:i", strtotime($c->created_at)),
                    );
                    array_push($kendala, $data);
                }
            }

            $source = TaskGambar::where('task_id', $request->id)->get();
            $count_source = TaskGambar::where('task_id', $request->id)->count();

            return response()->json(view('employee.task.modal',compact('task', 'kendala', 'page', 'source', 'count_source'))->render());
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

        $task = TaskEmployee::join('task', 'task_employee.task_id', 'task.id')->where('task_employee.task_id',$id)->select('task.id', 'task.title', 'task.start_date', 'task.due_date', 'task.description', 'task_employee.employee_id')->get();
        $source = TaskGambar::where('task_id', $id)->get();
        $count_source = TaskGambar::where('task_id', $id)->count();
        return view('employee.task.form',compact('employees','task', 'jenis', 'source', 'count_source'));
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
            'title'       => 'required|string',
            'description' => 'required|string',
            'start_date'  => 'required',
            'due_date'    => 'required',
            'employee'    => 'required|array',
        ]);
        // IF Validation fail
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        // Validation success
        }else{
            $task = Task::where('id',$id)->first();

            $task->title = $request->title;
            $task->description = $request->description;
            $task->start_date = $request->start_date;
            $task->due_date = $request->due_date;
            $task->is_it_done = 0;
            $task->creator = session('user_id');

            try {
                if($task->update()){
                    // Reset Employee Status when task updated
                    TaskEmployee::where('task_id', $task->id)->delete();
                    foreach($request->employee as $emp){
                        $emtask = new TaskEmployee(array(
                            'task_id' => $task->id,
                            'employee_id' => $emp,
                        ));
                        $emtask->save();
                    }

                    $no=TaskGambar::where('task_id', $id)->count();
                    if($no!=0){
                        $file = TaskGambar::where('task_id', $id)->latest()->first()->source;
                        $no = $file[0];
                    }
                    foreach($request->gambar as $gmbr){
                        // Upload Foto
                        if($gmbr <> NULL|| $gmbr <> ''){
                            $gambar = ++$no.' '.$request->title.'.'.$gmbr->getClientOriginalExtension();
                            $gmbr->move(public_path('assets/images/task/'),$gambar);

                            $petunjuk = new TaskGambar(array(
                                'task_id' => $task->id,
                                'source'  => $gambar,
                            ));
                            $petunjuk->save();
                        }
                    }

                    Log::setLog('EMTAU','Update Task: '.$id.' Title:'.$request->title);
                    return redirect()->route('task.index')->with('status','Task berhasil diupdate');
                }
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

    public function deleteImage($id)
    {
        try{
            $gambar = TaskGambar::where('id',$id)->first();
            if (file_exists(public_path('assets/images/task/').$gambar->source)) {
                unlink(public_path('assets/images/task/').$gambar->source);
            }
            $gambar->delete();
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

    public function ajxAddTaskComment(Request $request){
        $task_id = $request->id;
        $employee_id = $request->employee_id;
        $comment = $request->comment;

        $taskcomment = new TaskComment(array(
            'task_id'     => $task_id,
            'employee_id' => $employee_id,
            'comment'     => $comment,
        ));

        if($taskcomment->save()){
            $append = '<i class="ti-arrow-circle-right"></i> '.$taskcomment->employee->name.' ('.date("Y-m-d, H:i", strtotime($taskcomment->created_at)).') - '.$taskcomment->comment.'
            <br>';

            $data = array(
                'append' => $append,
            );
            return response()->json($data);
        }
    }

    public function taskDone($id, Request $request)
    {
        try{
            $data = Task::where('id', $id)->first();
            $data->is_it_done = 1;
            $data->update();

            if($request->page == "task"){
                return redirect()->route('task.index')->with('status','Task telah Selesai');
            }else{
                return redirect()->route('getHome')->with('status','Task telah Selesai');
            }
        }catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
