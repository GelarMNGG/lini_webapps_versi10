@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'akun contact person';
    $formRouteIndex = 'client.index';
    
    //contact person form
    $formClientContactUpdate = 'client-contact-person.update';
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
        @if ($contactData->firstname != null)
            <small>{{ ucfirst($pageTitle) }}</small> | <strong><span class="text-danger">{{ strtoupper($clientsData->name) }}</span> | {{ ucfirst($contactData->firstname).' '. ucfirst($contactData->lastname) }}</strong> 
        @else
            <small>{{ ucfirst($pageTitle) }}</small> | <strong><span class="text-danger">{{ strtoupper($clientsData->name) }} | {{ ucfirst($contactData->name) }}</span></strong>
        @endif
    </div>
    <form action="{{ route($formClientContactUpdate, $contactData->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method('PUT')

        <?php /*
        <div class="card-body bg-gray-lini-2">
            <div class="row">
                <div class="col-md-6">
                    <div class="row mg-0">
                        <div class="col card-box{{ $errors->has('image') ? ' has-error' : '' }}">
                            <h4 class="header-title mb-3">Foto profil</h4>
                            <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('admintheme/images/users/'.$contactData->image) }}"  />
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                        <label for="firstname">Nama depan</label>
                        <input type="text" class="form-control" name="firstname" value="{{ old('firstname') !== null ? old('firstname') : $contactData->firstname }}" data-parsley-minlength="3" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Nama belakang</label>
                        <input type="text" class="form-control" name="lastname" value="{{ old('lastname') !== null ? old('lastname') : $contactData->lastname }}">
                    </div>
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') !== null ? old('email') : $contactData->email }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md alert alert-warning">
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password">Password</label>
                            <input class="form-control" type="password" required name="password" id="password" data-parsley-minlength="6" value="{{ $contactData->password }}">
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
                            <input type="text" class="form-control" name="mobile"  value="{{ (old('mobile') !== null) ? old('mobile') : $contactData->mobile }}" data-parsley-minlength="9" required>
                        </div>
                        <div class="col-md form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                            <label for="">Status</label>
                            <select id="active" name="active" class="form-control" required>
                                @if (old('active') == 1 || $contactData->active == 1)
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
                    </div>
                    <div class="form-group{{ $errors->has('department_id') ? ' has-error' : '' }}">
                        <label for="department_id">Departemen</label>
                        <input type="text" name="department_id" value="{{ $contactData->department_id }}" hidden>
                    </div>
                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="">Alamat</label>
                        <textarea type="text" class="form-control" name="address" cols="10" rows="5" minlength="10">{{ old('address') !== null ? old('address') : $contactData->address }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        */ ?>

        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md-6">
                    <div class="row mg-0">
                        <div class="col-md{{ $errors->has('image') ? ' has-error' : '' }}">
                            <div class="card-box">
                                <h4 class="header-title mb-3">Foto profil</h4>
                                <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('admintheme/images/users/'.$contactData->image) }}"  />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md alert alert-warning">
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password">Password</label>
                            <input class="form-control" type="password" required name="password" id="password" data-parsley-minlength="6" value="{{ $contactData->password }}">
                            @if ($errors->has('password'))
                                <small class="form-text text-muted">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </small>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                            <label for="">Nomor handphone</label>
                            <input type="text" class="form-control" name="mobile"  value="{{ (old('mobile') !== null) ? old('mobile') : $contactData->mobile }}" data-parsley-minlength="9" required>
                        </div>
                        <div class="col-md form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                            <label for="">Status</label>
                            <select id="active" name="active" class="form-control" required>
                                @if (old('active') == 1 || $contactData->active == 1)
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                @else
                                    <option value="0">Inactive</option>
                                    <option value="1">Active</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label for="">Share</label>
                            <select name="status" class="form-control" required>
                                @if (!empty(old('status')) && old('status') == 1 || $contactData->status == 1)
                                    <option value="1">Shared</option>
                                    <option value="0">Private</option>
                                @else
                                    <option value="0">Private</option>
                                    <option value="1">Shared</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name">Nama panggilan</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : $contactData->name }}" data-parsley-minlength="3" required>
                    </div>
                    <div class="row">
                        <div class="col-md form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                            <label for="firstname">Nama depan</label>
                            <input type="text" class="form-control" name="firstname" value="{{ old('firstname') ? old('firstname') : $contactData->firstname }}" data-parsley-minlength="3" required>
                        </div>
                        <div class="col-md form-group">
                            <label for="lastname">Nama belakang</label>
                            <input type="text" class="form-control" name="lastname" value="{{ old('lastname') ? old('lastname') : $contactData->lastname }}">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') ? old('email') : $contactData->email }}" required>
                    </div>
                    <div class="form-group{{ $errors->has('user_level') ? ' has-error' : '' }}">
                        <label for="">Jabatan </label>
                        <input type="text" class="form-control" name="title" value="{{ old('title') ? old('title') : $contactData->title }}">
                    </div>
                </div>

                <div class="w-100"></div>
                <div class="col-md">
                    <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                        <label for="">Note</label>
                        <textarea type="text" class="form-control" name="note" cols="10" rows="5" minlength="10">{{ old('note') ? old('note') : $contactData->note }}</textarea>
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="">Alamat</label>
                        <textarea type="text" class="form-control" name="address" cols="10" rows="5" minlength="10">{{ old('address') !== null ? old('address') : $contactData->address }}</textarea>
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

</script>
@endsection
