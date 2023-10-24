@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Ubah Pertanyaan ';
    $formRouteIndex = 'admin-proc-test-psychology.index';
    $formRouteUpdate = 'admin-proc-test-psychology.update';
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
        <div class="row m-0">
            <div class="col-md form-group">
                    <label for="">Kategori </label>
                    <select name="question_cat" class="form-control select2{{ $errors->has('question_cat') ? ' has-error' : '' }}" required>
                        <?php
                            if(old('question_cat') != null) {
                                $question_cat = old('question_cat');
                            }elseif(isset($dataQuestion->question_cat)){
                                $question_cat = $dataQuestion->question_cat;
                            }else{
                                $question_cat = null;
                            }
                        ?>
                        @if ($questionCats != null)
                            @foreach ($questionCats as $data3)
                                @if ($data3->id == $question_cat)
                                    <option value="{{ $data3->id }}">{{ ucwords(strtolower($data3->name)) }}</option>
                                @endif
                            @endforeach
                            @foreach($questionCats as $data4)
                                @if ($data4->id != $question_cat)
                                    <option value="{{ $data4->id }}">{{ ucwords(strtolower($data4->name)) }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="0">Pilih kategori</option>
                            @foreach($questionCats as $data2)
                                <option value="{{ $data2->id }}">{{ ucwords(strtolower($data2->name)) }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
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
