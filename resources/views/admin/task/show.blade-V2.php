@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    ###form action route
    $formRouteIndex     = 'task.index';
    $formRouteEdit      = 'task.edit';
    $formRouteUpdate    = 'task.update';
    $formRouteShow    = 'task.show';
    
    if ($userId == $taskData->task_publisher_id && $userType == $taskData->publisher_type) {
        $pageTitle        = 'Detil penugasan';
        $taskPersonTitle  = 'Penerima tugas';
        $taskPersonId     = $taskData->task_receiver_id;
        $taskPersonType   = $taskData->receiver_type;
    }else{
        $pageTitle        = 'Detil tugas';
        $taskPersonTitle  = 'Pemberi tugas';
        $taskPersonId     = $taskData->task_publisher_id;
        $taskPersonType   = $taskData->publisher_type;
    }

    ###service categories
    $projectName = 'Internal';
    $customerName = 'Internal';
    $customerFirstname = null;
    $customerLastname = null;
    $projectKotaAsal = 'data';
    $projectKotaTujuan = 'internal'; 
    ###task status css
    if($taskData->task_status == 3):
        $statusCSS = "secondary";
    else:
        $statusCSS = "warning";
    endif
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


@if($taskData->task_publisher_id == $userId && $taskData->publisher_type == $userType || $taskData->task_receiver_id == $userId && $taskData->receiver_type == $userType)
    <div class="card mt-2">
        <div class="card-header text-center text-uppercase bb-orange">
            <strong>{{ ucfirst($pageTitle) }}</strong>
        </div>
        @if (session('status'))
            <div class="card-body">
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-12"> 
            <div class="row">
                <div class="col-md-8">
                    <div class="card-box task-detail">
                        <div class="dropdown float-right">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <!-- item-->
                                <a href="#" class="dropdown-item">Lihat</a>
                                <!-- item-->
                                <a href="javascript:history.go(-1);" class="dropdown-item">Kembali</a>
                            </div>
                        </div>
                        <div class="row">
                            <h2 class="text-secondary">{{ ucfirst($taskPersonTitle) }}</h2>
                        </div>
                        <div class="media mb-3">
                            @if($taskPersonType == 'admin')
                                @foreach($admins as $userReceiverType)
                                    @if($userReceiverType->id == $taskPersonId)
                                        <img class="d-flex mr-3 rounded-circle avatar-md" alt="64x64" src="{{ asset('admintheme/images/users/'.$userReceiverType->image) }}">
                                        <div class="media-body">
                                            <h4 class="media-heading mt-0">{{ ucfirst($userReceiverType->firstname).' '. ucfirst($userReceiverType->lastname) }}</h4>
                                            <span class="badge badge-{{ $statusBadge[$taskData->task_level] }}">{{ ucfirst($taskData->task_level_title) }}</span>
                                            | <small>{{ isset($taskData->category_name) ? ucwords($taskData->category_name) : 'Belum terkategori' }}</small>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                @foreach($users as $userReceiverType)
                                    @if($userReceiverType->id == $taskPersonId)
                                        <img class="d-flex mr-3 rounded-circle avatar-md" alt="64x64" src="{{ asset('admintheme/images/users/'.$userReceiverType->image) }}">
                                        <div class="media-body">
                                            <h4 class="media-heading mt-0">{{ ucfirst($userReceiverType->firstname).' '. ucfirst($userReceiverType->lastname) }}</h4>
                                            <span class="badge badge-{{ $statusBadge[$taskData->task_level] }}">{{ ucfirst($taskData->task_level_title) }}</span>
                                            | <small>{{ isset($taskData->category_name) ? ucwords($taskData->category_name) : 'Belum terkategori' }}</small>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <hr>
                        <div class="alert alert-warning">
                            <h4>{{ ucfirst($taskData->task_title) }}</h4>
                            <p class="text-muted">
                                {!! ucfirst($taskData->task_desc) !!}
                            </p>
                        </div>
                        <div class="row task-dates mb-0 mt-2">
                            <div class="col-lg-6">
                                <h5 class="font-600 m-b-5">Tanggal mulai</h5>
                                <p> {{ date("d F Y",strtotime($taskData->task_date)) }}<small class="text-muted"> {{ date("H:i a", strtotime($taskData->task_date)) }}</small></p>
                            </div>
                            <div class="col-lg-6">
                                <h5 class="font-600 m-b-5">Tanggal selesai</h5>
                                <p> {{ date("d F Y", strtotime($taskData->task_due_date)) }} <small class="text-muted">{{ date("H:i a",strtotime($taskData->task_due_date)) }}</small></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="text-right">
                                    @if ($taskData->task_status != 3)
                                        @if($taskData->task_receiver_id == $userId && $taskData->receiver_type == $userType)
                                            <form action="{{ route($formRouteUpdate, $taskData->task_id) }}" style="display:inline-block" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                @csrf
                                                @method('PUT')
                                    
                                                <input class="form-control" type="number" name="status" value="3" hidden>
                                                <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-magic' title='done'> </i> Ubah status ke Selesai</button>
                                            </form>
                                        @else
                                            <button class='btn btn-icon waves-effect waves-light btn-orange t-white'> <i class='fas fa-hourglass-half' title='done'> </i> On progress</button>
                                        @endif
                                    @elseif($taskData->task_publisher_id == $userId && $taskData->publisher_type == $userType)
                                        <form action="{{ route($formRouteUpdate, $taskData->task_id) }}" style="display:inline-block" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                            @csrf
                                            @method('PUT')
                                
                                            <input class="form-control" type="number" name="status" value="1" hidden>
                                            <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-magic' title='done'> </i> Ubah status ke belum Selesai</button>
                                        </form>
                                    @endif
                                    <a href="{{ route($formRouteIndex) }}" type="button" class="btn btn-blue-lini waves-effect">Kembali
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <form method="post" action="{{ route('task-comment.store') }}" enctype="multipart/form-data" class="card-box">
                        @csrf

                        <span class="input-icon icon-right">
                            <textarea rows="3" name="tc_comment" class="form-control" placeholder="Kirim komentar" value="{{ old('tc_comment') ?? '' }}" required></textarea>
                        </span>
                        <!-- comment data -->
                        <input type="text" name="tc_task_id" value="{{ $taskData->task_id }}" hidden>
                        <input type="text" name="tc_receiver_id" value="{{ $taskData->task_receiver_id }}" hidden>
                        <input type="text" name="receiver_type" value="{{ $taskData->receiver_type }}" hidden>
                        <input type="text" name="tc_publisher_id" value="{{ $taskData->task_publisher_id }}" hidden>
                        <input type="text" name="publisher_type" value="{{ $taskData->publisher_type }}" hidden>
                        <!-- comment data -->
                        <div class="pt-1 float-right">
                            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light" @if($taskData->task_status == 3) ?? disabled @endif>Kirim komentar</button>
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
                            <div class='media mb-3'>
                                @if($data1->publisher_type == 'user')
                                    @foreach($users as $user)
                                        @if($user->id == $data1->tc_publisher_id)
                                            <img src="{{ asset('admintheme/images/users/'.$user->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                                            <div class='media-body'>
                                                <h5 class='mt-0'><a href='#' class='text-dark'>{{ ucfirst($user->firstname).' '.ucfirst($user->lastname) }}</a><small class='ml-1 text-muted'>{{ date("H:i a", strtotime($data1->tc_date)) }}</small></h5>
                                                <p>{{ ucfirst($data1->tc_comment) }}</p>
                                                <div class='comment-footer'>
                                                    <span>{{ date('l, d M Y', strtotime($data1->tc_date)) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @elseif($data1->publisher_type == 'user')
                                    @foreach($users as $user)
                                        @if($user->id == $data1->tc_publisher_id)
                                            <img src="{{ asset('admintheme/images/users/'.$user->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                                            <div class='media-body'>
                                                <h5 class='mt-0'><a href='#' class='text-dark'>{{ ucfirst($user->firstname).' '.ucfirst($user->lastname) }}</a><small class='ml-1 text-muted'>{{ date("H:i a", strtotime($data1->tc_date)) }}</small></h5>
                                                <p>{{ ucfirst($data1->tc_comment) }}</p>
                                                <div class='comment-footer'>
                                                    <span>{{ date('l, d M Y', strtotime($data1->tc_date)) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach($admins as $admin)
                                        @if($admin->id == $data1->tc_publisher_id)
                                            <img src="{{ asset('admintheme/images/users/'.$admin->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                                            <div class='media-body'>
                                                <h5 class='mt-0'><a href='#' class='text-dark'>{{ ucfirst($admin->firstname).' '.ucfirst($admin->lastname) }}</a><small class='ml-1 text-muted'>{{ date("H:i a", strtotime($data1->tc_date)) }}</small></h5>
                                                <p>{{ ucfirst($data1->tc_comment) }}</p>
                                                <div class='comment-footer'>
                                                    <span>{{ date('l, d M Y', strtotime($data1->tc_date)) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div><!-- end col -->
                <div class="col-md-4">
                    <div class="card-box">
                        <div class="dropdown float-right">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <!-- item-->
                                <a href="{{ route($formRouteIndex) }}" class="dropdown-item">Lihat semua</a>
                            </div>
                        </div>
                        <h4 class="header-title mt-0 mb-3"><i class="mdi mdi-notification-clear-all mr-1"></i> Tugas lainnya</h4>
                        <ul class="list-group mb-0 user-list">
                            @if($countData > 0)
                                @foreach($otherTasks as $data2)
                                <li class='list-group-item'>
                                    <a href="{{ route($formRouteShow, $data2->task_id) }}" class='user-list-item'>
                                        <div class='user float-left mr-3'>
                                            <i class='mdi mdi-circle text-{{ $statusBadge[$data2->task_level] }}'></i>
                                        </div>
                                        <div class='user-desc overflow-hidden'>
                                            <h5 class='name mt-0 mb-1'>{{ ucwords($data2->task_title) }}</h5>
                                            <span class='desc text-muted font-12 text-truncate d-block'>{{ date("d M Y", strtotime($data2->task_due_date)).' - '.date("H:i a",strtotime($data2->task_due_date)) }}</span>
                                        </div>
                                    </a>
                                </li>
                                @endforeach
                            @else
                                <div class='alert alert-warning'>Belum ada tugas!</div>
                            @endif
                        </ul>
                    </div>
                </div><!-- end col -->
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
