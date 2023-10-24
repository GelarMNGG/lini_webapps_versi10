@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Ubah data keluarga';
    $formRouteIndex = 'tech-input-data-keluarga.index';
    $formRouteUpdate = 'tech-input-data-keluarga.update';
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


    <form class="w-100" action="{{ route($formRouteUpdate, $techFamilyInfo->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('father') ? ' has-error' : '' }}">
                    <label for="father">Ayah</label>
                    <input type="text" class="form-control" name="father" value="{{ old('father') ? old('father') : $techFamilyInfo->father }}" required>
                </div>
                <div class="col-md form-group{{ $errors->has('father_profession') ? ' has-error' : '' }}">
                    <label for="father_profession">Pekerjaan</label>
                    <input type="text" class="form-control" name="father_profession" value="{{ old('father_profession') ? old('father_profession') : $techFamilyInfo->father_profession }}" required>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('mother') ? ' has-error' : '' }}">
                    <label for="mother">Ibu</label>
                    <input type="text" class="form-control" name="mother" value="{{ old('mother') ? old('mother') : $techFamilyInfo->mother }}" data-parsley-minlength="3" required>
                </div>
                <div class="col-md form-group{{ $errors->has('mother_profession') ? ' has-error' : '' }}">
                    <label for="mother_profession">Pekerjaan</label>
                    <input type="text" class="form-control" name="mother_profession" value="{{ old('mother_profession') ? old('mother_profession') : $techFamilyInfo->mother_profession }}" required>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('spouse') ? ' has-error' : '' }}">
                    <label for="spouse">Suami/istri</label>
                    <input type="text" class="form-control" name="spouse" value="{{ old('spouse') ? old('spouse') : $techFamilyInfo->spouse }}" data-parsley-minlength="3">
                </div>
                <div class="col-md form-group{{ $errors->has('spouse_profession') ? ' has-error' : '' }}">
                    <label for="spouse_profession">Pekerjaan</label>
                    <input type="text" class="form-control" name="spouse_profession" value="{{ old('spouse_profession') ? old('spouse_profession') : $techFamilyInfo->spouse_profession }}">
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
