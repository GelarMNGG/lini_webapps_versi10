@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Clock out'; 
    $formRouteIndex  = 'attendance.index';
    $formRouteStore = 'user-clockout.store';
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
        <div>{{ date('l, d F Y') }}</div>
        <div class="display-3">{{ date('H:i A') }}</div>
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
            <!-- input -->
            <input type="text" name="clockout" value="{{ date('H:i:s') }}" hidden>
            <div class="row m-0" style="display:inline">
                <div class="col-md form-group">
                    <div class="row mg-0">
                        <div class="col card-box{{ $errors->has('clockout_image') ? ' has-error' : '' }}">
                            <h4 class="header-title mb-3">Foto</h4>
                            <input type="file" name="clockout_image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/attendance/default.png') }}" required />
                        </div>
                        <div class="col-md form-group mt-4">
                            <label>Note</label>
                            <textarea type="text" name="note" class="form-control" cols="10" rows="9">{{ old('note') ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md mt-3">
                    <label for=""></label>
                    <button type="submit" class="btn btn-info t-white" name="submit">Clock out</button>
                    <a href="{{ route($formRouteIndex) }}" type="button" class="btn btn-secondary">Kembali</a>
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
