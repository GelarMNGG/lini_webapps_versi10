@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Upload laporan'; 
    $statusBadge    = array('','info','danger','purple','pink','warning','dark');
    //form route
        $formRouteIndex = 'report-tech.index';
        $formRouteCreate = 'report-tech.create';
        $formRouteShow = 'report-tech.show';
    //upload file
        $formRouteFileStore = 'tech-report-file.store';
        $formRouteFileUpdate = 'tech-report-file.update';
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
            <div class="row">
                <div class="col-md m-1 text-center">
                    <?php $picture = 1; $text = 2; $files = 6;?>
                    @if($projectTemplate->type == $picture)
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
                                                    <a href="{{ route($formRouteCreate,'project_id='.$projectTemplate->project_id.'&task_id='.$projectTemplate->task_id.'&template_id='.$projectTemplate->template_id.'&subcat_id='.$dataSubcat->id) }}" class="btn btn-info">upload ({{ $dataSubcat->imageCount }} foto)</a>
                                                @else
                                                    <a href="{{ route($formRouteCreate,'project_id='.$projectTemplate->project_id.'&task_id='.$projectTemplate->task_id.'&template_id='.$projectTemplate->template_id.'&subcat_id='.$dataSubcat->id) }}" class="btn btn-danger">View ({{ $dataSubcat->imageCount }} foto)</a>
                                                @endif

                                                <span class="small">
                                                    @if($dataSubcat->approvedPMCount > 0)
                                                        <span class="text-success">Done</span>
                                                    @elseif($dataSubcat->approvedCount > 0)
                                                        <span class="text-info">Sedang direview PM</span>
                                                    @elseif($dataSubcat->submittedCount > 0)
                                                        <span class="text-info">Sedang direview QC</span>
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
                        @if($subcatsCustom != null)
                        <hr>
                        <div class="row m-0 text-left" style="padding-left:5px">
                            @foreach($dataSubcategoryCustomized as $dataSubcatCustomized)
                                @if(isset($subcatsCustom))
                                    @if($dataSubcatCustomized->cat_id == $projectTemplate->template_id && in_array($dataSubcatCustomized->id, $subcatsCustom))
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
                                                    <span class="text-info">Sedang direview PM</span>
                                                @elseif($dataSubcatCustomized->submittedCount > 0)
                                                    <span class="text-info">Sedang direview QC</span>
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
                    @elseif($projectTemplate->type == $text)
                        <?php $ist=1; ?>
                        <div class="row m-0 text-left" style="padding-left:5px">
                        @if(count($dataSubcategoryText) > 0)
                            @foreach($dataSubcategoryText as $dataSubcatText)
                                @if(isset($subcats))
                                    @if($dataSubcatText->cat_id == $projectTemplate->template_id && in_array($dataSubcatText->id, $subcats))
                                        <div class="col-md form-group">
                                            <input class="form-check-input" disabled="disabled" type="checkbox" value="{{ $dataSubcatText->id }}" id="CategoryCheck{{ $ist }}" name="subcat_id[]" checked>
                                            <label class="form-check-label" for="CategoryCheck{{ $ist }}">
                                                <small>{{ ucwords($dataSubcatText->name) }}</small> 

                                                @if($dataSubcatText->textCount < 1)
                                                    <a href="{{ route($formRouteCreate,'project_id='.$projectTemplate->project_id.'&task_id='.$projectTemplate->task_id.'&template_id='.$projectTemplate->template_id.'&subcat_id='.$dataSubcatText->id) }}" class="btn btn-info">upload text</a>
                                                @else
                                                    <a href="{{ route($formRouteCreate,'project_id='.$projectTemplate->project_id.'&task_id='.$projectTemplate->task_id.'&template_id='.$projectTemplate->template_id.'&subcat_id='.$dataSubcatText->id) }}" class="btn btn-danger">View text</a>
                                                @endif

                                                <span class="small">
                                                    @if($dataSubcatText->approvedPMCount > 0)
                                                        <span class="text-success">Done</span>
                                                    @elseif($dataSubcatText->approvedCount > 0)
                                                        <span class="text-info">Sedang direview PM</span>
                                                    @elseif($dataSubcatText->submittedCount > 0)
                                                        <span class="text-info">Sedang direview QC</span>
                                                    @else
                                                        <span class="text-warning">Belum disubmit</span>
                                                    @endif
                                                </span>
                                            </label>
                                        </div>
                                        @if($ist % 2 == 0)
                                            <div class="w-100"></div>
                                        @endif
                                    @endif
                                @else
                                    <div class="col-md form-group">
                                        <input class="form-check-input" type="checkbox" value="{{ $dataSubcatText->id }}" id="CategoryCheck{{ $ist }}" name="subcat_id[]" checked>
                                        <label class="form-check-label" for="CategoryCheck{{ $ist }}">
                                            <small>{{ ucwords($dataSubcatText->name) }}</small>
                                        </label>
                                    </div>
                                    @if($ist % 2 == 0)
                                        <div class="w-100"></div>
                                    @endif
                                @endif
                                <?php $ist++; ?>
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
                        <?php $ist2=1; ?>
                        @if($subcatsCustom != null)
                        <hr>
                        <div class="row m-0 text-left" style="padding-left:5px">
                            @foreach($dataSubcategoryCustomizedText as $dataSubcatCustomizedText)
                                @if(isset($subcatsCustom))
                                    @if($dataSubcatCustomizedText->cat_id == $projectTemplate->template_id && in_array($dataSubcatCustomizedText->id, $subcatsCustom))
                                        <div class="col-md form-group">
                                            <input class="form-check-input" disabled="disabled" type="checkbox" value="{{ $dataSubcatCustomizedText->id }}" id="CategoryCheck{{ $ist2 }}" name="subcat_id[]" checked>
                                            <label class="form-check-label" for="CategoryCheck{{ $ist2 }}">
                                                <small>{{ ucwords($dataSubcatCustomizedText->name) }}</small> 

                                                @if($dataSubcatCustomizedText->textCount < 1)
                                                    <a href="{{ route($formRouteCreate,'project_id='.$projectTemplate->project_id.'&task_id='.$projectTemplate->task_id.'&template_id='.$projectTemplate->template_id.'&subcatcustom_id='.$dataSubcatCustomizedText->id) }}"><span class="text-danger">upload text</span></a>
                                                @else
                                                    <a href="{{ route($formRouteCreate,'project_id='.$projectTemplate->project_id.'&task_id='.$projectTemplate->task_id.'&template_id='.$projectTemplate->template_id.'&subcatcustom_id='.$dataSubcatCustomizedText->id) }}">View text</a>
                                                @endif

                                                @if($dataSubcatCustomizedText->approvedPMCount > 0)
                                                    <span class="text-success">Done</span>
                                                @elseif($dataSubcatCustomizedText->approvedCount > 0)
                                                    <span class="text-info">Sedang direview PM</span>
                                                @elseif($dataSubcatCustomizedText->submittedCount > 0)
                                                    <span class="text-info">Sedang direview QC</span>
                                                @else
                                                    <span class="text-warning">Belum disubmit</span>
                                                @endif

                                            </label>
                                        </div>
                                        @if($ist2 % 2 == 0)
                                            <div class="w-100"></div>
                                        @endif
                                    @endif
                                @else
                                    <div class="col-md form-group">
                                        <input class="form-check-input" type="checkbox" value="{{ $dataSubcatCustomizedText->id }}" id="CategoryCheck{{ $ist2 }}" name="subcat_id[]" checked>
                                        <label class="form-check-label" for="CategoryCheck{{ $ist2 }}">
                                            <small>{{ ucwords($dataSubcatCustomizedText->name) }}</small>
                                        </label>
                                    </div>
                                    @if($ist2 % 2 == 0)
                                        <div class="w-100"></div>
                                    @endif
                                @endif
                                <?php $ist2++; ?>
                            @endforeach
                        </div>
                        @endif
                    @elseif($projectTemplate->type == $files)



                        <?php $isf=1; ?>
                        <div class="row m-0 text-left" style="padding-left:5px">
                            @if(count($dataSubcategoryFiles) > 0)
                                @foreach($dataSubcategoryFiles as $dataSubcatFile)
                                    @if(isset($subcats))
                                        @if($dataSubcatFile->cat_id == $projectTemplate->template_id && in_array($dataSubcatFile->id, $subcats))
                                            <div class="col-md form-group">
                                                <input class="form-check-input" disabled="disabled" type="checkbox" value="{{ $dataSubcatFile->id }}" id="CategoryCheck{{ $isf }}" name="subcat_id[]" checked>
                                                <label class="form-check-label" for="CategoryCheck{{ $isf }}">
                                                    <small>{{ ucwords($dataSubcatFile->name) }} <span class="text-info">[{{ $dataSubcatFile->uploaded_file_name ?? '' }}]</span></small>

                                                    <a href="{{ asset('files/projects/report/template_files/'.$dataSubcatFile->file_name) }}" class="btn btn-warning">Download</a>

                                                    @if($dataSubcatFile->fileCount < 1)
                                                        <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#add_file{{ $isf }}" aria-expanded="false" aria-controls="add_file{{ $isf }}">Upload file</button>
                                                    @else
                                                        <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#add_file{{ $isf }}" aria-expanded="false" aria-controls="add_file{{ $isf }}">Ubah</button>
                                                    @endif
                                                    <span class="small">
                                                        @if($dataSubcatFile->approvedPMCount > 0)
                                                            <span class="text-success">Done</span>
                                                        @elseif($dataSubcatFile->approvedCount > 0)
                                                            <span class="text-info">Sedang direview PM</span>
                                                        @elseif($dataSubcatFile->submittedCount > 0)
                                                            <span class="text-info">Sedang direview QC</span>
                                                        @else
                                                            <span class="text-warning">Belum disubmit</span>
                                                        @endif
                                                    </span>
                                                </label>
                                            </div>
                                            <!-- collapse -->
                                                <div class="w-100"></div>
                                                <div class="collapse" id="add_file{{ $isf }}">
                                                    <div class="card-box">
                                                        <form action="{{ route($formRouteFileStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group{{ $errors->has('filled') ? ' has-error' : '' }}">
                                                                        <label>Upload file</label>

                                                                        <input type="hidden" name="project_id" value="{{ $projectTemplate->project_id }}">
                                                                        <input type="hidden" name="task_id" value="{{ $projectTemplate->task_id }}">
                                                                        <input type="hidden" name="prts_id" value="{{ $projectTemplate->template_id }}">
                                                                        <input type="hidden" name="subcat_id" value="{{ $dataSubcatFile->id }}">
                                                                        <input type="hidden" name="prf_id" value="{{ $dataSubcatFile->prf_id }}">
                                                                        <input type="hidden" name="status" value="2">

                                                                        <input type="file" class="form-control" name="filled" value="{{ old('filled') ?? old('filled') }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group" style="padding-top:30px;">
                                                                        <input type="submit" class="btn btn-orange" name="submit" value="Upload">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            <!-- collapse end -->
                                            @if($isf % 2 == 0)
                                                <div class="w-100"></div>
                                            @endif
                                        @endif
                                    @else
                                        <div class="col-md form-group">
                                            <input class="form-check-input" type="checkbox" value="{{ $dataSubcatFile->id }}" id="CategoryCheck{{ $isf }}" name="subcat_id[]" checked>
                                            <label class="form-check-label" for="CategoryCheck{{ $isf }}">
                                                <small>{{ ucwords($dataSubcatFile->name) }}</small>
                                            </label>
                                        </div>
                                        @if($isf % 2 == 0)
                                            <div class="w-100"></div>
                                        @endif
                                    @endif
                                    <?php $isf++; ?>
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
                        <?php $isf2=1; ?>
                        @if($subcatsCustom != null)
                            <hr>
                            <div class="row m-0 text-left" style="padding-left:5px">
                                @foreach($dataSubcategoryCustomizedFiles as $dataSubcatCustomizedFile)
                                    @if(isset($subcatsCustom))
                                        @if($dataSubcatCustomizedFile->cat_id == $projectTemplate->template_id && in_array($dataSubcatCustomizedFile->id, $subcatsCustom))
                                            <div class="col-md form-group">
                                                <input class="form-check-input" disabled="disabled" type="checkbox" value="{{ $dataSubcatCustomizedFile->id }}" id="CategoryCheck{{ $isf2 }}" name="subcat_id[]" checked>
                                                <label class="form-check-label" for="CategoryCheck{{ $isf2 }}">
                                                    <small>{{ ucwords($dataSubcatCustomizedFile->name) }} <span class="text-info">[{{ $dataSubcatCustomizedFile->uploaded_file_name ?? '' }}]</span></small> 
                                                    
                                                    <a href="{{ asset('files/projects/report/template_files/'.$dataSubcatCustomizedFile->file_name) }}" class="btn btn-warning">Download</a>

                                                    @if($dataSubcatCustomizedFile->fileCount < 1)
                                                        <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#add_file{{ $isf2 }}" aria-expanded="false" aria-controls="add_file{{ $isf2 }}">Upload file</button>
                                                    @else
                                                        <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#add_file{{ $isf2 }}" aria-expanded="false" aria-controls="add_file{{ $isf2 }}">Ubah</button>
                                                    @endif

                                                    @if($dataSubcatCustomizedFile->approvedPMCount > 0)
                                                        <span class="text-success">Done</span>
                                                    @elseif($dataSubcatCustomizedFile->approvedCount > 0)
                                                        <span class="text-info">Sedang direview PM</span>
                                                    @elseif($dataSubcatCustomizedFile->submittedCount > 0)
                                                        <span class="text-info">Sedang direview QC</span>
                                                    @else
                                                        <span class="text-warning">Belum disubmit</span>
                                                    @endif

                                                </label>
                                            </div>
                                            <!-- collapse -->
                                            <div class="w-100"></div>
                                                <div class="collapse" id="add_file{{ $isf2 }}">
                                                    <div class="card-box">
                                                        <form action="{{ route($formRouteFileStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group{{ $errors->has('filled') ? ' has-error' : '' }}">
                                                                        <label>Upload file</label>

                                                                        <input type="hidden" name="project_id" value="{{ $projectTemplate->project_id }}">
                                                                        <input type="hidden" name="task_id" value="{{ $projectTemplate->task_id }}">
                                                                        <input type="hidden" name="prts_id" value="{{ $projectTemplate->template_id }}">
                                                                        <input type="hidden" name="subcatcustom_id" value="{{ $dataSubcatCustomizedFile->id }}">
                                                                        <input type="hidden" name="prf_id" value="{{ $dataSubcatCustomizedFile->prf_id }}">
                                                                        <input type="hidden" name="status" value="2">

                                                                        <input type="file" class="form-control" name="filled" value="{{ old('filled') ?? old('filled') }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group" style="padding-top:30px;">
                                                                        <input type="submit" class="btn btn-orange" name="submit" value="Upload">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            <!-- collapse end -->
                                            @if($isf2 % 2 == 0)
                                                <div class="w-100"></div>
                                            @endif
                                        @endif
                                    @else
                                        <div class="col-md form-group">
                                            <input class="form-check-input" type="checkbox" value="{{ $dataSubcatCustomizedFile->id }}" id="CategoryCheck{{ $isf2 }}" name="subcat_id[]" checked>
                                            <label class="form-check-label" for="CategoryCheck{{ $isf2 }}">
                                                <small>{{ ucwords($dataSubcatCustomizedFile->name) }}</small>
                                            </label>
                                        </div>
                                        @if($isf2 % 2 == 0)
                                            <div class="w-100"></div>
                                        @endif
                                    @endif
                                    <?php $isf2++; ?>
                                @endforeach
                            </div>
                        @endif
                        
                    
                    @else
                        <div class="alert alert-warning">Tipe data belum tersedia</div>
                    @endif
                </div>
            </div>    
        </div>
        <div class="card-body">
            <div class="col-md">
                <a href="{{ route($formRouteIndex, 'project_id='.$projectTemplate->project_id.'&task_id='.$projectTemplate->task_id) }}" class="btn btn-blue-lini">Kembali</a>
            </div>
        </div>
    </div> <!-- card -->
@endsection

@section ('script')

@endsection
