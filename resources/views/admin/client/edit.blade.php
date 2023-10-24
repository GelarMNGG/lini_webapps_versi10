@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'client';
    $formRouteIndex = 'client.index';
    $formRouteUpdate= 'client.update';
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
        <strong>{{ ucfirst($pageTitle) }}</strong>
    </div>

    <form action="{{ route($formRouteUpdate, $client->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method('PUT')

        <div class="card-body bg-gray-lini-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Nama perusahaan</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') !== null ? old('name') : $client->name }}" data-parsley-minlength="3" required>
                                </div>
                                <div class="form-group card-box{{ $errors->has('logo') ? ' has-error' : '' }}">
                                    <h4 class="header-title mb-3">Logo</h4>
                                    <input type="file" name="logo" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/clients/'.$client->logo) }}"  />
                                    <small class="form-text text-muted">
                                        <strong>Ukuran ideal: (400 x 210)px 72dpi</strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- container-fluid -->
        </div>
        <div class="card-body">
            <input type="submit" class="btn btn-orange" name="submit" value="Ubah">
            <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini">Batal</a>
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
