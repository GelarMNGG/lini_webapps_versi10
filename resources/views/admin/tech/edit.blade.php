@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'akun teknisi';
    $formRouteIndex = 'admin-tech.index';
    $formRouteUpdate= 'admin-tech.update';
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
    <div class="card-header text-center bb-orange text-uppercase">
        @if ($techData->firstname !== null)
            {{ ucfirst($pageTitle) }} | <strong>{{ ucfirst($techData->firstname).' '. ucfirst($techData->lastname) }}</strong>
        @else
            {{ ucfirst($pageTitle) }} | <strong>{{ ucfirst($techData->name) }}</strong>
        @endif
    </div>
    <form action="{{ route($formRouteUpdate, $techData->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method('PUT')
        <div class="card-body bg-gray-lini-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md col-md-6{{ $errors->has('image') ? ' has-error' : '' }}">
                        <div class="card-box">
                            <h4 class="header-title mb-3">Foto profil</h4>
                            <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('admintheme/images/users/'.$techData->image) }}"  />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md form-group{{ $errors->has('firstname') ? ' has-error' : '' }}">
                                <label for="firstname">Nama depan</label>
                                <input type="text" class="form-control" name="firstname" value="{{ old('firstname') !== null ? old('firstname') : $techData->firstname }}" data-parsley-minlength="3" required>
                            </div>
                            <div class="col-md form-group">
                                <label for="lastname">Nama belakang</label>
                                <input type="text" class="form-control" name="lastname" value="{{ old('lastname') !== null ? old('lastname') : $techData->lastname }}" required>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') !== null ? old('email') : $techData->email }}" required>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                                <label for="">Nomor handphone</label>
                                <input type="text" class="form-control" name="mobile"  value="{{ (old('mobile') !== null) ? old('mobile') : $techData->mobile }}" data-parsley-minlength="9" required>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('active') ? ' has-error' : '' }}">
                                <label for="">Status</label>
                                <select id="active" name="active" class="form-control" required>
                                    @if (old('active') == 1 || $techData->active == 1)
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    @else
                                        <option value="0">Inactive</option>
                                        <option value="1">Active</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md form-group{{ $errors->has('skill_id') ? ' has-error' : '' }}">
                                <label for="">Keahlian</label>
                                <select id="skill_id" name="skill_id" class="form-control select2" required>
                                    @if (old('skill_id') || $techData->skill_id)
                                        @foreach($skills as $skill)
                                            @if($skill->id == old('skill_id') || $skill->id == $techData->skill_id)
                                                <option value="{{ $skill->id }}">{{ ucwords($skill->name)}}</option>
                                            @endif
                                        @endforeach
                                        @foreach($skills as $skill)
                                            @if($skill->id != old('skill_id')  || $skill->id != $techData->skill_id)
                                                <option value="{{ $skill->id }}">{{ ucwords($skill->name)}}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="0">Pilih keahlian</option>
                                        @foreach($skills as $skill)
                                            <option value="{{ $skill->id }}">{{ ucwords($skill->name)}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md form-group{{ $errors->has('norek') ? ' has-error' : '' }}">
                        <label for="">No rekening</label>
                        <textarea type="text" class="form-control" name="norek" cols="10" rows="2" minlength="10">{{ old('norek') !== null ? old('norek') : $techData->norek }}</textarea>
                    </div>
                    <div class="col-md alert alert-warning">
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password">Password</label>
                            <input class="form-control" type="password" required name="password" id="password" data-parsley-minlength="6" value="{{ $techData->password }}">
                            @if ($errors->has('password'))
                                <small class="form-text text-muted">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </small>
                            @endif
                        </div>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-md form-group{{ $errors->has('note_proc') ? ' has-error' : '' }}">
                        <label for="">Catatan</label>
                        <textarea type="text" class="form-control" name="note_proc" cols="10" rows="5" minlength="10">{{ old('note_proc') !== null ? old('note_proc') : $techData->note_proc }}</textarea>
                    </div>
                    <div class="col-md form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="">Alamat</label>
                        <textarea type="text" class="form-control" name="address" cols="10" rows="5" minlength="10">{{ old('address') !== null ? old('address') : $techData->address }}</textarea>
                    </div>
                </div>
            </div> <!-- container-fluid -->
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
