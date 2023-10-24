@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'To do detail'; 
    $formRouteIndex = 'task-leaders.index';
    $formRouteShow = 'task-leaders.show';

    //todo
    $formRouteEdit = 'task-leaders-todo.edit';
    $formRouteUpdate = 'task-leaders-todo.update';

    //todo file
    $formFileStore = 'task-leaders-todo-file.store';

    //todo comments
    $formCommentsStore = 'task-leaders-todo-comment.store';
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
        <span class="text-uppercase "><strong>{{ ucfirst($taskData->title) }}</strong></span>
        <br><span class="small text-uppercase text-danger"><strong>{{ ucwords($departmentData->name) }} Department</strong></span>
        <br><span><small class='text-muted'>{{ date('l, d M Y', strtotime($taskData->created_at)) }}</small></span>
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
                                <span class="small text-uppercase">check list</span>
                                <div class="float-right">@if($todoData->status == 1) <span class="badge badge-success">Done</span> @else <span class="badge badge-danger">On progress</span> @endif</div>
                                <h2 class="text-muted">
                                    {!! ucfirst($todoData->name) !!}
                                </h2>
                            </div>
                            <hr>
                            <div class="row m-0">
                                <h4 class="text-secondary">Supporting documents</h4>
                            </div>
                            <div class="media mb-3">
                                <div class="media-body">
                                    @if(isset($leadersTodoFiles))
                                        <?php $i = 1; ?>
                                        <div class="row mb-1">
                                            @foreach($leadersTodoFiles as $leadersFile)
                                                @if($leadersFile->task_id == $taskData->id)
                                                    <div class="col-md-12">
                                                        <span class="text-danger">#{{ $i }} </span>
                                                        <a href="{{ asset('img/upload-doc/task-leaders/'.$leadersFile->image) }}" class="display:inline-block;">[{{ ucfirst($leadersFile->image) }}]</a>
                                                    </div>
                                                @endif
                                            <?php $i++; ?>
                                            @endforeach
                                        </div>
                                    @else
                                        <span>Belum ada file</span>
                                    @endif

                                    <div>
                                        <button type="button" class="btn btn-orange" data-toggle="modal" data-target="#uploadDocModal{{ $todoData->id }}"> Upload file</button>
                                    </div>
                                    <!-- modal start -->
                                        <div class="modal fade" id="uploadDocModal{{ $todoData->id }}" tabindex="-1" role="dialog" aria-hidden="true">
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
                                                        <input type="number" name="todo_id" value="{{ $todoData->id }}" hidden>
                                                        <div class="row">
                                                            <div class="col-md{{ $errors->has('image') ? ' has-error' : '' }}">
                                                                <div class="card-box">
                                                                    <h4 class="header-title mb-3"><small>File untuk </small><br><span class="text-info">{{ ucfirst($todoData->name) }}</span></h4>
                                                                    <input type="file" name="image" class="dropify" data-max-file-size="4M" data-default-file="{{ asset('img/upload-doc/task-leaders/default.png') }}" required />
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
                            <div class="row">
                                <div class="col-sm-12 bg-lini-gray-2">
                                    <div class="text-right">
                                        @if ($todoData->status != 1)
                                            @if(Auth::user()->id == $taskData->publisher_id || Auth::user()->department_id == $taskData->publisher_department)
                                                <a href="{{ route($formRouteEdit, $todoData->id.'&'.$taskData->id) }}" type="button" class="btn btn-info waves-effect">Update</a>
                                            @endif
                                        @endif
                                        <a href="{{ route($formRouteShow,$taskData->id) }}" type="button" class="btn btn-blue-lini waves-effect">Kembali</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form method="post" action="{{ route($formCommentsStore) }}" enctype="multipart/form-data" class="card-box">
                            @csrf

                            <span class="input-icon icon-right{{ $errors->has('comment') ? ' has-error' : '' }}">
                                <textarea rows="3" name="comment" class="form-control" placeholder="Kirim komentar" required>{{ old('comment') ?? '' }}</textarea>
                            </span>
                            <!-- comment data -->
                            <input type="text" name="title" value="{{ $todoData->name }}" hidden>
                            <input type="text" name="task_id" value="{{ $taskData->id }}" hidden>
                            <input type="text" name="todo_id" value="{{ $todoData->id }}" hidden>
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
                                                    <h5 class='mt-0'>
                                                        <a href='#' class='text-dark'>{{ ucfirst($user->firstname).' '.ucfirst($user->lastname) }}</a>
                                                        <small class='ml-1 text-muted'>{{ date('l, d M Y', strtotime($data1->date)) }}</small>
                                                        <small class='ml-1 text-muted'>{{ date("H:i a", strtotime($data1->date)) }}</small>
                                                    </h5>

                                                    <p>{{ ucfirst($data1->comment) }}</p>
                                                </div>
                                            @endif
                                        @endforeach
                                    @elseif($data1->publisher_type == 'admin')
                                        @foreach($admins as $admin)
                                            @if($admin->id == $data1->publisher_id)
                                                <img src="{{ asset('admintheme/images/users/'.$admin->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                                                <div class='media-body'>
                                                    <h5 class='mt-0'>
                                                        <a href='#' class='text-dark'>{{ ucfirst($admin->firstname).' '.ucfirst($admin->lastname) }}</a>
                                                        <small class='ml-1 text-muted'>{{ date('l, d M Y', strtotime($data1->date)) }}</small>
                                                        <small class='ml-1 text-muted'>{{ date("H:i a", strtotime($data1->date)) }}</small>
                                                    </h5>

                                                    <p>{{ ucfirst($data1->comment) }}</p>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <?php /*
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
                                                <input type="number" name="comment_id" value="{{ $data1->id }}" hidden>
                                                <div class="row">
                                                    <div class="col-md{{ $errors->has('image') ? ' has-error' : '' }}">
                                                        <div class="card-box">
                                                            <h4 class="header-title mb-3"><small>File untuk </small><br><span class="text-info">{{ ucfirst($data1->comment) }}</span></h4>
                                                            <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/comment-file/task-leaders/default.png') }}" />
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
                            */ ?>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section ('script')

@endsection
