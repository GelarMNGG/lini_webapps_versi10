@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'edit task';
    $formRouteIndex = 'user-projects.show';
    $formRouteUpdate= 'user-projects-task.update';
    //procurement
    $back = 'admin-pr.index';
?>
@endsection

@section('content')
<div class="flash-message">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<div class="card mt-2">
    <div class="card-header text-center">
        Nama proyek: <strong><span class="text-info">{{ strtoupper($taskData->project_name) }}</span></strong>
        <br>Nama task: <strong><span class="text-danger">{{ isset($taskData->name) ? strtoupper($taskData->name) : 'Tidak ada data' }}</span></strong>
    </div>
    <div class="card-body">
        @if ($errors->any())
        <div class="col-md">
            <div class="alert alert-danger">
                <small class="form-text">
                    <strong>{{ $errors->first() }}</strong>
                </small>
            </div>
        </div>
        @endif
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="{{ route($formRouteUpdate, $taskData->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf
                        @method('PUT')
                        <!-- hidden data -->
                        <input type="text" name="status" value="{{ $taskData->status }}" hidden>
                        <input name="project_id" value="{{ $taskData->project_id }}" hidden>

                        <div class="row">
                            <div class="progress col-md mb-3">
                                <?php
                                    #default
                                    $taskStatus = 0;
                                    $progressValue = 0;
                                    $progressMax   = 4;
                                    #set conditions and value 
                                    if (isset($taskData->status)) {
                                        $taskStatus = $taskData->status;
                                        $progressValue = $taskStatus;
                                    }
                                ?>
                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ $progressValue }}" aria-valuemin="0" aria-valuemax="{{ $progressMax }}" style="width:{{ ($progressValue/$progressMax) * 100}}%">
                                    {{ ($progressValue/$progressMax) * 100}}% Selesai 
                                    @foreach($dataTaskStatus as $taskStatusName)
                                        @if($taskStatusName->id == $taskData->status)
                                            (tahap: {{ $taskStatusName->name }})
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- project -->
                            @if(Auth::user()->user_level == 22 && $userDepartment == 1)
                                <div class="w-100"></div>
                                <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Nama <small class="c-red">*</small></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : strtoupper($taskData->name) }}" data-parsley-minlength="3">
                                </div>
                                <div class="col-md mt-2 form-group{{ $errors->has('pm_id') ? ' has-error' : '' }}">
                                    <label for="pm_id">Project Manager</label>
                                    <input type="text" class="form-control" name="pm_id" value="{{ isset($taskData->pm_id) ? ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) : '' }}"  readonly>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md mt-2 form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                                    <label for="number">Nomor <small class="c-red">*</small></label>
                                    <input type="text" class="form-control" name="number" value="{{ old('number') ? old('number') : strtoupper($taskData->number) }}" data-parsley-minlength="3" >
                                </div>
                                <div class="col-md mt-2 form-group{{ $errors->has('budget') ? ' has-error' : '' }}">
                                    <label for="budget">Budget</label>
                                    <input type="number" class="form-control" name="budget" value="{{ old('budget') ? old('budget') : $taskData->budget }}">
                                </div>
                                <div class="w-100"></div>
                                <div class="w-100"></div>
                                <div class="col-md mt-3">
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-orange" name="submit" value="Ubah task">
                                        <a href="{{ route($formRouteIndex, $taskData->project_id) }}" class="btn btn-blue-lini">Batal</a>
                                    </div>
                                </div>
                            @endif
                            <!-- project -->
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- container-fluid -->
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'description' );
</script>
@endsection
