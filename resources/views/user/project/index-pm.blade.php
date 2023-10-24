@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar projects';
    $formRouteDashboard = 'user-projects.dashboard';
    $formRouteCreate = 'user-projects.create';
    $formRouteShow = 'user-projects.show';
    $formRouteEdit = 'user-projects.edit';
    $formRouteDestroy = 'user-projects.destroy';
    $formRouteProgress = 'user-projects.progress';
    //pr
    $formRoutePrShow = 'user-pr.show';
    //input pict category
    $formRouteProjectTemplateCreate = 'user-projects-template.index';
    $formRouteProjectTemplateEdit = 'user-projects-template.edit';
    //approve image report
    $formRouteProjectImageEdit = 'user-projects-image.edit';
    $formRouteProjectImageShow = 'user-projects-image.show';
    //create report
    $formRouteProjectReportCreate = 'user-projects-report.create';
    $formRouteProjectReportShow = 'user-projects-report.show';
    //css setting
    $statusBadge    = array('dark','danger','warning','info','success','purple','pink');
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
    <div class="card-header text-center text-uppercase bb-orange">
        <strong>{{ ucfirst($pageTitle) }} (<span class="text-info">{{ $projectsCount }}</span>)</strong>
    </div>

    @if(isset($projects))
        <div class="bg-gray-lini-2">
            <div class="card-body">
                <div class="row">
                    @if (isset($projects) && Auth::user()->department_id == 1)
                    
                        <!-- pm -->
                        @if(Auth::user()->user_level == 3)
                            <?php $separator=1; ?>
                            @foreach($projects as $dataProject)
                                <div class="col-sm p-2">
                                    <div class="bg-card-box br-5 p-2">
                                        <div class="badge badge-{{ $statusBadge[$dataProject->status] }} float-right">{{ $dataProject->project_status_name }}</div>
                                        <span class="text-danger"><strong>{{ strtoupper($dataProject->name) }}</strong></span> <span class="text-info">({{ $dataProject->taskCount }} task)</span>

                                        @if($dataProject->pm_id != null)
                                            @foreach($users as $dataPM)
                                                @if($dataPM->id == $dataProject->pm_id)
                                                    <br><small>PM:</small> <span class="text-success">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) }}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <br><span class="text-danger small">Belum ada PM</span>
                                        @endif

                                        <br>Nilai: <span class="text-info">{{ $dataProject->amount != null ? number_format($dataProject->amount) : '-' }}</span> 
                                        <br>Mulai: <span class="text-info">{{ $dataProject->date != null ? date('l, d F Y',strtotime($dataProject->date)) : '-' }}</span> 

                                        <div>

                                            <a href="{{ route($formRouteShow, $dataProject->id) }}" class='btn btn-icon waves-effect waves-light btn-warning t-white mt-1 mb-1'> <i class='fas fa-eye' title='Show'></i> Show </a>

                                            <!-- progress -->
                                            <a href="{{ route($formRouteProgress, $dataProject->id) }}" class="btn btn-success mt-1 mb-1"><i class="fas fa-eye"></i> Log</a>
                                        </div>

                                    </div>
                                </div>
                                <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                                <?php $separator++; ?>
                            @endforeach
                            <div class="col-md">
                                {{ $projects->links() }}
                            </div>
                        @endif
                        <!-- pc, qc doc, qc expense -->
                        @if(Auth::user()->user_level == 2 || Auth::user()->user_level == 4 || Auth::user()->user_level == 5 || Auth::user()->user_level == 6)
                            <?php $separator1=1; ?>
                            @foreach($projects as $data)
                                <div class="col-md p-2">
                                    <div class="bg-card-box br-5 p-2">
                                        @foreach($dataProjectStatus as $projectStatus)
                                            @if($projectStatus->id == $data->project_status)
                                                <div class="badge badge-{{ $statusBadge[$data->project_status] }} float-right">{{ $projectStatus->name }}</div>
                                            @endif
                                        @endforeach
                                        <span class="text-danger"><strong>{{ strtoupper($data->project_name) }}</strong></span>
                                        <br><small>Task:</small> <span class="text-info">{{ ucwords($data->name) }}</span>

                                        @if($data->pm_id != null)
                                            @foreach($users as $dataPM)
                                                @if($dataPM->id == $data->pm_id)
                                                    <br><small>PM:</small> <span class="text-success">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) }}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <br><span class="text-danger small">Belum ada PM</span>
                                        @endif

                                        @if($data->pc_id != null)
                                            @foreach($users as $dataPC)
                                                @if($dataPC->id == $data->pc_id)
                                                    <br><small>PC:</small> <span class="text-success">{{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname) }}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <br><span class="text-danger small">Belum ada PC</span>
                                        @endif
                                        
                                        @if($data->qce_id != null)
                                            @foreach($users as $dataQCE)
                                                @if($dataQCE->id == $data->qce_id)
                                                    <br><small>QCD:</small> <span class="text-success">{{ ucwords($dataQCE->firstname).' '.ucwords($dataQCE->lastname) }}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <br><span class="text-danger small">Belum ada QC Expenses</span>
                                        @endif
                                    
                                        @if($data->qcd_id != null)
                                            @foreach($users as $dataQCE)
                                                @if($dataQCE->id == $data->qcd_id)
                                                    <br><small>QCE:</small> <span class="text-success">{{ ucwords($dataQCE->firstname).' '.ucwords($dataQCE->lastname) }}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <br><span class="text-danger small">Belum ada QC Expense</span>
                                        @endif

                                        @if($data->qct_id != null)
                                            @foreach($users as $dataQCT)
                                                @if($dataQCT->id == $data->qct_id)
                                                    <br><small>QCT:</small> <span class="text-success">{{ ucwords($dataQCT->firstname).' '.ucwords($dataQCT->lastname) }}</span>
                                                @endif
                                            @endforeach
                                        @else
                                            <br><span class="text-danger small">Belum ada QC Tools</span>
                                        @endif
                                        
                                        <div>
                                            <a href="{{ route($formRouteShow, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-warning t-white mt-1 mb-1'> <i class='fas fa-eye' title='Show'></i> Show </a>

                                            <!-- progress -->
                                            <a href="{{ route($formRouteProgress, $data->id) }}" class="btn btn-success mt-1 mb-1"><i class="fas fa-eye"></i> Log</a>
                                        </div>
                                    </div>
                                </div>
                                <?php if($separator1 % 2 == 0){echo "<div class='w-100'></div>";} ?>
                                <?php $separator1++; ?>
                            @endforeach
                        @endif

                    </div>
                @endif
            </div>
        </div>
        @if($projects->total() > 10)
            <div class="card-body">
                {{ $projects->links() }}
            </div>
        @endif
    @else
        <div class="bg-gray-lini-2">
            <div class="card-body">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>
        </div>
    @endif

</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable(
            //"order":[]
        );
    } );
</script>
@endsection
