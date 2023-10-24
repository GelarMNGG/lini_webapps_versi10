@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Add file template';
    //store
        $formRouteStore = 'user-projects-report-file.store';
    //back
        $formRouteBack = 'user-projects-template.edit';
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

    <form action="{{ route($formRouteStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        <div class="card-body bg-gray-lini-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- hidden data -->
                        <input type="text" name="project_id" value="{{ $projectTask->project_id }}" hidden>
                        <input type="text" name="task_id" value="{{ $projectTask->id }}" hidden>
                        <input type="text" name="prts_id" value="{{ $projectTemplate->id }}" hidden>
                        <input type="text" name="subcat_id" value="{{ $subcatdata->id }}" hidden>

                        <div class="row m-0">
                            <div class="col-md-6">
                                <div class="{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <?php
                                        if (isset($subcatdata->name)) {
                                            $subcatname = "<span class='text-info'>".$subcatdata->name."</span>";
                                        }else{
                                            $subcatname = '<span class="text-danger">data tidak tersedia.</span>';
                                        }
                                    ?>
                                    <h4 class="header-title">Template untuk {!! $subcatname !!}</h4>
                                    <input type="file" name="name" class="dropify" data-max-file-size="2M" data-default-file="{{ asset('files/projects/report/template_files/default.png') }}"  />
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- container-fluid -->
            </div>
        </div>
        <div class="card-body">
            <div class="col-md mt-2 mb-2">
                <button type="submit" class="btn btn-orange" name="submit"><i class="fa fa-save"></i> Simpan</button>
                <a href="{{ route($formRouteBack,$projectTemplate->id) }}" class="btn btn-blue-lini">Kembali</a>
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
