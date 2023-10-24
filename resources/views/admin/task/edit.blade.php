@extends('layouts.dashboard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Edit tugas'; 
    $formRouteIndex = 'task.index';
    $formRouteUpdate = 'task.update';
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

    <form class="w-100" action="{{ route($formRouteUpdate, $taskData->task_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md form-group">
                    <label for="">Tipe user <small class="c-red">*</small></label>
                    <select id="receiver_type" name="receiver_type" class="form-control select2{{ $errors->has('receiver_type') ? ' has-error' : '' }}" required>
                        <?php
                            if(old('receiver_type') != null) {
                                $receiver_type = old('receiver_type');
                            }elseif(isset($taskData->receiver_type)){
                                $receiver_type = $taskData->receiver_type;
                            }else{
                                $receiver_type = null;
                            }
                        ?>
                        @if ($receiver_type != null)
                            @foreach ($userTypes as $userType)
                                @if ($userType->name == $receiver_type)
                                    <option value='{{ strtolower($receiver_type) }}'>{{ ucwords(strtolower($userType->name)) }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="">Pilih tipe user</option>
                        @endif
                        @foreach($userTypes as $userType)
                            <option value="{{ strtolower($userType->name) }}">{{ ucwords($userType->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md form-group">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="">Nama user <small class="c-red">*</small></label>
                            <select id="task_receiver_id" name="task_receiver_id" class="form-control{{ $errors->has('task_receiver_id') ? ' has-error' : '' }}" required>
                                <?php
                                    if(old('task_receiver_id') != null) {
                                        $task_receiver_id = old('task_receiver_id');
                                    }elseif(isset($taskData->task_receiver_id)){
                                        $task_receiver_id = $taskData->task_receiver_id;
                                    }else{
                                        $task_receiver_id = null;
                                    }
                                ?>
                                @if (isset($taskData->task_receiver_id))
                                    <option value="{{ $dataReceiver->id }}">{{ ucfirst($dataReceiver->firstname).' '.ucfirst($dataReceiver->lastname) }}</option>
                                @elseif($task_receiver_id != null)
                                    <option value="{{ $task_receiver_id }}">{{ ucfirst(strtolower(old('kota_tujuan_name'))) }}</option>
                                @else
                                    <option value="">Pilih user</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="">Tingkat prioritas <small class="c-red">*</small></label>
                            <select id="task_level" name="task_level" class="form-control select2{{ $errors->has('task_level') ? ' has-error' : '' }}" required>
                                <?php
                                    if(old('task_level') != null) {
                                        $task_level = old('task_level');
                                    }elseif(isset($taskData->task_level)){
                                        $task_level = $taskData->task_level;
                                    }else{
                                        $task_level = null;
                                    }
                                ?>
                                @if ($task_level != null)
                                    @foreach ($taskPriorities as $data2)
                                        @if ($data2->tl_id == $task_level)
                                            <option value="{{ $task_level ?? $data2->tl_id }}">{{ ucwords(strtolower($data2->tl_name)) }}</option>
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
                    </div>
                </div>
            </div>
            <div class="row m-0">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Judul tugas <small class="c-red">*</small></label>
                        <input type="text" name="task_title" class="form-control{{ $errors->has('task_title') ? ' has-error' : '' }}" value="{{ old('task_title') ? old('task_title') : ucfirst($taskData->task_title) }}" placeholder="Judul tugas" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md form-group">
                            <label>Tanggal mulai</label>
                            <input type="date" class="form-control{{ $errors->has('task_date') ? ' has-error' : '' }}" name="task_date" value="{{ old('task_date') ? old('task_date') : date('Y-m-d', strtotime($taskData->task_date)) }}" min="{{ date('Y-m-d') }}" placeholder="dd/mm/yyyy">
                            @if ($errors->has('task_date'))
                                <small class="form-text text-muted">
                                    <strong>{{ $errors->first('task_date') }}</strong>
                                </small>
                            @endif
                        </div>
                        <div class="col-md form-group">
                            <label>Tanggal akhir</label>
                            <input type="date" class="form-control{{ $errors->has('task_due_date') ? ' has-error' : '' }}" name="task_due_date" value="{{ old('task_due_date') ? old('task_due_date') : date('Y-m-d', strtotime($taskData->task_due_date)) }}" min="{{ date('Y-m-d') }}" placeholder="dd/mm/yyyy">
                            @if ($errors->has('task_due_date'))
                                <small class="form-text text-muted">
                                    <strong>{{ $errors->first('task_due_date') }}</strong>
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m-0" style="display:inline">
                <div class="col-md">
                    <label>Deskripsi tugas <small class="c-red">*</small></label>
                    <textarea name="task_desc" class="form-control" cols="10" rows="5" required>{{ old('task_desc') ? old('task_desc') : $taskData->task_desc }}</textarea>
                </div>
            </div>
            <div class="w-100 mt-2"></div>
            <div class="col-md alert alert-warning">
                <label for="">Bobot </label>
                <input type="number" name="grade" class="form-control{{ $errors->has('grade') ? ' has-error' : '' }}" value="{{ old('grade') ? old('grade') : $taskData->grade }}" placeholder="Bobot">
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <button type="submit" class="btn btn-orange" name="submit">Simpan</button>
                <a href="{{ route($formRouteIndex) }}" type="button" class="btn btn-blue-lini">Kembali</a>
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

    function ucwords (str) {
        return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
            return $1.toUpperCase();
        });
    }

    $('#receiver_type').on('change',function(){
        var stateID = $(this).val();  
        if(stateID){
            $.ajax({
            type:"GET",
            url:"{{url('admin/get-user-list')}}?input_param="+stateID,
            success:function(res){        
            if(res){
                $("#task_receiver_id").empty();
                $.each(res,function(key,value){
                    if (value.user_type == '{{$currentUserType}}') {
                        if (value.id != {{$currentUserId}}) {
                            if (value.firstname != null) {
                                $("#task_receiver_id").append('<option value="'+value.id+'">'+ucwords(value.firstname ? value.firstname : '')+' '+ucwords(value.lastname ? value.lastname : '')+'</option>');
                            }else{
                                $("#task_receiver_id").append('<option value="'+value.id+'">'+ucwords(value.name)+' '+ucwords(value.firstname ? value.firstname : '')+' '+ucwords(value.lastname ? value.lastname : '')+'</option>');
                            }
                        }
                    }else{
                        if (value.firstname != null) {
                            $("#task_receiver_id").append('<option value="'+value.id+'">'+ucwords(value.firstname ? value.firstname : '')+' '+ucwords(value.lastname ? value.lastname : '')+'</option>');
                        }else{
                            $("#task_receiver_id").append('<option value="'+value.id+'">'+ucwords(value.name)+' '+ucwords(value.firstname ? value.firstname : '')+' '+ucwords(value.lastname ? value.lastname : '')+'</option>');
                        }
                    }
                });
            
            }else{
                $("#task_receiver_id").empty();
            }
            }
            });
        }else{
            $("#task_receiver_id").empty();
        }
    });
</script>

@endsection
