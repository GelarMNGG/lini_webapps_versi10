@extends('layouts.dashboard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Edit pic colaborative task'; 
    $formRouteShow = 'task-internal.show';
    $formRouteUpdate = 'task-internal-pic.update';
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

    <form class="w-100" action="{{ route($formRouteUpdate,$currentPicData->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- hidden data -->
        <input type="hidden" name="task_id" value="{{ $taskData->id }}">

        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md form-group">
                    <label for="">Choose PIC <small class="c-red">*</small></label>
                    <select name="pic_id" class="form-control select2{{ $errors->has('pic_id') ? ' has-error' : '' }}" required>
                        <?php
                            if(old('pic_id') != null) {
                                $pic = old('pic_id');
                            }elseif(isset($currentPicData->pic_id)){
                                $pic = $currentPicData->pic_id;
                            }else{
                                $pic = null;
                            }
                        ?>
                        @if ($pic != null)
                            @foreach ($picDatas as $data2)
                                @if ($data2->id == $pic)
                                    <option value="{{ $data2->id }}">{{ ucwords(strtolower($data2->firstname)).' '.ucwords($data2->lastname) }}</option>
                                @endif
                            @endforeach
                            @foreach($picDatas as $data2)
                                @if ($data2->id != $pic)
                                    <option value="{{ $data2->id }}">{{ ucwords(strtolower($data2->firstname)).' '.ucwords($data2->lastname) }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="">Choose PIC</option>
                            @foreach($picDatas as $data2)
                                <option value="{{ $data2->id }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                            @endforeach
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
