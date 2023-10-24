@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar tugas'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');

    //route
    $formRouteCreate = 'task.create';
    $formRouteEdit = 'task.edit';
    $formRouteShow = 'task.show';

    //collaboration
    $formRouteCollaborationIndex = 'admin-collaboration.index';
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

    @if ($countData > 0 || $countDataTwo > 0)
        @if($countData > 0)
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-info float-left'>{{ $countData }}</div>
                <span class="text-uppercase"><strong>Tugas-tugas Anda</strong></span>
            </div>

            <div class="card-body bg-gray-lini-2">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="container-fluid">
                    <div class="row">
                        <?php $i1 = 0; ?>
                        @foreach ($tasks as $data)
                            <div class="col-md">
                                <div class="card-box">
                                    <?php 
                                        if($data->task_status == 3):
                                            $statusCSS = "secondary";
                                        else:
                                            $statusCSS = "warning";
                                        endif
                                    ?>
                                    <div class="alert alert-{{ $statusCSS }}">

                                        <div class="float-right text-right">
                                            @if($data->task_status == 3)
                                                <span class="badge badge-success">Done</span>
                                            @else
                                                <span class="badge badge-danger">On progress</span>
                                            @endif
                                            <br><span class='badge badge-{{ $statusBadge[$data->task_level] }}'>{{ ucfirst($data->task_level_title) }}</span>
                                            <?php
                                                if ($data->grade > 60) {
                                                    $badgeBobot = 'danger';
                                                }elseif($data->grade >= 30 && $data->grade <= 60){
                                                    $badgeBobot = 'info';
                                                }elseif($data->grade == 0){
                                                    $badgeBobot = 'dark';
                                                }else{
                                                    $badgeBobot = 'success';
                                                }
                                            ?>
                                            <br><span class="badge badge-{{ $badgeBobot }}"><small>Bobot: </small>{{ $data->grade }}%</span>
                                        </div>
                                        
                                        <ul class='list-inline'>
                                            <li class='list-inline-item text-center'>
                                                @if($data->publisher_type == 'admin')
                                                    @foreach($admins as $dataInput)
                                                        @if($dataInput->id == $data->task_publisher_id)
                                                            <a href='' data-toggle='tooltip' class='mt-0' data-placement='top'
                                                                title='' data-original-title="{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}">
                                                                <img src="{{ asset('admintheme/images/users/'.$dataInput->image) }}" alt="{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}" class='avatar-md rounded-circle'>
                                                            </a> <br>
                                                            <span><strong>{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}</strong></span>
                                                        @endif
                                                    @endforeach
                                                @elseif($data->publisher_type == 'user')
                                                    @foreach($users as $dataInput)
                                                        @if($dataInput->id == $data->task_publisher_id)
                                                            <a href='' data-toggle='tooltip' class='mt-0' data-placement='top'
                                                                title='' data-original-title="{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}">
                                                                <img src="{{ asset('admintheme/images/users/'.$dataInput->image) }}" alt="{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}" class='avatar-md rounded-circle'>
                                                            </a> <br>
                                                            <span><strong>{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}</strong></span>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <br>
                                                <small class='text-muted'>{{ date('l, d M Y',strtotime($data->task_date)) }}</small>
                                            </li>
                                            <li class='list-inline-item'>
                                                <h5 class='mt-1'>
                                                    <a href="#" class='text-dark'> </a>
                                                </h5>
                                                <h5 class='t-gray-1'>{{ ucfirst($data->task_title) }}</h5>
                                                <?php /* {!! \Illuminate\Support\Str::limit(ucfirst($data->task_desc),133,'...') !!} */ ?>
                                            </li>
                                        </ul>
                                        <div style="display:block">
                                            @if ($data->task_status != 3)
                                                <form action="{{ route('task.update', $data->task_id) }}" style="display:inline-block" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                    @csrf
                                                    @method('PUT')

                                                    <input class="form-control" type="text" name="nama_barang" value="{{ ucfirst($data->task_title) }}" hidden>
                                                    <input class="form-control" type="number" name="receiver_id" value="{{ $data->task_publisher_id }}" hidden>
                                                    <input class="form-control" type="text" name="receiver_type" value="{{ $data->publisher_type }}" hidden>
                                        
                                                    <input class="form-control" type="number" name="status" value="3" hidden>
                                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-magic' title='done'> </i> ubah ke selesai</button>
                                                </form>
                                            @endif
                                        
                                            <a href="{{ route($formRouteShow, $data->task_id) }}" class='btn btn-warning'><i class='fas fa-eye'></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $i1++; ?>
                            <?php 
                                if ($i1 % 2 == 0) {
                                    echo "<div class='w-100'></div>";
                                }
                            ?>
                        @endforeach
                        <div class="col-12">
                            <?php 
                                #$projects->setPath('teamuser?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                                echo $tasks;
                            ?>
                            <?php $paginator = $tasks; ?>
                            @include('includes.paginator')
                        </div>
                    </div>
                </div> <!-- container-fluid -->
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah task</a>
                    <a href="{{ route($formRouteCollaborationIndex) }}" class="btn btn-orange"><i class="fa fa-eye"></i> Collaboration</a>
                </div>
            </div>
        </div> <!-- card -->
        @endif
        @if($countDataTwo > 0)
        <div class="card mg-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-info float-left'>{{ $countDataTwo }}</div>
                <span class="text-uppercase"><strong>Penugasan-penugasan Anda</strong></span>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="container-fluid">
                    <div class="row">
                        <?php $i2 = 0; ?>
                        @foreach ($tasksTwo as $data1)
                            <div class="col-md">
                                <div class="card-box">
                                    <?php 
                                        if($data1->task_status == 3):
                                            $statusCSS = "secondary";
                                        else:
                                            $statusCSS = "warning";
                                        endif
                                    ?>
                                    <div class="alert alert-{{ $statusCSS }}">
                                        <div class="float-right text-right">
                                            @if($data1->task_status == 3)
                                                <span class="badge badge-success">Done</span>
                                            @else
                                                <span class="badge badge-danger">On progress</span>
                                            @endif
                                            <br><span class='badge badge-{{ $statusBadge[$data1->task_level] }}'>{{ ucfirst($data1->task_level_title) }}</span>
                                            <?php
                                                if ($data1->grade > 60) {
                                                    $badgeBobot = 'danger';
                                                }elseif($data1->grade >= 30 && $data1->grade <= 60){
                                                    $badgeBobot = 'info';
                                                }elseif($data1->grade == 0){
                                                    $badgeBobot = 'dark';
                                                }else{
                                                    $badgeBobot = 'success';
                                                }
                                            ?>
                                            <br><span class="badge badge-{{ $badgeBobot }}"><small>Bobot: </small>{{ $data1->grade }}%</span>
                                        </div>
                                        
                                        <ul class='list-inline'>
                                            <li class='list-inline-item text-center'>
                                                @if($data1->receiver_type == 'admin')
                                                    @foreach($admins as $dataInput)
                                                        @if($dataInput->id == $data1->task_receiver_id)
                                                            <a href='' data-toggle='tooltip' class='mt-0' data-placement='top'
                                                                title='' data-original-title="{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}">
                                                                <img src="{{ asset('admintheme/images/users/'.$dataInput->image) }}" alt="{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}" class='avatar-md rounded-circle'>
                                                            </a> <br>
                                                            <span><strong>{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}</strong></span>
                                                        @endif
                                                    @endforeach
                                                @elseif($data1->receiver_type == 'user')
                                                    @foreach($users as $dataInput)
                                                        @if($dataInput->id == $data1->task_receiver_id)
                                                            <a href='' data-toggle='tooltip' class='mt-0' data-placement='top'
                                                                title='' data-original-title="{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}">
                                                                <img src="{{ asset('admintheme/images/users/'.$dataInput->image) }}" alt="{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}" class='avatar-md rounded-circle'>
                                                            </a> <br>
                                                            <span><strong>{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}</strong></span>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <br>
                                                <small class='text-muted'>{{ date('l, d M Y', strtotime($data1->task_date)) }}</small>
                                            </li>
                                            <li class='list-inline-item'>
                                                <h5 class='mt-1'>
                                                    <a href="#" class='text-dark'> </a>
                                                </h5>
                                                <h5 class='t-gray-1'>{{ ucfirst($data1->task_title) }}</h5>
                                                <?php /* {!! \Illuminate\Support\Str::limit(ucfirst($data1->task_desc),133,'...') !!} */ ?>
                                            </li>
                                        </ul>
                                        <div style="display:block">
                                            <a href="{{ route($formRouteEdit, $data1->task_id) }}" class='btn btn-icon waves-effect waves-light btn-orange t-white'> <i class='fas fa-edit' title='done'> </i></a>
                                            
                                            <a href="{{ route($formRouteShow, $data1->task_id) }}" class='btn btn-warning'><i class='fas fa-eye'></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $i2++; ?>
                            <?php 
                                if ($i2 % 2 == 0) {
                                    echo "<div class='w-100'></div>";
                                }
                            ?>
                        @endforeach
                        <div class="col-12">
                            <?php 
                                #$projects->setPath('teamuser?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                            ?>
                            <?php $paginator = $tasksTwo; ?>
                            @include('includes.paginator')
                        </div>
                    </div>
                </div> <!-- container-fluid -->
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah task</a>
                    <a href="{{ route($formRouteCollaborationIndex) }}" class="btn btn-orange"><i class="fa fa-eye"></i> Collaboration</a>
                </div>
            </div>
        </div> <!-- card -->
        @endif
    @else
        <div class="card">
            <div class="card-header text-center text-uppercase bb-orange"><strong>{{ ucfirst($pageTitle) }}</strong></div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah task</a>
                    <a href="{{ route($formRouteCollaborationIndex) }}" class="btn btn-orange"><i class="fa fa-eye"></i> Collaboration</a>
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
