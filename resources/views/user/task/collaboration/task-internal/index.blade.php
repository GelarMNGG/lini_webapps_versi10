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
    $formMultiDeptCollaborationIndex = 'user-task-leaders.index';
    $formMultiDeptCollaborationShow = 'user-task-leaders.show';

    //internal department collaboration
    $formInternalDeptCollaborationIndex = 'user-task-internal.index';
    $formInternalDeptCollaborationCreate = 'user-task-internal.create';
    $formInternalDeptCollaborationShow = 'user-task-internal.show';
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
                    <!-- Internal department tasks -->
                    @foreach($tasks as $internalTask)
                        <div class="col-xl-4">
                            <div class="card-box project-box">
                                <?php 
                                    if ($internalTask->level == 3) {
                                        $internalLevelStatusCSS = 'danger';
                                    }elseif($internalTask->level == 2){
                                        $internalLevelStatusCSS = 'warning';
                                    }else{
                                        $internalLevelStatusCSS = 'success';
                                    }
                                ?>
                                <div class="badge badge-{{ $internalLevelStatusCSS }} float-right">{{ ucfirst($internalTask->level_title) }}</div>
                                <h4 class="mt-0">
                                    <a href="{{ route($formInternalDeptCollaborationShow,$internalTask->id) }}" class="text-dark">{{ ucwords($internalTask->title) }}</a>

                                    @if($internalTask->status == 1)
                                        <span class="text-success">[ <i class="mdi mdi-check-all"></i> ]</span>
                                    @else
                                        <?php 
                                            $total = $internalTask->onprogress_count + $internalTask->done_count;
                                            $progressCount = $internalTask->done_count;
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
                                        <span class="{{ $cssStatus }}">[ {{ $internalTask->done_count.'/'.$total }} ]</span>
                                    @endif
                                </h4>
                                <p>
                                    <span class="text-{{ $internalLevelStatusCSS }} text-uppercase font-13">Internal Collaboration</span>
                                    <br><span class="small">inisiated by 
                                        @if(isset($internalTask->admin_firstname))
                                            <strong>{{ ucwords($internalTask->admin_firstname).' '.ucwords($internalTask->admin_lastname) }}</strong>
                                        @else
                                            <strong>{{ ucwords($internalTask->user_firstname).' '.ucwords($internalTask->user_lastname) }}</strong>
                                        @endif
                                        | <span class="text-danger">{{ isset($internalTask->admin_title) ? ucwords($internalTask->admin_title) : 'Co Admin' }}</span> 
                                    </span>
                                </p>
                                <p class="text-muted font-13">{!! \Illuminate\Support\Str::limit($internalTask->description,255,'...') !!}<a href="#" class="text-primary">View more</a>
                                </p>
                
                                <ul class="list-inline">
                                    <li class="list-inline-item mr-4">
                                        <h4 class="mb-0">{{ $internalTask->done_count + $internalTask->onprogress_count }}</h4>
                                        <p class="text-muted">Task</p>
                                    </li>
                                    <li class="list-inline-item mr-4">
                                        <h4 class="mb-0">{{ $internalTask->file_count + $internalTask->comment_file_count + $internalTask->todo_file_count }}</h4>
                                        <p class="text-muted">Files</p>
                                    </li>
                                    <li class="list-inline-item">
                                        <h4 class="mb-0">{{ $internalTask->comment_count }}</h4>
                                        <p class="text-muted">Comments</p>
                                    </li>
                                </ul>
                
                                <div class="project-members mb-2">
                                    <h5 class="float-left mr-3">Team :</h5>
                                    <div class="avatar-group">
                                        @if($internalTask->publisher_type == 'admin')
                                            @foreach($admins as $admin)
                                                @if($admin->id == $internalTask->publisher_id)
                                                    <a href="#" class="avatar-group-item" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ ucwords($admin->firstname).' '.ucwords($admin->lastname) }}">
                                                        <img src="{{ asset('admintheme/images/users/'.$admin->image) }}" class="rounded-circle avatar-sm" alt="friend" />
                                                    </a>
                                                @endif
                                            @endforeach
                                        @else
                                            @foreach($users as $user)
                                                @if($user->id == $internalTask->publisher_id)
                                                    <a href="#" class="avatar-group-item" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ ucwords($user->firstname).' '.ucwords($user->lastname) }}">
                                                        <img src="{{ asset('admintheme/images/users/'.$user->image) }}" class="rounded-circle avatar-sm" alt="friend" />
                                                    </a>
                                                @endif
                                            @endforeach
                                        @endif
                                        <!-- staff data -->
                                        <?php 
                                            if (isset($internalTask->coadmin_id) && $internalTask->coadmin_id != '0') {
                                                $coadminDatas = unserialize($internalTask->coadmin_id);
                                            }else{
                                                $coadminDatas = [];
                                            }
                                        ?>

                                        <!-- coadmin data -->
                                            @if($coadminDatas != NULL)
                                                @foreach($users as $user)
                                                    @if(in_array($user->id,$coadminDatas))
                                                        <a href="#" class="avatar-group-item" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ ucwords($user->firstname).' '.ucwords($user->lastname) }}">
                                                            <img src="{{ asset('admintheme/images/users/'.$user->image) }}" class="rounded-circle avatar-sm" alt="friend" />
                                                        </a>
                                                    @endif
                                                @endforeach
                                            @endif
                                        
                                        <!-- PIC data -->
                                        @if(isset($staffDataInternal))
                                            @foreach($staffDataInternal as $staffInternal)
                                                @if($staffInternal->task_id == $internalTask->id)
                                                    @foreach($users as $user)
                                                        @if($coadminDatas != NULL)
                                                            @if($user->id == $staffInternal->pic_id  && !in_array($user->id,$coadminDatas))
                                                                <a href="#" class="avatar-group-item" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ ucwords($user->firstname).' '.ucwords($user->lastname) }}">
                                                                    <img src="{{ asset('admintheme/images/users/'.$user->image) }}" class="rounded-circle avatar-sm" alt="friend" />
                                                                </a>
                                                            @endif
                                                        @elseif($user->id == $staffInternal->pic_id)
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
                                @if($internalTask->status == 1)
                                    <?php $percentage = 100; ?>
                                @else
                                    <?php 
                                        $total = $internalTask->onprogress_count + $internalTask->done_count;
                                        $progressCount = $internalTask->done_count;
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
                    @if($coAdminCheck > 0)
                        <a href="{{ route($formInternalDeptCollaborationCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i></a>
                    @endif
                    <a href="{{ route($formMultiDeptCollaborationIndex) }}" class="btn btn-orange"><i class="fa fa-eye"></i> All <span class="text-uppercase font-weight-bold">Multi</span> department</a>
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
                        <p class="text-info text-uppercase font-13">Internal departemen</p>
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
                    @if($coAdminCheck > 0)
                        <a href="{{ route($formInternalDeptCollaborationCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i></a>
                    @endif
                    <a href="{{ route($formMultiDeptCollaborationIndex) }}" class="btn btn-orange"><i class="fa fa-eye"></i> All <span class="text-uppercase font-weight-bold">Multi</span> department</a>
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
