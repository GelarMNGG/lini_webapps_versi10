@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'template'; 
    $statusBadge    = array('','info','danger','purple','pink','warning','dark');
    //form route
    $formRouteIndex = 'admin-projects.index';
    $formRouteShow = 'admin-projects.show';
    $formRouteUpdate = 'admin-projects-template.update';
    $formRouteCategoryStore = 'admin-projects-category.store';
    //default cat array
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
</div>

    <div class="card">
        <div class="card-header text-center">
            <div>{{ ucfirst($pageTitle) }}
                <br><strong>{{ isset($projectTemplate->name) ? strtoupper($projectTemplate->name) : 'Data tidak tersedia' }}</strong>
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
                                                    <input class="" type="checkbox" value="{{ $dataCat->id }}" id="CategoryCheck{{ $i }}" name="cat_id[]" disabled="disabled" checked>
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
                                                                <input class="form-check-input" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is }}" name="subcat_id[]" disabled="disabled" checked>
                                                                <label class="form-check-label" for="CategoryCheck{{ $is }}">
                                                                    <small>{{ ucwords($dataSubcat->name) }}</small>
                                                                </label>
                                                            </div>
                                                            @if($is % 2 == 0)
                                                                <div class="w-100"></div>
                                                            @endif
                                                        @elseif($dataSubcat->cat_id == $dataCat->id)
                                                            <div class="col-md form-group">
                                                                <input class="form-check-input" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is }}" name="subcat_id[]" disabled="disabled">
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
                                                    <input class="" type="checkbox" value="{{ $dataCat->id }}" id="CategoryCheck{{ $i }}" name="cat_id[]" disabled="disabled">
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
                                                                <input class="form-check-input" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is2 }}" name="subcat_id[]" disabled="disabled" checked>
                                                                <label class="form-check-label" for="CategoryCheck{{ $is2 }}">
                                                                    <small>{{ ucwords($dataSubcat->name) }}</small>
                                                                </label>
                                                            </div>
                                                            @if($is2 % 2 == 0)
                                                                <div class="w-100"></div>
                                                            @endif
                                                        @elseif($dataSubcat->cat_id == $dataCat->id)
                                                            <div class="col-md form-group">
                                                                <input class="form-check-input" type="checkbox" value="{{ $dataSubcat->id }}" id="CategoryCheck{{ $is2 }}" name="subcat_id[]" disabled="disabled">
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

                                    <div class="w-100 mb-2"></div>
                                    <a href="{{ route($formRouteShow, $projectTemplate->project_id) }}" type="button" class="btn btn-secondary">Kembali</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div> <!-- container-fluid -->
        </div>
    </div> <!-- card -->
@endsection

@section ('script')

@endsection
