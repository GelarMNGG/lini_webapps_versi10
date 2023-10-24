@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Ajukan cash advance';
    $formRouteIndex = 'project-ca-tech.index';
    $formRouteStore = 'project-ca-tech.store';
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

            <div class="row m-0">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama dana <small class="c-red">*</small></label>
                        <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ old('name') ?? '' }}" placeholder="nama dana" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jumlah dana <small class="c-red">*</small></label>
                        <input type="number" name="amount" class="form-control{{ $errors->has('amount') ? ' has-error' : '' }}" value="{{ old('amount') ?? '' }}" placeholder="jumlah pengeluaran" required>
                    </div>
                </div>
            </div>
            <div class="row m-0">
                <div class="w-100"></div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md mt-2 mb-2">
                    
                <button type="submit" class="btn btn-orange" name="submit"><i class="fa fa-plus"></i> Ajukan</button>
                
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
