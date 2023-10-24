@extends('layouts.dashboard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Add pic colaborative task'; 
    $formRouteShow = 'task-leaders.show';
    $formRouteStore = 'task-leaders-pic.store';
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
    <div class="card-header text-center text-uppercase bb-orange">
        <strong>{{ ucfirst($pageTitle) }}</strong>
        <br><span class="text-info">{{ ucfirst($taskData->title) }}</span>
    </div>

    <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- hidden data -->
        <input type="hidden" name="task_id" value="{{ $taskData->id }}">
        <input type="hidden" name="department_id" value="{{ $departmentId }}">

        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md form-group">
                    <label for="">Choose PIC <small class="c-red">*</small></label>
                    <select name="pic_id" class="form-control select2{{ $errors->has('pic_id') ? ' has-error' : '' }}" required>
                        <?php
                            if(old('pic_id') != null) {
                                $pic = old('pic_id');
                            }else{
                                $pic = null;
                            }
                        ?>
                        @if ($pic != null)
                            @foreach ($picDatas as $data2)
                                @if ($data2->id == $pic)
                                    <option value="{{ $pic_id ?? $data2->id }}">{{ ucwords(strtolower($data2->firstname)).' '.ucwords($data2->lastname) }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="">Choose PIC</option>
                        @endif
                        @foreach($picDatas as $data2)
                            <option value="{{ $data2->id }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <button type="submit" class="btn btn-orange" name="submit">Create</button>
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
