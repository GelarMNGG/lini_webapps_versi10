@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'departmen';
    $formRouteIndex = 'department-lintaslog.index';
    $formRouteUpdate= 'department-lintaslog.update';
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
    <div class="card-header text-center text-uppercase">
        <strong>{{ ucfirst($pageTitle) }}</strong>
    </div>
    <form action="{{ route($formRouteUpdate, $departmentById->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name">Nama departemen</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') !== null ? old('name') : $departmentById->name }}" data-parsley-minlength="3" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <input type="submit" class="btn btn-orange" name="submit" value="Ubah">
                <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini">Batal</a>
            </div>
        </div>
    </form>
</div> <!-- container-fluid -->
@endsection

@section ('script')

@endsection
