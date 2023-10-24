@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Progres project logs';
    $statusBadge    = array('','pink','info','success','danger','purple','dark','warning');
?>
@endsection

@section('content')
<div class="flash-message mt-2">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<div class="card mt-2">
    <?php 
        #default
        $newCSS = '';
        $onprogressCSS = '';
        $reportingCSS = '';
        $finishedCSS = '';
        $progressValue = 0;
        $newCount = 0;
        $inpreparationCount = 0;
        $onprogressCount = 0;
        $reportingCount = 0;
        $finishedCount = 0;

        if (isset($project->newCount)) {
            $newCount = $project->newCount;
        }
        if (isset($project->inpreparationCount)) {
            $inpreparationCount = $project->inpreparationCount;
        }
        if (isset($project->onprogressCount)) {
            $onprogressCount = $project->onprogressCount;
        }
        if (isset($project->reportingCount)) {
            $reportingCount = $project->reportingCount;
        }
        if (isset($project->finishedCount)) {
            $finishedCount = $project->finishedCount;
        }

        if ($newCount > 0 || $inpreparationCount > 0) {
            $newCSS = 'active ';
        }
        if ($onprogressCount > 0) {
            $newCSS = 'active ';
            $onprogressCSS = 'active ';
        }
        if ($reportingCount > 0) {
            $newCSS = 'active ';
            $onprogressCSS = 'active ';
            $reportingCSS = 'active ';
        }
        if ($finishedCount > 0) {
            $newCSS = 'active ';
            $onprogressCSS = 'active ';
            $reportingCSS = 'active ';
            $finishedCSS = 'active ';
        }

        #percentage
        $progressMax = ($newCount + $inpreparationCount + $onprogressCount + $reportingCount + $finishedCount) * 4;
        $totalProgressedTask = $newCount + $inpreparationCount + $onprogressCount * 2 + $reportingCount * 3 + $finishedCount * 4;
        #set conditions and value 
        if (isset($totalProgressedTask)) {
            $progressValue = $totalProgressedTask;
        }

        //total task
        $totalTask = $newCount + $inpreparationCount + $onprogressCount + $reportingCount + $finishedCount;
    ?>
    <div class="card-header text-center bb-orange">
        <span class="text-uppercase"><strong>{{ ucfirst($pageTitle) }}</strong></span>
        <br><strong><span class="text-danger text-uppercase">{{ $project->name }}</span> ({{ $totalTask }} task)</strong>
        <br><small>Mulai:</small> <strong><span class="text-info">{{ $project->date != null ? date('l, d F Y', strtotime($project->date)) : '-' }}</span></strong>
    </div>

    <div class="card-body">
        <!-- Start Content-->
        <div class="col-md text-center mt-3 mb-3">
            <!-- progressbar -->
            <ul id="progressbar">
                <li class="{{ $newCSS }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-magic progress-icon"></i>
                    </div>
                    <div>
                        <strong>Persiapan</strong>
                        <br>({{ $newCount + $inpreparationCount }}) task
                    </div>
                </li>
                <li class="{{ $onprogressCSS }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-wrench progress-icon"></i>
                    </div>
                    <div class="progress-icon-box-1">
                    </div>
                    <div>
                        <strong>Dalam pengerjaan</strong>
                        <br>({{ $onprogressCount }}) task
                    </div>
                </li>
                <li class="{{ $reportingCSS }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-file-alt progress-icon"></i>
                    </div>
                    <div>
                        <strong>Penyiapan laporan</strong>
                        <br>({{ $reportingCount }}) task
                    </div>
                </li>
                <li class="{{ $finishedCSS }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-lock progress-icon"></i>
                    </div>
                    <div>
                        <strong>Selesai</strong>
                        <br>({{ $finishedCount }}) task
                    </div>
                </li>
            </ul>
            <div class="progress mb-3">
                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="{{ $progressMax }}" style="width:{{ $progressValue > 1 ? ($progressValue/$progressMax) * 100 : '0' }}%">
                    {{ $progressValue > 1 ? ($progressValue/$progressMax) * 100 : '0' }}% Selesai (success)
                </div>
            </div>
        </div>
        <hr>
        <div class="container-fluid">

            <div class="row">
                <div class="col-sm-12">
                    <div class="timeline" dir="ltr">
                        <article class="timeline-item alt">
                            <div class="time-show first">
                                <a href="#" class="btn btn-orange width-lg">Today</a>
                            </div>
                        </article>

                        <!-- laporan pekerjaan -->
                        <?php $i = 1; $ib = 1;?>
                        @if(count($dataLogs) > 0)
                            @foreach($dataLogs as $dataLog)
                                <?php 
                                    if($ib == 7){ $ib = 1;}
                                    if ($i % 2 == 1) {
                                        $css = '';
                                        $cssArrow = '';
                                    }else{
                                        $css = ' alt';
                                        $cssArrow = '-alt';
                                    }
                                ?>
                                <article class="timeline-item {{ $css }}">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow{{ $cssArrow }}"></span>
                                                <span class="timeline-icon bg-{{ $statusBadge[$ib] }}"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-{{ $statusBadge[$ib] }}">{{ $dataLog->date ? date('l, d F Y', strtotime($dataLog->date)) : 'Tanggal tidak tersedia' }}</h4>
                                                <p class="timeline-date text-muted"><small>{{ $dataLog->date ? date('H:i a', strtotime($dataLog->date)) : 'Jam tidak tersedia' }}</small></p>
                                                <p>{!! $dataLog->name !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                                <?php $i++; $ib++; ?>
                            @endforeach
                        @else
                            <article class="timeline-item">
                                <div class="timeline-desk">
                                    <div class="panel">
                                        <div class="panel-body">
                                            <span class="arrow"></span>
                                            <span class="timeline-icon bg-pink"><i class="mdi mdi-circle"></i></span>
                                            <h4 class="text-pink">{{ date('l, d F Y') }}</h4>
                                            <p class="timeline-date text-muted"><small>{{ date('H:i a') }}</small></p>
                                            <p>Belum ada data</p>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endif
                        <!-- laporan pekerjaan -->
                    </div>
                </div>
            </div>
            <!-- end row -->        

        </div> <!-- container-fluid -->
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable(
            "order":[]
        );
    } );
</script>
@endsection
