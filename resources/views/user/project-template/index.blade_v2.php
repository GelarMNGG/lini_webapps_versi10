@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Input kebutuhan gambar'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    //form route
    $formRouteIndex = 'user-projects.index';
    $formRouteStore = 'user-projects-template.store';
    $formRouteCategoryStore = 'user-projects-category.store';
?>
@endsection

@section('content')
<div class="flash-message">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

    <div class="card">
        <div class="card-header text-center">
            <div>{{ ucfirst($pageTitle) }}
                <br><strong>{{ strtoupper($projectData->name) }}</strong>
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
                            <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <!-- hidden data -->
                                <input type="text" value="{{ $projectData->id }}" name="project_id" hidden>
                                <input type="text" value="{{ $projectData->status }}" name="status" hidden>
                                
                                <div class="row">
                                    <?php $i=1; ?>
                                    @foreach($dataCategory as $dataCat)
                                        <div class="col-md alert alert-{{ $statusBadge[$i]}} m-1 text-center">
                                            <div class="form-group">
                                                <input class="" type="checkbox" value="{{ $dataCat->id }}" id="CategoryCheck{{ $i }}" name="cat_id[]">
                                                <br>
                                                <label class="" for="CategoryCheck{{ $i }}" style="min-height:43px;">
                                                    <strong>{{ ucwords($dataCat->name) }}</strong>
                                                </label>
                                            </div>
                                            <hr>
                                            <div class="row m-0" style="padding-left:5px">
                                                <?php $is=1; ?>
                                                @foreach($dataSubcategory as $dataSubcat)
                                                    @if($dataSubcat->cat_id == $dataCat->id)
                                                        <div class="form-group text-left">
                                                            <input class="form-check-input" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is }}" name="subcat_id[]">
                                                            <label class="form-check-label" for="CategoryCheck{{ $is }}">
                                                                <small>{{ ucwords($dataSubcat->name) }}</small>
                                                            </label>
                                                        </div>
                                                        @if($is % 1 == 0)
                                                            <div class="w-100"></div>
                                                        @endif

                                                    @endif
                                                    <?php $is++; ?>
                                                @endforeach
                                            </div>
                                        </div>
                                        @if($i % 4 == 0)
                                            <div class="w-100"></div>
                                        @endif
                                        <?php $i++; if($i==7){$i = 1;} ?>
                                    @endforeach
                                    <div class="w-100"></div>
                                    <div class="col-md mt-3">
                                        <label for=""></label>
                                        <button type="submit" class="btn btn-info t-white" name="submit">Simpan</button>
                                        <a href="{{ route($formRouteIndex,'status='.$projectData->status) }}" type="button" class="btn btn-secondary">Kembali</a>
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

                        @if(sizeof($projectReportCategorys) > 0)
                        <div class="card-box">
                            <h3 class="text-muted">Daftar kategori lainnya</h3>
                            @foreach($projectReportCategorys as $submitedCategory)
                                <div class="alert alert-warning">{{ ucfirst($submitedCategory->name) }} | {{ date('l, d F Y',strtotime($submitedCategory->date_submitted))}} | <a href="{{ route('user-projects-category.index', 'project_id='.$projectData->id) }}" class="badge badge-pink">Selengkapnya</a>
                                
                                    @if($submitedCategory->status == 3)
                                        <span class="badge badge-danger float-right">Pending</span>
                                    @else
                                        <span class="badge badge-success float-right">Approved</span>
                                    @endif

                                </div>
                            @endforeach
                        </div>
                        @endif

                    </div>
                </div>
            </div> <!-- container-fluid -->
        </div>
    </div> <!-- card -->
@endsection

@section ('script')

@endsection
