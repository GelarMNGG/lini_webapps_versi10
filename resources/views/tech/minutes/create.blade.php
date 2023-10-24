@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Tambah aktivitas harian';
    $formRouteIndex = 'minutes-tech.index';
    $formRouteStore = 'minutes-tech.store';
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

        <!-- hidden data -->
        <input type="hidden" name="task_id" value="{{ $projectTask->id }}">

        <div class="bg-gray-lini-2">
            <div class="card-body">
                <div class="row m-0">
                    <div class="col-md col-md-12">
                        <div class="form-group">
                            <label>Nama aktivitas <small class="c-red">*</small></label>
                            <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ old('name') ?? '' }}" placeholder="Nama aktivitas" required>
                        </div>
                    </div>
                    <div class="w-100"></div>
                        <div class="col-md form-group">
                            <div class="form-group">
                                <label>Jam mulai</label>
                                <div class="input-group">
                                    <input id="timepicker3" name="event_start" type="text" class="form-control" value="{{ old('event_start') ?? '' }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                    </div>
                                </div><!-- input-group -->
                            </div>
                        </div>
                        <div class="col-md form-group">
                            <div class="form-group">
                                <label>Jam selesai</label>
                                <div class="input-group">
                                    <input id="timepicker" name="event_end" type="text" class="form-control" value="{{ old('event_start') ?? '' }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                    </div>
                                </div><!-- input-group -->
                            </div>
                        </div>
                    <div class="w-100"></div>
                    <div class="col-md">
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" cols="10" rows="7">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md{{ $errors->has('image') ? ' has-error' : '' }}">
                        <div class="card-box card-box-reset-103">
                            <label class="">Dokumentasi</label>
                            <input type="file" name="image[]" multiple="true" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/minutes/tech/default.png') }}"  />
                            <small>Anda dapat mengupload banyak gambar secara bersamaan.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-12 mt-2 mb-2"> 
                <button type="submit" class="btn btn-orange" name="submit"><i class="fa fa-plus"></i> Tambah</button>
                
                <a href="{{ route($formRouteIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-blue-lini">Kembali</a>
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
