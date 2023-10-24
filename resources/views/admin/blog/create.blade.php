@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Tambah Artikel';
    $formRouteIndex = 'admin-blog.index';
    $formRouteStore = 'admin-blog.store';
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

    <form action="{{ route($formRouteStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf

        <div class="card-body bg-gray-lini-2">
            <div class="row">
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="title">Judul <small class="c-red">*</small></label>
                    <input type="text" class="form-control" name="title" value="{{ old('title') ?? old('title') }}" data-parsley-minlength="3" required>
                </div>
                <div class="col-sm-3 form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                    <label for="type">Type</label>
                    <select id="type" name="type" class="form-control select2" required>
                        @if (!empty(old('type')))
                            @foreach($blogsTypes as $dataType)
                                @if($dataType->id == old('type'))
                                    <option value="{{ $dataType->id }}">{{ ucwords($dataType->name)}}</option>
                                @endif
                            @endforeach
                            @foreach($blogsTypes as $dataType)
                                @if($dataType->id != old('type'))
                                    <option value="{{ $dataType->id }}">{{ ucwords($dataType->name)}}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="0">Pilih type</option>
                            @foreach($blogsTypes as $dataType)
                                <option value="{{ $dataType->id }}">{{ ucwords($dataType->name)}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('tags') ? ' has-error' : '' }}">
                    <label for="tags">Tags <small class="c-red">*</small> <small>Pisahkan dengan tanda koma (,).</small></label>
                    <textarea id="tags" name="tags" class="form-control" cols="10" rows="2">{{ old('tags') }}</textarea>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('summary') ? ' has-error' : '' }}">
                    <label>Ringkasan <small class="c-red">*</small></label>
                    <textarea id="summary" name="summary" class="form-control" cols="10" rows="5">{{ old('summary') }}</textarea>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                    <label>Konten <small class="c-red">*</small></label>
                    <textarea id="content" name="content" class="form-control" cols="10" rows="25">{{ old('content') }}</textarea>
                </div>
                <div class="w-100"></div>
                <div class="col card-box{{ $errors->has('image') ? ' has-error' : '' }}">
                    <h4 class="header-title mb-3">Gambar <small class="c-red">*</small></h4>
                    <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/blogs/default.png') }}"  />
                    <small class="text-secondary">Ukuran ideal 300px x 210px.</small>
                </div>
            </div>
        
        </div>
        <div class="card-body">
            <div class="col-md">
                <input type="submit" class="btn btn-orange" name="submit" value="Simpan">
                <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini">Batal</a>
            </div>
        </div>
    </form>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('content');
</script>
@endsection
