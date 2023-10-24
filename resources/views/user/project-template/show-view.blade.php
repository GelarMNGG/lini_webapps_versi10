@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Template'; 
    $statusBadge    = array('','info','danger','purple','pink','warning','dark');
    //form route
    $formRouteIndex = 'user-projects.index';
    $formRouteShow = 'user-projects.show';
    //template
    $formTemplateIndex = 'user-projects-template.index';
    $formTemplateUpdate = 'user-projects-template.update';
    //category
    $formRouteCategoryStore = 'user-projects-category.store';
    //template select
    $formTaskTemplatestore = 'user-projects-template.store';
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
        <p class="alert alert-danger">{{ $errors->first() }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
    @endif
</div>

    <div class="card mt-2">
        <div class="card-header text-center bb-orange">
            <div>{{ ucfirst($pageTitle) }}
                <br><strong>{{ $projectTemplate->name != null ? strtoupper($projectTemplate->name) : 'Belum ada nama' }}</strong>
            </div>
        </div>

        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card-box">
                            <form class="w-100" action="{{ route($formTaskTemplatestore) }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <!-- hidden -->
                                <input value="{{ $projectId }}" name="project_id" hidden>

                                <div class="row">

                                    <?php $i=1; ?>
                                    <div class="col-md alert alert-{{ $statusBadge[$i]}} m-1 text-center">
                                        <div class="row m-0 text-left" style="padding-left:5px">
                                            <?php $is2=1; ?>
                                            @if(count($dataSubcategory) > 0)
                                                @foreach($dataSubcategory as $dataSubcat)
                                                        <div class="col-md form-group">
                                                            <input class="form-check-input" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is2 }}" name="subcat_id[]" checked>
                                                            <label class="form-check-label" for="CategoryCheck{{ $is2 }}">
                                                                <small>{{ ucwords($dataSubcat->name) }}</small>
                                                            </label>
                                                        </div>
                                                        @if($is2 % 2 == 0)
                                                            <div class="w-100"></div>
                                                        @endif
                                                    
                                                    <?php $is2++; ?>
                                                @endforeach
                                            @else
                                                <div class="col-md form-group">
                                                    <label class="form-check-label">
                                                        <small>Belum ada data sub kategori.</small>
                                                    </label>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="w-100"></div>
                                    <div class="col-md">
                                        <a href="{{ route($formTemplateIndex) }}" type="button" class="btn btn-secondary">Kembali</a>

                                        <button type="button" class="btn btn-success" data-toggle="collapse" data-target="#add_category" aria-expanded="false" aria-controls="add_category">Tambah kategori</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="collapse" id="add_category">
                            <div class="card-box">
                                <form action="{{ route($formRouteCategoryStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                                <label for="name">Nama kategori</label>
                                                <input type="text" class="form-control" name="name" value="{{ old('name') ?? old('name') }}" data-parsley-minlength="3" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" style="padding-top:30px;">
                                                <input type="submit" class="btn btn-info" name="submit" value="Ajukan">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div> <!-- container-fluid -->
        </div>
    </div> <!-- card -->
@endsection

@section ('script')

@endsection
