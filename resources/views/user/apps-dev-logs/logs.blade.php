@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Log pembuatan aplikasi';
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $statusAppsBadge    = array('','info','purple','danger','success');
    //route
    $formRouteIndex = 'user-apps-dev-logs.index';
    $formRouteCreate = 'user-apps-dev-logs.create';
    $formRouteEdit = 'user-apps-dev-logs.edit';
    $formRouteDestroy = 'user-apps-dev-logs.destroy';
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
    @foreach($appsStatusDatas as $appsStatusData)
    <?php 
        #default
        $noStatus = 0;
        $onprogress = 1;
        $postpone = 2;
        $cancelled = 3;
        $done = 4;

        $noStatusCSS = '';
        $onprogressCSS = '';
        $postponeCSS = '';
        $cancelledCSS = '';
        $doneCSS = '';

        $noStatusACSS = 'disabled';
        $onprogressACSS = 'disabled';
        $postponeACSS = 'disabled';
        $cancelledACSS = 'disabled';
        $doneACSS = 'disabled';

        $progressValue = 0;

        $noStatusCount = $appsStatusData->noStatusCount;
        $onprogressCount = $appsStatusData->onProgressCount;
        $postponeCount = $appsStatusData->postponeCount;
        $cancelledCount = $appsStatusData->cancelledCount;
        $doneCount = $appsStatusData->doneCount;

        if ($noStatusCount > 0) {
            $noStatusCSS = 'active ';
            $noStatusButtonCSS = 'active ';
            $noStatusACSS = '';
        }
        if ($onprogressCount > 0) {
            $onprogressCSS = 'active ';
            $onprogressACSS = '';
        }
        if ($postponeCount > 0) {
            $postponeCSS = 'active ';
            $postponeACSS = '';
        }
        if ($cancelledCount > 0) {
            $cancelledCSS = 'active ';
            $cancelledACSS = '';
        }
        if ($doneCount > 0) {
            $doneCSS = 'active ';
            $doneACSS = '';
        }

        #percentage
        $progressMax = ($onprogressCount);
        if ($progressMax == 0) {
            $progressMax = 1;
        }
        $totalProgressedTask = $onprogressCount;
        #set conditions and value 
        if (isset($totalProgressedTask)) {
            $progressValue = $totalProgressedTask;
        }

        //total task
        $totalTask = $onprogressCount + $doneCount;
        if ($totalTask == 0) {
            $totalTask = 1;
        }
    ?>
    @endforeach

    <div class="card-header text-center bb-orange">
        <div class="text-uppercase">
            <strong>{{ ucfirst($pageTitle) }}</strong>
            <a href="{{ route ($formRouteIndex,'skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
            <!-- IT department -->
            @if(Auth::user()->department_id == 5)
                <br>
                <select name="did" class="form-control select2 text-uppercase" style="display:inline-block; text-align-last:center" onchange="location = this.value;">
                    @if (isset($requestProgrammer) || isset($requestDepartment) || isset($requestStatus))
                        @if(isset($requestProgrammer))
                            @if(isset($requestStatus))
                                @foreach ($departmensDatas as $dataOne)
                                    @if ($dataOne->id == $requestDepartment)
                                        <option value="{{ route($formRouteIndex,'did='.$dataOne->id.'&sid='.$requestStatus.'&pid='.$requestProgrammer) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                    @endif
                                @endforeach
                                @foreach ($departmensDatas as $dataOne)
                                    @if ($dataOne->id != $requestDepartment)
                                        <option value="{{ route($formRouteIndex,'did='.$dataOne->id.'&sid='.$requestStatus.'&pid='.$requestProgrammer) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                    @endif
                                @endforeach
                            @elseif(isset($requestDepartment))
                                @foreach ($departmensDatas as $dataOne)
                                    @if ($dataOne->id == $requestDepartment)
                                        <option value="{{ route($formRouteIndex,'did='.$dataOne->id.'&pid='.$requestProgrammer) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                    @endif
                                @endforeach
                                @foreach ($departmensDatas as $dataOne)
                                    @if ($dataOne->id != $requestDepartment)
                                        <option value="{{ route($formRouteIndex,'did='.$dataOne->id.'&pid='.$requestProgrammer) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                    @endif
                                @endforeach
                            @else
                                @foreach ($departmensDatas as $dataOne)
                                    @if ($dataOne->id == $requestDepartment)
                                        <option value="{{ route($formRouteIndex,'did='.$dataOne->id.'&pid='.$requestProgrammer) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                    @endif
                                @endforeach
                                @foreach ($departmensDatas as $dataOne)
                                    @if ($dataOne->id != $requestDepartment)
                                        <option value="{{ route($formRouteIndex,'did='.$dataOne->id.'&sid='.$requestStatus.'&pid='.$requestProgrammer) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                    @endif
                                @endforeach
                            @endif
                        @elseif(isset($requestStatus))
                            @if(isset($requestProgrammer))
                                @foreach ($departmensDatas as $dataOne)
                                    @if ($dataOne->id == $requestDepartment)
                                        <option value="{{ route($formRouteIndex,'did='.$dataOne->id.'&sid='.$requestStatus.'&pid='.$requestProgrammer) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                    @endif
                                @endforeach
                                @foreach ($departmensDatas as $dataOne)
                                    @if ($dataOne->id != $requestDepartment)
                                        <option value="{{ route($formRouteIndex,'did='.$dataOne->id.'&sid='.$requestStatus.'&pid='.$requestProgrammer) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                    @endif
                                @endforeach
                            @elseif(isset($requestDepartment))
                                @foreach ($departmensDatas as $dataOne)
                                    @if ($dataOne->id == $requestDepartment)
                                        <option value="{{ route($formRouteIndex,'did='.$dataOne->id.'&sid='.$requestStatus) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                    @endif
                                @endforeach
                                @foreach ($departmensDatas as $dataOne)
                                    @if ($dataOne->id != $requestDepartment)
                                        <option value="{{ route($formRouteIndex,'did='.$dataOne->id.'&sid='.$requestStatus) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                    @endif
                                @endforeach
                            @else
                                <option value="0">Pilih department</option>
                                @foreach ($departmensDatas as $dataOne)
                                    @if ($dataOne->id == $requestDepartment)
                                        <option value="{{ route($formRouteIndex,'did='.$dataOne->id.'&sid='.$requestStatus) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                    @endif
                                @endforeach
                                @foreach ($departmensDatas as $dataOne)
                                    @if ($dataOne->id != $requestDepartment)
                                        <option value="{{ route($formRouteIndex,'did='.$dataOne->id.'&sid='.$requestStatus) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                    @endif
                                @endforeach
                            @endif
                        @else
                            @foreach ($departmensDatas as $dataOne)
                                @if ($dataOne->id == $requestDepartment)
                                    <option value="{{ route($formRouteIndex,'did='.$dataOne->id) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                @endif
                            @endforeach
                            @foreach ($departmensDatas as $dataOne)
                                @if ($dataOne->id != $requestDepartment)
                                    <option value="{{ route($formRouteIndex,'did='.$dataOne->id) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                                @endif
                            @endforeach
                        @endif
                    @else
                        <option value="0">Pilih department</option>
                        @foreach ($departmensDatas as $dataOne)
                            <option value="{{ route($formRouteIndex,'did='.$dataOne->id) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                        @endforeach
                    @endif
                </select>
            @else
                @if (isset($requestDepartment))
                    @foreach ($departmensDatas as $dataOne)
                        @if ($dataOne->id == old('did'))
                            <option value="{{ route($formRouteIndex,'did='.$dataOne->id) }}">{{ ucwords(strtolower($dataOne->name)) }}</option>
                        @endif
                    @endforeach
                @endif
            @endif
            <!-- IT department end -->
            <!-- all department -->
                <br>
                <select name="pid" class="form-control select2 text-uppercase small" style="display:inline-block; text-align-last:center" onchange="location = this.value;">
                    @if (isset($requestProgrammer) || isset($requestDepartment) || isset($requestStatus))
                        @if(isset($requestDepartment))
                            @if(isset($requestStatus))
                                @if(isset($requestProgrammer))
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id == $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id != $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="0">Pilih programmer</option>
                                    @foreach ($programmersDatas as $data2)
                                        <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                    @endforeach
                                @endif
                            @elseif(isset($requestProgrammer))
                                @if(isset($requestStatus))
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id == $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id != $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id == $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id != $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            @else
                                <option value="0">Pilih programmer</option>
                                @foreach ($programmersDatas as $data2)
                                    <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                @endforeach
                            @endif
                        @elseif(isset($requestStatus))
                            @if(isset($requestDepartment))
                                @if(isset($requestProgrammer))
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id == $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id != $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="0">Pilih programmer</option>
                                    @foreach ($programmersDatas as $data2)
                                        <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                    @endforeach
                                @endif
                            @elseif(isset($requestProgrammer))
                                @if(isset($requestDepartment))
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id == $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id != $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id == $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id != $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            @else
                                <option value="0">Pilih programmer</option>
                                @foreach ($programmersDatas as $data2)
                                    <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                @endforeach
                            @endif
                        @else
                            @if(isset($requestDepartment))
                                @if(isset($requestStatus))
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id == $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id != $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id == $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id != $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            @elseif(isset($requestStatus))
                                @if(isset($requestDepartment))
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id == $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id != $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id == $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                    @foreach ($programmersDatas as $data2)
                                        @if ($data2->id != $requestProgrammer)
                                            <option value="{{ route($formRouteIndex,'sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            @else
                                @foreach ($programmersDatas as $data2)
                                    @if ($data2->id == $requestProgrammer)
                                        <option value="{{ route($formRouteIndex,'sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                    @endif
                                @endforeach
                                @foreach ($programmersDatas as $data2)
                                    @if ($data2->id != $requestProgrammer)
                                        <option value="{{ route($formRouteIndex,'sid='.$requestStatus.'&pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                                    @endif
                                @endforeach
                            @endif
                        @endif
                    @else
                        <option value="0">Pilih programmer</option>
                        @foreach ($programmersDatas as $data2)
                            <option value="{{ route($formRouteIndex,'pid='.$data2->id) }}">{{ ucwords($data2->firstname).' '.ucwords($data2->lastname) }}</option>
                        @endforeach
                    @endif
                </select>
            <!-- all department -->
            <!-- IT department -->
            @if(Auth::user()->department_id == 5)
                @if (isset($requestDepartment))
                    <a href="{{ route($formRouteCreate,'did='.$requestDepartment) }}" class="btn btn-orange mt-2"><i class="fa fa-plus"></i> Tambah log</a>
                @else
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange mt-2"><i class="fa fa-plus"></i> Tambah log</a>
                @endif
            @endif
            <!-- IT department end -->
            <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini mt-2"><i class="fa fa-redo-alt"></i></a>
        </div>
        <?php /*
        <br><small>Mulai:</small> <strong><span class="text-info">{{ $project->date != null ? date('l, d F Y', strtotime($project->date)) : '-' }}</span></strong>
        */ ?>
    </div>

    <div class="card-body">
        <!-- Start Content-->
        <div class="col-md text-center mt-3 mb-3">
            <!-- progressbar -->
            <ul id="progressbar" class="progressbar-it">
                <li class="{{ $noStatusCSS }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fas fa-tired progress-icon"></i>
                    </div>
                    <div class="progress-it-title">
                        @if (isset($requestProgrammer) || isset($requestDepartment))
                            @if(isset($requestProgrammer))
                                @if(isset($requestDepartment))
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$noStatus.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>No Status</strong></a>
                                @else
                                    <a href="{{ route($formRouteIndex,'sid='.$noStatus.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>No Status</strong></a>
                                @endif
                            @elseif(isset($requestDepartment))
                                @if(isset($requestProgrammer))
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$noStatus.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>No Status</strong></a>
                                @else
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$noStatus) }}" class="{{ $noStatusACSS }}"><strong>No Status</strong></a>
                                @endif
                            @else
                                <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$noStatus) }}" class="{{ $noStatusACSS }}"><strong>No Status</strong></a>
                            @endif
                        @else
                            <a href="{{ route($formRouteIndex,'sid='.$noStatus) }}" class="{{ $noStatusACSS }}"><strong>No Status</strong></a>
                        @endif
                    </div>
                    <span class="progress-it-title-bottom">({{ $noStatusCount }}) <span class="progress-it-title">task</span></span>
                </li>
                <li class="{{ $onprogressCSS }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fas fa-grin-squint-tears progress-icon"></i>
                    </div>
                    <div class="progress-it-title">
                        @if (isset($requestProgrammer) || isset($requestDepartment))
                            @if(isset($requestProgrammer))
                                @if(isset($requestDepartment))
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$onprogress.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>On Progress</strong></a>
                                @else
                                    <a href="{{ route($formRouteIndex,'sid='.$onprogress.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>On Progress</strong></a>
                                @endif
                            @elseif(isset($requestDepartment))
                                @if(isset($requestProgrammer))
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$onprogress.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>On Progress</strong></a>
                                @else
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$onprogress) }}" class="{{ $noStatusACSS }}"><strong>On Progress</strong></a>
                                @endif
                            @else
                                <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$onprogress) }}" class="{{ $noStatusACSS }}"><strong>On Progress</strong></a>
                            @endif
                        @else
                            <a href="{{ route($formRouteIndex,'sid='.$onprogress) }}" class="{{ $noStatusACSS }}"><strong>On Progress</strong></a>
                        @endif
                    </div>
                    <span class="progress-it-title-bottom">({{ $onprogressCount }}) <span class="progress-it-title">task</span></span>
                </li>
                <li class="{{ $postponeCSS }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fas fa-grin-tongue progress-icon"></i>
                    </div>
                    <div class="progress-icon-box-1">
                    </div>
                    <div class="progress-it-title">
                        @if (isset($requestProgrammer) || isset($requestDepartment))
                            @if(isset($requestProgrammer))
                                @if(isset($requestDepartment))
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$postpone.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>Postpone</strong></a>
                                @else
                                    <a href="{{ route($formRouteIndex,'sid='.$postpone.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>Postpone</strong></a>
                                @endif
                            @elseif(isset($requestDepartment))
                                @if(isset($requestProgrammer))
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$postpone.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>Postpone</strong></a>
                                @else
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$postpone) }}" class="{{ $noStatusACSS }}"><strong>Postpone</strong></a>
                                @endif
                            @else
                                <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$postpone) }}" class="{{ $noStatusACSS }}"><strong>Postpone</strong></a>
                            @endif
                        @else
                            <a href="{{ route($formRouteIndex,'sid='.$postpone) }}" class="{{ $noStatusACSS }}"><strong>Postpone</strong></a>
                        @endif
                    </div>
                    <span class="progress-it-title-bottom">({{ $postponeCount }}) <span class="progress-it-title">task</span></span>
                </li>
                <li class="{{ $cancelledCSS }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fas fa-grimace progress-icon"></i>
                    </div>
                    <div class="progress-it-title">
                        @if (isset($requestProgrammer) || isset($requestDepartment))
                            @if(isset($requestProgrammer))
                                @if(isset($requestDepartment))
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$cancelled.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>Cancelled</strong></a>
                                @else
                                    <a href="{{ route($formRouteIndex,'sid='.$cancelled.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>Cancelled</strong></a>
                                @endif
                            @elseif(isset($requestDepartment))
                                @if(isset($requestProgrammer))
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$cancelled.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>Cancelled</strong></a>
                                @else
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$cancelled) }}" class="{{ $noStatusACSS }}"><strong>Cancelled</strong></a>
                                @endif
                            @else
                                <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$cancelled) }}" class="{{ $noStatusACSS }}"><strong>Cancelled</strong></a>
                            @endif
                        @else
                            <a href="{{ route($formRouteIndex,'sid='.$cancelled) }}" class="{{ $noStatusACSS }}"><strong>Cancelled</strong></a>
                        @endif
                    </div>
                    <span class="progress-it-title-bottom">({{ $cancelledCount }}) <span class="progress-it-title">task</span></span>
                </li>
                <li class="{{ $doneCSS }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fas fa-grin-stars progress-icon"></i>
                    </div>
                    <div class="progress-it-title">
                        @if (isset($requestProgrammer) || isset($requestDepartment))
                            @if(isset($requestProgrammer))
                                @if(isset($requestDepartment))
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$done.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>Done</strong></a>
                                @else
                                    <a href="{{ route($formRouteIndex,'sid='.$done.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>Done</strong></a>
                                @endif
                            @elseif(isset($requestDepartment))
                                @if(isset($requestProgrammer))
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$done.'&pid='.$requestProgrammer) }}" class="{{ $noStatusACSS }}"><strong>Done</strong></a>
                                @else
                                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$done) }}" class="{{ $noStatusACSS }}"><strong>Done</strong></a>
                                @endif
                            @else
                                <a href="{{ route($formRouteIndex,'did='.$requestDepartment.'&sid='.$done) }}" class="{{ $noStatusACSS }}"><strong>Done</strong></a>
                            @endif
                        @else
                            <a href="{{ route($formRouteIndex,'sid='.$done) }}" class="{{ $noStatusACSS }}"><strong>Done</strong></a>
                        @endif
                    </div>
                    <span class="progress-it-title-bottom">({{ $doneCount }}) <span class="progress-it-title">task</span></span>
                </li>
            </ul>
            <div class="progress mb-3">
                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:{{ ($doneCount/$totalTask) * 100 }}%">
                    {{ ($doneCount/$totalTask) * 100 }}% Selesai (success)
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
                        <?php $i = 1; $ib = 1;?>
                        @if(count($appsDevLogsDatas) > 0)
                        <?php $ia = 1; ?>
                            @foreach($appsDevLogsDatas as $dataLog)
                                <?php 
                                    if($ib == 7){ $ib = 1;}
                                    if ($i % 2 == 1) {
                                        $css = '';
                                        $cssArrow = '';
                                        $cssFloat = 'right';
                                        $cssButton = 'left';
                                    }else{
                                        $css = ' alt';
                                        $cssArrow = '-alt';
                                        $cssFloat = 'left';
                                        $cssButton = 'right';
                                    }
                                ?>
                                <article class="timeline-item {{ $css }}">
                                    <div class="timeline-desk">
                                        <div class="panel{{ $dataLog->status == 0 ? ' bg-gray-op15' : '' }}">
                                            <div class="panel-body">
                                                <span class="arrow{{ $cssArrow }}"></span>
                                                <?php
                                                    if ($dataLog->percentage <= 10) {
                                                        $badge = 'danger';
                                                    }elseif($dataLog->percentage <= 35){
                                                        $badge = 'warning';
                                                    }elseif($dataLog->percentage <= 85){
                                                        $badge = 'info';
                                                    }else{
                                                        $badge = 'success';
                                                    }
                                                ?>
                                                <span class="float-{{ $cssFloat }} text-{{ $cssFloat }}">
                                                    @if ($dataLog->status == 4)
                                                        <span class="badge badge-success">
                                                            100<small>%</small>
                                                        </span>
                                                    @else
                                                        <span class="badge badge-{{ $badge }}">
                                                            {{ $dataLog->percentage }}<small>%</small>
                                                        </span>
                                                    @endif
                                                    <br>
                                                    <?php if($ia == 4){ $ia = 1;} ?>
                                                    @if ($dataLog->status > 0)
                                                        @foreach ($appsStatusDatas as $dataTwo)
                                                            @if ($dataTwo->id == $dataLog->status)
                                                                <span class="badge badge-{{ $statusAppsBadge[$dataLog->status] }}">
                                                                    {{ ucwords(strtolower($dataTwo->name)) }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </span>
                                                <span class="timeline-icon bg-{{ $statusBadge[$ib] }}"><i class="mdi mdi-circle"><span class="text-danger small" style="display:inline-block">{{ $i }}</span></i></span>
                                                
                                                <h4 class="text-{{ $statusBadge[$ib] }}">
                                                    {{ $dataLog->date ? date('l, d F Y', strtotime($dataLog->date)) : 'Tanggal tidak tersedia' }}
                                                    <!-- IT department -->
                                                    @if (isset($dataLog->department_id))
                                                        @foreach ($departmensDatas as $dataOne)
                                                            @if ($dataOne->id == $dataLog->department_id)
                                                                <span class="text-danger"><small>[{{ ucwords(strtolower($dataOne->name)) }}]</small></span>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </h4>
                                                <p>
                                                    <strong>{{ ucfirst($dataLog->name) }}</strong>
                                                    <!-- programmer -->
                                                    @if (isset($dataLog->programmer_id))
                                                        @foreach ($programmersDatas as $dataThree)
                                                            @if ($dataThree->id == $dataLog->programmer_id)
                                                                <br><small><span class="text-info">Programmer: {{ ucwords(strtolower($dataThree->firstname)).' '.ucwords(strtolower($dataThree->lastname)) }}</span></small>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                    <br>
                                                    <span class="timeline-date text-muted">
                                                        <small>Jam: {{ $dataLog->event_start ? date('H:i a', strtotime($dataLog->event_start)) : 'Jam tidak tersedia' }}</small> - <small>{{ $dataLog->event_end ? date('H:i a', strtotime($dataLog->event_end)) : 'Jam tidak tersedia' }}</small>

                                                        @if($dataLog->done_date != null)
                                                            <br><span class="text-danger"><small>Selesai: {{ date('l, d F Y', strtotime($dataLog->done_date)) }}</span></small>
                                                        @endif
                                                    </span>
                                                    <br>
                                                    @if($dataLog->note != null)
                                                        <span class="text-muted"><small>Note: {{ ucfirst($dataLog->note) }}</small></span>
                                                    @endif
                                                </p>
                                                <!-- IT department feature -->
                                                <div style="height:33px;">
                                                    @if(Auth::user()->department_id == 5)
                                                        <form action="{{ route($formRouteDestroy, $dataLog->id) }}" method="POST" class="float-{{ $cssButton }}" style="display:inline;">
                                                            @method('DELETE')
                                                            @csrf

                                                            <a href="{{ route($formRouteEdit, $dataLog->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i></a>

                                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                                <?php $i++; $ib++; $ia++; ?>
                            @endforeach
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
