@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Tambah slider';
    $formRouteIndex = 'slider.index';
    $formRouteStore = 'slider.store';
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
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="{{ route($formRouteStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mg-0">
                                    <div class="col card-box{{ $errors->has('image') ? ' has-error' : '' }}">
                                        <h4 class="header-title mb-3">Slider</h4>
                                        <input type="file" name="image" class="dropify" data-max-file-size="2M" data-default-file="{{ asset('img/sliders/default.png') }}"  />
                                        <small class="form-text text-muted">
                                            <strong>Ukuran ideal: (2500 x 1667)px 72dpi</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 pt-5">
                                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" name="title" value="{{ old('title') ?? old('title') }}" data-parsley-minlength="3" required>
                                </div>
                                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                    <label for="description">Deskripsi</label>
                                    <input type="text" class="form-control" name="description" value="{{ old('description') ?? old('description') }}" data-parsley-minlength="3" required>
                                </div>
                                <div class="form-group">
                                    <label for=""></label>
                                    <input type="submit" class="btn btn-info" name="submit" value="Simpan">
                                    <a href="{{ route($formRouteIndex) }}" class="btn btn-secondary">Batal</a>
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

@endsection
