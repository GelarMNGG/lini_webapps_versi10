@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'gambar service';
    $formRouteEdit = 'service.edit';
    $formRouteUpdate= 'service-image.update';
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
        Kategori service: <strong>{{ ucwords($serviceImage->service_name) }}</strong>
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
                    <form action="{{ route($formRouteUpdate, $serviceImage->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mg-0">
                                    <div class="col card-box{{ $errors->has('image') ? ' has-error' : '' }}">
                                        <h4 class="header-title mb-3">Gambar</h4>
                                        <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/services/'.$serviceImage->image) }}"  />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 pt-5">
                                <div class="form-group">
                                    <label>Kategori</label>
                                    <input type="text" class="form-control" value="{{ $serviceImage->service_name }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for=""></label>
                                    <input type="submit" class="btn btn-info" name="submit" value="Ubah">
                                    <a href="{{ route($formRouteEdit, $serviceImage->service_id) }}" class="btn btn-secondary">Batal</a>
                                </div>
                            </div>
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
