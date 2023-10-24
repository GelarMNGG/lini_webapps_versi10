@extends('layouts.dashboard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Dashboard';
    $statusBadge    = array('dark','info','success','danger','purple','pink','warning');
    $formManualShow = 'tech.manualdetail';
    $formTroubleshootingShow = 'tech.troubleshootingdetail';
?>
@endsection

@section('content')
<div class="flash-message mt-2">
    <!-- announcement -->
    @if(isset($flashMessageData))
        <p class="alert alert-{{ $flashMessageData->level }}">{{ ucfirst($flashMessageData->message) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
    @endif
    <!-- session -->
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<div class="card text-center mt-2">
    <div class="card-header text-uppercase bb-orange"><strong>{{ ucfirst($pageTitle) }}</strong></div>

    <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <div class="col-md">
            <div>
                <div class="icon-box-dashboard">
                    <a href="{{ route('project-tech.index') }}"><img class="icon-login" src="{{ asset('admintheme/images/icon/icon-proyek.png') }}"></a>
                    <a href="{{ route('project-tech.index') }}">Proyek</a>
                </div>
                @if(isset($currentTask->project_id))
                    <div class="icon-box-dashboard">
                        <a href="{{ route('minutes-tech.create','project_id='.$currentTask->project_id.'&task_id='.$currentTask->id) }}"><img class="icon-login" src="{{ asset('admintheme/images/icon/icon-activity.png') }}"></a>
                        <a href="{{ route('minutes-tech.create','project_id='.$currentTask->project_id.'&task_id='.$currentTask->id) }}">Aktifitas</a>
                    </div>
                    <div class="icon-box-dashboard">
                        <a href="{{ route('report-tech.index','project_id='.$currentTask->project_id.'&task_id='.$currentTask->id) }}"><img class="icon-login" src="{{ asset('admintheme/images/icon/icon-laporan.png') }}"></a>
                        <a href="{{ route('report-tech.index','project_id='.$currentTask->project_id.'&task_id='.$currentTask->id) }}">Laporan</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> <!-- container-fluid -->
<!-- tech project -->
@if(sizeof($dataProjects) > 0)
<div class="card">
    <div class="card-header bb-orange"><span class="badge badge-danger float-left mr-1">{{ sizeof($dataProjects) <= 5 ? sizeof($dataProjects) : '>5' }}</span> Project terbaru</div>
    <div class="bg-gray-lini">
        <div class="row m-0">
                @foreach($dataProjects as $dataProject)
                    <div class="col-md-6 p-2">
                        <div class="bg-card-box br-5 p-2">
                            @foreach($projectStatus as $dataStatus)
                                @if($dataStatus->id == $dataProject->status)
                                    <span class="badge badge-{{ $statusBadge[$dataStatus->id] }} float-right">{{ ucwords($dataStatus->name) }}</span>
                                @endif
                            @endforeach
                            {{ ucfirst($dataProject->name) }}
                            <br><span class="text-info">{{ date('l, d F Y',strtotime($dataProject->date_start))}}</span> 
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif
<!-- tech project end -->
<!-- manuals -->
@if(isset($dataManuals) && count($dataManuals) > 0)
    <div class="card">
        <div class="card-header bb-orange">Informasi
            @if(count($dataManuals) > 4)
                <span class="float-right"><a href="#">Lihat semua</a></span>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                @if(isset($dataManuals))
                    <?php $id = 1; ?>
                    @foreach($dataManuals as $dataManual)
                        <?php
                            if ($id % 2 == 0) {
                                $manualCSS = '';
                            } else{
                                $manualCSS = ' mb-2';
                            }
                        ?>
                        <div class="col-md-6{{ $manualCSS }}">
                            <img class="w-100 img-fluid img-thumbnail" src="{{ asset('img/blogs/'.$dataManual->image) }}">
                            <div class="dashboard-article-box bg-blue-lini-2">
                                <div class="col-md"><span class="text-uppercase">{{ $dataManual->title }}</span></div>
                                <div class="col-md mt-2" style="display:inline-block">
                                    <div class="float-left">
                                        <span class="text-secondary"><small>{{ $dataManual->views ? $dataManual->views : '0' }} <i class="fas fa-eye"></i></small></span>
                                    </div>
                                    <div class="float-right">
                                        <a href="{{ route($formManualShow, $dataManual->id) }}">Baca selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php $id++; ?>
                    @endforeach
                @else
                <div class="col-md-6 mb-2">
                    <img class="w-100 img-fluid img-thumbnail" src="{{ asset('admintheme/images/panduan/panduan-laporan.png') }}">
                    <div class="dashboard-article-box bg-blue-lini-2">
                        <span class="text-uppercase">Panduan pembuatan Laporan</span>
                        <div class="text-right mt-2">
                            <a href="#">Baca selengkapnya</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <img class="w-100 img-fluid img-thumbnail" src="{{ asset('admintheme/images/panduan/panduan-ca.png') }}">
                    <div class="dashboard-article-box bg-blue-lini-2">
                        <span class="text-uppercase">Panduan mengajukan cash advance</span>
                        <div class="text-right mt-2">
                            <a href="#">Baca selengkapnya</a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endif
<!-- manuals end -->
<!-- troubleshooting -->
@if(isset($dataTroubleshootings) && count($dataTroubleshootings) > 0)
    <div class="card">
        <div class="card-header bb-orange">Troubleshooting
            @if(count($dataTroubleshootings) > 4)
                <span class="float-right"><a href="#">Lihat semua</a></span>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                @if(isset($dataTroubleshootings))
                    <?php $id = 1; ?>
                    @foreach($dataTroubleshootings as $dataTroubleshooting)
                        <?php
                            if ($id % 2 == 0) {
                                $manualCSS = '';
                            } else{
                                $manualCSS = ' mb-2';
                            }
                        ?>
                        <div class="col-md-6{{ $manualCSS }}">
                            @if(isset($dataTroubleshooting->image) && $dataTroubleshooting->image != null)
                                <img class="w-100 img-fluid img-thumbnail" src="{{ asset('img/troubleshooting/'.$dataTroubleshooting->image) }}">
                            @else
                                <img class="w-100 img-fluid img-thumbnail" src="{{ asset('img/troubleshooting/default.png') }}">
                            @endif
                            <div class="dashboard-article-box bg-blue-lini-2">
                                <div class="col-md"><span class="text-uppercase">{{ $dataTroubleshooting->title }}</span></div>
                                <div class="col-md mt-2" style="display:inline-block">
                                    <div class="float-left">
                                        <span class="text-secondary"><small>{{ $dataTroubleshooting->view ? $dataTroubleshooting->view : '0' }} <i class="fas fa-eye"></i></small></span>
                                    </div>
                                    <div class="float-right">
                                        <a href="{{ route($formTroubleshootingShow, $dataTroubleshooting->id) }}">Baca selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php $id++; ?>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endif
<!-- troubleshooting end -->
@endsection
