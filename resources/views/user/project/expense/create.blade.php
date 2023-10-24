@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Tambah pengeluaran';
    $formRouteIndex = 'expenses-tech.index';
    $formRouteStore = 'expenses-tech.store';
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
        <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTask->project_name }}</span></strong>
        <br><small>Task:</small> <strong><span class="text-danger text-uppercase">{{ $projectTask->name }}</span></strong>
        <br><small>No task:</small> <strong><span class="text-warning text-uppercase">{{ $projectTask->number }}</span></strong>
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

        <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- hidden data -->
            <input type="text" name="project_id" value="{{ $projectTask->project_id }}" hidden>
            <input type="text" name="task_id" value="{{ $projectTask->id }}" hidden>
            <div class="row m-0">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama pengeluaran <small class="c-red">*</small></label>
                        <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ old('name') ?? '' }}" placeholder="Nama pengeluaran" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah pengeluaran <small class="c-red">*</small></label>
                        <input type="number" name="amount" class="form-control{{ $errors->has('amount') ? ' has-error' : '' }}" value="{{ old('amount') ?? '' }}" placeholder="jumlah pengeluaran" required>
                    </div>
                    <div class="col-md mt-3">
                        <label for=""></label>
                        <button type="submit" class="btn btn-info t-white" name="submit">Tambah</button>
                        <a href="{{ route($formRouteIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" type="button" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="{{ $errors->has('image') ? ' has-error' : '' }}">
                        <h4 class="header-title">Bukti pembelian</h4> 
                        <input type="file" name="image" class="dropify" data-max-file-size="2M" data-default-file="{{ asset('img/minutes/tech/default.png') }}"  />
                    </div>
                </div>
            </div>
            <div class="row m-0">
                <div class="w-100"></div>
            </div>
        </form>
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'solution' );
</script>
<script src="{{ asset('admintheme/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
@endsection
