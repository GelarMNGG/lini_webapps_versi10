@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Tambah pertanyaan';
    $formRouteIndex = 'admin-proc-test-psychology.index';
    $formRouteStore = 'admin-proc-test-psychology.store';
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
                    <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
                        <label>Pertanyaan <small class="c-red">*</small></label>
                        <textarea name="question" class="form-control" cols="10" rows="7" required>{{ old('question') }}</textarea>
                    </div>
                </div>
            </div>
        <div class="row m-0">
            <div class="col-md form-group">
                    <label for=""> Kategori </label>
                    <select id="question_cat" name="question_cat" class="form-control select2{{ $errors->has('question_cat') ? ' has-error' : '' }}" required>
                        @if (!empty(old('cat_id')))
                            @foreach ($questionCats as $dataCat)
                                @if ($dataCat->id == old('question_cat'))
                                    <option value='{{ strtolower($dataCat->id) }}'>{{ ucwords(strtolower($dataCat->name)) }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="0">Pilih kategori</option>
                        @endif
                        @foreach($questionCats as $dataCat)
                            <option value="{{ strtolower($dataCat->id) }}">{{ ucwords($dataCat->name) }}</option>
                        @endforeach
                    </select>
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
