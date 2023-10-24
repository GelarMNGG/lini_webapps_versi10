@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Collaboration Tasks';
    $pageTitleZero      = 'No Collaboration Tasks Yet';
    if (Auth::user()->department_id == 11) {
        $title1 = 'Collaboration Tasks list';
    }else{
        $title1 = 'Your collaboration task list';
    }
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');

    //multi department collaboration
    $formMultiDeptCollaborationCreate = 'task-leaders.create';
    $formMultiDeptCollaborationIndex = 'task-leaders.index';
    $formMultiDeptCollaborationShow = 'task-leaders.show';

    //internal department collaboration
    $formInternalDeptCollaborationIndex = 'task-internal.index';
    $formInternalDeptCollaborationShow = 'task-internal.show';
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

    @if ($countData > 0)
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <?php /* <div class='badge badge-info float-left'>{{ $countDataMulti + $countDataInternal }}</div> */ ?>
                <span class="text-uppercase"><strong>{{ $title1 }}</strong></span>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="row">
                    <!-- Multi department tasks -->
                    @foreach($tasks as $multiTask)
                        <div class="col-xl-4">
                            <div class="card-box project-box">
                                <?php 
                                    if ($multiTask->level == 3) {
                                        $multiLevelStatusCSS = 'danger';
                                    }elseif($multiTask->level == 2){
                                        $multiLevelStatusCSS = 'warning';
                                    }else{
                                        $multiLevelStatusCSS = 'success';
                                    }
                                ?>
                                <div class="badge badge-{{ $multiLevelStatusCSS }} float-right">{{ ucfirst($multiTask->level_title) }}</div>
                                <h4 class="mt-0">
                                    <a href="{{ route($formMultiDeptCollaborationShow,$multiTask->id) }}" class="text-dark">{{ ucwords($multiTask->title) }}</a>

                                    @if($multiTask->status == 1)
                                        <span class="text-success">[ <i class="mdi mdi-check-all"></i> ]</span>
                                    @else
                                        <?php 
                                            $total = $multiTask->onprogress_count + $multiTask->done_count;
                                            $progressCount = $multiTask->done_count;
                                            if ($total > 0 && $progressCount > 0) {
                                                $percentage = ($progressCount/$total) * 100;
                                            }else{
                                                $percentage = 0;
                                            }
                                            if ($percentage < 45 && $percentage != 0) {
                                                $cssStatus = 'text-danger';
                                            }elseif($percentage >= 45 && $percentage < 75){
                                                $cssStatus = 'text-warning';
                                            }elseif($percentage >= 75 && $percentage < 100){
                                                $cssStatus = 'text-info';
                                            }elseif($total > 0 && $progressCount == 0){
                                                $cssStatus = 'text-danger';
                                            }else{
                                                $cssStatus = 'text-success';
                                            }
                                        ?>
                                        <span class="{{ $cssStatus }}">[ {{ $multiTask->done_count.'/'.$total }} ]</span>
                                    @endif
                                </h4>
                                <p>
                                    <span class="text-{{ $multiLevelStatusCSS }} text-uppercase font-13">Multi Collaboration</span>
                                    <br><span class="small">inisiated by 
                                        @if(isset($multiTask->admin_firstname))
                                            <strong>{{ ucwords($multiTask->admin_firstname).' '.ucwords($multiTask->admin_lastname) }}</strong>
                                        @else
                                            <strong>{{ ucwords($multiTask->user_firstname).' '.ucwords($multiTask->user_lastname) }}</strong>
                                        @endif
                                        | <span class="text-danger">{{ ucwords($multiTask->dept_name) }}</span> 
                                    </span>
                                </p>
                                <p class="text-muted font-13">{!! \Illuminate\Support\Str::limit($multiTask->description,255,'...') !!}<a href="#" class="text-primary"> View more</a>
                                </p>
                
                                <ul class="list-inline">
                                    <li class="list-inline-item mr-4">
                                        <h4 class="mb-0">{{ $multiTask->done_count + $multiTask->onprogress_count }}</h4>
                                        <p class="text-muted">Task</p>
                                    </li>
                                    <li class="list-inline-item mr-4">
                                        <h4 class="mb-0">{{ $multiTask->file_count + $multiTask->comment_file_count + $multiTask->todo_file_count }}</h4>
                                        <p class="text-muted">Files</p>
                                    </li>
                                    <li class="list-inline-item">
                                        <h4 class="mb-0">{{ $multiTask->comment_count }}</h4>
                                        <p class="text-muted">Comments</p>
                                    </li>
                                </ul>
                
                                <div class="project-members mb-2">
                                    <h5 class="mr-3">Departments :</h5>
                                    <div class="avatar-group">
                                        <?php $multiTasksdepartments = unserialize($multiTask->receiver_department); ?>
                                        @if($multiTasksdepartments != NULL)
                                            @foreach($departments as $department)
                                                @if(in_array($department->id,$multiTasksdepartments))
                                                    <div class="badge badge-info">{{ ucfirst($department->name) }}</div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <div class="project-members mb-2">
                                    <h5 class="float-left mr-3">Team :</h5>
                                    <div class="avatar-group">
                                        @if($multiTask->publisher_type == 'admin')
                                            @foreach($admins as $admin)
                                                @if($admin->id == $multiTask->publisher_id)
                                                    <a href="#" class="avatar-group-item" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ ucwords($admin->firstname).' '.ucwords($admin->lastname) }}">
                                                        <img src="{{ asset('admintheme/images/users/'.$admin->image) }}" class="rounded-circle avatar-sm" alt="friend" />
                                                    </a>
                                                @endif
                                            @endforeach
                                        @else
                                            @foreach($users as $user)
                                                @if($user->id == $multiTask->publisher_id)
                                                    <a href="#" class="avatar-group-item" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ ucwords($user->firstname).' '.ucwords($user->lastname) }}">
                                                        <img src="{{ asset('admintheme/images/users/'.$user->image) }}" class="rounded-circle avatar-sm" alt="friend" />
                                                    </a>
                                                @endif
                                            @endforeach
                                        @endif
                                        <!-- coadmin data -->
                                        @if(isset($multiTask->coadmin_id))
                                            <?php $coadminData = unserialize($multiTask->coadmin_id); ?>
                                            @if($coadminData != NULL)
                                                @foreach($users as $user)
                                                    @if(in_array($user->id,$coadminData))
                                                        <a href="#" class="avatar-group-item" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ ucwords($user->firstname).' '.ucwords($user->lastname) }}">
                                                            <img src="{{ asset('admintheme/images/users/'.$user->image) }}" class="rounded-circle avatar-sm" alt="friend" />
                                                        </a>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endif
                                        <!-- pic data -->
                                        @if(isset($staffDataMulti))
                                            @foreach($staffDataMulti as $staffMulti)
                                                @if($staffMulti->task_id == $multiTask->id)
                                                    @foreach($users as $user)
                                                        @if($user->id == $staffMulti->pic_id)
                                                            <a href="#" class="avatar-group-item" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ ucwords($user->firstname).' '.ucwords($user->lastname) }}">
                                                                <img src="{{ asset('admintheme/images/users/'.$user->image) }}" class="rounded-circle avatar-sm" alt="friend" />
                                                            </a>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                                <?php $percentage = 0; ?>
                                @if($multiTask->status == 1)
                                    <?php $percentage = 100; ?>
                                @else
                                    <?php 
                                        $total = $multiTask->onprogress_count + $multiTask->done_count;
                                        $progressCount = $multiTask->done_count;
                                        if ($total > 0 && $progressCount > 0) {
                                            $percentage = ($progressCount/$total) * 100;
                                        }else{
                                            $percentage = 0;
                                        }
                                        if ($percentage < 45 && $percentage != 0) {
                                            $cssStatus = 'text-danger';
                                            $cssProgressStatus = 'danger';
                                        }elseif($percentage >= 45 && $percentage < 75){
                                            $cssStatus = 'text-warning';
                                            $cssProgressStatus = 'warning';
                                        }elseif($percentage >= 75 && $percentage < 100){
                                            $cssStatus = 'text-info';
                                            $cssProgressStatus = 'info';
                                        }else{
                                            $cssStatus = 'text-success';
                                            $cssProgressStatus = 'success';
                                        }
                                    ?>
                                @endif
                
                                <h5>Progress <span class="{{ $cssStatus }} float-right">{{ round($percentage) }}%</span></h5>
                                <div class="progress progress-bar-alt-{{ $cssProgressStatus }} progress-sm">
                                    <div class="progress-bar bg-{{ $cssProgressStatus }} progress-animated wow animated animated"
                                            role="progressbar" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="{{ $total }}"
                                            style="width: {{ $percentage }}%; visibility: visible; animation-name: animationProgress;">
                                    </div><!-- /.progress-bar .progress-bar-danger -->
                                </div><!-- /.progress .no-rounded -->
                
                            </div>
                        </div><!-- end col-->
                    @endforeach
                    <div class="col-12">
                        <?php 
                            #$projects->setPath('teamuser?project_id='.$projectTask->project_id.'&id='.$projectTask->id);
                            echo $tasks;
                        ?>
                        <?php $paginator = $tasks; ?>
                        @include('includes.paginator')
                    </div>
                </div>
            </div>
            <!-- Multi department tasks -->
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formMultiDeptCollaborationCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i></a>
                    <a href="{{ route($formInternalDeptCollaborationIndex) }}" class="btn btn-orange"><i class="fa fa-eye"></i> All <span class="text-uppercase font-weight-bold">Internal</span> department</a>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header text-center text-uppercase bb-orange text-danger"><strong>{{ ucfirst($pageTitleZero) }}</strong></div>

            <div class="card-body bg-gray-lini-2">
                <!-- Dummy data -->
                <div class="col-xl-4">
                    <div class="card-box project-box">
                        <div class="badge badge-info float-right">On progress</div>
                        <h4 class="mt-0"><a href="" class="text-dark">Contoh Proyek Kolaborasi</a></h4>
                        <p class="text-info text-uppercase font-13">Multi departemen</p>
                        <p class="text-muted font-13">Jika kamu dilibatkan dalam suatu proyek kolaborasi, maka deskripsi proyek akan ditambilkan disini...<a href="#" class="text-primary">View more</a>
                        </p>

                        <ul class="list-inline">
                            <li class="list-inline-item mr-4">
                                <h4 class="mb-0">33</h4>
                                <p class="text-muted">Task</p>
                            </li>
                            <li class="list-inline-item mr-4">
                                <h4 class="mb-0">77</h4>
                                <p class="text-muted">Files</p>
                            </li>
                            <li class="list-inline-item">
                                <h4 class="mb-0">144</h4>
                                <p class="text-muted">Comments</p>
                            </li>
                        </ul>

                        <div class="project-members mb-2">
                            <h5 class="float-left mr-3">Team :</h5>
                            <div class="avatar-group">
                                @foreach($usersDummy as $user)
                                    <a href="#" class="avatar-group-item" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ ucwords($user->firstname).' '.ucwords($user->lastname) }}">
                                        <img src="{{ asset('admintheme/images/users/'.$user->image) }}" class="rounded-circle avatar-sm" alt="friend" />
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <h5>Progress <span class="text-info float-right">77%</span></h5>
                        <div class="progress progress-bar-alt-info progress-sm">
                            <div class="progress-bar bg-info progress-animated wow animated animated"
                                    role="progressbar" aria-valuenow="77" aria-valuemin="0" aria-valuemax="100"
                                    style="width: 77%; visibility: visible; animation-name: animationProgress;">
                            </div><!-- /.progress-bar .progress-bar-danger -->
                        </div><!-- /.progress .no-rounded -->

                    </div>
                </div><!-- end col-->
            </div>

            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formMultiDeptCollaborationCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i></a>
                    <a href="{{ route($formInternalDeptCollaborationIndex) }}" class="btn btn-orange"><i class="fa fa-eye"></i> All <span class="text-uppercase font-weight-bold">Internal</span> department</a>
                </div>
            </div>
        </div> <!-- container-fluid -->
    @endif
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
