
@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Upload document';
    $formRouteIndex = 'tech-input-doc.index';
    $formRouteStore = 'tech-input-doc.store';
    $formRouteStore = 'tech-input-doc.store';

    //route back
    $formRouteBack = 'tech-input-data-diri.index';
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
    <div class="card-header text-center bb-orange">
        <strong class="card-header text-center text-uppercase">{{ ucfirst($pageTitle) }}</strong>
    </div>

    <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md form-group{{ $errors->has('doc_type') ? ' has-error' : '' }} ">
                    <label for="">Tipe dokumen</label>
                    <select name="doc_type" class="form-control" placeholder="Tipe dokumen" required>
                        <?php 
                            if (old('doc_type') != NULL) {
                                $dataTypes = old('doc_type');
                            }else{
                                $dataTypes = NULL;
                            }
                        ?>
                        @if($dataTypes != NULL)
                            @foreach($docTypes as $docType)
                                @if($docType->id == $dataTypes)
                                    <option value="{{ $docType->id }}">{{ ucwords($docType->name)}}</option>
                                @endif
                            @endforeach
                            @foreach($docTypes as $docType)
                                @if($docType->id != $dataTypes && !in_array($docType->id, $docDatas))
                                    <option value="{{ $docType->id }}">{{ ucwords($docType->name)}}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="0">Pilih salah satu</option>
                            @foreach($docTypes as $docType)
                                @if(!in_array($docType->id, $docDatas))
                                    <option value="{{ $docType->id }}">{{ ucwords($docType->name)}}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="w-100"></div>
                <div class="col-md{{ $errors->has('image') ? ' has-error' : '' }}">
                    <div class="card-box">
                        <h4 class="header-title mb-3">Dokumen</h4>
                        <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/upload-doc/tech/default.png') }}" />
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md mt-2 mb-2">
                <button type="submit" class="btn btn-orange" name="submit">Simpan</button>
                <a href="{{ route($formRouteBack) }}" type="button" class="btn btn-blue-lini">Kembali</a>
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
