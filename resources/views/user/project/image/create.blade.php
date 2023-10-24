@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Pilih foto laporan pekerjaan';
    $statusBadge    = array('','info','danger','purple','pink','warning','dark');

    //image
    $formRouteShow = 'user-projects-image.show';
    $formRouteUpdate= 'user-projects-image.update';

    //image comments
    $formImageCommentsStore = 'user-projects-image-comments.store';

?>
@endsection

@section('content')
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid mt-2">
            <div class="flash-message">
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
            <div class="row">
                <div class="col-md-12">
                    <div class="card-box">
                        <div class="panel-heading text-center text-uppercase">
                            <small>Proyek:</small> <strong><span class="text-info">{{ isset($projectTask->project_name) ? strtoupper($projectTask->project_name) : '' }}</span></strong>
                            <br><small>Task:</small> <strong><span class="text-danger">{{ isset($projectTask->name) ? strtoupper($projectTask->name) : 'Belum ada task' }}</span></strong>
                            <br><small>Template:</small> <strong><span class="text-warning">{{ isset($projectTemplate->name) ? strtoupper($projectTemplate->name) : 'Belum ada data' }}</span></strong>
                        </div>
                        <hr>
                        <div class="panel-body">

                            <form action="{{ route($formRouteUpdate,$projectTemplate->id) }}" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                @csrf
                                @method('PUT')
                              
                                <div class="row mt-4">
                                    <div class="col-md-12">

                                        @foreach($dataSubcategory as $dataSubcat)
                                            @if($dataSubcat->id == $subcatId)

                                                <div class="col-md mb-2 text-center">
                                                    <span>{{ ucwords($dataSubcat->name) }}</span>
                                                </div>
                                                <div class="w-100"></div>

                                                <div class="row m-0">
                                                    @foreach($subcatsPictureByCatCount as $subcatsPicture)
                                                        @foreach($dataProjectPictures as $dataProjectPicture)

                                                            <div class="col-sm form-group">

                                                                <label>
                                                                    <button type="button" class="btn badge-pill text-dark button-img-report" data-toggle="modal" data-target="#cat_id_modal{{ $dataProjectPicture->id }}"><i class="fas fa-eye"></i> </button>

                                                                    @if(Auth::user()->user_level == 3)
                                                                        <input type="checkbox" class="checkbox-image" id="selectedImage{{ $dataSubcat->id }}[]" name="selectedImage[]" value="{{ $dataProjectPicture->id }}" @if($dataProjectPicture->selected_image) checked @endif />
                                                                    @else
                                                                        <input type="checkbox" class="checkbox-image" id="selectedImage{{ $dataSubcat->id }}[]" name="selectedImage[]" value="{{ $dataProjectPicture->id }}" @if($dataProjectPicture->selected_image) checked @endif @if($dataProjectPicturesStatus->countApproved > 0) ?? disabled @endif />
                                                                    
                                                                    @endif

                                                                    <div class="img-report-box border-dashed border-rounded">
                                                                        @if($dataProjectPicture->selected_image)
                                                                            <h5 class="text-warning h5-report-terpilih">Terpilih</h5>
                                                                        @endif

                                                                        <img name="image" class="img-fluid img-thumbnail img-report" src="{{ asset('/img/projects/'.$dataProjectCategory->folder.'/'.$dataProjectPicture->image) }}"  />
                                                                        
                                                                    </div>
                                                                </label>
                                                                <br><a href="{{ asset('/img/projects/'.$dataProjectCategory->folder.'/'.$dataProjectPicture->image) }}" class="badge badge-info" download>Download</a>
                                                            </div> 

                                                            <!-- Modal -->
                                                            <div class="modal fade" id="cat_id_modal{{ $dataProjectPicture->id }}" tabindex="-1" role="dialog" aria-labelledby="projectImageModal" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                                                    <div class="modal-content-img">
                                                                        <div class="modal-body text-center">
                                                                        <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                                            <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/projects/'.$dataProjectCategory->folder.'/'.$dataProjectPicture->image) }}"  />
                                                                            <div class="alert alert-warning" id="projectImageModal">
                                                                                <h5>
                                                                                    <span class="text-muted">{{ ucfirst($dataSubcat->name) }}</span>
                                                                                </h5>
                                                                            </div>
                                                                        </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                        @endforeach

                                                    @endforeach
                                                    
                                                </div>
                                                                                                    
                                            @endif

                                        @endforeach
                                        
                                    </div>
                                </div>
                            
                                <hr>
                                <div class="d-print-none">
                                    <div class="float-right">
                                        <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>
                                
                                        <!-- hidden data -->
                                        <input value="{{ $projectTask->project_id }}" name="project_id" hidden>
                                        <input value="{{ $projectTask->id }}" name="task_id" hidden>
                                        <input value="{{ $projectTemplate->template_id }}" name="template_id" hidden>
                                        <input value="{{ $subcatId }}" name="{{ $subcatName }}" hidden>
                                        <input value="{{ $subcatName }}" name="subcat_name" hidden>

                                        @if(Auth::user()->user_level == 3)
                                            @if($dataProjectPicturesStatus->countPMApproved < 1)
                                                <!-- the data status -->
                                                <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Approve</button>
                                            @else
                                                <input class="form-control" type="text" name="status" value="4" hidden>
                                                <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Edit</button>
                                            @endif
                                        @endif

                                        @if(Auth::user()->user_level == 4)
                                            @if($dataProjectPicturesStatus->countSubmitted > 0 && $dataProjectPicturesStatus->countApproved < 1 && $dataProjectPicturesStatus->countPMApproved < 1)
                                                <input class="form-control" type="text" name="status" value="3" hidden>
                                                <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Approve</button>
                                            @endif
                                        @endif

                                        <a href="{{ route($formRouteShow,$projectTemplate->template_id.'?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" type="button" class="btn btn-secondary">Kembali</a>

                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </form>
                            @if(Auth::user()->user_level == 3)
                                @if($dataProjectPicturesStatus->countSubmitted > 0 && $dataProjectPicturesStatus->countPMApproved < 1)
                                    <form action="{{ route($formRouteUpdate,$projectTemplate->id) }}" class="text-right mt-1" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                        @csrf
                                        @method('PUT')

                                        <!-- hidden data -->
                                        <input value="{{ $projectTask->project_id }}" name="project_id" hidden>
                                        <input value="{{ $projectTask->id }}" name="task_id" hidden>
                                        <input value="{{ $projectTemplate->template_id }}" name="template_id" hidden>
                                        <input value="{{ $subcatId }}" name="{{ $subcatName }}" hidden>
                                        <input class="form-control" type="text" name="status" value="1" hidden>

                                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-times' title='done'> </i> Reject</button>
                                    </form>
                                @endif
                            @else
                                @if($dataProjectPicturesStatus->countSubmitted > 0 && $dataProjectPicturesStatus->countApproved < 1)
                                    <form action="{{ route($formRouteUpdate,$projectTemplate->id) }}" class="text-right mt-1" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                        @csrf
                                        @method('PUT')

                                        <!-- hidden data -->
                                        <input value="{{ $projectTask->project_id }}" name="project_id" hidden>
                                        <input value="{{ $projectTask->id }}" name="task_id" hidden>
                                        <input value="{{ $projectTemplate->template_id }}" name="template_id" hidden>
                                        <input value="{{ $subcatId }}" name="{{ $subcatName }}" hidden>
                                        <input class="form-control" type="text" name="status" value="1" hidden>

                                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-times' title='done'> </i> Reject</button>
                                    </form>
                                @endif
                            @endif

                        </div>
                    </div>
                    
                    <?php /* css setting */ ?>
                    <?php $css_1 = 'secondary'; ?>
                    <?php $css_2 = 'secondary'; ?>
                    <?php $css_3 = 'secondary'; ?>
                    <?php $css_text_1 = 'dark'; ?>
                    <?php $css_text_2 = 'dark'; ?>
                    <?php $css_text_3 = 'dark'; ?>
                    <?php $fa_code_1 = 'times text-danger'; ?>
                    <?php $fa_code_2 = 'times text-danger'; ?>
                    <?php $fa_code_3 = 'times text-danger'; ?>
                    
                    @if(isset($approverData->approved_by_pm_at))
                        <?php $css_1 = 'warning'; $css_2 = 'warning'; $css_3 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; $css_text_2 = 'text-info'; $css_text_3 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; $fa_code_2 = 'check text-success'; $fa_code_3 = 'check text-success'; ?>
                    @elseif(isset($approverData->approved_at))
                        <?php $css_1 = 'warning'; $css_2 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; $css_text_2 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; $fa_code_2 = 'check text-success'; ?>
                    @elseif(isset($approverData->submitted_at))
                        <?php $css_1 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; ?>
                    @endif

                    <?php /*
                    <div class="alert alert-{{ $css_3 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_3 }}"></i> ]</strong> Foto laporan disetujui oleh <span class="{{ $css_text_3 }}">Project Manager</span>. 
                        <div class="float-right">{{ isset($approverData->approved_by_pm_at) ? date('l, d F Y', strtotime($approverData->approved_by_pm_at)) : 'Belum disetujui' }}</div>
                    </div>
                    */ ?>
                    <div class="alert alert-{{ $css_2 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_2 }}"></i> ]</strong> Foto laporan disetujui oleh <span class="{{ $css_text_2 }}">Admin Document</span>.
                        <div class="float-right">{{ isset($approverData->approved_at) ? date('l, d F Y', strtotime($approverData->approved_at)) : 'Belum disetujui' }}</div>
                    </div>
                    <div class="alert alert-{{ $css_1 }}">
                        <strong>[ <i class="fas fa-{{ $fa_code_1 }}"></i> ]</strong> Foto laporan diajukan oleh <span class="{{ $css_text_1 }}">Teknisi</span>.
                        <div class="float-right">{{ isset($approverData->submitted_at) ? date('l, d F Y', strtotime($approverData->submitted_at)) : 'Belum disubmit' }}</div>
                    </div>

                    <div class="d-print-none">
                        <form method="post" action="{{ route($formImageCommentsStore) }}" enctype="multipart/form-data" class="card-box">
                            @csrf

                            <span class="input-icon icon-right">
                                <textarea rows="3" name="comment" class="form-control" placeholder="Kirim komentar" value="{{ old('comment') ?? '' }}" required></textarea>
                            </span>
                            <!-- comment data -->
                            <input value="{{ $projectTask->project_id }}" name="project_id" hidden>
                            <input value="{{ $projectTask->id }}" name="task_id" hidden>
                            <input value="{{ $projectTemplate->template_id }}" name="template_id" hidden>
                            <input value="{{ $subcatId }}" name="{{ $subcatName }}" hidden>
                            <input value="{{ $dataProjectPicturesStatus->id }}" name="pri_id" hidden>
                            <!-- comment data -->
                            <div class="pt-1 float-right">
                                <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light">Kirim komentar</button>
                            </div>
                            <ul class="nav nav-pills profile-pills mt-1">
                                <li>
                                    <a href="#"><i class="far fa-smile"></i></a>
                                </li>
                            </ul>
                        </form>

                        @if($dataProjectPicturesStatus->commentsCount > 0)
                            @foreach ($dataComments as $data1)
                            <div class="card-box">
                                <div class='media mb-3'>
                                    @if($data1->publisher_type == 'user')
                                        @foreach($users as $dataUser)
                                            @if($data1->publisher_id == $dataUser->id)
                                                <img src="{{ asset('admintheme/images/users/'.$dataUser->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                                                <div class='media-body'>
                                                    <h5 class='mt-0'>
                                                        <a href='#' class='text-dark'>
                                                            {{ ucfirst($dataUser->firstname).' '.ucfirst($dataUser->lastname) }} 
                                                            <span class="text-info">{{ ucwords($dataUser->title) }}</span>
                                                        </a>
                                                        <small class='ml-1 text-muted'>{{ date("H:i a", strtotime($data1->date)) }}</small>
                                                    </h5>
                                                    <p>{{ ucfirst($data1->comment) }}</p>
                                                    <div class='comment-footer'>
                                                        <span>{{ date('l, d M Y', strtotime($data1->date)) }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach($techs as $techData)
                                            @if($data1->publisher_id == $techData->id)
                                                <img src="{{ asset('admintheme/images/users/'.$techData->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                                                <div class='media-body'>
                                                    <h5 class='mt-0'>
                                                        <a href='#' class='text-dark'>
                                                            {{ ucfirst($techData->firstname).' '.ucfirst($techData->lastname) }} 
                                                            <span class="text-info">{{ isset($techData->title) ? ucwords($techData->title) : 'Teknisi' }}</span>
                                                        </a>
                                                        <small class='ml-1 text-muted'>{{ date("H:i a", strtotime($data1->date)) }}</small>
                                                    </h5>
                                                    <p>{{ ucfirst($data1->comment) }}</p>
                                                    <div class='comment-footer'>
                                                        <span>{{ date('l, d M Y', strtotime($data1->date)) }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>
            <!-- end row -->        
            
        </div> <!-- container-fluid -->

    </div> <!-- content -->
@endsection

@section ('script')
<script>
    $('input[type="checkbox"]').on('change', function() {
        $('input[id="' + this.id + '"]').not(this).prop('checked', false);
        //$('input[name="' + this.name + '"]').not(this).prop('checked', false);
    });
</script>

@endsection
