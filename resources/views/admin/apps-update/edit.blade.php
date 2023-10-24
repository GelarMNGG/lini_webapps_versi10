@extends('layouts.dashboard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Edit update'; 
    $formRouteIndex = 'apps-update.index';
    $formRouteUpdate = 'apps-update.update';
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

    <form class="w-100" action="{{ route($formRouteUpdate, $appsUpdateData->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Judul update <small class="c-red">*</small></label>
                        <input type="text" name="title" class="form-control{{ $errors->has('title') ? ' has-error' : '' }}" value="{{ old('title') ? old('title') : ucfirst($appsUpdateData->title) }}" placeholder="Judul update" required>
                    </div>
                </div>
                <div class="col-md form-group">
                    <label for="">Kategori </label>
                    <select id="cat_id" name="cat_id" class="form-control select2{{ $errors->has('cat_id') ? ' has-error' : '' }}" required>
                        <?php
                            if(old('cat_id') != null) {
                                $cat_id = old('cat_id');
                            }elseif(isset($appsUpdateData->cat_id)){
                                $cat_id = $appsUpdateData->cat_id;
                            }else{
                                $cat_id = null;
                            }
                        ?>
                        @if ($cat_id != null)
                            @foreach ($appsUpdateCats as $dataOne)
                                @if ($dataOne->id == $cat_id)
                                    <option value='{{ strtolower($dataOne->id) }}'>{{ ucwords(strtolower($dataOne->name)) }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="0">Pilih kategori</option>
                        @endif
                        @foreach($appsUpdateCats as $dataOne)
                            <option value="{{ strtolower($dataOne->id) }}">{{ ucwords($dataOne->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <button type="submit" class="btn btn-orange" name="submit">Kirim</button>
                <a href="{{ route($formRouteIndex) }}" type="button" class="btn btn-blue-lini">Kembali</a>
            </div>
        </div>
    </form>


</div> <!-- container-fluid -->
@endsection

@section ('script')

<script>
</script>

@endsection
