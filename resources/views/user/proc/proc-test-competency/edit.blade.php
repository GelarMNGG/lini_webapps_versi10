@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Ubah Pertanyaan';
    $formRouteIndex = 'user-proc-competency-test.index';
    $formRouteUpdate = 'user-proc-competency-test.update';
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


    <form class="w-100" action="{{ route($formRouteUpdate, $dataQuestion->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md">
                    <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
                        <label>Pertanyaan <small class="c-red">*</small></label>
                        <textarea name="question" class="form-control" cols="10" rows="7" required>{{ old('question') ? old('question') : $dataQuestion->question }}</textarea>
                    </div>
                </div>
            </div>
            <div class="col-md">
                <label>Pilihan Jawaban <small class="c-red">*</small></label>
                <select name="answer" class="form-control">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
                <br />
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <button type="submit" class="btn btn-orange" name="submit">Ubah</button>
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
