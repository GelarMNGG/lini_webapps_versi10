@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'kategori';
    $formRouteIndex = 'admin-projects-category.index';
    $formRouteUpdate= 'admin-projects-category.update';
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
    <div class="card-header text-center text-uppercase bb-orange">
        <strong>{{ ucfirst($pageTitle) }}</strong>
    </div>
    <form action="{{ route($formRouteUpdate, $projectReportCategory->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method('PUT')
        <div class="card-body bg-gray-lini-2"> 
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name">Nama kategori</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') !== null ? old('name') : $projectReportCategory->name }}" data-parsley-minlength="3" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Nama pemohon</label>
                            <input type="text" class="form-control" value="{{ ucwords($projectReportCategory->publisher_firstname.' '.$projectReportCategory->publisher_lastname) }}" readonly>
                        </div>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                            <label for="">Status</label>
                            <select name="status" class="form-control">
                                @if (old('status') == 1 || $projectReportCategory->status == 1)
                                    <option value="1">Active</option>
                                    <option value="3">Inactive</option>
                                @else
                                    <option value="3">Inactive</option>
                                    <option value="1">Active</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Tanggal pengajuan</label>
                            <input type="text" class="form-control" value="{{ date('l, d F Y',strtotime($projectReportCategory->date_submitted))}}" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for=""></label>
                    </div>
                </div>
            </div> <!-- container-fluid -->
        </div>
        <div class="card-body">
            <input type="submit" class="btn btn-orange" name="submit" value="Ubah">
            <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini">Batal</a>
        </div>
    </form>
</div> <!-- container-fluid -->
@endsection

@section ('script')

@endsection
