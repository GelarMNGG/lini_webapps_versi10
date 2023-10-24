@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Edit template'; 
    $statusBadge    = array('','info','danger','purple','pink','warning','dark');
    //form route
        $formRouteIndex = 'user-projects.index';
        $formRouteUpdate = 'user-projects-template.update';
        $formRouteCategoryStore = 'user-projects-category.store';
    //subcategory
        $formRouteSubCategoryStore = 'user-projects-subcategory.store';
    //add subcategory kustom
        $formSubcategoryCreate = 'subcategory-customized.store';
    //file template
        $formRouteFileTemplateCreate = 'user-projects-report-file.create';
        $formRouteFileTemplateEdit = 'user-projects-report-file.edit';
    //default cat array
        if (!isset($subcats) || $subcats == null) {
            $subcats = [];
        }
        if (!isset($subcatscustom) || $subcatscustom == null) {
            $subcatscustom = [];
        }
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
            Proyek: <strong><span class="text-info">{{ isset($projectTemplate->project_name) ? strtoupper($projectTemplate->project_name) : 'Belum ada' }}</span></strong>
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
                                <input value="{{ $projectTemplate->project_id }}" name="project_id" hidden>
                                <input value="{{ $projectTemplate->task_id }}" name="task_id" hidden>

                                <div class="row">

                                    <?php $i=1; ?> 
                                    <div class="col-md alert alert-success m-1 text-center">
                                        <div class="row m-0 text-left" style="padding-left:5px">
                                            <?php $is=1; ?>
                                            @foreach($dataSubcategory as $dataSubcat)

                                                @if(isset($subcats))
                                                    @if($dataSubcat->cat_id == $projectTemplate->template_id && in_array($dataSubcat->id, $subcats))
                                                        <div class="col-md form-group">
                                                            <input class="form-check-input" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is }}" name="subcat_id[]" checked>
                                                            <label class="form-check-label" for="CategoryCheck{{ $is }}">
                                                                <small>{{ ucwords($dataSubcat->name) }}</small>
                                                            </label>
                                                            <!-- template files -->
                                                            @if($projectTemplate->type == 6)
                                                                @if(isset($projectTemplateFile))
                                                                    <span class="float-right">
                                                                        <a href="{{ asset('files/projects/report/template_files/'.$projectTemplateFile->name) }}"> [{{ $projectTemplateFile->name}}] </a>
                                                                        
                                                                        <a href="{{ route($formRouteFileTemplateEdit,$projectTemplateFile->id) }}"><i class="fas fa-edit text-danger"></i> <span class="small text-danger">Ubah</span></a>
                                                                    </span>
                                                                @else
                                                                    <!-- upload file -->
                                                                    <span class="float-right">
                                                                        <a href="{{ route($formRouteFileTemplateCreate,'pid='.$projectTemplate->project_id.'&tid='.$projectTemplate->task_id.'&prts='.$projectTemplate->id.'&sid='.$dataSubcat->id) }}"><i class="fas fa-upload text-danger"></i> <span class="small text-danger">Upload file</span></a>
                                                                    </span>
                                                                    <!-- upload file end --> 
                                                                @endif
                                                            @endif
                                                            <!-- template files end -->
                                                        </div>
                                                        @if($is % 2 == 0)
                                                            <div class="w-100"></div>
                                                        @endif
                                                    @elseif($dataSubcat->cat_id == $projectTemplate->template_id)
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
                                                @else
                                                    <div class="col-md form-group">
                                                        <input class="form-check-input" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is }}" name="subcat_id[]" checked>
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
                                    <div class="w-100"></div>
                                    @if(count($subcatsCustomized) > 0)
                                    <div class="col-md mt-2">
                                        <label for="name">Sub kategori tambahan</label>
                                    </div>
                                    <div class="w-100"></div>
                                    <div class="col-md alert alert-warning m-1 text-center">
                                        <div class="row m-0 text-left" style="padding-left:5px">
                                            <?php $is2=1; ?>
                                            @foreach($subcatsCustomized as $dataSubcatCustom)

                                                @if(isset($subcatscustom))
                                                    @if($dataSubcatCustom->cat_id == $projectTemplate->template_id && in_array($dataSubcatCustom->id, $subcatscustom))
                                                        <div class="col-md form-group">
                                                            <input class="form-check-input" type="checkbox" value="{{ $dataSubcatCustom->id }}" id="CategoryCheck{{ $is2 }}" name="subcatcustom_id[]" checked>
                                                            <label class="form-check-label" for="CategoryCheck{{ $is2 }}">
                                                                <small>{{ ucwords($dataSubcatCustom->name) }}</small>
                                                            </label>
                                                        </div>
                                                        @if($is2 % 2 == 0)
                                                            <div class="w-100"></div>
                                                        @endif
                                                    @elseif($dataSubcatCustom->cat_id == $projectTemplate->template_id)
                                                        <div class="col-md form-group">
                                                            <input class="form-check-input" type="checkbox" value="{{ $dataSubcatCustom->id }}" id="CategoryCheck{{ $is2 }}" name="subcatcustom_id[]">
                                                            <label class="form-check-label" for="CategoryCheck{{ $is2 }}">
                                                                <small>{{ ucwords($dataSubcatCustom->name) }}</small>
                                                            </label>
                                                        </div>
                                                        @if($is2 % 2 == 0)
                                                            <div class="w-100"></div>
                                                        @endif
                                                    @endif
                                                @else
                                                    <div class="col-md form-group">
                                                        <input class="form-check-input" type="checkbox" value="{{ $dataSubcatCustom->id }}" id="CategoryCheck{{ $is2 }}" name="subcatcustom_id[]" checked>
                                                        <label class="form-check-label" for="CategoryCheck{{ $is2 }}">
                                                            <small>{{ ucwords($dataSubcatCustom->name) }}</small>
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

                                        <!-- if category = image, then add sub on else off -->
                                        @if($projectTemplate->type == 1 || count($dataSubcategory) < 1)
                                            <button type="button" class="btn btn-success" data-toggle="collapse" data-target="#add_subcategory" aria-expanded="false" aria-controls="add_subcategory">Tambah sub kategori</button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="collapse" id="add_subcategory">
                            <div class="card-box">
                                <form action="{{ route($formRouteSubCategoryStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                    @csrf
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                                <label for="name">Nama sub kategori</label>

                                                <input value="{{ $projectTemplate->template_id }}" name="cat_id" hidden>
                                                <input value="1" name="page" hidden>
                                                <input type="text" class="form-control" name="name" value="{{ old('name') ?? old('name') }}" data-parsley-minlength="3" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" style="padding-top:30px;">
                                                <input type="submit" class="btn btn-info" name="submit" value="Tambah">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- if category = image, then add sub on else off -->
                            @if($projectTemplate->type == 1 || count($dataSubcategory) < 1)
                            <div class="col-md">
                                <!-- subcategory customized -->
                                <form class="w-100" action="{{ route($formSubcategoryCreate) }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <!-- hidden -->
                                    <input value="{{ $projectTemplate->project_id }}" name="project_id" hidden>
                                    <input value="{{ $projectTemplate->task_id }}" name="task_id" hidden>
                                    <input value="{{ $projectTemplate->template_id }}" name="cat_id" hidden>

                                    <div class="row ml-0 mr-0 alert alert-warning">
                                        <div class="col-md m-1{{ $errors->has('subcatname') ? ' has-error' : '' }}">
                                            <label for="subcatname">Tambah sub kategori (kustom)<small class="c-red">*</small></label>
                                            <input type="text" class="form-control" name="subcatname" value="{{ old('subcatname') }}" data-parsley-minlength="2" required>
                                        </div>
                                        <div class="col-md mt-1">
                                            <label class="text-white">.</label>
                                            <div class="w-100"></div>
                                            <button type="submit" class="btn btn-warning t-white" name="submit">Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    @if(count($projectReportCategorys) > 0)
                        <div class="col-md card-box">
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
            </div> <!-- container-fluid -->
        </div>
    </div> <!-- card -->
@endsection

@section ('script')

@endsection
