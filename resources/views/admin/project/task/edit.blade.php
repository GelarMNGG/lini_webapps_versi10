@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'edit task';
    $formRouteIndex = 'admin-projects.show';
    $formRouteUpdate= 'admin-projects-task.update';
    //procurement
    $back = 'admin-pr.index';
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
        Proyek: <span class="text-uppercase"><strong>{{ ucwords($taskData->project_name) }}</strong></span>
    </div>
    <form action="{{ route($formRouteUpdate, $taskData->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method('PUT')

        <div class="card-body bg-gray-lini-2">    
            <!-- hidden data -->
            <input type="text" name="status" value="{{ $taskData->status }}" hidden>
            <input name="project_id" value="{{ $taskData->project_id }}" hidden>

            <div class="row">
                <!-- project -->
                @if($userDepartment == 1 || $userDepartment == 9)
                    <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name">Nama task<small class="c-red">*</small></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : ucfirst($taskData->name) }}" data-parsley-minlength="3" {{ $userDepartment == 1 ? 'required' : 'readonly' }}>
                    </div>
                    <div class="col-md mt-2 form-group{{ $errors->has('pm_id') ? ' has-error' : '' }}">
                        <label for="pm_id">Project Manager</label>
                        <input type="text" class="form-control" name="pm_id" value="{{ isset($taskData->pm_id) ? ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) : '' }}"  readonly>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-md mt-2 form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                        <label for="number">Nomor <small class="c-red">*</small></label>
                        <input type="text" class="form-control" name="number" value="{{ old('number') ? old('number') : strtoupper($taskData->number) }}" {{ $userDepartment == 1 ? 'required' : 'readonly' }}>
                    </div>
                    <div class="col-md mt-2 form-group{{ $errors->has('budget') ? ' has-error' : '' }}">
                        <label for="budget">Budget</label>
                        <input type="number" class="form-control" name="budget" value="{{ old('budget') ? old('budget') : $taskData->budget }}"  {{ $userDepartment == 1 ? '' : 'readonly' }}>
                    </div>
                    <div class="w-100"></div>
                    @if($userDepartment == 9 && $dataPRCount > 0)
                        <div class="col-md alert alert-warning mt-2 form-group{{ $errors->has('tech_id') ? ' has-error' : '' }}">
                            <label>Teknisi</label>
                            <select name="tech_id" class="form-control select2" required>
                            <?php 
                                //qct_id
                                if(old('tech_id') != null) {
                                    $tech_id = old('tech_id');
                                }elseif(isset($taskData->tech_id)){
                                    $tech_id = $taskData->tech_id;
                                }else{
                                    $tech_id = null;
                                }
                            ?>
                                @if($tech_id != null)
                                    @foreach($dataTechs as $dataTech)
                                        @if($dataTech->id == $tech_id)
                                            <option value="{{ $dataTech->id }}">{{ ucwords($dataTech->firstname).' '.ucwords($dataTech->lastname)}}</option>
                                        @endif
                                    @endforeach
                                    @foreach($dataTechs as $dataTech)
                                        @if($dataTech->id != $tech_id)
                                            <option value="{{ $dataTech->id }}">{{ ucwords($dataTech->firstname).' '.ucwords($dataTech->lastname)}}</option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="0">Pilih Teknisi</option>
                                    @foreach($dataTechs as $dataTech)
                                        <option value="{{ $dataTech->id }}">{{ ucwords($dataTech->firstname).' '.ucwords($dataTech->lastname)}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @endif
                @endif
                <!-- project -->
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <input type="submit" class="btn btn-orange" name="submit" value="Ubah task">
                @if($userDepartment == 9)
                    <a href="{{ route($back) }}" class="btn btn-blue-lini">Batal</a>
                @else
                    <a href="{{ route($formRouteIndex, $taskData->project_id) }}" class="btn btn-blue-lini">Batal</a>
                @endif
            </div>
        </div>
    </form>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'description' );
</script>
@endsection
