@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'service';
    $formRouteIndex = 'service.index';
    $formRouteUpdate= 'service.update';
    $formRouteCreateImage = 'service-image.create';
    $formRouteDestroyImage = 'service-image.destroy';
    $formRouteEditImage = 'service-image.edit';
?>
@endsection

@section('content')
<div class="card">
<div class="flash-message">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

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
                    <div class="row alert alert-warning">
                        <label class="header-title mb-3">Foto dokumentasi</label>
                        <div class="w-100"></div>
                        <div class="col-md">
                            <div class="row text-center">
                                @if(sizeof($serviceImages) > 0)
                                    @foreach($serviceImages as $dataImg)
                                    <div class="col-md col-md-3">
                                        <img src="{{ asset('img/services/'.$dataImg->image) }}" alt="{{ $dataImg->image }}" class="avatar-xl rounded">
                                        <form action="{{ route($formRouteDestroyImage, $dataImg->id) }}" method="POST">
                                        @method('DELETE')
                                        @csrf
                                            <a href="{{ route($formRouteEditImage, $dataImg->id) }}" class="btn-link btn-edit-image"><i class="fa fa-edit"></i></a>
                                            <button type="submit" class="btn btn-link" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                        </form>
                                    </div>
                                    @endforeach
                                @endif
                                <div class="w-100"></div>
                                <a href="{{ route($formRouteCreateImage, 'id='.$service->id) }}" class="btn btn-info mb-3 mt-1"><i class="fa fa-plus"></i> Tambah</a>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route($formRouteUpdate, $service->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md{{ $errors->has('icon') ? ' has-error' : '' }}">
                                <label class="header-title mb-3">Icon</label>
                                <input type="file" name="icon" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/services/icon/'.$service->icon) }}"  />
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name">Nama <small class="c-red">*</small></label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : $service->name }}" data-parsley-minlength="3" required>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label>Deskripsi </label>
                                <textarea id="description" name="description" class="form-control" cols="10" rows="15">{{ old('description') ? old('description') : $service->description }}</textarea>
                            </div>
                            <div class="w-100"></div>
                            <div class="form-group">
                                <label for=""></label>
                                <input type="submit" class="btn btn-info" name="submit" value="Ubah">
                                <a href="{{ route($formRouteIndex) }}" class="btn btn-secondary">Batal</a>
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
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'description' );
</script>
@endsection
