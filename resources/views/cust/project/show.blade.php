@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'detail proyek';

    //back to dashboard
    $formRouteBack = 'cust-projects.dashboard';

    $formRouteIndex = 'cust-projects.index';
    $formRouteUpdate= 'cust-projects.update';

    //template
    $formImageReportEdit = 'cust-projects-image-report.edit';
    $formImageReportShow = 'cust-projects-image-report.show';

    //payment summary
    $formRoutePaymentSummaryIndex = 'cust-projects.dashboard';
    #$formRoutePaymentSummaryIndex = 'cust-project-payment-summary.index';
?>
@endsection

@section('content')
<div class="flash-message mt-2">
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

<div class="card mt-2">
    <div class="card-header text-center bb-orange">
        Nama proyek: <strong><span class="text-info">{{ strtoupper($project->name) }}</span></strong>
        <br>Kategori: <strong><span class="text-danger">{{ isset($project->procat_name) ? strtoupper($project->procat_name) : 'Belum dikategorikan' }}</span></strong>
    </div>
    <div class="card-body bg-gray-lini-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <!-- project -->
                        @if($userDepartment == 1)
                           
                            <!-- project data -->
                            <div id="project" class="col-md">
                                <?php
                                    #default
                                    $projectStatus = 0;
                                    $progressValue = 0;
                                    $progressMax   = $project->taskCount * 4;
                                    #taskcount
                                    $totalProgressedTask = $project->taskStatus0 * 0 + $project->taskStatus1 + $project->taskStatus2 * 2 + $project->taskStatus3 * 3 + $project->taskStatus4 * 4;
                                    #set conditions and value 
                                    if (isset($totalProgressedTask)) {
                                        $projectStatus = $totalProgressedTask;
                                        $progressValue = $projectStatus;
                                    }
                                ?>
                                <div class="progress mb-3">
                                    @if($progressMax < 1)
                                        <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ $progressValue }}" aria-valuemin="0" aria-valuemax="{{ $progressMax }}" style="width:0%">
                                        </div>
                                    @else
                                        <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ $progressValue }}" aria-valuemin="0" aria-valuemax="{{ $progressMax }}" style="width:{{ ($progressValue/$progressMax) * 100}}%">
                                            {{ ($progressValue/$progressMax) * 100}}% Selesai (success)
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md text-center">
                                    @if (count($projectTaskDatas) > 0)
                                        <small>Jumlah task: {{ $project->taskCount ?? '-' }}</small>
                                        | <small><span class="text-info">Selesai: {{ $project->taskStatus4 ?? '-' }} task</span></small>
                                        | <small><span class="text-success">Dalam pengerjaan: {{ $project->taskStatus0 + $project->taskStatus1 + $project->taskStatus2 + $project->taskStatus3 }} task</span></small>
                                    @endif
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label for="name">Nama <small class="c-red">*</small></label>
                                        <input type="text" class="form-control" name="name" value="{{ strtoupper($project->name) }}" data-parsley-minlength="3" readonly>
                                    </div>
                                    <div class="col-md mt-2 form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                                        <label for="number">Nomor <small class="c-red">*</small></label>
                                        <input type="text" class="form-control" name="number" value="{{ strtoupper($project->number) }}" data-parsley-minlength="3" readonly>
                                    </div>
                                    <div class="w-100"></div>
                                    <div class="col-md mt-2 form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                                        <label for="location">Lokasi </label>
                                        <input type="text" class="form-control" name="location" value="{{ old('location') ? old('location') : $project->location }}" data-parsley-minlength="3" readonly>
                                    </div>
                                    <div class="col-md mt-2 form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                        <label for="amount">Amount <small class="c-red">*</small></label>
                                        <input type="number" class="form-control" name="amount" value="{{ old('amount') ? old('amount') : $project->amount }}" readonly>
                                    </div>
                                    <div class="w-100"></div>
                                    <div class="col-md mt-2 form-group">
                                        <label for="">Project Manager</label>
                                        @if ($project->pm_id)
                                            @foreach($dataUsers as $dataPM)
                                                @if($dataPM->id == $project->pm_id)
                                                    <a href="#" class="form-control">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname)}}</a>
                                                @endif
                                            @endforeach
                                        @else
                                            <input class="form-control" value="Not available" readonly>
                                        @endif
                                    </div>

                                </div>
                            </div>
                            <!-- task data -->
                            <div class="w-100"><hr></div>
                            <div id="task" class="col-md">
                                @if (count($projectTaskDatas) > 0)
                                    <div class="row m-0">     
                                        <?php $i = 1; ?>
                                        @foreach ($projectTaskDatas as $data)
                                            <div class="col-md alert alert-warning mlr1-small-screen">
                                                {{ strtoupper($data->name) }}

                                                <?php
                                                    if ($data->status == 4) {
                                                        $badge = 'success';
                                                    }elseif($data->status <= 1){
                                                        $badge = 'danger';
                                                    }else{
                                                        $badge = 'info';
                                                    }
                                                ?>
                                                <span class="badge badge-{{ $badge }}">
                                                    @if($data->status == 0)
                                                        new
                                                    @else
                                                        @foreach($dataTaskStatus as $taskStatus)
                                                            @if($taskStatus->id == $data->status)
                                                                {{ $taskStatus->name }}
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </span>

                                                <!-- teams -->
                                                    @if($data->pc_id != null)
                                                        @foreach($dataUsers as $dataPC)
                                                            @if($dataPC->id == $data->pc_id)
                                                                <br><span class="text-success"><small>PC: {{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname) }}</small></span>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                
                                                    @if($data->qcd_id != null)
                                                        @foreach($dataUsers as $dataQCD)
                                                            @if($dataQCD->id == $data->qcd_id)
                                                                <br><span class="text-success"><small>QCD: {{ ucwords($dataQCD->firstname).' '.ucwords($dataQCD->lastname) }}</small></span>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                
                                                    @if($data->qce_id != null)
                                                        @foreach($dataUsers as $dataQCE)
                                                            @if($dataQCE->id == $data->qce_id)
                                                                <br><span class="text-success"><small>QCE:{{ ucwords($dataQCE->firstname).' '.ucwords($dataQCE->lastname) }}</small></span>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                
                                                    @if($data->tech_id != null)
                                                        @foreach($dataTechs as $dataTech)
                                                            @if($dataTech->id == $data->tech_id)
                                                                <br><span class="text-success"><small>{{ ucwords($dataTech->firstname).' '.ucwords($dataTech->lastname) }}</small></span>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                <!-- team end -->

                                                @if($data->date_start)
                                                    <br><small>Mulai: <span class="text-info">{{ date('l d F Y', strtotime($data->date_start)) }}</span></small>
                                                @endif
                                                @if($data->date_end)
                                                    <br><small>Selesai: <span class="text-info">{{ date('l d F Y', strtotime($data->date_end)) }}</span></small>
                                                @endif

                                                <?php 
                                                    if($data->reportImageCount){
                                                        $cssImageReport = '';
                                                    }else{
                                                        $cssImageReport = ' disabled';
                                                    }
                                                ?>
                                                <div class="row">
                                                    <div class="col-md">
                                                        <a href="{{ route($formImageReportEdit, $data->id.'.'.$project->id) }}" class='btn waves-effect waves-light btn-orange mt-1 mb-1 w-100{{ $cssImageReport }}'> <small><i class='fas fa-images'></i> Approve laporan gambar</small></a>
                                                    </div>
                                                    <div class="col-md">
                                                        <a href="{{ route($formImageReportEdit, $data->id.'.'.$project->id) }}" class='btn waves-effect waves-light btn-orange mt-1 mb-1 w-100{{ $cssImageReport }}'> <small><i class='fas fa-file-signature'></i> Approve laporan proyek</small></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                                if ($i % 3 == 0) {
                                                    echo "<div class='w-100'></div>";
                                                }
                                            ?>
                                            <?php $i++; ?>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                        <!-- project -->
                    </div>
                </div>
            </div>
        </div> <!-- container-fluid -->
    </div>
    <div class="card-body">
        <div class="row">
            <a href="{{ route($formRouteBack) }}" class="btn btn-blue-lini">Kembali</a>
        </div>
    </div>
</div> <!-- card -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable(
            // "order":[]
        );
    } );
</script>
@endsection
