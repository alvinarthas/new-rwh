@php
    use App\TaskEmployee;
@endphp
<div class="card-box table-responsive">
    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="row">
                    <div class="col-12">
                        <div class="p-20">
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Judul Tugas</label>
                                <div class="col-8">
                                    <input type="text" class="form-control" parsley-trigger="change" placeholder="Judul" required name="title" id="title" value="@isset($task[0]->title){{$task[0]->title}}@endisset" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Deskripsi Tugas</label>
                                <div class="col-8">
                                    <textarea class="form-control" name="description" id="description" placeholder="Deskripsi dan detail tugas yang diberikan" rows="4" cols="100" disabled>@isset($task[0]->description){{ $task[0]->description }}@endisset</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Deadline Pengerjaan</label>
                                <div class="col-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" parsley-trigger="change" required placeholder="yyyy/mm/dd" name="due_date" id="due_date" value="@isset($task[0]->due_date){{$task[0]->due_date}}@endisset" data-date-format="yyyy-mm-dd" autocomplete="off" disabled>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="ti-calendar"></i></span>
                                        </div>
                                    </div><!-- input-group -->
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Ditujukan kepada</label>
                                <div class="col-8">
                                    @foreach($task as $t)
                                        <i class="ti-angle-double-right"></i> {{ $t->employee->name }}
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(TaskEmployee::where('employee_id',session('user_id'))->where('task_id', $task[0]->task_id)->count() == 1)
            <div class="row pull-right">
                <form class="form-horizontal" role="form" action="{{ route('task.updatestatus', ['id' => $task[0]->id]) }}" enctype="multipart/form-data" method="POST">
                    {{ method_field('PUT') }}
                    @csrf
                    <input type="hidden" name="employee_id" value="{{session('user_id')}}">
                    <button class="btn btn-success waves-effect w-md waves-success m-b-5">Done <span class="mdi mdi-thumb-up-outline"></span></button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    $(".select2").select2({
        templateResult: formatState,
        templateSelection: formatState,
        closeOnSelect: false,
        allowClear: true,
    });
</script>
