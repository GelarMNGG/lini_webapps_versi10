@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Ubah pilihan jawaban';
    $formRouteIndex = 'admin-proc-test-psychology.index';
    $formRouteUpdate = 'admin-test-psychology-answers.update';
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
    <form class="w-100" action="{{ route($formRouteUpdate,$dataAnswer->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="card-body bg-gray-lini-2">
            <div class="col-md"><h3>{{ ucfirst(strtolower($dataAnswer->question)) }}</h3></div>
            <div class="row m-0">
                <div class="col-md">
                    <div class="form-group{{ $errors->has('answer_a') ? ' has-error' : '' }}">
                        <label>Pilihan Jawaban A <small class="c-red">*</small></label>
                        <textarea name="answer_a" class="form-control" cols="10" rows="2" required>{{ old('answer_a') ? old('answer_a') : $dataAnswer->answer_a }}</textarea>
                    </div>
                    <div class="form-group{{ $errors->has('answer_b') ? ' has-error' : '' }}">
                        <label>Pilihan Jawaban B <small class="c-red">*</small></label>
                        <textarea name="answer_b" class="form-control" cols="10" rows="2" required>{{ old('answer_b') ? old('answer_b') : $dataAnswer->answer_b }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <button type="submit" class="btn btn-orange" name="submit">Tambah</button>
            <a href="{{ route($formRouteIndex) }}" type="button" class="btn btn-blue-lini">Kembali</a>
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
