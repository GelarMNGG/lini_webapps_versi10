@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Laporan pekerjaan';
    $statusBadge    = array('','info','danger','purple','pink','warning','dark');
    //form route
    $formRouteIndex = 'user-projects-image.index';
    $formRouteStore = 'user-projects-image.store';
    $formRouteUpdate= 'user-projects-image.update';
    //back route
    $formProjectRouteIndex = 'user-projects-template.index';
    //image comments
    $formImageCommentsStore = 'user-projects-image-comments.store';
?>
@endsection

@section('content')
    <div class="content mt-2">
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="flash-message">
                @foreach (['danger','warning','success','info'] as $msg)
                    @if (Session::has('alert-'.$msg))
                        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                    @endif
                @endforeach

                @if($dataProjectPicturesStatus->countApproved < 1)
                    <p class="alert alert-danger">Anda belum memberikan persetujuan atas laporan kebutuhan gambar.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                @endif

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
                        <span class="logo report-logo float-left">
                            <img src="{{ asset('img/'.$companyInfo->logo) }}" alt="logo {{ $companyInfo->name }}" height="57">
                        </span>
                        <div class="panel-heading text-center text-uppercase">
                            <h3>{{ $pageTitle }}</h3>
                            <small>Proyek:</small> <strong><span class="text-info">{{ $infoProjectTask->project_name != null ? strtoupper($infoProjectTask->project_name) : '' }}</span></strong>
                        </div>
                        <hr>
                        <div class="panel-body">
                            <form action="{{ route($formRouteUpdate,$dataProjectPicturesStatus->id) }}" style="display:inline-block; width:100%" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                @csrf
                                @method('PUT')

                                <div class="clearfix">
                                    <div class="float-left">
                                        <span>Nama teknisi: {{ ucwords($dataTechnician->firstname).' '.ucwords($dataTechnician->lastname) }}</span>
                                        <br>Project: <span class="text-uppercase">{{ $infoProjectTask->name }}</span>
                                    </div>
                                    <div class="float-right">
                                        <h4>No Task #<strong><span class="text-uppercase">{{ $infoProjectTask->number }}</span></strong> </h4>
                                        <span>Tanggal: {{ date('l, d F Y') }}</span>
                                    </div>
                                </div>
                                <hr>
                                
                                <div class="row mt-4">
                                    <div class="col-md-12 text-center">
                                        <h4 class="text-uppercase">{{ $projectTemplate->name }}</h4>
                                    </div>
                                    <div class="w-100"></div>
                                    <div class="col-md-12">
                                        @foreach($subcats as $subcat)
                                            <?php $is=1; ?>
                                            @foreach($dataSubcategory as $dataSubcat)
                                                @if($dataSubcat->id == $subcat)

                                                    <div class="row m-0">

                                                        <!-- data pictures by category count  -->
                                                        @foreach($subcatsPictureByCatCount as $subcatsPicture)
                                                            <!-- data pictures -->
                                                            @foreach($dataProjectPictures as $dataProjectPicture)
                                                                @if($dataProjectPicture->cat_id == $subcatsPicture->cat_id && $dataProjectPicture->subcat_id == $subcat)
                                                                    
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
                                                                                    <h5 class="text-warning" style="position:absolute; top:50%; left:41%">Terpilih</h5>
                                                                                @endif
                                                                                <img name="image" class="img-report" src="{{ asset('/img/projects/'.$dataProjectCategory->folder.'/'.$dataProjectPicture->image) }}"  />
                                                                            </div>
                                                                        </label>
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
                                                                                            <span class="text-uppercase">{{ ucfirst($dataCat->name) }}: </span>
                                                                                            <span class="text-muted">{{ ucfirst($dataSubcat->name) }}</span>
                                                                                        </h5>
                                                                                    </div>
                                                                                </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach

                                                        @endforeach
                                                        
                                                    </div>
                                                    <div class="w-100"></div>
                                                    <div class="col-md mt-1 text-center">
                                                        <span>{{ ucwords($dataSubcat->name) }}</span>
                                                    </div>
                                                    
                                                @endif
                                                <?php $is++; ?>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                                <hr>

                                <div class="row m-0 mb-5">
                                    <div class="col-md">
                                        <div class="clearfix">
                                            <h5 class="small text-dark text-uppercase mb-5">dibuat, {{ isset($dataTechnician->title) ? ucwords($dataTechnician->title) : '' }}
                                            <br><span class="text-muted">{{ isset($dataProjectPicturesStatus->submitted_at) ? date('l, d F Y', strtotime($dataProjectPicturesStatus->submitted_at)) : '' }}</span></h5>
                                            <small>
                                                {{ ucwords($dataTechnician->firstname).' '.ucwords($dataTechnician->lastname) }}
                                            </small>
                                        </div>
                                    </div>

                                    @if($dataProjectPicturesStatus->countApproved > 0)
                                        <div class="col-md">
                                            <h5 class="small text-dark text-uppercase mb-5">Disetujui, {{ isset($dataApprover->title) ? ucwords($dataApprover->title) : '' }}
                                            <br><span class="text-muted">{{ isset($dataApprover->approved_at) ? date('l, d F Y', strtotime($dataApprover->approved_at)) : '' }}</span></h5>
                                            <small>
                                                {{ ucwords($dataApprover->firstname).' '.ucwords($dataApprover->lastname) ?? ''}}
                                            </small>
                                        </div>
                                    @endif

                                    @if($dataProjectPicturesStatus->countPMApproved > 0)
                                        <div class="col-md">
                                            <h5 class="small text-dark text-uppercase mb-5">Disetujui, {{ ucwords($dataProjectManager->title) ?? '' }}<br><span class="text-muted">{{ isset($dataProjectManager->approved_by_pm_at) ? date('l, d F Y', strtotime($dataProjectManager->approved_by_pm_at)) : '' }}</span></h5>
                                            <small>
                                                {{ ucwords($dataProjectManager->firstname).' '.ucwords($dataProjectManager->lastname) ?? ''}}
                                            </small>
                                        </div>
                                    @endif

                                </div>
                                <div class="d-print-none">
                                    <hr>
                                    <div class="float-right">
                                        <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>
                                        <a href="{{ route($formProjectRouteIndex,'project_id='.$infoProjectTask->project_id.'&task_id='.$infoProjectTask->id) }}" type="button" class="btn btn-secondary">Kembali</a>
                                                   
                                        <!-- supporting data -->
                                        <input value="{{ $infoProjectTask->status }}" name="project_status" hidden>

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
                                            @if($dataProjectPicturesStatus->countApproved > 0)
                                                @if($dataProjectPicturesStatus->countPMApproved < 1)
                                                <!-- the data status -->
                                                <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-edit' title='done'> </i> Edit</button>
                                                @endif
                                            @else
                                                <input class="form-control" type="text" name="status" value="3" hidden>
                                                <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Approve</button>
                                            @endif
                                        @endif

                                    </div>
                                    <div class="clearfix"></div>

                                    <hr>
                                </div>
                            </form>

                        </div>
                    </div>

                    <?php /* css setting */ ?>
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
                    
                    @if(isset($dataProjectManager->approved_by_pm_at))
                        <?php $css_1 = 'warning'; $css_2 = 'warning'; $css_3 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; $css_text_2 = 'text-info'; $css_text_3 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; $fa_code_2 = 'check text-success'; $fa_code_3 = 'check text-success'; ?>
                    @elseif(isset($dataApprover->approved_at))
                        <?php $css_1 = 'warning'; $css_2 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; $css_text_2 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; $fa_code_2 = 'check text-success'; ?>
                    @elseif(isset($dataTechnician->submitted_at))
                        <?php $css_1 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; ?>
                    @endif

                    <?php /*
                    <div class="alert alert-{{ $css_3 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_3 }}"></i> ]</strong> Laporan disetujui oleh <span class="{{ $css_text_3 }}">Project Manager</span>. 
                        <div class="float-right">{{ isset($dataProjectManager->approved_by_pm_at) ? date('l, d F Y', strtotime($dataProjectManager->approved_by_pm_at)) : 'Belum disetujui' }}</div>
                    </div>
                    */ ?>
                    <div class="alert alert-{{ $css_2 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_2 }}"></i> ]</strong> Laporan disetujui oleh <span class="{{ $css_text_2 }}">Admin Document</span>.
                        <div class="float-right">{{ isset($dataApprover->approved_at) ? date('l, d F Y', strtotime($dataApprover->approved_at)) : 'Belum disetujui' }}</div>
                    </div>
                    <div class="alert alert-{{ $css_1 }}">
                        <strong>[ <i class="fas fa-{{ $fa_code_1 }}"></i> ]</strong> Laporan diajukan oleh <span class="{{ $css_text_1 }}">Teknisi</span>.
                        <div class="float-right">{{ isset($dataTechnician->submitted_at) ? date('l, d F Y', strtotime($dataTechnician->submitted_at)) : 'Belum disubmit' }}</div>
                    </div>

                    <div class="d-print-none">
                        <form method="post" action="{{ route($formImageCommentsStore) }}" enctype="multipart/form-data" class="card-box">
                            @csrf

                            <span class="input-icon icon-right">
                                <textarea rows="3" name="comment" class="form-control" placeholder="Kirim komentar" value="{{ old('comment') ?? '' }}" required></textarea>
                            </span>
                            <!-- comment data -->
                            <input value="{{ $infoProjectTask->project_id }}" name="project_id" hidden>
                            <input value="{{ $infoProjectTask->id }}" name="task_id" hidden>
                            <input value="{{ $dataProjectPicturesStatus->id }}" name="pri_id" hidden>
                            <input value="{{ $dataTechnician->id }}" name="receiver_id" hidden>
                            <input value="{{ $dataTechnician->user_type }}" name="receiver_type" hidden>
                            <!-- comment data -->
                            <div class="pt-1 float-right">
                                <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" @if($dataProjectPicturesStatus->countPMApproved > 0) ?? disabled @endif>Kirim komentar</button>
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
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'description' );
</script>
@endsection
