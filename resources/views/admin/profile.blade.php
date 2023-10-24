@extends('layouts.dashboard-form')

@section ('data') 
<?php ### customized data
    $pageTitle      = 'Profile';
    $formProfileUpdate = 'profil-admin.update';
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

    <form action="{{ route($formProfileUpdate, $userProfile->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method ('PUT')

        <div class="card-body bg-gray-lini-2">
            <div class="row">
                <div class="col-md-6">
                    <div class="row mg-0">
                        <div class="col{{ $errors->has('image') ? ' has-error' : '' }}">
                            <div class="card-box">
                                <h4 class="header-title mb-3">Foto profil</h4>
                                <input type="file" name="image" class="dropify" data-max-file-size="4M" data-default-file="{{ asset('admintheme/images/users/'.$userProfile->image) }}"  />
                                <small>Ukuran ideal adalah <strong>kotak</strong>.</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                        <label for="firstname">Nama depan</label>
                        <input type="text" class="form-control" name="firstname" value="{{ old('firstname') !== null ? old('firstname') : $userProfile->firstname }}" data-parsley-minlength="3" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Nama belakang</label>
                        <input type="text" class="form-control" name="lastname" value="{{ old('lastname') !== null ? old('lastname') : $userProfile->lastname }}" required>
                    </div>
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') !== null ? old('email') : $userProfile->email }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                        <label for="">Nomor handphone</label>
                        <input type="text" class="form-control" name="mobile"  value="{{ old('mobile') !== null ? old('mobile') : $userProfile->mobile }}" data-parsley-minlength="10" required>
                    </div>
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        <label for="">Jabatan</label>
                        @if(Auth::user()->company_id == 1 && Auth::user()->department_id == 5)
                            <input type="text" class="form-control" name="title"  value="{{ old('title') ? old('title') : $userProfile->title }}" placeholder="" data-parsley-minlength="2" required>
                        @else
                            <input type="text" class="form-control" name="title"  value="{{ old('title') ? old('title') : $userProfile->title }}" placeholder="" data-parsley-minlength="2" readonly>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('department_id') ? ' has-error' : '' }}">
                        <label for="department_id">Departemen</label>
                        <select id="department_id" name="department_id" class="form-control select2" disabled>
                            @if (!empty(old('department_id')) || !empty($userProfile->department_id))
                                @foreach($departments as $department)
                                    @if($department->id == old('department_id') || $department->id == $userProfile->department_id)
                                        <option value="{{ $department->id }}">{{ ucwords($department->name)}}</option>
                                    @endif
                                @endforeach
                                @foreach($departments as $department)
                                    @if($department->id != old('department_id') && $department->id != $userProfile->department_id)
                                        <option value="{{ $department->id }}">{{ ucwords($department->name)}}</option>
                                    @endif
                                @endforeach
                            @else
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ ucwords($department->name)}}</option>
                                @endforeach
                            @endif
                        </select>
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
                <a href="{{ route($dashboardLink) }}" class="btn btn-blue-lini">Batal</a>
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
