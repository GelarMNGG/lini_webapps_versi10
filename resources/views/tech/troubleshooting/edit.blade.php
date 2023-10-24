@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Troubleshooting';
    $formRouteIndex = 'tech-troubleshooting.index';
    $formRouteUpdate = 'tech-troubleshooting.update';
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

    <form action="{{ route($formRouteUpdate, $troubleshootingData->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method ('PUT')
        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md">
                    <div class="form-group">
                        <label>Judul kasus <small class="c-red">*</small></label>
                        <input type="text" name="title" class="form-control{{ $errors->has('title') ? ' has-error' : '' }}" value="{{ old('title') ? old('title') : $troubleshootingData->title }}" placeholder="Judul" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="">Status </label>
                    <select name="status" class="form-control select2{{ $errors->has('status') ? ' has-error' : '' }}" required>
                        <?php
                            if(old('status') != null) {
                                $status = old('status');
                            }elseif(isset($troubleshootingData->status)){
                                $status = $troubleshootingData->status;
                            }else{
                                $status = null;
                            }
                        ?>
                        @if ($status != null)
                            @foreach ($statusDatas as $dataOne)
                                @if ($dataOne->id == $status)
                                    <option value='{{ strtolower($dataOne->id) }}'>{{ ucwords(strtolower($dataOne->name)) }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="0">Pilih status</option>
                        @endif
                        @foreach($statusDatas as $dataOne)
                            @if ($dataOne->id != 3 && $dataOne->id != $status)
                                <option value="{{ strtolower($dataOne->id) }}">{{ ucwords($dataOne->name) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group">
                    <label>Jam mulai</label>
                    <div class="input-group">
                        <input id="timepicker3" name="event_start" type="text" class="form-control" value="{{ old('event_start') ? old('event_start') : date('G:i A', strtotime($troubleshootingData->event_start)) }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                        </div>
                    </div><!-- input-group -->
                </div>
                <div class="col-md form-group">
                    <label>Jam selesai</label>
                    <div class="input-group">
                        <input id="timepicker" name="event_end" type="text" class="form-control" value="{{ old('event_end') ? old('event_end') : date('G:i A', strtotime($troubleshootingData->event_end)) }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                        </div>
                    </div><!-- input-group -->
                </div> 
            </div>
            <div class="row m-0" style="display:inline">
                <div class="col-md form-group{{ $errors->has('problem') ? ' has-error' : '' }}">
                    <label>Deskripsi masalah <small class="c-red">*</small></label>
                    <textarea name="problem" class="form-control" cols="10" rows="5" required>{{ old('problem') ? old('problem') : $troubleshootingData->problem }}</textarea>
                </div>
                <div class="col-md form-group{{ $errors->has('solution') ? ' has-error' : '' }}">
                    <label>Langkah-langkah penyelesaian <small class="c-red">*</small></label>
                    <textarea name="solution" class="form-control" cols="10" rows="15" required>{{ old('solution') ? old('solution') : $troubleshootingData->solution }}</textarea>
                </div>
                <div class="col-md form-group">
                    <div class="row mg-0">
                        <div class="col card-box{{ $errors->has('image') ? ' has-error' : '' }}">
                            <h4 class="header-title mb-3">Dokumentasi</h4>
                            @if($troubleshootingData->image != null)
                                <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/troubleshooting/'.$troubleshootingData->image) }}"  />
                            @else
                                <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/troubleshooting/default.png') }}"  />
                            @endif
                        </div>
                    </div>
                </div>
            </div>    
        </div>
        <div class="card-body">
            <div class="fcol-md">
                <input type="submit" class="btn btn-orange" name="submit" value="Ubah">
                <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini">Batal</a>
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
