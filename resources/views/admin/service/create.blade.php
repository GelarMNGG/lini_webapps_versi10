@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Tambah service';
    $formRouteIndex = 'service.index';
    $formRouteStore = 'service.store';
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
    <div class="card-header text-center">{{ ucfirst($pageTitle) }}</div>

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
                    <form action="{{ route($formRouteStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf
                        
                        <div class="row">
                            <div class="col card-box{{ $errors->has('icon') ? ' has-error' : '' }}">
                                <h4 class="header-title mb-3">Icon</h4>
                                <input type="file" name="icon" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/services/icon/default.png') }}"  />
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name">Nama <small class="c-red">*</small></label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') ?? old('name') }}" data-parsley-minlength="3" required>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label>Deskripsi </label>
                                <textarea id="description" name="description" class="form-control" cols="10" rows="15">{{ old('description') }}</textarea>
                            </div>
                            <div class="w-100"></div>
                            <div class="form-group">
                                <label for=""></label>
                                <input type="submit" class="btn btn-info" name="submit" value="Simpan">
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
    CKEDITOR.replace('description');
</script>
@endsection
