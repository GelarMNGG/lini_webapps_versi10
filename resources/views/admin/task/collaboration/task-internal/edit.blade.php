@extends('layouts.dashboard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Edit internal-colaborative task'; 
    $formRouteShow = 'task-internal.show';
    $formRouteUpdate = 'task-internal.update';
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

    <form class="w-100" action="{{ route($formRouteUpdate, $taskData->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md">
                    <div class="form-group">
                        <label>Title <small class="c-red">*</small></label>
                        <input type="text" name="title" class="form-control{{ $errors->has('title') ? ' has-error' : '' }}" value="{{ old('title') ? old('title') : ucfirst($taskData->title) }}" placeholder="Title" required>
                    </div>
                </div>
                <div class="w-100"></div>
                <div class="col-md">
                    <div class="form-group{{ $errors->has('client_id') ? ' has-error' : '' }}">
                        <label for="client_id">Customer</label>
                        <select id="client_id" name="client_id" class="form-control select2" required>
                            <?php
                                if(old('client_id') != null) {
                                    $clientId = old('client_id');
                                }elseif(isset($taskData->client_id)){
                                    $clientId = $taskData->client_id;
                                }else{
                                    $clientId = null;
                                }
                            ?>
                            @if (!empty($clientId))
                                @foreach($clientDatas as $clientData)
                                    @if($clientData->id == $clientId)
                                        <option value="{{ $clientData->id }}">{{ ucwords($clientData->name)}}</option>
                                    @endif
                                @endforeach
                                @foreach($clientDatas as $clientData)
                                    @if($clientData->id != $clientId)
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
                            }if(isset($taskData->level) != null) {
                                $task_level = $taskData->level;
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
                    <input type="date" class="form-control{{ $errors->has('date_start') ? ' has-error' : '' }}" name="date_start" value="{{ old('date_start') ? old('date_start') : date('Y-m-d', strtotime($taskData->date_start)) }}" min="{{ date('Y-m-d,strtotime($taskData->date_start)') }}" placeholder="dd/mm/yyyy">
                    @if ($errors->has('date_start'))
                        <small class="form-text text-muted">
                            <strong>{{ $errors->first('date_start') }}</strong>
                        </small>
                    @endif
                </div>
                <div class="col-md form-group">
                    <label>End date</label>
                    <input type="date" class="form-control{{ $errors->has('date_end') ? ' has-error' : '' }}" name="date_end" value="{{ old('date_end') ? old('date_end') : date('Y-m-d',strtotime($taskData->date_end)) }}" min="{{ date('Y-m-d,strtotime($taskData->date_start)') }}" placeholder="dd/mm/yyyy">
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
                        }elseif(isset($coAdminDatas) != NULL) {
                            $taskCoAdmins = $coAdminDatas;
                        }else{
                            $taskCoAdmins = NULL;
                        }
                        if ($coAdminDatas == NULL) {
                            $coAdminDatas = [];
                        }
                    ?>
                    <select class="select2 select2-multiple" name="coadmin_id[]" multiple="multiple" multiple data-placeholder="Choose ...">
                        @if(isset($coAdmins))
                            @foreach($coAdmins as $coAdmin)
                                @if(!in_array($coAdmin->id,$coAdminDatas))
                                    <option value="{{ $coAdmin->id }}">{{ ucwords($coAdmin->firstname).' '.ucwords($coAdmin->lastname) }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    @if ($taskCoAdmins != null)
                        <span class="small"> Co admin yang sudah ditunjuk: </span>
                        @foreach($coAdmins as $coAdmin)
                            @if (in_array($coAdmin->id, $coAdminDatas))
                                <span class="badge badge-info">{{ ucwords($coAdmin->firstname).' '.ucwords($coAdmin->lastname) }}</span>
                            @endif
                        @endforeach
                    @endif
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group">
                    <label>Descriptions <small class="c-red">*</small></label>
                    <textarea id="description" name="description" class="form-control" cols="10" rows="5" required>{{ old('description') ? old('description') : $taskData->description }}</textarea>
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
