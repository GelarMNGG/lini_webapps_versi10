@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Ubah Pertanyaan';
    $formRouteIndex = 'admin-proc-assesment-question.index';
    $formRouteUpdate = 'admin-proc-assesment-question.update';
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
                <div class="col-md">
                    <div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
                        <label>Jawaban yang benar <small class="c-red">*</small></label>
                        <select name="right_answer" class="form-control select2{{ $errors->has('right_answer') ? ' has-error' : '' }}" required>
                            <?php
                                if(old('right_answer') != null) {
                                    $right_answer = old('right_answer');
                                }elseif(isset($dataQuestion->right_answer)){
                                    $right_answer = $dataQuestion->right_answer;
                                }else{
                                    $right_answer = null;
                                }
                            ?>
                            @if ($right_answer != null)
                                @if ($right_answer == 'a')
                                    <option value="a">{{ ucwords(strtolower($dataAnswer->answer_a)) }}</option>
                                @elseif ($right_answer == 'b')
                                    <option value="b">{{ ucwords(strtolower($dataAnswer->answer_b)) }}</option>
                                @elseif ($right_answer == 'c')
                                    <option value="c">{{ ucwords(strtolower($dataAnswer->answer_c)) }}</option>
                                @elseif ($right_answer == 'd')
                                    <option value="d">{{ ucwords(strtolower($dataAnswer->answer_d)) }}</option>
                                @endif
                                <option value="a">{{ ucwords(strtolower($dataAnswer->answer_a)) }}</option>
                                <option value="b">{{ ucwords(strtolower($dataAnswer->answer_b)) }}</option>
                                <option value="c">{{ ucwords(strtolower($dataAnswer->answer_c)) }}</option>
                                <option value="d">{{ ucwords(strtolower($dataAnswer->answer_d)) }}</option>
                            @else
                                <option value="0">Pilih jawaban</option>
                                <option value="a">{{ ucwords(strtolower($dataAnswer->answer_a)) }}</option>
                                <option value="b">{{ ucwords(strtolower($dataAnswer->answer_b)) }}</option>
                                <option value="c">{{ ucwords(strtolower($dataAnswer->answer_c)) }}</option>
                                <option value="d">{{ ucwords(strtolower($dataAnswer->answer_d)) }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="row m-0">
                <div class="col-md form-group">
                        <label for="">Kategori </label>
                        <select name="cat_id" class="form-control select2{{ $errors->has('cat_id') ? ' has-error' : '' }}" required>
                            <?php
                                if(old('cat_id') != null) {
                                    $cat_id = old('cat_id');
                                }elseif(isset($dataQuestion->cat_id)){
                                    $cat_id = $dataQuestion->cat_id;
                                }else{
                                    $cat_id = null;
                                }
                            ?>
                            @if ($cat_id != null)
                                @foreach ($questionCats as $data3)
                                    @if ($data3->id == $cat_id)
                                        <option value="{{ $data3->id }}">{{ ucwords(strtolower($data3->name)) }}</option>
                                    @endif
                                @endforeach
                                @foreach($questionCats as $data4)
                                    @if ($data4->id != $cat_id)
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
