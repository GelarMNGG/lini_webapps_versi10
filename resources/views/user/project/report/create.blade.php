@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Buat laporan';
    $formRouteIndex = 'user-projects.index';
    //report route
    $formRouteProjectReportStore = 'user-projects-report.store';
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
        Project: <strong><span class="text-danger text-uppercase">{{ $projectTask->project_name }}</span></strong>
        <br><small>No WO:</small> <strong><span class="text-info text-uppercase">{{ $projectTask->number }}</span></strong>
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
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="{{ route($formRouteProjectReportStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf
                        
                        <div class="row">
                            <div class="col-md form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="title">Judul <small class="c-red">*</small></label>
                                <input type="text" class="form-control" name="title" value="{{ old('title') ?? old('title') }}" data-parsley-minlength="9">
                            </div>
                            <div class="col-md form-group{{ $errors->has('sub_title') ? ' has-error' : '' }}">
                                <label for="sub_title">Sub Judul <small class="c-red">*</small></label>
                                <input type="text" class="form-control" name="sub_title" value="{{ old('sub_title') ?? old('sub_title') }}" data-parsley-minlength="9">
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('summary') ? ' has-error' : '' }}">
                                <label>Ringkasan </label>
                                <textarea id="summary" name="summary" class="form-control" cols="10" rows="7">{{ old('summary') }}</textarea>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                                <label>Isi laporan </label>
                                <textarea id="content" name="content" class="form-control" cols="10" rows="19">{{ old('content') }}</textarea>
                            </div>
                            <div class="w-100"></div>
                            <div class="col card-box{{ $errors->has('image') ? ' has-error' : '' }}">
                                <h4 class="header-title mb-3">Image cover</h4>
                                <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/projects/report/default.png') }}"  />
                            </div>
                            <div class="w-100"></div>
                            <div class="form-group">
                                <label for=""></label>
                                <input type="submit" class="btn btn-info" name="submit" value="Simpan">
                                <a href="{{ route($formRouteIndex) }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- container-fluid -->
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('content');
</script>
@endsection
