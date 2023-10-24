@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Approve laporan'; 
    $statusBadge    = array('','info','danger','purple','pink','warning','dark');

    //form route
    $formRouteIndex = 'user-projects-template.index';

    //image
    $formRouteCreate = 'user-projects-image.create';
    $formRouteShow = 'user-projects-image.show';
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
            <div>
                <small>Proyek:</small> <strong><span class="text-info">{{ isset($projectTemplate->project_name) ? strtoupper($projectTemplate->project_name) : '' }}</span></strong>
                <br><small>Task:</small> <strong><span class="text-danger">{{ isset($projectTemplate->task_name) ? strtoupper($projectTemplate->task_name) : 'Belum ada task' }}</span></strong>
                <br><small>Template:</small> <strong><span class="text-warning">{{ isset($projectTemplate->name) ? strtoupper($projectTemplate->name) : 'Belum ada data' }}</span></strong>
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

                            <div class="row">
                            
                                <div class="col-md m-1 text-center">
                                    <?php $is=1; ?>
                                    <div class="row m-0 text-left" style="padding-left:5px">
                                    @if(count($dataSubcategory) > 0)
                                        @foreach($dataSubcategory as $dataSubcat)
                                            @if(isset($subcats))
                                                @if($dataSubcat->cat_id == $projectTemplate->template_id && in_array($dataSubcat->id, $subcats))
                                                    <div class="col-md form-group">
                                                        <input class="form-check-input" disabled="disabled" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is }}" name="subcat_id[]" checked>
                                                        <label class="form-check-label" for="CategoryCheck{{ $is }}">
                                                            <small>{{ ucwords($dataSubcat->name) }}</small> 

                                                            @if($dataSubcat->imageCount < 3)
                                                                <a href="{{ route($formRouteCreate,'project_id='.$projectTemplate->project_id.'&task_id='.$projectTemplate->task_id.'&template_id='.$projectTemplate->template_id.'&subcat_id='.$dataSubcat->id) }}" class="badge badge-info">upload ({{ $dataSubcat->imageCount }} foto)</a>
                                                            @else
                                                                <a href="{{ route($formRouteCreate,'project_id='.$projectTemplate->project_id.'&task_id='.$projectTemplate->task_id.'&template_id='.$projectTemplate->template_id.'&subcat_id='.$dataSubcat->id) }}" class="badge badge-danger">View ({{ $dataSubcat->imageCount }} foto)</a>
                                                            @endif

                                                            <span class="small">
                                                                @if($dataSubcat->approvedPMCount > 0)
                                                                    <span class="text-success">Done</span>
                                                                @elseif($dataSubcat->approvedCount > 0)
                                                                    @if(Auth::user()->user_level == 3)
                                                                        <span class="text-danger">Menunggu approval Anda</span>
                                                                    @else
                                                                        <span class="text-info">Sedang direview PM</span>
                                                                    @endif
                                                                @elseif($dataSubcat->submittedCount > 0)
                                                                    @if(Auth::user()->user_level == 3)
                                                                        <span class="text-info">Sedang direview QC</span>
                                                                    @else
                                                                        <span class="text-danger">Menunggu approval Anda</span>
                                                                    @endif
                                                                @else
                                                                    <span class="text-warning">Belum disubmit</span>
                                                                @endif
                                                            </span>
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
                                    <!-- customized subcategories -->
                                    <?php $is2=1; ?>
                                    @if(count($dataSubcategoryCustomized) > 0)
                                    <hr>
                                    <div class="row m-0 text-left" style="padding-left:5px">
                                        @foreach($dataSubcategoryCustomized as $dataSubcatCustomized)
                                            @if(isset($subcatcustoms))
                                                @if($dataSubcatCustomized->cat_id == $projectTemplate->template_id && in_array($dataSubcatCustomized->id, $subcatcustoms))
                                                    <div class="col-md form-group">
                                                        <input class="form-check-input" disabled="disabled" type="checkbox" value="{{ $dataSubcatCustomized->id }}" id="CategoryCheck{{ $is2 }}" name="subcat_id[]" checked>
                                                        <label class="form-check-label" for="CategoryCheck{{ $is2 }}">
                                                            <small>{{ ucwords($dataSubcatCustomized->name) }}</small> 

                                                            @if($dataSubcatCustomized->imageCount < 3)
                                                                <a href="{{ route($formRouteCreate,'project_id='.$projectTemplate->project_id.'&task_id='.$projectTemplate->task_id.'&template_id='.$projectTemplate->template_id.'&subcatcustom_id='.$dataSubcatCustomized->id) }}"><span class="text-danger">upload ({{ $dataSubcatCustomized->imageCount }} foto)</span></a>
                                                            @else
                                                                <a href="{{ route($formRouteCreate,'project_id='.$projectTemplate->project_id.'&task_id='.$projectTemplate->task_id.'&template_id='.$projectTemplate->template_id.'&subcatcustom_id='.$dataSubcatCustomized->id) }}">View ({{ $dataSubcatCustomized->imageCount }} foto)</a>
                                                            @endif

                                                            @if($dataSubcatCustomized->approvedPMCount > 0)
                                                                <span class="text-success">Done</span>
                                                            @elseif($dataSubcatCustomized->approvedCount > 0)
                                                                @if(Auth::user()->user_level == 3)
                                                                    <span class="text-danger">Menunggu approval Anda</span>
                                                                @else
                                                                    <span class="text-info">Sedang direview PM</span>
                                                                @endif
                                                            @elseif($dataSubcatCustomized->submittedCount > 0)
                                                                @if(Auth::user()->user_level == 3)
                                                                    <span class="text-info">Sedang direview QC</span>
                                                                @else
                                                                    <span class="text-danger">Menunggu approval Anda</span>
                                                                @endif
                                                            @else
                                                                <span class="text-warning">Belum disubmit</span>
                                                            @endif

                                                        </label>
                                                    </div>
                                                    @if($is2 % 2 == 0)
                                                        <div class="w-100"></div>
                                                    @endif
                                                @endif
                                            @else
                                                <div class="col-md form-group">
                                                    <input class="form-check-input" type="checkbox" value="{{ $dataSubcatCustomized->id }}" id="CategoryCheck{{ $is2 }}" name="subcat_id[]" checked>
                                                    <label class="form-check-label" for="CategoryCheck{{ $is2 }}">
                                                        <small>{{ ucwords($dataSubcatCustomized->name) }}</small>
                                                    </label>
                                                </div>
                                                @if($is2 % 2 == 0)
                                                    <div class="w-100"></div>
                                                @endif
                                            @endif
                                            <?php $is2++; ?>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="col-md mt-2">
                                <a href="{{ route($formRouteIndex, 'project_id='.$projectTemplate->project_id.'&task_id='.$projectTemplate->task_id) }}" type="button" class="btn btn-blue-lini">Kembali</a>
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
