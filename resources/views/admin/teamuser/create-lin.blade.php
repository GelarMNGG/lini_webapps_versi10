@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Tambah staff';
    $formRouteIndex = 'teamuser.index';
    $formRouteStore = 'teamuser.store';
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
    
    <form action="{{ route($formRouteStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        <div class="card-body bg-gray-lini-2">
            <div class="row">
                <div class="col-md-6">
                    <div class="row mg-0">
                        <div class="col-md{{ $errors->has('image') ? ' has-error' : '' }}">
                            <div class="card-box">
                                <h4 class="header-title mb-3">Foto profil</h4>
                                <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('admintheme/images/users/default.png') }}"  />
                            </div>
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name">Nama panggilan</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') ?? old('name') }}" data-parsley-minlength="3" required>
                    </div>
                    <div class="row">
                        <div class="col-md form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                            <label for="firstname">Nama depan</label>
                            <input type="text" class="form-control" name="firstname" value="{{ old('firstname') ?? old('firstname') }}" data-parsley-minlength="3" required>
                        </div>
                        <div class="col-md form-group">
                            <label for="lastname">Nama belakang</label>
                            <input type="text" class="form-control" name="lastname" value="{{ old('lastname') ?? old('lastname') }}">
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') ?? old('email') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md alert alert-warning">
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password">Password</label>
                            <input class="form-control" type="password" required name="password" id="password" data-parsley-minlength="6" placeholder="Masukkan password">
                            @if ($errors->has('password'))
                                <small class="form-text text-muted">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </small>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Masukkan ulang password</label>
                            <input class="form-control" name="password_confirmation" type="password" data-parsley-equalto="#password" required placeholder="Masukkan ulang password">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                            <label for="">Nomor handphone</label>
                            <input type="text" class="form-control" name="mobile"  value="{{ old('mobile') ?? '' }}" placeholder="contoh: 62817717708" data-parsley-minlength="9" required>
                        </div>
                        <div class="col-md form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                            <label for="">Status</label>
                            <select id="active" name="active" class="form-control" required>
                                @if (!empty(old('active')) && old('active') == 1)
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                @else
                                    <option value="0">Inactive</option>
                                    <option value="1">Active</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('user_level') ? ' has-error' : '' }}">
                        <label for="">Jabatan </label>
                        <select id="user_level" name="user_level" class="form-control select2" required>
                            @if (old('user_level'))
                                @foreach($userLevels as $userLevel)
                                    @if($userLevel->id == old('user_level'))
                                        <option value="{{ $userLevel->role != null ? $userLevel->role : $userLevel->id }}">{{ ucwords($userLevel->name)}}</option>
                                    @endif
                                @endforeach
                                @foreach($userLevels as $userLevel)
                                    @if($userLevel->id != old('user_level'))
                                        <option value="{{ $userLevel->role != null ? $userLevel->role : $userLevel->id }}">{{ ucwords($userLevel->name)}}</option>
                                    @endif
                                @endforeach
                            @else
                                <option value="0">Pilih jabatan</option>
                                @foreach($userLevels as $userLevel)
                                    <option value="{{ $userLevel->role != null ? $userLevel->role : $userLevel->id }}">{{ ucwords($userLevel->name)}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group{{ $errors->has('department_id') ? ' has-error' : '' }}">
                        <label for="department_id">Departemen</label>

                        @if(strtolower(Auth::user()->user_type) == 'admin')
                            <select id="department_id" name="department_id" class="form-control select2" required>
                                @if (!empty(old('department_id')))
                                    @foreach($departments as $department)
                                        @if($department->id == old('department_id'))
                                            <option value="{{ $department->id }}">{{ ucwords($department->name)}}</option>
                                        @endif
                                    @endforeach
                                    @foreach($departments as $department)
                                        @if($department->id != old('department_id'))
                                            <option value="{{ $department->id }}">{{ ucwords($department->name)}}</option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="0">Pilih departemen</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ ucwords($department->name)}}</option>
                                    @endforeach
                                @endif
                            </select>
                        @else
                        <input type="text" class="form-control" name="department_id" value="{{ Auth::user()->department_id }}" hidden>
                        <select class="form-control" disabled>
                            @foreach($departments as $department)
                                @if($department->id == Auth::user()->department_id)
                                    <option value="{{ $department->id }}">{{ ucwords($department->name)}}</option>
                                @endif
                            @endforeach
                        </select>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="">Alamat</label>
                        <textarea type="text" class="form-control" name="address" cols="10" rows="5" minlength="10">{{ old('address') ?? old('address') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <input type="submit" class="btn btn-orange" name="submit" value="Simpan">
                <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini">Batal</a>
            </div>
        </div>
    </form>
</div> <!-- container-fluid -->
@endsection

@section ('script')

@endsection
