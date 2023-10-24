@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
        $pageTitle      = 'Laporan '.strtoupper($projectTask->project_name).'-Task '.ucwords(strtolower($projectTask->name));
        $statusBadge    = array('','info','danger','purple','pink','warning','dark');
    //return
        $formRouteBack = 'report-tech.index';
    //template
        $formRouteTemplateShow = 'user-projects-template.show';
    //image
        $formRouteShow = 'user-projects.show';
        $formRouteUpdate= 'user-projects.update';
    //report format
        $formReportFormatStore = 'user-projects-report-qc.store';
        $formReportFormatUpdate = 'user-projects-report-qc.update';
        $formReportFormatDestroy = 'user-projects-report-qc.destroy';
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
                        <div class="panel-heading text-center">
                            <h1 class="text-uppercase">Laporan <strong>{{ isset($projectTask->project_name) ? strtoupper($projectTask->project_name) : '' }}</strong></h1>
                            <h2 class="text-uppercase"> <strong><span class="text-danger">{{ isset($projectTask->name) ? strtoupper($projectTask->name) : 'Belum ada task' }}</span></strong></h2>
                            <!-- report date -->
                            {{ date('l, d F Y',strtotime($projectReportAllData->created_at)) }}
                        </div>
                        <hr>
                        <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- content -->
                                        @if(isset($projectReportFormatDatas))
                                            @foreach($projectReportFormatDatas as $projectReportFormatData)
                                                <div class="">
                                                    <div class="box">
                                                        <h4> {{ ucfirst($projectReportFormatData->name) }}</h4>
                                                        <div class="row">
                                                            @if(isset($projectReportFormatData->template_id))
                                                                @if(isset($projectReportTemplateContentDatas))
                                                                    <!-- tipe -->
                                                                    <?php $image = 1; ?>
                                                                    @if($projectReportFormatData->type == $image)
                                                                        <?php $iContent = 1; ?>
                                                                        @foreach($projectReportTemplateContentDatas as $templateContent)
                                                                            @if($templateContent->template_id == $projectReportFormatData->template_id)
                                                                                <?php 
                                                                                    //column count
                                                                                    $columnData = $projectReportFormatData->image_count;
                                                                                    if ($columnData == 3) {
                                                                                        $column = 4;
                                                                                        $columnDiv = 3;
                                                                                    }elseif($columnData == 2){
                                                                                        $column = 6;
                                                                                        $columnDiv = 2;
                                                                                    }else{
                                                                                        $column = 12;
                                                                                        $columnDiv = 1;
                                                                                    }
                                                                                ?>
                                                                                <div class="col-md-{{$column}}">
                                                                                    <figure class="figure">
                                                                                        <div class="img-report-all">
                                                                                            <img src="{{ asset('img/projects/'.$dataProject->folder.'/'.$templateContent->image) }}">
                                                                                        </div>
                                                                                        <figcaption class="figure-caption text-center">{{ isset($templateContent->subcat_name) ? ucfirst($templateContent->subcat_name) : ucfirst($templateContent->subcatcust_name) }}</figcaption>
                                                                                    </figure>
                                                                                </div>
                                                                                <?php 
                                                                                    if ($iContent % $columnDiv == 0) {
                                                                                        echo "<div class='w-100'></div>";
                                                                                    }
                                                                                ?>
                                                                                <?php $iContent++; ?>
                                                                            @endif
                                                                        @endforeach
                                                                    @else
                                                                    <?php $ict = 1; ?>
                                                                        @foreach($projectReportTemplateContentDatasText as $templateContent)
                                                                            @if($templateContent->template_id == $projectReportFormatData->template_id)
                                                                                <?php 
                                                                                    //column count
                                                                                    $columnData = $projectReportFormatData->image_count;
                                                                                    if ($columnData == 3) {
                                                                                        $column = 4;
                                                                                        $columnDiv = 3;
                                                                                    }elseif($columnData == 2){
                                                                                        $column = 6;
                                                                                        $columnDiv = 2;
                                                                                    }else{
                                                                                        $column = 12;
                                                                                        $columnDiv = 1;
                                                                                    }
                                                                                ?>
                                                                                <div class="col-md-{{$column}} text-justify">
                                                                                    {!! $templateContent->text !!}
                                                                                </div>
                                                                                <?php 
                                                                                    if ($ict % $columnDiv == 0) {
                                                                                        echo "<div class='w-100'></div>";
                                                                                    }
                                                                                ?>
                                                                                <?php $ict++; ?>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                @else
                                                                    <div class="alert alert-warning">Belum ada data.</div>
                                                                @endif
                                                            @else
                                                                <img src="{{ asset('img/projects/report/report_format/'.$projectReportFormatData->image) }}" class="img-fluid">
                                                            @endif
                                                
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
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
                                            <h5 class="small text-dark text-uppercase mb-5">{{ isset($dataProject->customer_name) ? ucwords($dataProject->customer_name) : 'Belum ada' }}
                                            </h5>
                                            <hr class="mb-0">
                                            <small>
                                                Name: {{ isset($dataProject->customer_pic_firstname) ? ucfirst($dataProject->customer_pic_firstname).' '.ucfirst($dataProject->customer_pic_lastname) : '' }} 
                                                <br>Date: <span class="text-muted">{{ isset($projectReportAllData->approved_by_cust_at) ? date('l, d F Y',strtotime($projectReportAllData->approved_by_cust_at)) : '-' }}</span>
                                            </small>
                                        </div>
                                    </div>
                                    @if(isset($dataProject->partner_name))
                                        <div class="col-md">
                                            <h5 class="small text-dark text-uppercase mb-5">{{ isset($dataProject->partner_name) ? ucwords($dataProject->partner_name) : '' }}</h5>
                                            <hr class="mb-0">
                                            <small>
                                                Nama: {{ isset($dataProject->partner_pic_firstname) ? ucfirst($dataProject->partner_pic_firstname).' '.ucfirst($dataProject->partner_pic_lastname) : '' }} 
                                                <br>Date: <span class="text-muted">{{ isset($projectReportAllData->approved_by_partner_at) ? date('l, d F Y',strtotime($projectReportAllData->approved_by_partner_at)) : '-' }}</span>
                                            </small>
                                        </div>
                                    @endif
                                    <div class="col-md">
                                        <h5 class="small text-dark text-uppercase mb-5">{{ isset($companyInfo->name) ? ucwords($companyInfo->name) : 'PT Lima Inti Sinergi' }}</h5>
                                        <hr class="mb-0">
                                        <small>
                                            Nama: {{ isset($dataProject->pm_firstname) ? ucfirst($dataProject->pm_firstname).' '.ucfirst($dataProject->pm_lastname) : '' }} 
                                            <br>Date: <span class="text-muted"> {{ isset($projectReportAllData->approved_by_pm_at) ? date('l, d F Y',strtotime($projectReportAllData->approved_by_pm_at)) : '-' }}</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="d-print-none">
                                    <div class="float-right">
                                        <form action="{{ route($formReportFormatUpdate,$projectReportAllData->id) }}" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                        <?php /*
                                        <form action="{{ route($formReportFormatUpdate,$projectReportFormatData->id) }}" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate> */ ?>
                                            @csrf
                                            @method('PUT')
                                            <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>

                                            <!-- hidden data -->
                                            <input type="hidden" name="project_id" value="{{ $projectTask->project_id }}">
                                            <input type="hidden" name="task_id" value="{{ $projectTask->id }}">

                                            @if(Auth::user()->user_level == 3)
                                                @if(!isset($projectReportAllData->approved_by_pm_at) && $projectReportAllData->status == 1)
                                                    <!-- the data status -->
                                                    <input class="form-control" type="text" name="status" value="2" hidden>
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Approve</button>
                                                @elseif(isset($projectReportAllData->approved_by_pm_at) && !isset($projectReportAllData->approved_by_cust_at))
                                                    <input class="form-control" type="text" name="status" value="0" hidden>
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Reset</button>
                                                @endif
                                            @endif

                                            @if(Auth::user()->user_level == 4)
                                                @if(!isset($projectReportAllData->submitted_at))
                                                    <input class="form-control" type="text" name="status" value="1" hidden>
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-warning" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Submit</button>
                                                @endif
                                                @if(isset($projectReportAllData->approved_by_pm_at) && $projectReportAllData->status == 2)
                                                    <input class="form-control" type="text" name="status" value="3" hidden>
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" onclick="return confirm('Apakah Anda yakin share laporan {{ strtoupper($projectTask->project_name) }} - Task {{ ucwords(strtolower($projectTask->name)) }} ke pelanggan?')" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Share to customer</button>
                                                @endif
                                            @endif

                                            <a href="{{ route($formRouteBack,'?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" type="button" class="btn btn-secondary">Kembali</a>
                                        </form>

                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            @if(Auth::user()->user_level == 3)
                                @if($projectReportAllData->countSubmitted > 0 && $projectReportAllData->countPMApproved < 1)
                                    <form action="route($formRouteUpdate,$projectTemplate->id)" class="text-right mt-1" style="display:inline-block; width:100%;" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                        @csrf
                                        @method('PUT')

                                        <!-- hidden data -->
                                        <input type="hidden" name="project_id" value="{{ $projectTask->project_id }}">
                                        <input type="hidden" name="task_id" value="{{ $projectTask->id }}">
                                        <input type="hidden" name="status" value="0">

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
                    <?php $css_4 = 'secondary'; ?>
                    <?php $css_text_1 = 'dark'; ?>
                    <?php $css_text_2 = 'dark'; ?>
                    <?php $css_text_3 = 'dark'; ?>
                    <?php $css_text_4 = 'dark'; ?>
                    <?php $fa_code_1 = 'times text-danger'; ?>
                    <?php $fa_code_2 = 'times text-danger'; ?>
                    <?php $fa_code_3 = 'times text-danger'; ?>
                    <?php $fa_code_4 = 'times text-danger'; ?>
                    
                    @if(isset($projectReportAllData->approved_by_cust_at))
                        <?php $css_1 = 'warning'; $css_ = 'warning'; $css_3 = 'warning'; $css_4 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; $css_text_2 = 'text-info'; $css_text_3 = 'text-info'; $css_text_4 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; $fa_code_2 = 'check text-success'; $fa_code_3 = 'check text-success'; $fa_code_4 = 'check text-success'; ?>
                    @elseif(isset($projectReportAllData->approved_by_pm_at))
                        <?php $css_1 = 'warning'; $css_2 = 'warning'; $css_3 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; $css_text_2 = 'text-info'; $css_text_3 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; $fa_code_2 = 'check text-success'; $fa_code_3 = 'check text-success'; ?>
                    @elseif(isset($projectReportAllData->submitted_at))
                        <?php $css_1 = 'warning'; $css_2 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; $css_text_2 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; $fa_code_2 = 'check text-success'; ?>
                    @elseif(isset($projectReportAllData->technician))
                        <?php $css_1 = 'warning'; ?>
                        <?php $css_text_1 = 'text-info'; ?>
                        <?php $fa_code_1 = 'check text-success'; ?>
                    @endif
                    <!-- Approval notifications -->
                    <div class="alert alert-{{ $css_4 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_4 }}"></i> ]</strong> Laporan disetujui oleh <span class="{{ $css_text_4 }}">Pelanggan</span>. 
                        <div class="float-right">{{ isset($projectReportAllData->approved_by_cust_at) ? date('l, d F Y', strtotime($projectReportAllData->approved_by_cust_at)) : 'Belum disetujui' }}</div>
                    </div>
                    <div class="alert alert-{{ $css_3 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_3 }}"></i> ]</strong> Laporan disetujui oleh <span class="{{ $css_text_3 }}">Project Manager</span>. 
                        <div class="float-right">{{ isset($projectReportAllData->approved_by_pm_at) ? date('l, d F Y', strtotime($projectReportAllData->approved_by_pm_at)) : 'Belum disetujui' }}</div>
                    </div>
                    <div class="alert alert-{{ $css_2 }}"> 
                        <strong>[ <i class="fas fa-{{ $fa_code_2 }}"></i> ]</strong> Laporan diajukan oleh <span class="{{ $css_text_2 }}">Admin Document</span>.
                        <div class="float-right">{{ isset($projectReportAllData->submitted_at) ? date('l, d F Y', strtotime($projectReportAllData->submitted_at)) : 'Belum disubmit' }}</div>
                    </div>
                    <?php /*
                    <div class="alert alert-{{ $css_1 }}">
                        <strong>[ <i class="fas fa-{{ $fa_code_1 }}"></i> ]</strong> Format laporan diajukan oleh <span class="{{ $css_text_1 }}">Teknisi</span>.
                        <div class="float-right">{{ isset($projectReportAllData->submitted_at) ? date('l, d F Y', strtotime($projectReportAllData->submitted_at)) : 'Belum disubmit' }}</div>
                    </div>
                    */ ?>
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
