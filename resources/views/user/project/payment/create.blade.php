@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Upload bukti transfer';
    $formRouteIndex = 'user-project-payment-summary.index';
    $formRouteStore = 'user-project-payment-summary.store';
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
        <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTaskInfo->project_name }}</span></strong>
        <br><small>Task:</small> <strong><span class="text-danger text-uppercase">{{ $projectTaskInfo->name }}</span></strong>
        <br><small>No task:</small> <strong><span class="text-warning text-uppercase">{{ $projectTaskInfo->number }}</span></strong>
    </div>

    <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card-body bg-gray-lini-2">
            <!-- hidden data -->
            <input type="text" name="project_id" value="{{ $projectTaskInfo->project_id }}" hidden>
            <input type="text" name="task_id" value="{{ $projectTaskInfo->id }}" hidden>
            <div class="row m-0">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama <small class="c-red">*</small></label>
                        <input type="text" name="title" class="form-control{{ $errors->has('title') ? ' has-error' : '' }}" value="{{ old('title') ?? '' }}" placeholder="Nama" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah <small class="c-red">*</small></label>
                        <input type="number" name="amount" class="form-control{{ $errors->has('amount') ? ' has-error' : '' }}" value="{{ old('amount') ?? '' }}" placeholder="jumlah" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan <small class="c-red">*</small></label>
                        <textarea type="text" class="form-control" name="description" cols="10" rows="5" minlength="10">{{ old('description') ?? old('description') }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="{{ $errors->has('image') ? ' has-error' : '' }}">
                        <h4 class="header-title">Bukti transfer</h4>
                        <input type="file" name="image" class="dropify" data-max-file-size="2M" data-default-file="{{ asset('img/minutes/tech/default.png') }}"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <button type="submit" class="btn btn-orange" name="submit">Upload</button>
                <a href="{{ route($formRouteIndex,'project_id='.$projectTaskInfo->project_id.'&task_id='.$projectTaskInfo->id) }}" type="button" class="btn btn-blue-lini">Kembali</a>
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
