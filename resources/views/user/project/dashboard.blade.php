@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar semua proyek';
    $statusBadge    = array('dark','info','success','danger','purple','pink','warning');
    //form link
    $formRouteIndex = 'user-projects.index';
    $formRouteShow = 'user-projects.show';
    $formRouteEdit = 'user-projects.edit';
?>
@endsection

@section('content')
<div class="flash-message">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<div class="card">
    <div class="card-header text-center">{{ ucfirst($pageTitle) }}</div>

    <div class="card-body">
        <!-- Start Content-->
        <div class="col-md text-center mt-3 mb-3">
            <!-- progressbar -->
            <?php $i=0; $i2=0; $i3=0; $i4=0; ?>
            @foreach($projects as $data)
                <?php 
                    //default data
                    $newCount1 = $data->newCount;
                    $onpreparationCount1 = $data->onpreparationCount;
                    $onprogressCount1 = $data->onprogressCount;
                    $reportingCount1 = $data->reportingCount;
                    $finishedCount1 = $data->finishedCount;
                    
                    $totalStatus1 = $newCount1 + $onpreparationCount1 + $onprogressCount1 * 2 + $reportingCount1 * 3 + $finishedCount1 * 4;
                    $max1 = $data->taskCount * 4;
                    if ($max1 < 1) { $max1 = 1; }

                    $currentStatus = round($totalStatus1/$max1 * 100);
                ?>

                @if($currentStatus <= 25)
                    <?php $i++; ?>
                @elseif($currentStatus > 25 && $currentStatus <= 50)
                    <?php $i2++; ?>
                @elseif($currentStatus > 50 && $currentStatus <= 75)
                    <?php $i3++; ?>
                @else
                    <?php $i4++; ?>
                @endif
                <?php 
                    $newCount = $i;
                    $onprogressCount = $i2;
                    $reportingCount = $i3;
                    $finishedCount = $i4;
                ?>
            @endforeach
            <ul id="progressbar">

                <li class="{{ $newCount > 0 ? 'active ' : '' }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-magic progress-icon"></i>
                    </div>
                    <div>
                        <strong>Persiapan</strong>
                        <br><h3 class="text-muted">{{ $newCount }}</h3>
                    </div>
                </li>
                <li class="{{ $onprogressCount > 0 ? 'active ' : '' }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-wrench progress-icon"></i>
                    </div>
                    <div class="progress-icon-box-1">
                    </div>
                    <div>
                        <strong>Dalam pengerjaan</strong>
                        <br><h3 class="text-muted">{{ $onprogressCount }}</h3>
                    </div>
                </li>
                <li class="{{ $reportingCount > 0 ? 'active ' : '' }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-file-alt progress-icon"></i>
                    </div>
                    <div>
                        <strong>Penyiapan laporan</strong>
                        <br><h3 class="text-muted">{{ $reportingCount }}</h3>
                    </div>
                </li>
                <li class="{{ $finishedCount > 0 ? 'active ' : '' }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-lock progress-icon"></i>
                    </div>
                    <div>
                        <strong>Selesai</strong>
                        <br><h3 class="text-muted">{{ $finishedCount }}</h3>
                    </div>
                </li>

            </ul>
        </div>
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="" class="display table dt-responsive nowrap">
                        <tbody>
                                <tr>
                                    <td width="25%">
                                        @foreach($projects as $data)
                                            <?php 
                                                //default data
                                                $newCount1 = $data->newCount;
                                                $onpreparationCount1 = $data->onpreparationCount;
                                                $onprogressCount1 = $data->onprogressCount;
                                                $reportingCount1 = $data->reportingCount;
                                                $finishedCount1 = $data->finishedCount;
                                                
                                                $totalStatus1 = $newCount1 + $onpreparationCount1 + $onprogressCount1 * 2 + $reportingCount1 * 3 + $finishedCount1 * 4;
                                                $max1 = $data->taskCount * 4;
                                                if ($max1 < 1) { $max1 = 1; }

                                                $currentStatus = round($totalStatus1/$max1 * 100);
                                            ?>

                                            @if($currentStatus <= 25)
                                                <div class="alert alert-warning p-1">{{ ucwords($data->name) }}
                                                <?php
                                                    if ($currentStatus > 75 && $currentStatus <= 100) {
                                                        $badge = 'success';
                                                    }elseif($currentStatus > 50 && $currentStatus <= 75){
                                                        $badge = 'info';
                                                    }elseif($currentStatus > 25 && $currentStatus <= 50){
                                                        $badge = 'pink';
                                                    }else{
                                                        $badge = 'danger';
                                                    }
                                                ?>
                                                <span class="badge badge-{{ $badge }}">{{ $currentStatus }}%</span>
                                                <br> <a href="{{ route($formRouteShow, $data->id) }}" class="text-success">Show</a>
                                                <a href="{{ route($formRouteEdit, $data->id) }}" class="text-success">Edit</a></div>
                                            @endif
                                            <?php $totalNewCount = $newCount1; $totalOnpreparationCount = $onpreparationCount1; ?>
                                        @endforeach
                                    </td>
                                    <td width="25%">
                                        @foreach($projects as $data)
                                            <?php 
                                                //default data
                                                $newCount1 = $data->newCount;
                                                $onpreparationCount1 = $data->onpreparationCount;
                                                $onprogressCount1 = $data->onprogressCount;
                                                $reportingCount1 = $data->reportingCount;
                                                $finishedCount1 = $data->finishedCount;
                                                
                                                $totalStatus1 = $newCount1 + $onpreparationCount1 + $onprogressCount1 * 2 + $reportingCount1 * 3 + $finishedCount1 * 4;
                                                $max1 = $data->taskCount * 4;
                                                if ($max1 < 1) { $max1 = 1; }

                                                $currentStatus = round($totalStatus1/$max1 * 100);
                                            ?>

                                            @if($currentStatus > 25 && $currentStatus <= 50)
                                                <div class="alert alert-warning p-1">{{ ucwords($data->name) }}
                                                <?php
                                                    if ($currentStatus > 75 && $currentStatus <= 100) {
                                                        $badge = 'success';
                                                    }elseif($currentStatus > 50 && $currentStatus <= 75){
                                                        $badge = 'info';
                                                    }elseif($currentStatus > 25 && $currentStatus <= 50){
                                                        $badge = 'pink';
                                                    }else{
                                                        $badge = 'danger';
                                                    }
                                                ?>
                                                <span class="badge badge-{{ $badge }}">{{ $currentStatus }}%</span>
                                                <br> <a href="{{ route($formRouteShow, $data->id) }}" class="text-success">Show</a>
                                                <a href="{{ route($formRouteEdit, $data->id) }}" class="text-success">Edit</a></div>
                                            @endif
                                            <?php $totalOnprogressCount = $onprogressCount1; ?>
                                        @endforeach
                                    </td>
                                    <td width="25%">
                                        @foreach($projects as $data)
                                            <?php 
                                                //default data
                                                $newCount1 = $data->newCount;
                                                $onpreparationCount1 = $data->onpreparationCount;
                                                $onprogressCount1 = $data->onprogressCount;
                                                $reportingCount1 = $data->reportingCount;
                                                $finishedCount1 = $data->finishedCount;
                                                
                                                $totalStatus1 = $newCount1 + $onpreparationCount1 + $onprogressCount1 * 2 + $reportingCount1 * 3 + $finishedCount1 * 4;
                                                $max1 = $data->taskCount * 4;
                                                if ($max1 < 1) { $max1 = 1; }

                                                $currentStatus = round($totalStatus1/$max1 * 100);
                                            ?>

                                            @if($currentStatus > 50 && $currentStatus <= 75)
                                                <div class="alert alert-warning p-1">{{ ucwords($data->name) }}
                                                <?php
                                                    if ($currentStatus > 75 && $currentStatus <= 100) {
                                                        $badge = 'success';
                                                    }elseif($currentStatus > 50 && $currentStatus <= 75){
                                                        $badge = 'info';
                                                    }elseif($currentStatus > 25 && $currentStatus <= 50){
                                                        $badge = 'pink';
                                                    }else{
                                                        $badge = 'danger';
                                                    }
                                                ?>
                                                <span class="badge badge-{{ $badge }}">{{ $currentStatus }}%</span>
                                                <br> <a href="{{ route($formRouteShow, $data->id) }}" class="text-success">Show</a>
                                                <a href="{{ route($formRouteEdit, $data->id) }}" class="text-success">Edit</a></div>
                                            @endif
                                            <?php $totalReportingCount = $reportingCount1; ?>
                                        @endforeach
                                    </td>
                                    <td width="25%">
                                        @foreach($projects as $data)
                                            <?php 
                                                //default data
                                                $newCount1 = $data->newCount;
                                                $onpreparationCount1 = $data->onpreparationCount;
                                                $onprogressCount1 = $data->onprogressCount;
                                                $reportingCount1 = $data->reportingCount;
                                                $finishedCount1 = $data->finishedCount;
                                                
                                                $totalStatus1 = $newCount1 + $onpreparationCount1 + $onprogressCount1 * 2 + $reportingCount1 * 3 + $finishedCount1 * 4;
                                                $max1 = $data->taskCount * 4;
                                                if ($max1 < 1) { $max1 = 1; }

                                                $currentStatus = round($totalStatus1/$max1 * 100);
                                            ?>

                                            @if($currentStatus > 75 && $currentStatus <= 100)
                                                <div class="alert alert-warning p-1">{{ ucwords($data->name) }}
                                                <?php
                                                    if ($currentStatus > 75 && $currentStatus <= 100) {
                                                        $badge = 'success';
                                                    }elseif($currentStatus > 50 && $currentStatus <= 75){
                                                        $badge = 'info';
                                                    }elseif($currentStatus > 25 && $currentStatus <= 50){
                                                        $badge = 'pink';
                                                    }else{
                                                        $badge = 'danger';
                                                    }
                                                ?>
                                                <span class="badge badge-{{ $badge }}">{{ $currentStatus }}%</span>
                                                <br> <a href="{{ route($formRouteShow, $data->id) }}" class="text-success">Show</a>
                                                <a href="{{ route($formRouteEdit, $data->id) }}" class="text-success">Edit</a></div>
                                            @endif
                                            <?php $totalFinishedCount = $finishedCount1; ?>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td width="25%">
                                        @if($totalNewCount > 0 || $totalOnpreparationCount > 0)
                                            <div class="text-center"><a href="{{ route($formRouteIndex, 'status=1') }}" class="btn btn-info"><i class="fas fa-eye"></i> Lihat semua</a></div>
                                        @endif
                                    </td>
                                    <td width="25%">
                                        @if($totalOnprogressCount > 0)
                                            <div class="text-center"><a href="{{ route($formRouteIndex, 'status=2') }}" class="btn btn-info"><i class="fas fa-eye"></i> Lihat semua</a></div>
                                        @endif
                                    </td>
                                    <td width="25%">
                                        @if($totalReportingCount > 0)
                                            <div class="text-center"><a href="{{ route($formRouteIndex, 'status=3') }}" class="btn btn-info"><i class="fas fa-eye"></i> Lihat semua</a></div>
                                        @endif
                                    </td>
                                    <td width="25%">
                                        @if($totalFinishedCount > 0)
                                            <div class="text-center"><a href="{{ route($formRouteIndex, 'status=4') }}" class="btn btn-info"><i class="fas fa-eye"></i> Lihat semua</a></div>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
