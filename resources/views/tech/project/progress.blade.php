@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Progres projects';
    $statusBadge    = array('dark','info','success','danger','purple','pink','warning');
    //form project route
    $formRouteProjectIndex = 'project-tech.index';
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
    <div class="card-header text-center bb-orange">
        <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTask->project_name }}</span></strong>
        <br><small>Task:</small> <strong><span class="text-danger text-uppercase">{{ $projectTask->name }}</span></strong>
        <br><small>No task:</small> <strong><span class="text-warning text-uppercase">{{ $projectTask->number }}</span></strong>
        <br><a href="{{ route($formRouteProjectIndex,'task_id='.$projectTask->id) }}" type="button" class="btn btn-blue-lini mb-1 ml-1">Kembali</a>
    </div>

    <div class="card-body">
        <!-- Start Content-->
        <div class="col-md text-center mt-3 mb-3">
            <?php 
                #default
                $newCSS = '';
                $onprogressCSS = '';
                $reportingCSS = '';
                $finishedCSS = '';
                $progressValue = 0;
                $progressMax = 100;
                
                $toolsReportCount = 0;
                $expensesReportCount = 0;
                $projectReportCount = 0;
                $finishedCount = 0;

                if (isset($projectTask->tools_report_count) && $projectTask->tools_report_count > 0) {
                    $toolsReportCount = 1;
                }
                if (isset($projectTask->expenses_report_count) && $projectTask->expenses_report_count > 0) {
                    $expensesReportCount = 1;
                }
                if (isset($projectTask->images_report_count) && $projectTask->images_report_count > 0) {
                    $projectReportCount = 1;
                }

                #percentage
                $progressValue = $toolsReportCount + $expensesReportCount + $projectReportCount + $finishedCount;
                $totalCount = 4;

                if ($toolsReportCount > 0) {
                    $newCSS = 'active ';
                }
                if ($expensesReportCount > 0) {
                    $onprogressCSS = 'active ';
                }
                if ($projectReportCount > 0) {
                    $reportingCSS = 'active ';
                }
                if ($finishedCount > 0) {
                    $newCSS = 'active ';
                    $onprogressCSS = 'active ';
                    $reportingCSS = 'active ';
                    $finishedCSS = 'active ';
                }
            ?>
            <!-- progressbar -->
            <ul id="progressbar" class="progressbar-it-4">
                <li class="{{ $newCSS }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-wrench progress-icon"></i>
                    </div>
                    <div class="progress-it-title">
                        <strong>Laporan pengembalian alat</strong>
                        <br>
                        @if($toolsReportCount > 0)
                            <span class="mt-0 text-success"><i class="mdi mdi-check-all"></i></span>
                        @else
                            <span class="text-danger small"><strong>Belum selesai</strong></span>
                        @endif
                    </div>
                </li>
                <li class="{{ $onprogressCSS }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-file-invoice progress-icon"></i>
                    </div>
                    <div class="progress-icon-box-1">
                    </div>
                    <div class="progress-it-title">
                        <strong>Laporan pengeluaran</strong>
                        <br>
                        @if($expensesReportCount > 0)
                            <span class="mt-0 text-success"><i class="mdi mdi-check-all"></i></span>
                        @else
                            <span class="text-danger small"><strong>Belum selesai</strong></span>
                        @endif
                    </div>
                </li>
                <li class="{{ $reportingCSS }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-file-signature progress-icon"></i>
                    </div>
                    <div class="progress-it-title">
                        <strong>Laporan pekerjaan</strong>
                        <br>
                        @if($projectReportCount > 0)
                            <span class="mt-0 text-success"><i class="mdi mdi-check-all"></i></span>
                        @else
                            <span class="text-danger small"><strong>Belum selesai</strong></span>
                        @endif
                    </div>
                </li>
                <li class="{{ $finishedCSS }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fas fa-hands progress-icon"></i>
                    </div>
                    <div class="progress-it-title">
                        <strong>Pembayaran</strong>
                        <br>
                        @if($finishedCount > 0)
                            <span class="mt-0 text-success"><i class="mdi mdi-check-all"></i></span>
                        @else
                            <span class="text-danger small"><strong>Belum dilakukan</strong></span>
                        @endif
                    </div>
                </li>
            </ul>
            <div class="progress mb-3">
                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="{{ $progressMax }}" style="width:{{ $totalCount > 0 ? ($progressValue/$totalCount) * 100 : 0 }}%">
                    {{ $totalCount > 1 ? ($progressValue/$totalCount) * 100 : '0' }}% Selesai (success)
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
                        <?php
                            /////custom css style
                            if (isset($dataReportImage)) {
                                if (isset($dataReportImage->approved_by_pm_at)) {
                                    $report1 = 2;
                                }else{ $report1 = 1; }
                            }else{ $report1 = 0; }
                            if (isset($dataExpense)) {
                                if (isset($dataExpense->approved_by_pm_at)) {
                                    $report2 = 2;
                                }else{ $report2 = 1; }
                            }else{ $report2 = 0; }
                            if (isset($dataTool)) {
                                if (isset($dataTool->approved_at)) {
                                    $report3 = 2;
                                }else{ $report3 = 1; }
                            }else{ $report3 = 0; }

                            $reportImagesStyle = $report1;
                            $reportExpensesStyle = $report1 + $report2;
                            $reportToolsStyle = $report1 + $report2 + $report3;

                            $totalReportStyleCount = $report1 + $report2 + $report3;
                        ?>
                        @if($totalReportStyleCount > 110)
                        <!-- laporan pekerjaan -->
                            @if(isset($dataReportImage) && isset($dataReportImage->approved_by_pm_at))
                                <article class="timeline-item{{ $reportImagesStyle == 2 ? '' : ' alt' }}">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow{{ $reportImagesStyle == 1 ? '' : ' alt' }}"></span>
                                                <span class="timeline-icon bg-warning"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-warning">{{ date('l, d F Y', strtotime($dataReportImage->approved_by_pm_at)) }}</h4>
                                                <p class="timeline-date text-muted"><small>{{ date('H:s a', strtotime($dataReportImage->approved_by_pm_at)) }}</small></p>
                                                <p>Laporan pekerjaan <span class="text-success">disetujui</span>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endif
                            @if(isset($dataReportImage))
                                <article class="timeline-item{{ $reportImagesStyle == 1 ? '' : ' alt' }}">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow{{ $reportImagesStyle == 2 ? '' : ' alt' }}"></span>
                                                <span class="timeline-icon bg-pink"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-pink">{{ date('l, d F Y', strtotime($dataReportImage->submitted_at)) }}</h4>
                                                <p class="timeline-date text-muted"><small>{{ date('H:s a', strtotime($dataReportImage->submitted_at)) }}</small></p>
                                                <p>Laporan pekerjaan diajukan.</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endif
                        <!-- laporan pekerjaan -->

                        <!-- laporan expenses -->
                            @if(isset($dataExpense) && isset($dataExpense->approved_by_pm_at))
                                <article class="timeline-item{{ $reportExpensesStyle % 2 == 1 ? ' alt' : '' }}">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow{{ $reportExpensesStyle % 2 == 1 ? ' alt' : '' }}"></span>
                                                <span class="timeline-icon bg-primary"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-primary">{{ date('l, d F Y', strtotime($dataExpense->approved_by_pm_at)) }}</h4>
                                                <p class="timeline-date text-muted"><small>{{ date('H:s a', strtotime($dataExpense->approved_by_pm_at)) }}</small></p>
                                                <p>Laporan pengeluaran <span class="text-success">disetujui</span>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endif
                            @if(isset($dataExpense))
                                <article class="timeline-item{{ $reportExpensesStyle % 2 == 0 ? ' alt' : '' }}">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow{{ $reportExpensesStyle % 2 == 0 ? ' alt' : '' }}"></span>
                                                <span class="timeline-icon bg-success"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-success">{{ date('l, d F Y', strtotime($dataExpense->submitted_at)) }}</h4>
                                                <p class="timeline-date text-muted"><small>{{ date('H:s a', strtotime($dataExpense->submitted_at)) }}</small></p>
                                                <p>Laporan pengeluaran diajukan.</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endif
                        <!-- laporan expenses -->

                        <!-- laporan pengembalian alat -->
                            @if(isset($dataTool) && isset($dataTool->approved_at))
                                <article class="timeline-item{{ $reportToolsStyle % 2 == 1 ? ' alt' : '' }}">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow{{ $reportToolsStyle % 2 == 1 ? ' alt' : '' }}"></span>
                                                <span class="timeline-icon bg-danger"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-danger">{{ date('l, d F Y', strtotime($dataTool->approved_at)) }}</h4>
                                                <p class="timeline-date text-muted"><small>{{ date('H:s a', strtotime($dataTool->approved_at)) }}</small></p>
                                                <p>Laporan pengembalian alat <span class="text-success">disetujui</span>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endif
                            @if(isset($dataTool))
                                <article class="timeline-item{{ $reportToolsStyle % 2 == 0 ? ' alt' : '' }}">
                                    <div class="timeline-desk">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <span class="arrow{{ $reportToolsStyle % 2 == 0 ? ' alt' : '' }}"></span>
                                                <span class="timeline-icon bg-danger"><i class="mdi mdi-circle"></i></span>
                                                <h4 class="text-danger">{{ date('l, d F Y', strtotime($dataTool->submitted_at)) }}</h4>
                                                <p class="timeline-date text-muted"><small>{{ date('H:s a', strtotime($dataTool->submitted_at)) }}</small></p>
                                                <p>Laporan pengembalian alat diajukan.</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endif
                        <!-- laporan pengembalian alat -->
                        @else
                            <article class="timeline-item">
                                <div class="timeline-desk">
                                    <div class="panel">
                                        <div class="panel-body">
                                            <span class="arrow"></span>
                                            <span class="timeline-icon bg-pink"><i class="mdi mdi-circle"></i></span>
                                            <h4 class="text-pink">{{ date('l, d F Y') }}</h4>
                                            <p class="timeline-date text-muted"><small>{{ date('H:s a') }}</small></p>
                                            <p>Belum ada data</p>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endif
                    </div>
                </div>
            </div>
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
