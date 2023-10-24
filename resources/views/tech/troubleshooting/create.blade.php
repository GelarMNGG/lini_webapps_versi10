@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Tambah troubleshooting';
    $formRouteIndex = 'tech-troubleshooting.index';
    $formRouteStore = 'tech-troubleshooting.store';
    $formRouteCreateTroubleshootingImage = 'troubleshooting-image.create';
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
                        <label>Judul kasus <small class="c-red">*</small></label>
                        <input type="text" name="title" class="form-control{{ $errors->has('title') ? ' has-error' : '' }}" value="{{ old('title') ?? '' }}" placeholder="Judul" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="">Status </label>
                    <select name="status" class="form-control select2{{ $errors->has('status') ? ' has-error' : '' }}" required>
                        <?php
                            if(old('status') != null) {
                                $status = old('status');
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
                            @if ($dataOne->id != 3)
                                <option value="{{ strtolower($dataOne->id) }}">{{ ucwords($dataOne->name) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group">
                    <label>Jam mulai</label>
                    <div class="input-group">
                        <input id="timepicker3" name="event_start" type="text" class="form-control" value="{{ old('event_start') ?? '' }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                        </div>
                    </div><!-- input-group -->
                </div>
                <div class="col-md form-group">
                    <label>Jam selesai</label>
                    <div class="input-group">
                        <input id="timepicker" name="event_end" type="text" class="form-control" value="{{ old('event_start') ?? '' }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                        </div>
                    </div><!-- input-group -->
                </div>
            </div>
            <div class="row m-0" style="display:inline">
                <div class="col-md form-group{{ $errors->has('problem') ? ' has-error' : '' }}">
                    <label>Deskripsi masalah <small class="c-red">*</small></label>
                    <textarea name="problem" class="form-control" cols="10" rows="5" required>{{ old('problem') }}</textarea>
                </div>
                <div class="col-md form-group{{ $errors->has('solution') ? ' has-error' : '' }}">
                    <label>Langkah-langkah penyelesaian <small class="c-red">*</small></label>
                    <textarea name="solution" class="form-control" cols="10" rows="15" required>{{ old('solution') }}</textarea>
                </div>
                <div class="col-md form-group">
                    <div class="row mg-0">
                        <div class="col card-box{{ $errors->has('image') ? ' has-error' : '' }}">
                            <h4 class="header-title mb-3">Dokumentasi</h4>
                            <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/troubleshooting/default.png') }}"  />
                        </div>
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
