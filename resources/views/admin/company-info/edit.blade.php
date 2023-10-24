@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle       = 'company info';
    $formRouteUpdate = 'company-info.update';
?>
@endsection

@section('content')
<div class="flash-message">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<div class="card">
    <div class="card-header text-center">
        {{ ucfirst($pageTitle) }}
    </div>
    <div class="card-body">
        @if ($errors->any())
        <div class="col-md">
            <div class="alert alert-danger">
                <small class="form-text">
                    <strong>{{ $errors->first() }}</strong>
                </small>
            </div>
        </div>
        @endif
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="{{ route($formRouteUpdate, $companyData->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mg-0">
                                    <div class="col card-box{{ $errors->has('logo') ? ' has-error' : '' }}">
                                        <h4 class="header-title mb-3">Logo perusahaan</h4>
                                        <input type="file" name="logo" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/'.$companyData->logo) }}"  />
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Nama</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') !== null ? old('name') : $companyData->name }}" data-parsley-minlength="3" required>
                                </div>
                                <div class="form-group{{ $errors->has('slogan') ? ' has-error' : '' }}">
                                    <label for="slogan">Slogan</label>
                                    <input type="text" class="form-control" name="slogan" value="{{ old('slogan') !== null ? old('slogan') : $companyData->slogan }}" required>
                                </div>
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email') !== null ? old('email') : $companyData->email }}" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-8 form-group{{ $errors->has('url') ? ' has-error' : '' }}">
                                        <label for="url">Website</label>
                                        <input type="text" class="form-control" name="url" value="{{ old('url') !== null ? old('url') : $companyData->url }}" placeholder="ct: sagiyo.com" required>
                                    </div>
                                    <div class="col-md-4 form-group{{ $errors->has('year') ? ' has-error' : '' }}">
                                        <label for="year">Tahun berdiri</label>
                                        <input type="number" class="form-control" name="year" value="{{ old('year') !== null ? old('year') : $companyData->year }}" placeholder="ct: sagiyo.com" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                        <label for="">Nomor telepon</label>
                                        <input type="text" class="form-control" name="phone"  value="{{ (old('phone') !== null) ? old('phone') : $companyData->phone }}" data-parsley-minlength="9" required>
                                    </div>
                                    <div class="col-md form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                                        <label for="">Nomor handphone</label>
                                        <input type="text" class="form-control" name="mobile"  value="{{ (old('mobile') !== null) ? old('mobile') : $companyData->mobile }}" data-parsley-minlength="9" required>
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('keywords') ? ' has-error' : '' }}">
                                    <label for="">Keywords</label>
                                    <textarea type="text" class="form-control" name="keywords" cols="10" rows="3" minlength="10">{{ old('keywords') !== null ? old('keywords') : $companyData->keywords }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="">Ringkasan (maksimal 200 karakter)</label>
                                    <textarea type="text" class="form-control{{ $errors->has('brief') ? ' has-error' : '' }}" name="brief" cols="10" rows="5" minlength="10">{{ old('brief') !== null ? old('brief') : $companyData->brief }}</textarea>
                                </div>
                                <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                    <label for="">Alamat</label>
                                    <textarea type="text" class="form-control" name="address" cols="10" rows="4" minlength="10">{{ old('address') !== null ? old('address') : $companyData->address }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="rek">Nomor rekening</label>
                                    <textarea type="text" class="form-control" name="rek" cols="10" rows="3">{{ old('rek') !== null ? old('rek') : $companyData->rek }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('map') ? ' has-error' : '' }}">
                            <label for="">Map</label>
                            <textarea type="text" class="form-control" name="map" cols="10" rows="7" minlength="10">{{ old('map') !== null ? old('map') : $companyData->map }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for=""></label>
                            <input type="submit" class="btn btn-info" name="submit" value="Ubah">
                            <a href="{{ route($dashboardLink) }}"  class="btn btn-secondary" role="button">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- container-fluid -->
    </div>
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
