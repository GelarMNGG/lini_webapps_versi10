@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Upload bukti tes';
    $formRouteIndex = 'user-covid-test.index';
    $formRouteStore = 'user-covid-test.uploadImage';
?>
@endsection

@section('content')
<div class="flash-message mt-2">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<div class="card mt-2">
    <div class="card-header text-center bb-orange">
        <strong><span class="text-info text-uppercase">{{ $pageTitle }}</span></strong>
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

        <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- hidden data -->
            <input type="text" name="ctr_id" value="{{ $ctr_id }}" hidden>
            
            <div class="row m-0">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama <small class="c-red">*</small></label>
                        <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ ucwords($covidData->name) ?? '' }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Jabatan <small class="c-red">*</small></label>
                        <input type="text" name="title" class="form-control{{ $errors->has('title') ? ' has-error' : '' }}" value="{{ ucwords($covidData->title) ?? '' }}" disabled>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="{{ $errors->has('image') ? ' has-error' : '' }}">
                        <h4 class="header-title">Bukti tes</h4>
                        <input type="file" name="image" class="dropify" data-max-file-size="2M" data-default-file="{{ asset('img/default.png') }}"/>
                    </div>
                </div>

                <div class="form-group mt-3">
                        <label for=""></label>
                        <button type="submit" class="btn btn-orange t-white" name="submit">Upload</button>
                        <a href="{{ route($formRouteIndex) }}" type="button" class="btn btn-blue-lini">Kembali</a>
                    </div>
            </div>
        </form>
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'solution' );
</script>
<script src="{{ asset('admintheme/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
@endsection
