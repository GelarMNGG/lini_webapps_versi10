@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Laporan pekerjaan';
    $statusBadge    = array('','info','danger','purple','pink','warning','dark');
    //form route
    $formRouteIndex = 'cust-projects-image-report.index';
    $formRouteStore = 'cust-projects-image-report.store';
    $formRouteUpdate= 'cust-projects-image-report.update';
    //back route
    $formProjectRouteShow = 'cust-projects.show';
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
                        <div class="panel-body">
                            <form action="{{ route($formRouteUpdate,$dataProjectPicturesStatus->id) }}" class="w-100" style="display:inline-block" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                @csrf
                                @method('PUT')

                                <div class="clearfix">
                                    <div class="float-left">
                                        <span>Nama teknisi: {{ ucwords($dataTechnician->firstname).' '.ucwords($dataTechnician->lastname) }}</span>
                                        <br>Project: <span class="text-uppercase">{{ $infoProjectTask->project_name }}</span>
                                    </div>
                                    <div class="float-right">
                                        <h4>No Task #<strong><span class="text-uppercase">{{ $infoProjectTask->number }}</span></strong> </h4>
                                        <span>Tanggal: {{ date('d F Y') }}</span>
                                    </div>
                                </div>
                                <hr>
                                
                                @foreach($projectTemplateDatas as $projectTemplate)
                                    <?php
                                        $subcats = unserialize($projectTemplate->subcat_id);
                                        $subcatcustoms = unserialize($projectTemplate->subcatcustom_id);
                                    ?>
                                
                                    @foreach($dataCategory as $dataCat)
                                        @if($dataCat->id == $projectTemplate->template_id)
                                        <div class="row mt-4">
                                            <div class="col-md-12 text-center">
                                                <h4 class="text-uppercase">{{ $dataCat->name }}</h4>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="row">
                                                @foreach($subcats as $subcat)
                                                    <?php $is=1; ?>
                                                    @foreach($dataSubcategory as $dataSubcat)
                                                        @if($dataSubcat->cat_id == $dataCat->id && $dataSubcat->id == $subcat)

                                                            <div class="col-md">
                                                            
                                                                <!-- hidden data -->
                                                                <?php 
                                                                /* <input name="category[]" value="{{ $dataCat->id }}" hidden/> -->
                                                                <!-- <input name="subcategory[]" value="{{ $dataSubcat->id }}" hidden/> -->
                                                                <!-- supporting data -->
                                                                <!-- <input value="{{ $project->id }}" name="project_id" hidden> -->
                                                                <!-- <input value="{{ $project->pwo_id }}" name="pwo_id" hidden> 
                                                                */ ?>
                                                                <input class="form-control" type="text" name="status" value="3" hidden>

                                                                    <!-- data pictures by category count  -->
                                                                    @foreach($subcatsPictureByCatCount as $subcatsPicture)
                                                                        <!-- data pictures -->
                                                                        @foreach($dataProjectPictures as $dataProjectPicture)
                                                                            @if($dataProjectPicture->cat_id == $subcatsPicture->cat_id && $dataProjectPicture->subcat_id == $subcat && $dataProjectPicture->selected_image == 1)
                                                                                
                                                                                <div class="col-md form-group">
                                                                                    <button type="button" class="btn badge-pill text-dark button-img-report" data-toggle="modal" data-target="#cat_id_modal{{ $dataProjectPicture->id }}"><i class="fas fa-eye"></i> </button>

                                                                                    <input type="checkbox" class="checkbox-image" id="selectedImage{{ $dataSubcat->id }}[]" name="selectedImage[]" value="{{ $dataProjectPicture->id }}" @if($dataProjectPicture->selected_image) checked @endif />
                                                                                
                                                                                    <label>
                                                                                        <div class="img-report-box">
                                                                                            <img name="image" class="img-fluid img-thumbnail" src="{{ asset('/img/projects/'.$dataProjectCategory->folder.'/'.$dataProjectPicture->image) }}"  />
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






                                                                        <?php
                                                                            if ($dataProjectPicture->selected_image == 0) {
                                                                                $subcatCSS = 'danger';
                                                                            }else{
                                                                                $subcatCSS = 'info';
                                                                            }
                                                                        ?>




                                                                        
                                                                        @endforeach

                                                                    @endforeach
                                                                <div class="col-md-12"><h6>{{ ucwords($dataSubcat->name) }}</h6></div>
                                                            </div>
                                                            <div class="w-100"></div>
                                                            <!-- <div class="col-md mt-1 text-center">
                                                            </div> -->
                                                            
                                                            
                                                        @endif
                                                        <?php $is++; ?>
                                                    @endforeach
                                                @endforeach
                                            </div>
                                        </div>
                                        <hr>
                                        @endif
                                    @endforeach <!-- category -->
                                @endforeach <!-- template -->

                                <div class="row m-0 mb-5">
                                    <div class="col-md">
                                        <div class="clearfix">
                                            <h5 class="small text-dark text-uppercase mb-5">disiapkan<br><span class="text-muted">{{ isset($dataProjectPicturesStatus->submitted_at) ? date('l, d F Y', strtotime($dataProjectPicturesStatus->submitted_at)) : '' }}</span></h5>
                                            <small>
                                                {{ ucwords($dataTechnician->firstname).' '.ucwords($dataTechnician->lastname) }}
                                            </small>
                                        </div>
                                    </div>
                                    @if(isset($approverProfile->jabatan))
                                        <div class="col-md">
                                            <h5 class="small text-dark text-uppercase mb-5">Disetujui, {{ ucwords($approverProfile->jabatan) ?? '' }}
                                            <br><span class="text-muted">{{ date('l, d F Y', strtotime($dataProjectPicturesStatus->approved_at)) ?? '' }}</span></h5>
                                            <small>
                                                {{ ucwords($approverProfile->firstname).' '.ucwords($approverProfile->lastname) ?? ''}}
                                            </small>
                                        </div>
                                    @endif
                                    @if($dataProjectPicturesStatus->countPMApproved > 0 && isset($approverPMProfile->jabatan))
                                        <div class="col-md">
                                            <h5 class="small text-dark text-uppercase mb-5">Disetujui, {{ ucwords($approverPMProfile->jabatan) ?? '' }}<br><span class="text-muted">{{ date('l, d F Y', strtotime($dataProjectPicturesStatus->approved_by_pm_at)) ?? '' }}</span></h5>
                                            <small>
                                                {{ ucwords($approverPMProfile->firstname).' '.ucwords($approverPMProfile->lastname) ?? ''}}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <hr>
                                <div class="d-print-none">
                                    <div class="float-right">
                                        <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light mt-1"><i class="fa fa-print"></i></a>
                                        <a href="{{ route($formProjectRouteShow,$infoProjectTask->project_id) }}" type="button" class="btn btn-blue-lini mt-1">Kembali</a>

                                        @if(isset($approverProfile))
                                            <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning mt-1" name="submit"><i class='fas fa-edit' title='done'> </i> Edit laporan gambar</button>
                                        @else
                                            <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning mt-1" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Approve semua gambar</button>
                                        @endif

                                    </div>
                                    <div class="clearfix"></div>
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
                    
                    @if($dataProjectPicturesStatus->status == 4)
                        <?php $css_1 = 'warning'; $css_2 = 'warning'; $css_3 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; $css_text_2 = 'text-info'; $css_text_3 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; $fa_code_2 = 'check text-success'; $fa_code_3 = 'check text-success'; ?>
                    @elseif($dataProjectPicturesStatus->status == 3)
                        <?php $css_1 = 'warning'; $css_2 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; $css_text_2 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; $fa_code_2 = 'check text-success'; ?>
                    @else
                        <?php $css_1 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; ?>
                    @endif

                    <div class="alert alert-{{ $css_3 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_3 }}"></i> ]</strong> Laporan disetujui oleh <span class="{{ $css_text_3 }}">Project Manager</span>. 
                        <div class="float-right">{{ isset($dataProjectPicturesStatus->approved_by_pm_at) ? date('l, d F Y', strtotime($dataProjectPicturesStatus->approved_by_pm_at)) : 'Belum disetujui' }}</div>
                    </div>
                    <div class="alert alert-{{ $css_2 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_2 }}"></i> ]</strong> Laporan disetujui oleh <span class="{{ $css_text_2 }}">Admin Document</span>.
                        <div class="float-right">{{ date('l, d F Y', strtotime($dataProjectPicturesStatus->approved_at)) ?? '' }}</div>
                    </div>
                    <div class="alert alert-{{ $css_1 }}">
                        <strong>[ <i class="fas fa-{{ $fa_code_1 }}"></i> ]</strong> Laporan diajukan oleh <span class="{{ $css_text_1 }}">Teknisi</span>.
                        <div class="float-right">{{ date('l, d F Y', strtotime($dataProjectPicturesStatus->submitted_at)) ?? '' }}</div>
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
