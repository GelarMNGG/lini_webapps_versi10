@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Ubah Checklist';
    $formRouteIndex = 'admin-user-acceptance-test.index';
    $formRouteUpdate = 'admin-user-acceptance-test.update';
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


    <form class="w-100" action="{{ route($formRouteUpdate, $dataUat->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card-body bg-gray-lini-2">
            <div class="row">
                <div class="col-md">
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        <label>Judul <small class="c-red">*</small></label>
                        <input type="text" name="title" class="form-control{{ $errors->has('title') ? ' has-error' : '' }}" value="{{ old('title') ? old('title') : $dataUat->title }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md">
                    <div class="form-group{{ $errors->has('steps') ? ' has-error' : '' }}">
                        <label>Langkah-langkah </label>
                        <input type="text" name="steps" class="form-control" value="{{ old('steps') ? old('steps') : $dataUat->steps }}" required>
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-group{{ $errors->has('link_id') ? ' has-error' : '' }}">
                        <label>Link </label>
                        <input type="text" name="link_id" class="form-control" value="{{ old('link_id') ? old('link_id') : $dataUat->link_id }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <button type="submit" class="btn btn-orange" name="submit">Ubah</button>
                <a href="{{ route($formRouteIndex) }}" type="button" class="btn btn-blue-lini">Batal</a>
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
