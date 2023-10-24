@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Tambah bug report';
    $formRouteIndex = 'tech-bug-report.index';
    $formRouteStore = 'tech-bug-report.store';
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
    <div class="card-header text-center text-uppercase bb-orange"><strong>{{ ucfirst($pageTitle) }}</strong></div>


    <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md">
                    <div class="form-group">
                        <label>Nama Bug <small class="c-red">*</small></label>
                        <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ old('name') ?? '' }}" required>
                    </div>
                </div>
            </div>
            <div class="col-md">
                <div class="form-group{{ $errors->has('reproduce') ? ' has-error' : '' }}">
                    <label>Langkah-langkah Error</label>
                    <input type="text" name="reproduce" class="form-control" value="{{ old('reproduce') }}" required>
                    <small>Contoh: <strong>Pilih Activity > Pilih Tambah Activitas > ketika membuka terjadi kesalahan/ERROR</strong>.</small>
                </div>
            </div>
            <div class="row m-0">
                <div class="col-md">
                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" cols="10" rows="9" required>{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="col-md{{ $errors->has('image') ? ' has-error' : '' }}">
                    <label>Dokumentasi</label>
                    <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/bug-report/default.png') }}"  />
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <button type="submit" class="btn btn-orange" name="submit">Tambah</button>
                <a href="{{ route($formRouteIndex) }}" type="button" class="btn btn-blue-lini">Kembali</a>
            </div>
        </div>
    </form>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'solution' );
</script>
<script src="{{ asset('admintheme/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
@endsection
