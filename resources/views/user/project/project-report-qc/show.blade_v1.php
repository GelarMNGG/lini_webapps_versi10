@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Format laporan pekerjaan';
    $statusBadge    = array('','info','danger','purple','pink','warning','dark');
    //template
    $formRouteTemplateShow = 'user-projects-template.show';
    //image
    $formRouteShow = 'user-projects.show';
    $formRouteUpdate= 'user-projects.update';
    //image comments
    $formImageCommentsStore = 'user-projects-report-qc-comments.store';

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
                        @if($dataProject->customer_logo != null)
                            <span class="logo report-logo float-left">
                                <img src="{{ asset('img/clients/'.$dataProject->customer_logo) }}" alt="logo {{ $dataProject->customer_name }}">
                            </span>
                        @endif
                        @if($dataProject->partner_logo != null)
                            <span class="logo report-logo float-right">
                                <img src="{{ asset('img/clients/'.$dataProject->partner_logo) }}" alt="logo {{ $dataProject->partner_name }}">
                            </span>
                        @else
                            <span class="logo report-logo float-right">
                                <img src="{{ asset('img/'.$companyInfo->logo) }}" alt="logo {{ $companyInfo->name }}">
                            </span>
                        @endif
                        <div class="panel-heading text-center text-uppercase">
                            <small>Proyek:</small> <strong><span class="text-info">{{ isset($projectTask->project_name) ? strtoupper($projectTask->project_name) : '' }}</span></strong>
                            <br><small>Task:</small> <strong><span class="text-danger">{{ isset($projectTask->name) ? strtoupper($projectTask->name) : 'Belum ada task' }}</span></strong>
                        </div>
                        <hr>
                        <div class="panel-body">

                            
                                <div class="row mt-4">
                                    <div class="col-md-12">

                                        <!-- content -->





                                        <?php $i=1; ?>
                                        @if(isset($projectReportTemplateDatas))
                                            @foreach($projectReportTemplateDatas as $projectReportTemplateData)
                                                <div class="card-body border-dashed rounded d-flex mb-2">
                                                    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#liniModal{{ $i }}">
                                                        <i class="fa fa-plus"></i> Format laporan
                                                    </button> 
                                                    <h5 class="ml-2">{{ $projectReportTemplateData->name }}</h5>
                                                </div>
                                                <!-- modal -->
                                                    <div class="modal fade" id="liniModal{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="liniModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="liniModalLabel">Format laporan <span class="text-uppercase text-danger">{{ $projectReportTemplateData->name }}</span></h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            
                                                            <div class="modal-body">
                                                                <div id="basicwizard">

                                                                    <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                                                        <li class="nav-item">
                                                                            <a href="#basictab1" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active"> 
                                                                                <i class="mdi mdi-account-circle mr-1"></i>
                                                                                <span class="d-none d-sm-inline">Text</span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a href="#basictab2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                                                                <i class="mdi mdi-face-profile mr-1"></i>
                                                                                <span class="d-none d-sm-inline">Image</span>
                                                                            </a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a href="#basictab3" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                                                                <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                                                                <span class="d-none d-sm-inline">Mix</span>
                                                                            </a>
                                                                        </li>
                                                                    </ul>

                                                                    <div class="tab-content border-0 mb-0">
                                                                        <div class="tab-pane active" id="basictab1">
                                                                            <div class="row">
                                                                                <div class="col-12">
                                                                                    <div class="form-group row mb-3">
                                                                                        <label class="col-md-3 col-form-label" for="userName">User name</label>
                                                                                        <div class="col-md-9">
                                                                                            <input type="text" class="form-control" id="userName" name="userName" value="Coderthemes">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group row mb-3">
                                                                                        <label class="col-md-3 col-form-label" for="password"> Password</label>
                                                                                        <div class="col-md-9">
                                                                                            <input type="password" id="password" name="password" class="form-control" value="123456789">
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <div class="form-group row mb-3">
                                                                                        <label class="col-md-3 col-form-label" for="confirm">Re Password</label>
                                                                                        <div class="col-md-9">
                                                                                            <input type="password" id="confirm" name="confirm" class="form-control" value="123456789">
                                                                                        </div>
                                                                                    </div>
                                                                                </div> <!-- end col -->
                                                                            </div> <!-- end row -->
                                                                        </div>

                                                                        <div class="tab-pane" id="basictab2">
                                                                            <div class="row">
                                                                                <div class="col-12">
                                                                                    <div class="form-group row mb-3">
                                                                                        <label class="col-md-3 col-form-label" for="name"> First name</label>
                                                                                        <div class="col-md-9">
                                                                                            <input type="text" id="name" name="name" class="form-control" value="Francis">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group row mb-3">
                                                                                        <label class="col-md-3 col-form-label" for="surname"> Last name</label>
                                                                                        <div class="col-md-9">
                                                                                            <input type="text" id="surname" name="surname" class="form-control" value="Brinkman">
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="form-group row mb-3">
                                                                                        <label class="col-md-3 col-form-label" for="email">Email</label>
                                                                                        <div class="col-md-9">
                                                                                            <input type="email" id="email" name="email" class="form-control" value="cory1979@hotmail.com">
                                                                                        </div>
                                                                                    </div>
                                                                                </div> <!-- end col -->
                                                                            </div> <!-- end row -->
                                                                        </div>

                                                                        <div class="tab-pane" id="basictab3">
                                                                            <div class="row">
                                                                                <div class="col-12">
                                                                                    <div class="text-center">
                                                                                        <h2 class="mt-0"><i class="mdi mdi-check-all"></i></h2>
                                                                                        <h3 class="mt-0">Thank you !</h3>

                                                                                        <p class="w-75 mb-2 mx-auto">Quisque nec turpis at urna dictum luctus. Suspendisse convallis dignissim eros at volutpat. In egestas mattis dui. Aliquam
                                                                                            mattis dictum aliquet.</p>

                                                                                        <div class="mb-3">
                                                                                            <div class="custom-control custom-checkbox">
                                                                                                <input type="checkbox" class="custom-control-input" id="customCheck1">
                                                                                                <label class="custom-control-label" for="customCheck1">I agree with the Terms and Conditions</label>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div> <!-- end col -->
                                                                            </div> <!-- end row -->
                                                                        </div>

                                                                        <ul class="list-inline wizard mb-0">
                                                                            <li class="previous list-inline-item">
                                                                                <a href="javascript: void(0);" class="btn btn-secondary">Previous</a>
                                                                            </li>
                                                                            <li class="next list-inline-item float-right">
                                                                                <a href="javascript: void(0);" class="btn btn-secondary">Next</a>
                                                                            </li>
                                                                        </ul>

                                                                    </div> <!-- tab-content -->
                                                                    </div> <!-- end #basicwizard-->
                                                            </div>
                                                            
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary">Save changes</button>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- modal end -->
                                                <?php $i++; ?>
                                            @endforeach
                                        @else
                                            <div class="alert alert-warning">
                                                Belum ada data template.
                                                <a href="{{ route($formRouteTemplateShow,'?project_id='.$projectReportAllData->project_id.'&task_id='.$projectReportAllData->task_id) }}" class="btn btn-success">Buat template</a>
                                            </div>
                                        @endif


                                        





                                    </div>
                                </div>
                                <hr>
                                <div class="d-print-none">
                                    <div class="float-right">
                                        <form action="route($formRouteUpdate,$projectTemplate->id)" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                            @csrf
                                            @method('PUT')
                                            <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>

                                            <!-- hidden data -->
                                            <input value="{{ $projectTask->project_id }}" name="project_id" hidden>
                                            <input value="{{ $projectTask->id }}" name="task_id" hidden>

                                            @if(Auth::user()->user_level == 3)
                                                @if(isset($dataProjectReportAll->approved_by_pm_at))
                                                    <!-- the data status -->
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Approve</button>
                                                @else
                                                    <input class="form-control" type="text" name="status" value="4" hidden>
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Edit</button>
                                                @endif
                                            @endif

                                            @if(Auth::user()->user_level == 4)
                                                @if(!isset($dataProjectReportAll->submitted_at))
                                                    <input class="form-control" type="text" name="status" value="1" hidden>
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Submit</button>
                                                @endif
                                            @endif

                                            <a href="{{ route($formRouteShow,$projectTask->id) }}" type="button" class="btn btn-secondary">Kembali</a>
                                        </form>

                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            @if(Auth::user()->user_level == 3)
                                @if($dataProjectReportAll->countSubmitted > 0 && $dataProjectReportAll->countPMApproved < 1)
                                    <form action="route($formRouteUpdate,$projectTemplate->id)" class="text-right mt-1" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                        @csrf
                                        @method('PUT')

                                        <!-- hidden data -->
                                        <input value="{{ $projectTask->project_id }}" name="project_id" hidden>
                                        <input value="{{ $projectTask->id }}" name="task_id" hidden>
                                        <input class="form-control" type="text" name="status" value="0" hidden>

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
                    <!-- Approval notifications -->
                    <div class="alert alert-{{ $css_3 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_3 }}"></i> ]</strong> Format laporan disetujui oleh <span class="{{ $css_text_3 }}">Project Manager</span>. 
                        <div class="float-right">{{ isset($approverData->approved_by_pm_at) ? date('l, d F Y', strtotime($approverData->approved_by_pm_at)) : 'Belum disetujui' }}</div>
                    </div>
                    
                    <div class="alert alert-{{ $css_2 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_2 }}"></i> ]</strong> Format laporan diajukan oleh <span class="{{ $css_text_2 }}">Admin Document</span>.
                        <div class="float-right">{{ isset($approverData->approved_at) ? date('l, d F Y', strtotime($approverData->approved_at)) : 'Belum disubmit' }}</div>
                    </div>
                    <?php /*
                    <div class="alert alert-{{ $css_1 }}">
                        <strong>[ <i class="fas fa-{{ $fa_code_1 }}"></i> ]</strong> Format laporan diajukan oleh <span class="{{ $css_text_1 }}">Teknisi</span>.
                        <div class="float-right">{{ isset($approverData->submitted_at) ? date('l, d F Y', strtotime($approverData->submitted_at)) : 'Belum disubmit' }}</div>
                    </div>
                    */ ?>
                    <div class="d-print-none">
                        <form method="post" action="{{ route($formImageCommentsStore) }}" enctype="multipart/form-data" class="card-box">
                            @csrf

                            <span class="input-icon icon-right">
                                <textarea rows="3" name="comment" class="form-control" placeholder="Kirim komentar" value="{{ old('comment') ?? '' }}" required></textarea>
                            </span>
                            <!-- comment data -->
                            <input value="{{ $projectTask->project_id }}" name="project_id" hidden>
                            <input value="{{ $projectTask->id }}" name="task_id" hidden>
                            <input value="{{ isset($dataProjectReportAll->id) ?? $dataProjectReportAll->id }}" name="pra_id" hidden>
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

                        @if(isset($dataProjectReportAll->commentsCount) && $dataProjectReportAll->commentsCount > 0)
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
    
</script>

@endsection
