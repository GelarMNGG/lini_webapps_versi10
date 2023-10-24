@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'blog';
    $formRouteIndex = 'admin-blog.index';
    $formRouteUpdate= 'admin-blog.update';
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

    <form action="{{ route($formRouteUpdate, $blog->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method('PUT')

        <div class="card-body bg-gray-lini-2">           
            <div class="row">
                <div class="col-md form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                    <label for="title">Judul <small class="c-red">*</small></label>
                    <input type="text" class="form-control" name="title" value="{{ old('title') ? old('title') : $blog->title }}" data-parsley-minlength="3" required>
                </div>
                <div class="col-md mb-2">
                    <label for="">Status <small class="c-red">*</small></label>
                    <select name="status" class="form-control{{ $errors->has('status') ? ' has-error' : '' }}" required>
                        @if (old('status') == 1 || $blog->status == 1)
                            <option value="1">Published</option>
                            <option value="o">Draft</option>
                        @else
                            <option value="0">Draft</option>
                            <option value="1">Published</option>
                        @endif
                    </select>
                </div>
                <div class="col-md mb-2">
                    <label for="">Kategori <small class="c-red">*</small></label>
                    <select id="category_id" name="category_id" class="form-control disabled{{ $errors->has('category_id') ? ' has-error' : '' }}" required>
                        <option value="0">Belum ada</option>
                    </select>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('tags') ? ' has-error' : '' }}">
                    <label for="tags">Tags <small class="c-red">*</small> <small>Pisahkan dengan tanda koma (,).</small></label>
                    <textarea id="tags" name="tags" class="form-control" cols="10" rows="2">{{ old('tags') ? old('tags') : $blog->tags }}</textarea>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('summary') ? ' has-error' : '' }}">
                    <label>Ringkasan <small class="c-red">*</small></label>
                    <textarea id="summary" name="summary" class="form-control" cols="10" rows="5">{{ old('summary') ? old('summary') : $blog->summary }}</textarea>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                    <label>Konten <small class="c-red">*</small></label>
                    <textarea id="content" name="content" class="form-control" cols="10" rows="25">{{ old('content') ? old('content') : $blog->content }}</textarea>
                </div>
                <div class="w-100"></div>
                <div class="col card-box{{ $errors->has('image') ? ' has-error' : '' }}">
                    <h4 class="header-title mb-3">Gambar <small class="c-red">*</small></h4>
                    <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/blogs/'.$blog->image) }}"  />
                    <small class="text-secondary">Ukuran ideal 300px x 210px.</small>
                </div>
            </div>     
        </div>
        <div class="card-body">
            <div class="form-group">
                <input type="submit" class="btn btn-orange" name="submit" value="Ubah">
                <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini">Batal</a>
            </div>
        </div>

    </form>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'content' );
</script>
@endsection
