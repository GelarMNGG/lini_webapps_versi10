@extends('layouts.dashboard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Edit check list colaborative task'; 
    $formRouteShow = 'task-internal.show';
    $formRouteUpdate = 'task-internal-todo.update';
?>
@endsection

@section('content')
<div class="flash-message mt-2">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
    @if ($errors->any())
        <p class="alert alert-danger">
            <small class="form-text">
                <strong>{{ $errors->first() }}</strong>
            </small>
        </p>
    @endif
</div>

<div class="card mt-2">
    <div class="card-header text-center bb-orange">
        <span class="text-uppercase"><strong>{{ ucfirst($pageTitle) }}</strong></span>
        <br><span class="text-uppercase text-info">{{ ucfirst($taskData->title) }}</span>
        <br><span class="small text-danger"><strong>{{ ucwords($todoData->pic_firstname).' '.ucwords($todoData->pic_lastname) }}</strong></span>
    </div>

    <form class="w-100" action="{{ route($formRouteUpdate,$currentTodoData->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- hidden data -->
        <input type="hidden" name="task_id" value="{{ $taskData->id }}">

        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md form-group">
                    <label for="">Check list name<small class="c-red">*</small></label>
                    <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ old('name') ? old('name') :  ucfirst($todoData->name) }}" placeholder="Check list name" required>
                </div>
                <div class="col-md-2 form-group">
                    <label for="">Status<small class="c-red">*</small></label>
                    <select name="status" class="form-control select2" required>
                        @if (!empty(old('status') || isset($currentTodoData->status)))
                            @if(old('status') == 1 || $currentTodoData->status == 1)
                                <option value="1">Done</option>
                                <option value="0">On progress</option>
                            @else
                                <option value="0">On progress</option>
                                <option value="1">Done</option>
                            @endif
                        @else
                            <option value="0">On progress</option>
                            <option value="1">Done</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <button type="submit" class="btn btn-orange" name="submit">Save</button>
                <a href="{{ route($formRouteShow,$taskData->id) }}" type="button" class="btn btn-blue-lini">Back</a>
            </div>
        </div>
    </form>


</div> <!-- container-fluid -->
@endsection

@section ('script')

<script>
</script>

@endsection
