@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Upload bukti transfer cash advance';
    $formRouteIndex = 'user-projects-ca.index';
    $formRouteStore = 'user-projects-ca.store';
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
        <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTask->project_name }}</span></strong>
        <br><small>Task:</small> <strong><span class="text-danger text-uppercase">{{ $projectTask->name }}</span></strong>
        <br><small>No task:</small> <strong><span class="text-warning text-uppercase">{{ $projectTask->number }}</span></strong>
    </div>


    <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card-body bg-gray-lini-2">
            <!-- hidden data -->
            <input type="text" name="project_id" value="{{ $projectTask->project_id }}" hidden>
            <input type="text" name="task_id" value="{{ $projectTask->id }}" hidden>
            <input type="text" name="id" value="{{ $theId }}" hidden>

            <div class="row m-0">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama cash advance <small class="c-red">*</small></label>
                        <input type="text" name="name" class="form-control" value="{{ $dataCashAdvance->name ?? '' }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Jumlah cash advance <small class="c-red">*</small></label>
                        <input type="number" name="amount" class="form-control" value="{{ $dataCashAdvance->amount ?? '' }}" hidden readonly>
                        <input type="text" name="" class="form-control" value="Rp. {{ number_format($dataCashAdvance->amount) ?? '' }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="{{ $errors->has('image') ? ' has-error' : '' }}">
                        <h4 class="header-title">Bukti transfer</h4>
                        <input type="file" name="image" class="dropify" data-max-file-size="2M" data-default-file="{{ asset('img/cash-advance/tech/default.png') }}"  />
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <label for=""></label>
                <button type="submit" class="btn btn-orange" name="submit">Upload</button>
                <a href="{{ route($formRouteIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" type="button" class="btn btn-blue-lini">Kembali</a>
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
