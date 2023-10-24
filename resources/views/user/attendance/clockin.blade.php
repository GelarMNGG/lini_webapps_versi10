@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Clock in'; 
    $formRouteIndex  = 'attendance.index';
    $formRouteStore = 'user-clockin.store';
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
    <div class="card-header text-center bb-orange">
        <div>{{ date('l, d F Y') }}</div>
        <div class="display-3">{{ date('H:i A') }}</div>
    </div>


    <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card-body bg-gray-lini-2">
            <!-- input -->
            <input type="text" name="clockin" value="{{ date('H:i:s') }}" hidden>
            <div class="row m-0" style="display:inline">
                <div class="col-md form-group">
                    <div class="row mg-0">
                        <div class="col-md form-group{{ $errors->has('clockin_image') ? ' has-error' : '' }}">
                            <h4 class="header-title mb-2">Foto</h4>
                            <input type="file" name="clockin_image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/attendance/default.png') }}" required />
                        </div>
                        <div class="col-md form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                            <label>Note</label>
                            <textarea type="text" name="note" class="form-control" cols="10" rows="9">{{ old('note') ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <button type="submit" class="btn btn-orange" name="submit">Clock in</button>
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
