@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Template'; 
    $statusBadge    = array('','info','danger','purple','pink','warning','dark');

    //form route
    $formRouteIndex = 'admin-projects.index';
    $formRouteShow = 'admin-projects.show';

    //template
    $formTemplateIndex = 'admin-projects-template.index';
    
    //template select
    $formTaskTemplatestore = 'admin-projects-template.store';
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
                <br><strong>{{ isset($projectTemplate->name) ? strtoupper($projectTemplate->name) : 'Belum ada nama' }}</strong>
            </div>
        </div>

        <form class="w-100" action="{{ route($formTaskTemplatestore) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body bg-gray-lini-2">

                <!-- hidden -->
                <input value="{{ $projectId ?? '' }}" name="project_id" hidden>
                <input value="{{ $projectTemplate->template_id ?? '' }}" name="template_id" hidden>

                <div class="row">
                    <?php $is=1; ?>
                    <div class="col-md alert alert-{{ $statusBadge[$is]}} m-1 text-center">
                        <div class="row m-0 text-left" style="padding-left:5px">
                        @if(count($dataSubcategory) > 0)
                            @foreach($dataSubcategory as $dataSubcat)
                                @if(isset($subcats))
                                    @if($dataSubcat->cat_id == $projectTemplate->template_id && in_array($dataSubcat->id, $subcats))
                                        <div class="col-md form-group">
                                            <input class="form-check-input" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is }}" name="subcat_id[]" checked>
                                            <label class="form-check-label" for="CategoryCheck{{ $is }}">
                                                <small>{{ ucwords($dataSubcat->name) }}</small>
                                            </label>
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
                        @else
                            <div class="col-md form-group">
                                <label class="form-check-label">
                                    <small>Belum ada data sub kategori.</small>
                                </label>
                            </div>
                        @endif
                        </div>
                    </div>

                    @if(isset($infoTaskProject) && count($dataSubcategory) > 0 && $templateCount < 1)
                        <div class="w-100"></div>
                        <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name">Nama template <small class="c-red">*</small></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : $projectTemplate->name }}" data-parsley-minlength="3" required>
                        </div>

                        <div class="col-md mt-2">
                            <label for="">Task <small class="c-red">*</small></label>
                            <select name="task_id" class="form-control select2 mb-1" required>
                                @if($infoTaskProject->id)
                                    <option value="{{ $infoTaskProject->id }}">{{ ucwords($infoTaskProject->name) }}</option>
                                @else
                                    <option value="0">Pilih task</option>
                                    @foreach($infoTaskProject as $projectTask)
                                        @if($projectTask->project_id == $projectId)
                                            <option value="{{ $projectTask->id }}">{{ ucwords($projectTask->name) }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="col-md">
                    @if(isset($taskId) && isset($projectId))
                        <a href="{{ route($formTemplateIndex, 'project_id='.$projectId.'&task_id='.$taskId) }}" type="button" class="btn btn-blue-lini">Kembali</a>
                    @else
                        <a href="{{ route($formTemplateIndex) }}" type="button" class="btn btn-blue-lini">Kembali</a>
                    @endif

                </div>
            </div>
        </form>

    </div> <!-- card -->
@endsection

@section ('script')

@endsection
