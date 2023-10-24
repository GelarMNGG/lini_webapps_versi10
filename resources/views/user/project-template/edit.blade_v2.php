@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Edit template'; 
    $statusBadge    = array('','info','danger','purple','pink','warning','dark');
    //form route
    $formRouteIndex = 'user-projects.index';
    $formRouteUpdate = 'user-projects-template.update';
    $formRouteCategoryStore = 'user-projects-category.store';
    //default cat array
    if ($cats == null) {
        $cats = [];
    }
    if ($subcats == null) {
        $subcats = [];
    }
?>
@endsection

@section('content')
<div class="flash-message">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
    @if ($errors->any())
        <p class="alert alert-danger">{{ $errors->first() }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
    @endif
</div>

    <div class="card">
        <div class="card-header text-center">
            Proyek: <strong><span class="text-info">{{ strtoupper($projectTemplate->project_name) }}</span></strong>
            <br>Task: <strong><span class="text-danger">{{ isset($projectTemplate->task_name) ? strtoupper($projectTemplate->task_name) : 'Belum ada task' }}</span></strong>
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
                            <form class="w-100" action="{{ route($formRouteUpdate, $projectTemplate->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- hidden -->
                                <input value="{{ $projectTemplate->project_status }}" name="project_status" hidden>

                                <div class="row">

                                    <?php $i=1; ?>
                                    @foreach($dataCategory as $dataCat)
                                        @if(in_array($dataCat->id, $cats))
                                            <div class="col-md alert alert-success m-1 text-center">
                                                <div class="form-group">
                                                    <input class="" type="checkbox" value="{{ $dataCat->id }}" id="CategoryCheck{{ $i }}" name="cat_id[]" checked>
                                                    <br>
                                                    <label class="" for="CategoryCheck{{ $i }}" style="min-height:43px;">
                                                        <strong>{{ ucwords($dataCat->name) }} ll</strong>
                                                    </label>
                                                </div>
                                                <hr>
                                                <div class="row m-0 text-left" style="padding-left:5px">
                                                    <?php $is=1; ?>
                                                    @foreach($dataSubcategory as $dataSubcat)
                                                        @if($dataSubcat->cat_id == $dataCat->id && in_array($dataSubcat->id, $subcats))
                                                            <div class="col-md form-group">
                                                                <input class="form-check-input" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is }}" name="subcat_id[]" checked>
                                                                <label class="form-check-label" for="CategoryCheck{{ $is }}">
                                                                    <small>{{ ucwords($dataSubcat->name) }}</small>
                                                                </label>
                                                            </div>
                                                            @if($is % 2 == 0)
                                                                <div class="w-100"></div>
                                                            @endif
                                                        @elseif($dataSubcat->cat_id == $dataCat->id)
                                                            <div class="col-md form-group">
                                                                <input class="form-check-input" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is }}" name="subcat_id[]">
                                                                <label class="form-check-label" for="CategoryCheck{{ $is }}">
                                                                    <small>{{ ucwords($dataSubcat->name) }}</small>
                                                                </label>
                                                            </div>
                                                            @if($is % 2 == 0)
                                                                <div class="w-100"></div>
                                                            @endif
                                                        @endif
                                                        <?php $is++; ?>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md alert alert-{{ $statusBadge[$i]}} m-1 text-center">
                                                <div class="form-group">
                                                    <input class="" type="checkbox" value="{{ $dataCat->id }}" id="CategoryCheck{{ $i }}" name="cat_id[]">
                                                    <br>
                                                    <label class="" for="CategoryCheck{{ $i }}" style="min-height:43px;">
                                                        <strong>{{ ucwords($dataCat->name) }}</strong>
                                                    </label>
                                                </div>
                                                <hr>
                                                <div class="row m-0 text-left" style="padding-left:5px">
                                                    <?php $is2=1; ?>
                                                    @foreach($dataSubcategory as $dataSubcat)
                                                        @if($dataSubcat->cat_id == $dataCat->id && in_array($dataSubcat->id, $subcats))
                                                            <div class="col-md form-group">
                                                                <input class="form-check-input" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is2 }}" name="subcat_id[]" checked>
                                                                <label class="form-check-label" for="CategoryCheck{{ $is2 }}">
                                                                    <small>{{ ucwords($dataSubcat->name) }}</small>
                                                                </label>
                                                            </div>
                                                            @if($is2 % 2 == 0)
                                                                <div class="w-100"></div>
                                                            @endif
                                                        @elseif($dataSubcat->cat_id == $dataCat->id)
                                                            <div class="col-md form-group">
                                                                <input class="form-check-input" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is2 }}" name="subcat_id[]">
                                                                <label class="form-check-label" for="CategoryCheck{{ $is2 }}">
                                                                    <small>{{ ucwords($dataSubcat->name) }}</small>
                                                                </label>
                                                            </div>
                                                            @if($is2 % 2 == 0)
                                                                <div class="w-100"></div>
                                                            @endif
                                                        @endif
                                                        <?php $is2++; ?>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif 
                                        
                                        @if($i % 4 == 0)
                                            <div class="w-100"></div>
                                        @endif
                            
                                        <?php $i++; if($i==5){$i = 1;} ?>

                                    @endforeach

                                    <div class="w-100"></div>

                                    <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name">Nama template <small class="c-red">*</small></label>
                                        <input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : $projectTemplate->name }}" data-parsley-minlength="3" required>
                                    </div>

                                    <div class="col-md mt-2 form-group">
                                        <label for="" class="text-white">.</label>
                                        <div class="w-100"></div>
                                        <button type="submit" class="btn btn-info t-white" name="submit">Simpan</button>

                                        <a href="javascript:history.go(-1)" type="button" class="btn btn-secondary">Kembali</a>


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

                        @if(count($projectReportCategorys) > 0)
                            <div class="card-box">
                                <h3 class="text-muted">Daftar kategori lainnya</h3>
                                @foreach($projectReportCategorys as $submitedCategory)
                                    <div class="alert alert-warning">{{ ucfirst($submitedCategory->name) }} | {{ date('l, d F Y',strtotime($submitedCategory->date_submitted))}} | <a href="{{ route('user-projects-category.index', 'project_id='.$projectTemplate->project_id) }}" class="badge badge-pink">Selengkapnya</a>
                                    
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
