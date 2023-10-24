@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Clock in'; 
    $formRouteIndex  = 'attendance.index';
    $formRouteStore = 'user-clockout.update';
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

        <form class="w-100" action="{{ route($formRouteStore, $clockoutData->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method ('PUT')
            <div class="row m-0" style="display:inline">
                <div class="col-md form-group">
                    <label>Clock out</label>
                    <div class="input-group">
                        <input id="timepicker3" name="clockout" type="text" class="form-control" value="{{ old('clockout') ? old('clockout') : date('H:i A', strtotime($clockoutData->clockout)) }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                        </div>
                    </div><!-- input-group -->
                </div>
                <div class="col-md form-group">
                    <label>Note</label>
                    <textarea type="text" name="note" class="form-control" cols="10" rows="3">{{ old('note') ?? '' }}</textarea>
                </div>
                <div class="col-md mt-3">
                    <label for=""></label>
                    <button type="submit" class="btn btn-info t-white" name="submit">Request</button>
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
