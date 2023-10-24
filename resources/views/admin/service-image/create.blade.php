@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Tambah gambar service';
    $formRouteIndex = 'service.index';
    $formRouteStore = 'service-image.store';
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
        Kategori service: <strong>{{ strtoupper($service->name) }}</strong>
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
                    <form action="{{ route($formRouteStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mg-0">
                                    <div class="col card-box{{ $errors->has('image') ? ' has-error' : '' }}">
                                        <h4 class="header-title mb-3">Gambar</h4>
                                        <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/services/default.png') }}"  />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 pt-5">
                                <div class="form-group">
                                    <label for="service_id">Kategori</label>
                                    <input type="text" class="form-control" name="service_id" value="{{ $service->id }}" data-parsley-minlength="1" hidden>
                                    <input type="text" class="form-control" value="{{ $service->name }}" data-parsley-minlength="1" disabled>
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
