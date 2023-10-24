@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'akun staff';
    $formRouteIndex = 'user-teamuser.index';
    $formRouteUpdate= 'user-teamuser.update';
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
        @if ($userProfile->firstname != null)
            <small>{{ ucfirst($pageTitle) }}</small> | <strong>{{ ucfirst($userProfile->firstname).' '. ucfirst($userProfile->lastname) }}</strong>
        @else
            <small>{{ ucfirst($pageTitle) }}</small> | <strong>{{ ucfirst($userProfile->name) }}</strong>
        @endif
    </div>
    <form action="{{ route($formRouteUpdate, $userProfile->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method('PUT')

        <div class="card-body bg-gray-lini-2">
            <div class="row">
                <div class="col-md-6">
                    <div class="row mg-0">
                        <div class="col card-box{{ $errors->has('image') ? ' has-error' : '' }}">
                            <h4 class="header-title mb-3">Foto profil</h4>
                            <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('admintheme/images/users/'.$userProfile->image) }}"  />
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                        <label for="firstname">Nama depan</label>
                        <input type="text" class="form-control" name="firstname" value="{{ old('firstname') !== null ? old('firstname') : $userProfile->firstname }}" data-parsley-minlength="3" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Nama belakang</label>
                        <input type="text" class="form-control" name="lastname" value="{{ old('lastname') !== null ? old('lastname') : $userProfile->lastname }}">
                    </div>
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') !== null ? old('email') : $userProfile->email }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md alert alert-warning">
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password">Password</label>
                            <input class="form-control" type="password" required name="password" id="password" data-parsley-minlength="6" value="{{ $userProfile->password }}">
                            @if ($errors->has('password'))
                                <small class="form-text text-muted">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </small>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                            <label for="">Nomor handphone</label>
                            <input type="text" class="form-control" name="mobile"  value="{{ (old('mobile') !== null) ? old('mobile') : $userProfile->mobile }}" data-parsley-minlength="9" required>
                        </div>
                        <div class="col-md form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                            <label for="">Status</label>
                            <select id="active" name="active" class="form-control" required>
                                @if (old('active') == 1 || $userProfile->active == 1)
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                @else
                                    <option value="0">Inactive</option>
                                    <option value="1">Active</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        <label for="">Jabatan</label>
                        <select id="user_level" name="user_level" class="form-control select2">
                        <?php 
                            //qct_id
                            if(old('user_level') != null) {
                                $user_level = old('user_level');
                            }elseif(isset($userProfile->user_level)){
                                $user_level = $userProfile->user_level;
                            }else{
                                $user_level = null;
                            }
                        ?>
                            @if ($user_level != null)
                                @foreach($userLevels as $userLevel)
                                    @if($userLevel->id == $user_level)
                                        @if($userLevel->role != Auth::user()->user_level)
                                            <option value="{{ $userLevel->role != null ? $userLevel->role : $userLevel->id }}">{{ ucwords($userLevel->name)}}</option>
                                        @endif
                                    @endif
                                @endforeach
                                @foreach($userLevels as $userLevel)
                                    @if($userLevel->id != $user_level)
                                        @if($userLevel->role != Auth::user()->user_level)
                                            <option value="{{ $userLevel->role != null ? $userLevel->role : $userLevel->id }}">{{ ucwords($userLevel->name)}}</option>
                                        @endif
                                    @endif
                                @endforeach
                            @else
                                <option value="0">Pilih jabatan</option>
                                @foreach($userLevels as $userLevel)
                                    @if($userLevel->role != Auth::user()->user_level)
                                        <option value="{{ $userLevel->role != null ? $userLevel->role : $userLevel->id }}">{{ ucwords($userLevel->name)}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group{{ $errors->has('department_id') ? ' has-error' : '' }}">
                        <label for="department_id">Departemen</label>
                        <select id="department_id" name="department_id" class="form-control select2" disabled>
                            @foreach($departments as $department)
                                @if($department->id == $userProfile->department_id)
                                    <option value="{{ $department->id }}">{{ ucwords($department->name)}}</option>
                                @endif
                            @endforeach
                        </select>
                        <input type="text" name="department_id" value="{{ $userProfile->department_id }}" hidden>
                    </div>
                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="">Alamat</label>
                        <textarea type="text" class="form-control" name="address" cols="10" rows="5" minlength="10">{{ old('address') !== null ? old('address') : $userProfile->address }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <input type="submit" class="btn btn-orange" name="submit" value="Ubah">
                <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini">Batal</a>
            </div>
        </div>
    </form>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script type=text/javascript>

    function ucwords (str) {
        return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
            return $1.toUpperCase();
        });
    }
    $('#province').on('change',function(){
        var stateID = $(this).val();  
        if(stateID){
            $.ajax({
            type:"GET",
            url:"{{url('get-city-list')}}?province_id="+stateID,
            success:function(res){        
            if(res){
                $("#city").empty();
                $.each(res,function(key,value){
                    $("#city").append('<option value="'+key+'">'+ucwords(value)+'</option>');
                });
            
            }else{
                $("#city").empty();
            }
            }
            });
        }else{
            $("#city").empty();
        }
    });
</script>
@endsection
