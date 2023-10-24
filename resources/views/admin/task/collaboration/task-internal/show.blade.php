@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle = 'Task detail';
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    ###form action route
    $formRouteIndex     = 'task-internal.index';
    $formRouteEdit    = 'task-internal.edit';
    $formRouteUpdate    = 'task-internal.update';

    //task comment
    $formCommentsCreate = 'task-internal-comment.store';

    //upload route
    $formFileStore    = 'task-internal-file.store';
    $formFileDestroy    = 'task-internal-file.destroy';
    
    //upload comment file route
    $formCommentFileStore    = 'task-internal-comment-file.store';
    $formCommentFileDestroy    = 'task-internal-comment-file.destroy';

    //pic route
    $formPicCreate    = 'task-internal-pic.create';
    $formPicEdit    = 'task-internal-pic.edit';
    $formPicDestroy    = 'task-internal-pic.destroy';

    //todo
    $formTodoCreate = 'task-internal-todo.create';
    $formTodoEdit = 'task-internal-todo.edit';
    $formTodoShow = 'task-internal-todo.show';
    $formTodoDestroy = 'task-internal-todo.destroy';
    
    //todo file
    $formTodoFileDestroy = 'task-internal-todo-file.destroy';

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

@if($taskData->publisher_id == $userId && $taskData->publisher_type == $userType || Auth::user()->department_id == $taskData->department_id || Auth::user()->department_id == 11)
    <div class="card mt-2">
        <div class="card-header text-center bb-orange">
            <span class="text-uppercase "><strong>{{ ucfirst($taskData->title) }}</strong></span>
            <?php 
                if ($taskData->level == 3) {
                    $internalLevelStatusCSS = 'danger';
                }elseif($taskData->level == 2){
                    $internalLevelStatusCSS = 'warning';
                }else{
                    $internalLevelStatusCSS = 'success';
                }
            ?>
            <span class="badge badge-{{ $internalLevelStatusCSS }}" style="margin-left:10px;">{{ ucfirst($taskData->task_level_title) }}</span>
            <br><span class="small">
                Inisiated by 
                @if(strtolower($taskData->publisher_type) == 'admin')
                    <strong>{{ ucwords($taskData->admin_firstname).' '.ucwords($taskData->admin_lastname) }}</strong>
                @else
                    <strong>{{ ucwords($taskData->user_firstname).' '.ucwords($taskData->user_lastname) }}</strong>
                @endif
            </span>
            <br><span><small class='text-muted'>Issued on: {{ date('l, d M Y', strtotime($taskData->created_at)) }}</small></span>
            <br>
            <div class="row task-dates mb-0 mt-2">
                <div class="col-md">
                    <h5 class="font-600 m-b-5">Project start</h5>
                    <p> {{ date("d F Y",strtotime($taskData->date_start)) }}<small class="text-muted"> {{ date("H:i a", strtotime($taskData->date_start)) }}</small></p>
                </div>
                <?php $percentage = 0; ?>
                @if(isset($internalTodosCount))
                    <div class="col-sm">
                    <?php 
                        $total = $internalTodosCount->onprogress_count + $internalTodosCount->done_count;
                        $progressCount = $internalTodosCount->done_count;
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
                        <h2 class="{{ $cssStatus }}">[ {{ $internalTodosCount->done_count.'/'.$total }} ]</h2>
                        <span class="small text-muted">progress</span>
                    </div>
                @endif
                <div class="col-md">
                    <h5 class="font-600 m-b-5">Project closing</h5>
                    <p> {{ date("d F Y", strtotime($taskData->date_end)) }} <small class="text-muted">{{ date("H:i a",strtotime($taskData->date_end)) }}</small></p>
                </div>
            </div>
            <?php 
                $coAdminDatas[] = $coAdminDatas;
            ?>
            @if(count($coAdminDatas) > 0)
                <div class="row">
                    @foreach($users as $user)
                        @if(in_array($user->id,$coAdminDatas))
                            <div class="col-md">
                                <span class="small text-danger">Co admin</span>
                                <br><span class="small"><strong>{{ ucwords($user->firstname).' '.ucwords($user->lastname) }}</strong></span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
        <div class="col-md">
            <div id="bar" class="progress mb-3">
                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:{{ $percentage }}%">
                {{ $percentage }}% Selesai (success)
                </div>
            </div>
        </div>
        @if (session('status'))
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            </div>
        @endif
        <div class="card-body bg-gray-lini-2">
            <div class="row">
                <div class="col-12"> 
                    <div class="row">
                        <div class="col-md">
                            <div class="card-box task-detail">
                                <div class="dropdown float-right">
                                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="javascript:history.go(-1);" class="dropdown-item">Kembali</a>
                                    </div>
                                </div>
                                <div class="alert alert-warning">
                                    <p class="text-muted">
                                        {!! ucfirst($taskData->description) !!}
                                    </p>
                                </div>
                                <hr>
                                <div class="row m-0">
                                    <h4 class="text-secondary">Collaborators</h4>
                                </div>
                                <?php $pd=1; ?>
                                @if(isset($picDatas))
                                    @foreach($picDatas as $picData)
                                        @if($picData->task_id == $taskData->id)
                                            <div class="row m-0">
                                                <h4 class="text-secondary"><span class="text-danger">{{ ucfirst($picData->pic_firstname).' '.ucfirst($picData->pic_lastname) }}</span> | 
                                                    <form action="{{ route($formPicDestroy, $picData->id) }}" style="display:inline;" method="POST">
                                                        @method('DELETE')
                                                        @csrf

                                                        <!-- hidden data -->
                                                        <input type="hidden" name="task_id" value="{{ $taskData->id }}">

                                                        <a href="{{ route($formTodoCreate,'tid='.$taskData->id.'&pid='.$picData->id) }}" class="badge badge-pink">Add to do</a>

                                                        <a href="{{ route($formPicEdit, $picData->id.'&'.$taskData->id) }}" class='badge badge-info'> Edit PIC</a>
                                                        
                                                        @if(Auth::user()->department_id == $taskData->department_id)
                                                            <button type="submit" class="badge badge-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')">Hapus</button>
                                                        @endif
                                                    </form>
                                                </h4>
                                            </div>
                                            <div class="media mb-3">
                                                <div class="media-body">
                                                    <div class="row m-0">
                                                        <style>
                                                            a {color:#000}
                                                            a:hover {color:#d3d3d3}
                                                        </style>
                                                        @if(isset($internalTodos))
                                                            <?php $s=1; ?>
                                                            @foreach($internalTodos as $todo)
                                                                @if($todo->receiver_id == $picData->pic_id && $todo->receiver_type == $picData->pic_type)
                                                                    <div class="col-md-12 mb-1">
                                                                        <input class="form-check-input" type="checkbox" value="{{ $todo->status }}" id="CategoryCheck{{ $s }}" name="status" style="margin-left:-0.7rem;" @if($todo->status == 1)checked @endif disabled>
                                                                        <label class="form-check-label" style="margin-left:7px;" for="CategoryCheck{{ $s }}">
                                                                            <a href="{{ route($formTodoShow,$todo->id.'?tid='.$taskData->id) }}" class="">{{ ucfirst($todo->name) }}</a>

                                                                            @if(isset($todo->requester_type))
                                                                                <small>added by </small>
                                                                                @if(strtolower($todo->requester_type) == 'user')
                                                                                    @foreach($users as $user)
                                                                                        @if($user->id == $todo->requester_id)
                                                                                            <span class="small">{{ ucfirst($user->firstname).' '. ucfirst($user->lastname) }}</span>
                                                                                        @endif
                                                                                    @endforeach
                                                                                @elseif(strtolower($todo->requester_type) == 'admin')
                                                                                    @foreach($admins as $user)
                                                                                        @if($user->id == $todo->requester_id)
                                                                                            <span class="small">{{ ucfirst($user->firstname).' '. ucfirst($user->lastname) }}</span>
                                                                                        @endif
                                                                                    @endforeach
                                                                                @endif
                                                                            @endif
                                                                        </label>
                                                                        @if((Auth::user()->id == $todo->requester_id && Auth::user()->user_type == $todo->requester_type) || Auth::user()->department_id == $taskData->department_id)
                                                                            <form action="{{ route($formTodoDestroy, $todo->id) }}" style="display:inline;" method="POST">
                                                                                @method('DELETE')
                                                                                @csrf

                                                                                <!-- hidden data -->
                                                                                <input type="hidden" name="task_id" value="{{ $taskData->id }}">

                                                                                <a href="{{ route($formTodoEdit, $todo->id.'&'.$taskData->id) }}" class='badge badge-info'> Edit</a>

                                                                                <button type="submit" class="badge badge-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')">Hapus</button>
                                                                                
                                                                            </form>
                                                                        @endif
                                                                        @if($todo->status == 1)
                                                                            <span class="text-success"><i class="mdi mdi-check-all">done</i></span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            <?php $s++; ?>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        @else
                                            <a href="{{ route($formPicCreate,'did='.$userReceiver->department_id.'&tid='.$taskData->id) }}" class="btn btn-orange mr-1">Add PIC</a>
                                        @endif
                                    @endforeach
                                    <div class="row m-0">
                                        <a href="{{ route($formPicCreate,'tid='.$taskData->id) }}" class="btn btn-orange mr-1">Add PIC</a>
                                    </div>
                                    <hr>
                                @else
                                    <div class="row m-0">
                                        <a href="{{ route($formPicCreate,'tid='.$taskData->id) }}" class="btn btn-orange mr-1">Add PIC</a>
                                    </div>
                                    <hr>
                                @endif
                                <!-- supporting document -->
                                    <div class="row m-0">
                                        <h4 class="text-secondary">Supporting documents</h4>
                                    </div>
                                    <div class="media mb-3">
                                        <div class="media-body">
                                            @if(isset($internalFiles))
                                                <?php $i = 1; ?>
                                                <div class="row mb-1">
                                                    @foreach($internalFiles as $internalFile)
                                                        @if($internalFile->task_id == $taskData->id)
                                                            <div class="col-md-12">
                                                                <span class="text-danger">#{{ $i }} </span>
                                                                <a href="{{ asset('img/upload-doc/task-internal/'.$internalFile->image) }}" class="display:inline-block;">[{{ ucfirst($internalFile->image) }}]</a>

                                                                <!-- delete file -->
                                                                    @if(Auth::user()->id == $taskData->publisher_id || Auth::user()->department_id == $taskData->department_id)
                                                                    <form action="{{ route($formFileDestroy, $internalFile->id) }}" style="display:inline;" method="POST">
                                                                        @method('DELETE')
                                                                        @csrf

                                                                        <!-- hidden data -->
                                                                        <input type="hidden" name="task_id" value="{{ $taskData->id }}">

                                                                        <button type="submit" class="badge badge-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')">Hapus</button>
                                                                        
                                                                    </form>
                                                                    @endif
                                                                <!-- delete file -->
                                                            </div>
                                                        @endif
                                                    <?php $i++; ?>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span>Belum ada file</span>
                                            @endif

                                            <div>
                                                <button type="button" class="btn btn-orange" data-toggle="modal" data-target="#uploadDocModal{{ $taskData->id }}"> Upload file</button>
                                            </div>
                                            <!-- modal start -->
                                                <div class="modal fade" id="uploadDocModal{{ $taskData->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header no-bd">
                                                                <h5 class="modal-title">
                                                                    <span class="fw-mediumbold text-danger"> Tutup</span> 
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span class="text-danger" aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form class="w-100" action="{{ route($formFileStore) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <!-- hidden data -->
                                                                <input type="number" name="task_id" value="{{ $taskData->id }}" hidden>
                                                                <div class="row">
                                                                    <div class="col-md{{ $errors->has('image') ? ' has-error' : '' }}">
                                                                        <div class="card-box">
                                                                            <h4 class="header-title mb-3"><small>File untuk </small><br><span class="text-info">{{ ucfirst($taskData->title) }}</span></h4>
                                                                            <input type="file" name="image" class="dropify" data-max-file-size="4M" data-default-file="{{ asset('img/upload-doc/task-internal/default.png') }}" required />
                                                                        </div>
                                                                    </div>
                                                                    <div class="w-100"></div>
                                                                    <div class="col-md mt-2 mb-2">
                                                                        <button type="submit" class="btn btn-orange" name="submit">Simpan</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <!-- modal start end -->
                                        </div>
                                    </div>
                                    <hr>
                                <!-- supporting document -->
                                <!-- other documents -->
                                @if(count($commentFiles) > 0 || count($internalTodoFiles) > 0)
                                    <div class="row m-0">
                                        <h4 class="text-secondary">Other documents</h4>
                                    </div>
                                    <div class="media mb-3">
                                            <div class="media-body">
                                                <div class="row mb-1">
                                                @if(isset($commentFiles))
                                                    <?php $icl = 1; ?>
                                                        @foreach($commentFiles as $commentLeaderFiles)
                                                            @if($commentLeaderFiles->task_id == $taskData->id)
                                                                <div class="col-md-12">
                                                                    <span class="text-danger">#{{ $icl }} </span>
                                                                    <a href="{{ asset('img/upload-doc/task-internal/'.$commentLeaderFiles->image) }}" class="display:inline-block;">[{{ ucfirst($commentLeaderFiles->image) }}]</a>

                                                                    <!-- delete file -->
                                                                        @if(Auth::user()->id == $taskData->publisher_id || Auth::user()->department_id == $taskData->department_id)
                                                                        <form action="{{ route($formCommentFileDestroy, $commentLeaderFiles->id) }}" style="display:inline;" method="POST">
                                                                            @method('DELETE')
                                                                            @csrf

                                                                            <!-- hidden data -->
                                                                            <input type="hidden" name="task_id" value="{{ $taskData->id }}">

                                                                            <button type="submit" class="badge badge-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')">Hapus</button>
                                                                            
                                                                        </form>
                                                                        @endif
                                                                    <!-- delete file end -->
                                                                </div>
                                                            @endif
                                                        <?php $icl++; ?>
                                                        @endforeach
                                                @endif
                                                @if(isset($internalTodoFiles))
                                                    @foreach($internalTodoFiles as $internalTodoFile)
                                                        @if($internalTodoFile->task_id == $taskData->id)
                                                            <div class="col-md-12">
                                                                <span class="text-danger">#{{ $icl }} </span>
                                                                <a href="{{ asset('img/upload-doc/task-internal/'.$internalTodoFile->image) }}" class="display:inline-block;">[{{ ucfirst($internalTodoFile->image) }}]</a>

                                                                <!-- delete file -->
                                                                    @if(Auth::user()->id == $taskData->publisher_id || Auth::user()->department_id == $taskData->department_id)
                                                                        <form action="{{ route($formTodoFileDestroy, $internalTodoFile->id) }}" style="display:inline;" method="POST">
                                                                            @method('DELETE')
                                                                            @csrf

                                                                            <!-- hidden data -->
                                                                            <input type="hidden" name="task_id" value="{{ $taskData->id }}">

                                                                            <button type="submit" class="badge badge-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')">Hapus</button>
                                                                            
                                                                        </form>
                                                                    @endif
                                                                <!-- delete file end -->
                                                            </div>
                                                        @endif
                                                    <?php $icl++; ?>
                                                    @endforeach
                                                @endif
                                                </div>
                                            </div>
                                        </div>
                                    <hr>
                                @endif
                                <!-- other documents -->
                                <div class="row">
                                    <div class="col-sm-12 bg-lini-gray-2">
                                        <div class="text-right">
                                            @if ($taskData->status != 3)
                                                @if(Auth::user()->id == $taskData->publisher_id)
                                                    <a href="{{ route($formRouteEdit, $taskData->id) }}" type="button" class="btn btn-info waves-effect">Edit</a>
                                                @else
                                                    <button class='btn btn-icon waves-effect waves-light btn-orange t-white'> <i class='fas fa-hourglass-half' title='done'> </i> On progress</button>
                                                @endif
                                            @endif
                                            <a href="{{ route($formRouteIndex) }}" type="button" class="btn btn-blue-lini waves-effect">Kembali</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <form method="post" action="{{ route($formCommentsCreate) }}" enctype="multipart/form-data" class="card-box">
                                @csrf

                                <span class="input-icon icon-right{{ $errors->has('comment') ? ' has-error' : '' }}">
                                    <textarea rows="3" name="comment" class="form-control" placeholder="Kirim komentar" required>{{ old('comment') ?? '' }}</textarea>
                                </span>
                                <!-- comment data -->
                                <input type="text" name="title" value="{{ $taskData->title }}" hidden>
                                <input type="number" name="task_id" value="{{ $taskData->id }}" hidden>
                                <input type="text" name="level" value="{{ $taskData->level }}" hidden>
                                <!-- comment data -->
                                <div class="pt-1 float-right">
                                    <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" @if($taskData->status == 3 && $taskData->publisher_id != Auth::user()->id) disabled @endif>Kirim komentar</button>
                                </div>
                                <ul class="nav nav-pills profile-pills mt-1">
                                    <li>
                                        <a href="#"><i class="far fa-smile"></i></a>
                                    </li>
                                </ul>
                            </form>
                            @if($countComments > 0)
                                @foreach ($dataComments as $data1)
                                <div class="card-box">
                                    <div class='media'>
                                        @if($data1->publisher_type == 'user')
                                            @foreach($users as $user)
                                                @if($user->id == $data1->publisher_id)
                                                    <img src="{{ asset('admintheme/images/users/'.$user->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                                                    <div class='media-body'>
                                                        <span class="float-right text-right p-0">
                                                            @if(Auth::user()->id == $data1->publisher_id)
                                                                <button type="button" class="badge badge-dark" data-toggle="modal" data-target="#uploadModal{{ $data1->id }}"> Upload file</button>
                                                            @endif
                                                            @if($data1->countFiles > 0)
                                                                <br><span class="badge badge-danger">{{ $data1->countFiles }} file</span>
                                                            @endif
                                                        </span>

                                                        <h5 class='mt-0'>
                                                            <a href='#' class='text-dark'>{{ ucfirst($user->firstname).' '.ucfirst($user->lastname) }}</a>
                                                            <small class='ml-1 text-muted'>{{ date('l, d M Y', strtotime($data1->date)) }}</small>
                                                            <small class='ml-1 text-muted'>{{ date("H:i a", strtotime($data1->date)) }}</small>
                                                        </h5>

                                                        <p>{{ ucfirst($data1->comment) }}</p>

                                                        @if($data1->countFiles > 0)
                                                            <span class="text-info"><small>Download:</small></span>
                                                            <div class='comment-footer'>
                                                                <?php $ia=1; ?>
                                                                @foreach($commentFiles as $commentFile)
                                                                    @if($commentFile->comment_id == $data1->id)
                                                                        <span class="text-danger ml-1">#{{ $ia }}.</span><a href="{{ asset('img/comment-file/task-internal/'.$commentFile->image) }}">[{{ $commentFile->image }}]</a>
                                                                    @endif
                                                                    <?php $ia++; ?>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        @elseif($data1->publisher_type == 'admin')
                                            @foreach($admins as $admin)
                                                @if($admin->id == $data1->publisher_id)
                                                    <img src="{{ asset('admintheme/images/users/'.$admin->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                                                    <div class='media-body'>
                                                        <span class="float-right text-right p-0">
                                                            @if(Auth::user()->id == $data1->publisher_id)
                                                                <button type="button" class="badge badge-dark" data-toggle="modal" data-target="#uploadModal{{ $data1->id }}"> Upload file</button>
                                                            @endif

                                                            @if($data1->countFiles > 0)
                                                                <br><span class="badge badge-danger">{{ $data1->countFiles }} file</span>
                                                            @endif
                                                        </span>

                                                        <h5 class='mt-0'>
                                                            <a href='#' class='text-dark'>{{ ucfirst($admin->firstname).' '.ucfirst($admin->lastname) }}</a>
                                                            <small class='ml-1 text-muted'>{{ date('l, d M Y', strtotime($data1->date)) }}</small>
                                                            <small class='ml-1 text-muted'>{{ date("H:i a", strtotime($data1->date)) }}</small>
                                                        </h5>

                                                        <p>{{ ucfirst($data1->comment) }}</p>

                                                        @if($data1->countFiles > 0)
                                                            <span class="text-info"><small>Dowload:</small></span>
                                                            <div class='comment-footer'>
                                                                <?php $i=1; ?>
                                                                @foreach($commentFiles as $commentFile)
                                                                    @if($commentFile->comment_id == $data1->id)
                                                                        <span class="text-danger ml-1">#{{ $i }}.</span><a href="{{ asset('img/comment-file/task-internal/'.$commentFile->image) }}">[{{ $commentFile->image }}]</a>
                                                                    @endif
                                                                    <?php $i++; ?>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- modal start -->
                                    <div class="modal fade" id="uploadModal{{ $data1->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header no-bd">
                                                    <h5 class="modal-title">
                                                        <span class="fw-mediumbold text-danger"> Tutup</span> 
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span class="text-danger" aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form class="w-100" action="{{ route($formCommentFileStore) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <!-- hidden data -->
                                                    <input type="number" name="task_id" value="{{ $taskData->id }}" hidden>
                                                    <input type="number" name="comment_id" value="{{ $data1->id }}" hidden>
                                                    <div class="row">
                                                        <div class="col-md{{ $errors->has('image') ? ' has-error' : '' }}">
                                                            <div class="card-box">
                                                                <h4 class="header-title mb-3"><small>File untuk </small><br><span class="text-info">{{ ucfirst($data1->comment) }}</span></h4>
                                                                <input type="file" name="image" class="dropify" data-max-file-size="4M" data-default-file="{{ asset('img/comment-file/task-internal/default.png') }}" required />
                                                            </div>
                                                        </div>
                                                        <div class="w-100"></div>
                                                        <div class="col-md mt-2 mb-2">
                                                            <button type="submit" class="btn btn-orange" name="submit">Simpan</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <!-- modal start end -->
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
<script type="text/javascript">
    window.location = "{{ route($formRouteIndex) }}";
</script>
@endif

@endsection

@section ('script')

@endsection
