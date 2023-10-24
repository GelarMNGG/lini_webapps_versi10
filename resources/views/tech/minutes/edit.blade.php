@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Aktivitas harian';
    $formRouteIndex = 'minutes-tech.index';
    $formRouteUpdate = 'minutes-tech.update';
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
    <form action="{{ route($formRouteUpdate, $techMinute->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method ('PUT')
        <!-- hidden data -->
        <input type="text" name="project_id" value="{{ $projectTask->project_id }}" hidden>
        <input type="text" name="task_id" value="{{ $projectTask->id }}" hidden>
        <div class="bg-gray-lini-2">
            <div class="card-body">
                <div class="row m-0">
                    <div class="col-md col-md-12">
                        <div class="form-group">
                            <label>Nama aktivitas <small class="c-red">*</small></label>
                            <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ old('name') ? old('name') : ucfirst($techMinute->name) }}" placeholder="Nama aktivitas" required>
                        </div>
                    </div>
                    <div class="col-md form-group">
                        <label>Jam mulai</label>
                        <div class="input-group">
                            <input id="timepicker3" name="event_start" type="text" class="form-control" value="{{ old('event_start') ? old('event_start') : date('G:i A', strtotime($techMinute->event_start)) }}" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                            </div>
                        </div><!-- input-group -->
                    </div>
                    <div class="col-md form-group">
                        <label>Jam selesai</label>
                        <div class="input-group">
                            <input id="timepicker" name="event_end" type="text" class="form-control" value="{{ old('event_end') ? old('event_end') : date('G:i A', strtotime($techMinute->event_end)) }}" required>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                            </div>
                        </div><!-- input-group -->
                    </div>
                    <div class="w-100"></div>
                    <div class="col-md">
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label>Deskripsi masalah</label>
                            <textarea name="description" class="form-control" cols="10" rows="7" required>{{ old('description') ? old('description') : ucfirst($techMinute->description) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="card-box card-box-reset-103{{ $errors->has('image') ? ' has-error' : '' }}">
                            <label class="">Dokumentasi</label>
                            @if(isset($techMinute->image) && $techMinute->image != 'default.png')
                                <input type="file" name="image[]" multiple="true" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/minutes/tech/'.$techMinute->image) }}"/>
                            @else
                                <input type="file" name="image[]" multiple="true" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/minutes/tech/default.png') }}"/>
                            @endif
                            <small>Anda dapat mengupload banyak gambar secara bersamaan.</small>
                        </div>
                    </div>
                </div>
                @if($techMinute->images_count > 0)
                    <hr>
                    <div class="row">
                        <?php $i=1; ?>
                        @foreach($techMinutesImages as $techMinutesImage)
                            @if($techMinutesImage->projmin_id == $techMinute->id)
                            <div class="col-md mb-2">
                                <button type="button" class="btn badge-pill text-dark" data-toggle="modal" style="position:absolute;" data-target="#minutesModal{{ $techMinutesImage->id }}"><i class="fas fa-eye"></i> </button>
                                <img class="img-ca" src="{{ asset('img/minutes/tech/'.$techMinutesImage->image) }}" style="max-height:177px; object-fit:cover">
                                <!-- Modal -->
                                    <div class="modal fade" id="minutesModal{{ $techMinutesImage->id }}" tabindex="-1" role="dialog" aria-labelledby="projectMinutes" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                            <div class="modal-content-img">
                                                <div class="modal-body text-center">
                                                <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                    <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/minutes/tech/'.$techMinutesImage->image) }}"  />
                                                    <div class="alert alert-warning" id="projectMinutes">
                                                        <h5>
                                                            Foto aktifitas: <span class="text-muted">{{ ucfirst($techMinute->name) }}</span>
                                                        </h5>
                                                    </div>
                                                </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <?php 
                                if ($i % 3 == 0) {
                                    echo "<div class='w-100'></div>";
                                }
                                $i++; 
                            ?>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="col-12 mt-2 mb-2">
                <button type="submit" class="btn btn-orange" name="submit"><i class="fa fa-plus"></i> Simpan</button>
                
                <a href="{{ route($formRouteIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-blue-lini">Kembali</a>
            </div>
        </div>
    </form>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'description' );
</script>
<script src="{{ asset('admintheme/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
@endsection
