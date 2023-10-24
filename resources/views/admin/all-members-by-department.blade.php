@extends('layouts.dashboard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Team members'; 
    $dashboardLink  = 'user.index';
    $department = Auth::user()->department_id;
    $statusBadge    = array('','dark','info','success','danger','purple','pink','warning');
    $formTroubleshootingShow = 'user.troubleshootingdetail';
?>
@endsection

@section('content')
<div class="flash-message mt-2">
    <!-- announcement -->
    @if(isset($basicRulesofConduct))
        <p class="alert alert-warning"><strong>Aturan dasar no.#{{$basicRulesofConduct->id}}</strong>:
            <br>{{ ucfirst($basicRulesofConduct->name) }} 
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
    @endif
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

<!-- teams -->
<div class="card">
    <div class="card-header">
        <span class="badge badge-danger float-left mr-1">{{ $dataTeams->total() <= 100 ? $dataTeams->total() + 1 : '>100' }}</span> 
        <strong>{{ strtoupper($dataAdmin->dept_name) }}</strong> team members
    </div>
    <div class="card-body bg-gray-lini-2">
        <div class="row">
            @if(isset($dataAdmin))
                <div class="col-xl-3 col-md-6">
                    <div class="card-box widget-user">
                        <div class="media">
                            <div class="avatar-lg mr-3">
                                <img src="{{ asset('admintheme/images/users/'.$dataAdmin->image) }}" class="img-fluid rounded-circle" alt="{{ ucwords($dataAdmin->firstname).' '.ucwords($dataAdmin->lastname) }}">
                            </div>
                            <div class="media-body overflow-hidden">
                                <h5 class="mt-0 mb-1">{{ ucwords($dataAdmin->firstname).' '.ucwords($dataAdmin->lastname) }}</h5>
                                <p class="text-muted mb-2 font-13 text-truncate">{{ strtolower($dataAdmin->email) }}</p>
                                <small class="text-danger"><b>{{ ucwords($dataAdmin->title) }} | {{ isset($dataAdmin->mobile) ? $dataAdmin->mobile : '-' }}</b></small>
                            </div>
                        </div>
                    </div>
                </div><!-- end col -->
            @endif
            <?php $i = 1; ?>
            @foreach($dataTeams as $dataTeam)
                <?php if($i==8){$i=1;} ?>
                <div class="col-xl-3 col-md-6">
                    <div class="card-box widget-user">
                        <div class="media">
                            <div class="avatar-lg mr-3">
                                <img src="{{ asset('admintheme/images/users/'.$dataTeam->image) }}" class="img-fluid rounded-circle" alt="{{ ucwords($dataTeam->firstname).' '.ucwords($dataTeam->lastname) }}">
                            </div>
                            <div class="media-body overflow-hidden">
                                <h5 class="mt-0 mb-1">{{ ucwords($dataTeam->firstname).' '.ucwords($dataTeam->lastname) }}</h5>
                                <p class="text-muted mb-2 font-13 text-truncate">{{ strtolower($dataTeam->email) }}</p>
                                <small class="text-{{ $statusBadge[$i] }}"><b>{{ isset($dataTeam->user_title) ? ucwords($dataTeam->user_title) : 'Staff' }} | {{ isset($dataTeam->mobile) ? $dataTeam->mobile : '-' }}</b></small>
                            </div>
                        </div>
                    </div>
                </div><!-- end col -->
                <?php $i++; ?>
            @endforeach
            <div class="w-100"></div>
            <div class="col-md">
                <?php 
                    #$dataTeams->setPath('minutes-tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                    #{{ $dataTeams->links() }}
                    echo $dataTeams;
                ?>
                <?php $paginator = $dataTeams; ?>
                @include('includes.paginator')
            </div>
        </div>
    </div>
</div>
<!-- teams end -->

@endsection
