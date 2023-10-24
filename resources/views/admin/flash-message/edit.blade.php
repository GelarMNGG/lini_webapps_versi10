@extends('layouts.dashboard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Edit pesan'; 
    $formRouteIndex = 'flash-messages.index';
    $formRouteUpdate = 'flash-messages.update';
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

    <form class="w-100" action="{{ route($formRouteUpdate, $flashMessageData->id) }}" method="POST" enctype="multipart/form-data">
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
                            }elseif(isset($flashMessageData->receiver_type)){
                                $receiver_type = $flashMessageData->receiver_type;
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
                            <option value="0">Pilih tipe user</option>
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
                            <select id="receiver_id" name="receiver_id" class="form-control{{ $errors->has('receiver_id') ? ' has-error' : '' }}" required>
                                <?php
                                    if(old('receiver_id') != null) {
                                        $receiver_id = old('receiver_id');
                                    }elseif(isset($flashMessageData->receiver_id)){
                                        $receiver_id = $flashMessageData->receiver_id;
                                    }else{
                                        $receiver_id = null;
                                    }
                                ?>
                                @if (isset($flashMessageData->receiver_id))
                                    <option value="{{ $userData->id }}">{{ ucfirst($userData->firstname).' '.ucfirst($userData->lastname) }}</option>
                                @elseif($receiver_id != null)
                                    <option value="{{ $receiver_id }}">{{ ucfirst(strtolower(old('receiver_id'))) }}</option>
                                @else
                                    <option value="0">Pilih user</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="">Tingkat prioritas <small class="c-red">*</small></label>
                            <select id="level" name="level" class="form-control select2{{ $errors->has('level') ? ' has-error' : '' }}" required>
                                <?php
                                    if(old('level') != null) {
                                        $level = old('level');
                                    }elseif(isset($flashMessageData->level)){
                                        $level = $flashMessageData->level;
                                    }else{
                                        $level = null;
                                    }
                                ?>
                                @if ($level != null)
                                    <option value="{{ strtolower($level) }}">{{ strtoupper($level) }}</option>
                                @else
                                    <option value="1">Pilih tingkat prioritas</option>
                                @endif
                                    <option value="success">SUCCESS - Normal</option>
                                    <option value="warning">WARNING - Sedang</option>
                                    <option value="danger">DANNGER - Penting</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m-0">
                <div class="col-md-9">
                    <div class="form-group">
                        <label>Pesan <small class="c-red">*</small></label>
                        <input type="text" name="message" class="form-control{{ $errors->has('message') ? ' has-error' : '' }}" value="{{ old('message') ? old('message') : strtolower($flashMessageData->message) }}" placeholder="Pesan" required>
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-group">
                        <label>View <small class="c-red">*</small></label>
                        <input type="number" name="views" class="form-control{{ $errors->has('views') ? ' has-error' : '' }}" min="0" value="{{ old('views') ? old('views') : $flashMessageData->views }}" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <button type="submit" class="btn btn-orange" name="submit">Kirim</button>
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
            url:"{{url('admin/get-user-list-all')}}?input_param="+stateID,
            success:function(res){        
            if(res){
                $("#receiver_id").empty();
                $.each(res,function(key,value){
                    if (value.user_type == '{{$currentUserType}}') {
                        if (value.id != {{$currentUserId}}) {
                            if (value.firstname != null) {
                                $("#receiver_id").append('<option value="'+value.id+'">'+ucwords(value.firstname ? value.firstname : '')+' '+ucwords(value.lastname ? value.lastname : '')+'</option>');
                            }else{
                                $("#receiver_id").append('<option value="'+value.id+'">'+ucwords(value.name)+' '+ucwords(value.firstname ? value.firstname : '')+' '+ucwords(value.lastname ? value.lastname : '')+'</option>');
                            }
                        }
                    }else{
                        if (value.firstname != null) {
                            $("#receiver_id").append('<option value="'+value.id+'">'+ucwords(value.firstname ? value.firstname : '')+' '+ucwords(value.lastname ? value.lastname : '')+'</option>');
                        }else{
                            $("#receiver_id").append('<option value="'+value.id+'">'+ucwords(value.name)+' '+ucwords(value.firstname ? value.firstname : '')+' '+ucwords(value.lastname ? value.lastname : '')+'</option>');
                        }
                    }
                });
            
            }else{
                $("#receiver_id").empty();
            }
            }
            });
        }else{
            $("#receiver_id").empty();
        }
    });
</script>

@endsection
