@extends('layouts.dashboard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Create multi-colaborative task'; 
    $formRouteIndex = 'user-task-leaders.index';
    $formRouteStore = 'user-task-leaders.store';
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
    <div class="card-header text-center text-uppercase bb-orange"><strong>{{ ucfirst($pageTitle) }}</strong></div>

    <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md">
                    <div class="form-group">
                        <label>Title <small class="c-red">*</small></label>
                        <input type="text" name="title" class="form-control{{ $errors->has('title') ? ' has-error' : '' }}" value="{{ old('title') ?? '' }}" placeholder="Title" required>
                    </div>
                </div>
                <div class="w-100"></div>
                <div class="col-md">
                    <div class="form-group{{ $errors->has('client_id') ? ' has-error' : '' }}">
                        <label for="client_id">Customer</label>
                        <select id="client_id" name="client_id" class="form-control select2" required>
                            @if (!empty(old('client_id')))
                                @foreach($clientDatas as $clientData)
                                    @if($clientData->id == old('client_id'))
                                        <option value="{{ $clientData->id }}">{{ ucwords($clientData->name)}}</option>
                                    @endif
                                @endforeach
                                @foreach($clientDatas as $clientData)
                                    @if($clientData->id != old('client_id'))
                                        <option value="{{ $clientData->id }}">{{ ucwords($clientData->name)}}</option>
                                    @endif
                                @endforeach
                            @else
                                <option value="0">Select customer</option>
                                @foreach($clientDatas as $clientData)
                                    <option value="{{ $clientData->id }}">{{ ucwords($clientData->name)}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md">
                    <label for="">Tingkat prioritas <small class="c-red">*</small></label>
                    <select id="level" name="level" class="form-control select2{{ $errors->has('level') ? ' has-error' : '' }}" required>
                        <?php
                            if(old('level') != null) {
                                $task_level = old('level');
                            }else{
                                $task_level = null;
                            }
                        ?>
                        @if ($task_level != null)
                            @foreach ($taskPriorities as $data2)
                                @if ($data2->tl_id == $task_level)
                                    <option value="{{ $level ?? $data2->tl_id }}">{{ ucwords(strtolower($data2->tl_name)) }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="1">Pilih tingkat prioritas</option>
                        @endif
                        @foreach($taskPriorities as $data2)
                            <option value="{{ $data2->tl_id }}">{{ ucwords($data2->tl_name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md form-group">
                    <label>Start date</label>
                    <input type="date" class="form-control{{ $errors->has('date_start') ? ' has-error' : '' }}" name="date_start" value="{{ old('date_start') ?? '' }}" min="{{ date('Y-m-d') }}" placeholder="dd/mm/yyyy">
                    @if ($errors->has('date_start'))
                        <small class="form-text text-muted">
                            <strong>{{ $errors->first('date_start') }}</strong>
                        </small>
                    @endif
                </div>
                <div class="col-md form-group">
                    <label>End date</label>
                    <input type="date" class="form-control{{ $errors->has('date_end') ? ' has-error' : '' }}" name="date_end" value="{{ old('date_end') ?? '' }}" min="{{ date('Y-m-d') }}" placeholder="dd/mm/yyyy">
                    @if ($errors->has('date_end'))
                        <small class="form-text text-muted">
                            <strong>{{ $errors->first('date_end') }}</strong>
                        </small>
                    @endif
                </div>
            </div>
            <div class="row m-0">
                <div class="col-md form-group">
                    <label>Co admins <small class="c-red">*</small></label>
                    <?php
                        if(old('coadmin_id[]') != NULL) {
                            $taskCoAdmins = old('coadmin_id[]');
                        }else{
                            $taskCoAdmins = [];
                        }
                    ?>
                    <select class="select2 select2-multiple" name="coadmin_id[]" multiple="multiple" multiple data-placeholder="Choose ...">
                        @if(isset($coAdmins))
                            @foreach($coAdmins as $coAdmin)
                                <option value="{{ $coAdmin->id }}">{{ ucwords($coAdmin->firstname).' '.ucwords($coAdmin->lastname) }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md form-group">
                    <label>Departments <small class="c-red">*</small></label>
                    <select class="select2 select2-multiple" name="receiver_department[]" multiple="multiple" multiple data-placeholder="Choose ..." required>
                        @if(isset($departmentDatas))
                            @foreach($departmentDatas as $departmentData)
                                <option value="{{ $departmentData->id }}">{{ ucwords($departmentData->name) }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group">
                    <label>Descriptions <small class="c-red">*</small></label>
                    <textarea id="description" name="description" class="form-control" cols="10" rows="5" required>{{ old('description') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <button type="submit" class="btn btn-orange" name="submit">Create</button>
                <a href="{{ route($formRouteIndex) }}" type="button" class="btn btn-blue-lini">Back</a>
            </div>
        </div>
    </form>


</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $('.datepicker').datepicker({
        dateFormat:'mm/dd/yy',
        startDate: new Date()
    });
</script>
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('description');
</script>
@endsection
