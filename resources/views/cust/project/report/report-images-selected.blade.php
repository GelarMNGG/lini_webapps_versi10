@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Laporan pekerjaan';
    $statusBadge    = array('','info','danger','purple','pink','warning','dark');
    //form route
    $formRouteIndex = 'cust-projects-image-report.index';
    $formRouteShow = 'cust-projects-image-report.show';
    //back route
    $formProjectRouteIndex = 'cust-projects.index';
    $formProjectRouteShow = 'cust-projects.show';
    //image comments
    $formImageCommentsStore = 'cust-projects-image-comments.store';
    //report comments
    $formReportCommentsStore = 'cust-projects.store';
    #$formReportCommentsStore = 'cust-projects-report-comments.store';
    #$formReportCommentsDestroy = 'cust-projects-report-comments.destroy';
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
                        @if(isset($project->customer_logo))
                            <span class="logo report-logo float-left">
                                <img src="{{ asset('img/clients/'.$project->customer_logo) }}" alt="logo {{ $project->customer_name }}">
                            </span>
                        @endif
                        @if(isset($project->partner_logo))
                            <span class="logo report-logo float-right">
                                <img src="{{ asset('img/'.$companyInfo->logo) }}" alt="logo {{ $companyInfo->name }}">
                            </span>
                        @endif
                        <div class="panel-heading text-center text-uppercase">
                            <h1>{{ $pageTitle }}</h1>

                            Project: <strong><span class="text-uppercase text-info">{{ $project->name }}</span></strong>
                            <br><small>Task:</small> <strong><span class="text-uppercase text-danger">{{ $infoProjectTask->name }}</span></strong>
                        </div>
                        <hr>

                        <div class="col-md-12 panel-body">
                            <div class="panel-heading text-center text-uppercase">
                                <h3>Lampiran foto</h3>
                            </div>
                            <hr>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <h4 class="text-uppercase">{{ $projectTemplate->category_name ?? 'Belum dikategorikan' }}</h4>
                                    </div>
                                    <div class="w-100"></div>
                                    <div class="col-md-12">
                                        <div class="row m-0">

                                            @if(count($dataProjectPictures) > 0)
                            
                                                <!-- data pictures -->
                                                @if(count($dataProjectPictures) > 0)
                                                    <?php $i = 1; ?>
                                                    @foreach($dataProjectPictures as $dataProjectPicture)
                                                        <div class="col-md form-group">
                                                            <button type="button" class="btn badge-pill text-dark button-img-report" data-toggle="modal" data-target="#cat_id_modal{{ $dataProjectPicture->id }}"><i class="fas fa-eye"></i> </button>

                                                            <label>
                                                                <div class="img-report-box">
                                                                    <img name="image" class="img-fluid img-thumbnail" src="{{ asset('/img/projects/'.$dataProjectCategory->folder.'/'.$dataProjectPicture->image) }}"  />
                                                                </div>
                                                            </label>
                                                            <div class="col-md mt-1 text-center">
                                                                <span>{{ ucwords($dataProjectPicture->subcat_name) }}</span>

                                                                <!-- share -->
                                                                @if($dataProjectPicture->approved_by_pm_at != null && $dataProjectPicture->shared < 1 && Auth::user()->user_level == 4)
                                                                <div class="approve-button">
                                                                    <form action="{{ route($formRouteUpdate,$dataProjectPicture->id) }}" style="display:inline-block;"  method="post" enctype="multipart/form-data" data-parsley-validate novalidate>

                                                                        @csrf
                                                                        @method('PUT')

                                                                        <!-- hidden data -->
                                                                        <input type="text" name="project_id" value="{{ $project->id }}" hidden>
                                                                        <input type="text" name="task_id" value="{{ $infoProjectTask->id }}" hidden>
                                                                        <input type="text" name="template_id" value="{{ $templateId }}" hidden>
                                                                        <input type="text" name="subcat_id" value="{{ $dataProjectPicture->subcat_id }}" hidden>
                                                                        
                                                                        <input type="text" name="shared" value="1" hidden>

                                                                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-check' title='Share'> </i> Share to customer</button>

                                                                    </form>
                                                                </div>
                                                                @endif

                                                                <!-- approve & reject -->
                                                                @if($dataProjectPicture->approved_by_pm_at == null && Auth::user()->user_level == 3)
                                                                <div class="approve-button">
                                                                    <form action="{{ route($formRouteUpdate,$dataProjectPicture->id) }}" style="display:inline-block;"  method="post" enctype="multipart/form-data" data-parsley-validate novalidate>

                                                                        @csrf
                                                                        @method('PUT')

                                                                        <!-- hidden data -->
                                                                        <input type="text" name="project_id" value="{{ $project->id }}" hidden>
                                                                        <input type="text" name="task_id" value="{{ $infoProjectTask->id }}" hidden>
                                                                        <input type="text" name="template_id" value="{{ $templateId }}" hidden>
                                                                        <input type="text" name="subcat_id" value="{{ $dataProjectPicture->subcat_id }}" hidden>
                                                                        
                                                                        <input type="text" name="status" value="4" hidden>

                                                                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-success" name="submit"><i class='fas fa-check' title='Approve'> </i> </button>

                                                                    </form>
                                                                    <form action="{{ route($formRouteUpdate,$dataProjectPicture->id) }}" style="display:inline-block;"  method="post" enctype="multipart/form-data" data-parsley-validate novalidate>

                                                                        @csrf
                                                                        @method('PUT')

                                                                        <!-- hidden data -->
                                                                        <input type="text" name="project_id" value="{{ $project->id }}" hidden>
                                                                        <input type="text" name="task_id" value="{{ $infoProjectTask->id }}" hidden>
                                                                        <input type="text" name="template_id" value="{{ $templateId }}" hidden>
                                                                        <input type="text" name="subcat_id" value="{{ $dataProjectPicture->subcat_id }}" hidden>

                                                                        <input type="text" name="status" value="1" hidden>

                                                                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-times' title='Reject'> </i> </button>

                                                                    </form>
                                                                </div>
                                                                @endif

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
                                                                                    <span class="text-uppercase">{{ $projectTemplate->category_name ?? 'Belum dikategorikan' }}: </span>
                                                                                    <span class="text-muted">{{ ucfirst($dataProjectPicture->subcat_name) }}</span>
                                                                                </h5>
                                                                            </div>
                                                                        </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($i % 3 == 0)
                                                            <div class="w-100"></div>
                                                        @endif

                                                        <?php $i++; ?>
                                                    @endforeach
                                                @endif

                                                @if(count($dataProjectPicturesCustom) > 0)
                                                    <?php $i2 = 1; ?>
                                                    @foreach($dataProjectPicturesCustom as $dataProjectPicture2)
                                                        <div class="col-md form-group">
                                                            <button type="button" class="btn badge-pill text-dark button-img-report" data-toggle="modal" data-target="#cat_id_modal{{ $dataProjectPicture2->id }}"><i class="fas fa-eye"></i> </button>

                                                            @if($dataProjectPicturesCustom->shared > 0)
                                                                <h5 class="text-warning" style="position:absolute; top:45%; left:31%">Terkirim ke pelanggan</h5>
                                                            @endif

                                                            <label>
                                                                <div class="img-report-box">
                                                                    <img name="image" class="img-fluid img-thumbnail" src="{{ asset('/img/projects/'.$dataProjectCategory->folder.'/'.$dataProjectPicture2->image) }}"  />
                                                                </div>
                                                            </label>
                                                            <div class="col-md mt-1 text-center">
                                                                <span>{{ ucwords($dataProjectPicture2->subcat_name) }}</span>

                                                                <!-- share -->
                                                                @if($dataProjectPicture2->approved_by_pm_at != null && $dataProjectPicture2->shared < 1 && Auth::user()->user_level == 4)
                                                                <div class="approve-button">
                                                                    <form action="{{ route($formRouteUpdate,$dataProjectPicture2->id) }}" style="display:inline-block;"  method="post" enctype="multipart/form-data" data-parsley-validate novalidate>

                                                                        @csrf
                                                                        @method('PUT')

                                                                        <!-- hidden data -->
                                                                        <input type="text" name="project_id" value="{{ $project->id }}" hidden>
                                                                        <input type="text" name="task_id" value="{{ $infoProjectTask->id }}" hidden>
                                                                        <input type="text" name="template_id" value="{{ $templateId }}" hidden>
                                                                        <input type="text" name="subcatcustom_id" value="{{ $dataProjectPicture2->subcatcustom_id }}" hidden>
                                                                        
                                                                        <input type="text" name="shared" value="1" hidden>

                                                                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-check' title='Share'> </i> Share to customer</button>

                                                                    </form>
                                                                </div>
                                                                @endif

                                                                <!-- approve & reject -->
                                                                @if($dataProjectPicture2->approved_by_pm_at == null && Auth::user()->user_level == 3)
                                                                <div class="approve-button">
                                                                    <form action="{{ route($formRouteUpdate,$dataProjectPicture2->id) }}" style="display:inline-block;"  method="post" enctype="multipart/form-data" data-parsley-validate novalidate>

                                                                        @csrf
                                                                        @method('PUT')

                                                                        <!-- hidden data -->
                                                                        <input type="text" name="project_id" value="{{ $project->id }}" hidden>
                                                                        <input type="text" name="task_id" value="{{ $infoProjectTask->id }}" hidden>
                                                                        <input type="text" name="template_id" value="{{ $templateId }}" hidden>
                                                                        <input type="text" name="subcatcustom_id" value="{{ $dataProjectPicture2->subcatcustom_id }}" hidden>
                                                                        
                                                                        <input type="text" name="status" value="4" hidden>

                                                                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-success" name="submit"><i class='fas fa-check' title='Approve'> </i> </button>

                                                                    </form>
                                                                    <form action="{{ route($formRouteUpdate,$dataProjectPicture2->id) }}" style="display:inline-block;"  method="post" enctype="multipart/form-data" data-parsley-validate novalidate>

                                                                        @csrf
                                                                        @method('PUT')

                                                                        <!-- hidden data -->
                                                                        <input type="text" name="project_id" value="{{ $project->id }}" hidden>
                                                                        <input type="text" name="task_id" value="{{ $infoProjectTask->id }}" hidden>
                                                                        <input type="text" name="template_id" value="{{ $templateId }}" hidden>
                                                                        <input type="text" name="subcatcustom_id" value="{{ $dataProjectPicture2->subcatcustom_id }}" hidden>

                                                                        <input type="text" name="status" value="1" hidden>

                                                                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-times' title='Reject'> </i> </button>

                                                                    </form>
                                                                </div>
                                                                @endif

                                                            </div>
                                                            <!-- Modal -->
                                                            <div class="modal fade" id="cat_id_modal{{ $dataProjectPicture2->id }}" tabindex="-1" role="dialog" aria-labelledby="projectImageModal" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                                                    <div class="modal-content-img">
                                                                        <div class="modal-body text-center">
                                                                        <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                                            <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/projects/'.$dataProjectCategory->folder.'/'.$dataProjectPicture2->image) }}"  />
                                                                            <div class="alert alert-warning" id="projectImageModal">
                                                                                <h5>
                                                                                    <span class="text-uppercase">{{ ucfirst($projectTemplate->category_name) }}: </span>
                                                                                    <span class="text-muted">{{ ucfirst($dataProjectPicture2->subcat_name) }}</span>
                                                                                </h5>
                                                                            </div>
                                                                        </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($i2 % 3 == 0)
                                                            <div class="w-100"></div>
                                                        @endif

                                                        <?php $i2++; ?>
                                                    @endforeach
                                                @endif

                                            @else
                                                <div class="col-md alert alert-warning">Belum ada data</div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                                <hr>
                                    
                                <div class="row m-0 mb-5">
                                    <div class="col-md">
                                        <span class="text-uppercase"><strong>Signature</strong></span>
                                    </div>
                                    <div class="w-100"></div>
                                    <div class="col-md">
                                        <div class="clearfix">
                                            <h5 class="small text-dark text-uppercase mb-5">{{ isset($project->customer_name) ? ucwords($project->customer_name) : '' }}
                                            </h5>
                                            <hr class="mb-0">
                                            <small>
                                                Name: {{ isset($project->customer_pic_firstname) ? ucfirst($project->customer_pic_firstname).' '.ucfirst($project->customer_pic_lastname) : '' }} 
                                                <br>Date: <span class="text-muted"></span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <h5 class="small text-dark text-uppercase mb-5">{{ isset($companyInfo->name) ? ucwords($companyInfo->name) : '' }}</h5>
                                        <hr class="mb-0">
                                        <small>
                                            Nama: 
                                            <br>Date: <span class="text-muted"></span>
                                        </small>
                                    </div>
                                </div>
                                <div class="d-print-none">
                                    <hr>
                                    <div class="float-right">


                                        <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>

                                        @if(Auth::user()->user_level == 3)
                                        <?php 
                                            //selected sub categories
                                            if ($subcatIds != null) {
                                                $totalsubcat = count($subcatIds);
                                            }else{
                                                $totalsubcat = 0;
                                            }
                                            if ($subcatcustomIds != null) {
                                                $totalsubcatcustom = count($subcatcustomIds);
                                            }else{
                                                $totalsubcatcustom = 0;
                                            }
                                            $totalPics = $totalsubcat + $totalsubcatcustom;

                                            //selected image report
                                            if ($dataProjectPictures != null) {
                                                $totalpicsubcat = count($dataProjectPictures);
                                            }else{
                                                $totalpicsubcat = 0;
                                            }
                                            if ($dataProjectPicturesCustom != null) {
                                                $totalpicsubcatcustom = count($dataProjectPicturesCustom);
                                            }else{
                                                $totalpicsubcatcustom = 0;
                                            }
                                            $totalUploadedPics = $totalpicsubcat + $totalpicsubcatcustom;
                                        ?>
                                            @if($dataProjectPicturesStatus->countPMApproved == $totalPics)
                                            <form action="{{ route($formRouteUpdate,$dataProjectPicturesStatus->id) }}" style="display:inline-block;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>

                                                @csrf
                                                @method('PUT')
                    
                                                <!-- hidden data -->
                                                <input type="text" name="project_id" value="{{ $project->id }}" hidden>
                                                <input type="text" name="task_id" value="{{ $infoProjectTask->id }}" hidden>
                                                <input type="text" name="template_id" value="{{ $templateId }}" hidden>

                                                <input type="text" name="all" value="1" hidden>
                                                <input type="text" name="status" value="4" hidden>

                                                <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Approve semua</button>

                                            </form>
                                            <form action="{{ route($formRouteUpdate,$dataProjectPicturesStatus->id) }}" style="display:inline-block;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>

                                                @csrf
                                                @method('PUT')
                    
                                                <!-- hidden data -->
                                                <input type="text" name="project_id" value="{{ $project->id }}" hidden>
                                                <input type="text" name="task_id" value="{{ $infoProjectTask->id }}" hidden>
                                                <input type="text" name="template_id" value="{{ $templateId }}" hidden>

                                                <input type="text" name="all" value="1" hidden>
                                                <input type="text" name="status" value="1" hidden>

                                                <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Reject semua</button>

                                            </form>
                                            @else
                                                <button class="btn btn-danger">Gambar belum lengkap ({{ $totalUploadedPics.'/'.$totalPics}})</button>
                                            @endif
                                        @endif
                                        
                                        <a href="{{ route($formProjectRouteShow, $project->id) }}" type="button" class="btn btn-blue lini mt-1">Kembali</a>

                                    </div>
                                    <div class="clearfix"></div>

                                    <hr>
                                </div>

                        </div>
                        
                    </div>
                    <!-- comments start -->
                    <div class="d-print-none">
                        <div class="row">
                            <div class="col-md">
                                <form method="post" action="{{ route($formReportCommentsStore) }}" enctype="multipart/form-data" class="card-box">
                                    @csrf

                                    <span class="input-icon icon-right">
                                        <textarea rows="3" name="comment" class="form-control" placeholder="Kirim komentar" value="{{ old('comment') ?? '' }}" required></textarea>
                                    </span>
                                    <!-- comment data -->




                                    <?php /*
                                    <input value="{{ $project->id }}" name="project_id" hidden>
                                    <input value="{{ $project->pwo_id }}" name="pwo_id" hidden>
                                    */ ?>



                                    <!-- comment data -->
                                    <div class="pt-1 float-right">

                                    <?php /*
                                        <button type="submit" name="submit" class="btn btn-danger btn-sm waves-effect waves-light" @if($dataProjectReportCount < 1) ?? disabled @endif>Kirim komentar</button>
                                        */ ?>





                                        <button type="submit" name="submit" class="btn btn-danger btn-sm waves-effect waves-light">Kirim komentar</button>
                                    </div>
                                    <ul class="nav nav-pills profile-pills mt-1">
                                        <li>
                                            <a href="#"><i class="far fa-smile"></i></a>
                                        </li>
                                    </ul>
                                </form>
                                @if(Auth::user()->user_type == 'user')
                                <div class="alert alert-warning">Komunikasi dengan <span class="text-info"><strong>Pelanggan</strong></span>.</div>
                                @endif
                                @if($dataProjectReportCommentsCount > 0)
                                    @foreach ($dataComments as $data20)
                                    <div class="card-box">
                                        <div class='media mb-3'>
                                            @if($data20->publisher_type == 'user')
                                                @foreach($users as $dataUser)
                                                    @if($data20->publisher_id == $dataUser->id)
                                                        <img src="{{ asset('admintheme/images/users/'.$dataUser->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                                                        <div class='media-body'>
                                                            <h5 class='mt-0'>
                                                                <a href='#' class='text-dark'>
                                                                    {{ ucfirst($dataUser->firstname).' '.ucfirst($dataUser->lastname) }} 
                                                                    <span class="text-info">{{ ucwords($dataUser->title) }}</span>
                                                                </a>
                                                            </h5>
                                                            <p>{{ ucfirst($data20->comment) }}</p>
                                                            <div class='comment-footer'>
                                                                <span>{{ date('l, d M Y', strtotime($data20->date)) }}</span>
                                                                <span class="text-info"> - </span><small class='text-muted'>{{ date("H:i a", strtotime($data20->date)) }}</small>
                                                            </div>
                                                            <form action="{{ route($formReportCommentsDestroy, $data20->id) }}" method="POST">
                                                                @method('DELETE')
                                                                @csrf
                                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                                            </form>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                @foreach($customers as $cutomerData)
                                                    @if($data20->publisher_id == $cutomerData->id)
                                                        <img src="{{ asset('admintheme/images/users/'.$cutomerData->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                                                        <div class='media-body'>
                                                            <h5 class='mt-0'>
                                                                <a href='#' class='text-dark'>
                                                                    {{ ucfirst($cutomerData->firstname).' '.ucfirst($cutomerData->lastname) }} 
                                                                    <span class="text-info">{{ isset($cutomerData->title) ? ucwords($cutomerData->title) : 'Teknisi' }}</span>
                                                                </a>
                                                            </h5>
                                                            <p>{{ ucfirst($data20->comment) }}</p>
                                                            <div class='comment-footer'>
                                                                <span>{{ date('l, d M Y', strtotime($data20->date)) }}</span>
                                                                <span class="text-info"> - </span><small class='text-muted'>{{ date("H:i a", strtotime($data20->date)) }}</small>
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
                            <!-- internal -->
                            @if(Auth::user()->user_type == 'user')
                                <div class="col-md">
                                    <form method="post" action="{{ route($formImageCommentsStore) }}" enctype="multipart/form-data" class="card-box">
                                        @csrf

                                        <span class="input-icon icon-right">
                                            <textarea rows="3" name="comment" class="form-control" placeholder="Kirim komentar" value="{{ old('comment') ?? '' }}" required></textarea>
                                        </span>
                                        <!-- comment data -->


                                        
                                        
                                        <input value="{{ $project->id }}" name="project_id" hidden>
                                        <input value="{{ $projectTemplate->task_id }}" name="task_id" hidden>
                                        <input value="{{ $dataProjectPicturesStatus->id }}" name="pri_id" hidden>
                                        <input value="1" name="comment_status" hidden>



                                        <!-- comment data -->
                                        <div class="pt-1 float-right">
                                            <button type="submit" name="submit" class="btn btn-info btn-sm waves-effect waves-light">Kirim komentar</button>
                                        </div>
                                        <ul class="nav nav-pills profile-pills mt-1">
                                            <li>
                                                <a href="#"><i class="far fa-smile"></i></a>
                                            </li>
                                        </ul>
                                    </form>
                                    <div class="alert alert-warning">Komunikasi sesama <span class="text-info"><strong>Tim Internal</strong></span>.</div>
                                    @if($dataProjectPicturesStatus->commentsCount > 0)
                                        @foreach ($dataComments as $data21)
                                        <div class="card-box">
                                            <div class='media mb-3'>
                                                @if($data21->publisher_type == 'user')
                                                    @foreach($users as $dataUser)
                                                        @if($data21->publisher_id == $dataUser->id)
                                                            <img src="{{ asset('admintheme/images/users/'.$dataUser->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                                                            <div class='media-body'>
                                                                <h5 class='mt-0'>
                                                                    <a href='#' class='text-dark'>
                                                                        {{ ucfirst($dataUser->firstname).' '.ucfirst($dataUser->lastname) }} 
                                                                        <span class="text-info">{{ ucwords($dataUser->title) }}</span>
                                                                    </a>
                                                                </h5>
                                                                <p>{{ ucfirst($data21->comment) }}</p>
                                                                <div class='comment-footer'>
                                                                    <span>{{ date('l, d M Y', strtotime($data21->date)) }}</span>
                                                                    <span class="text-info"> - </span><small class='text-muted'>{{ date("H:i a", strtotime($data21->date)) }}</small>
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
                            @endif
                        </div>
                    </div>
                    <!-- comments end -->

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
