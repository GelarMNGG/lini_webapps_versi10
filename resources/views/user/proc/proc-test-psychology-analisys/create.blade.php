@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Tambah kategori pertanyaan';
    $formRouteIndex = 'user-test-psychology-analisys.index';
    $formRouteStore = 'user-test-psychology-analisys.store';
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
                        <label>Kategori <small class="c-red">*</small></label>
                        <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ old('name') ?? '' }}" placeholder="Nama kategori" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi <small class="c-red">*</small></label>
                        <input type="text" name="description" class="form-control{{ $errors->has('description') ? ' has-error' : '' }}" value="{{ old('description') ?? '' }}" placeholder="Deskripsi" required>
                    </div>
                    <div class="form-group">
                        <label>Rekomendasi <small class="c-red">*</small></label>
                        <input type="text" name="recommendation" class="form-control{{ $errors->has('recommendation') ? ' has-error' : '' }}" value="{{ old('recommendation') ?? '' }}" placeholder="Rekomendasi" required>
                    </div>
                    <div class="form-group">
                        <label>Profesi <small class="c-red">*</small></label>
                        <input type="text" name="profession" class="form-control{{ $errors->has('profession') ? ' has-error' : '' }}" value="{{ old('profession') ?? '' }}" placeholder="Profesi" required>
                    </div>
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
