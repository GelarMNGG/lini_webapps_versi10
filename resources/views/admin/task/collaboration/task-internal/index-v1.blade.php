@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Internal Collaboration Tasks';
    if (Auth::user()->department_id == 11) {
        $title1 = 'Collaboration Tasks list';
        $title2 = 'Collaboration Tasks list';
    }else{
        $title1 = 'Your check list';
        $title2 = 'Your requests list';
    }
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');

    //route
    $formRouteCreate = 'task-internal.create';
    $formRouteEdit = 'task-internal.edit';
    $formRouteShow = 'task-internal.show';

    //multi dept collaboration task
    $formMultiDeptCollaborationIndex = 'task-leaders.index';

    //collaboration
    $formRouteBack = 'admin-collaboration.index';
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
        @if($countData > 0)
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-info float-left'>{{ $countData }}</div>
                <span class="text-uppercase"><strong>{{ $title1 }}</strong></span>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="container-fluid">
                    <div class="row">
                        <?php $i = 0; ?>
                        @foreach ($tasks as $data)
                            <div class="col-md">
                                <div class="card-box">
                                    <?php 
                                        if($data->status == 3):
                                            $statusCSS = "secondary";
                                        else:
                                            $statusCSS = "warning";
                                        endif
                                    ?>
                                    <div class="alert alert-{{ $statusCSS }}">
                                        <div class="float-right text-right">
                                            @if($data->status == 1)
                                                <span class='badge badge-success float-right'>Done</span>
                                            @else
                                                <?php 
                                                    $total = $data->onprogress_count + $data->done_count;
                                                    $progressCount = $data->done_count;
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
                                                <h2 class="{{ $cssStatus }}">[ {{ $data->done_count.'/'.$total }} ]</h2>
                                            @endif

                                            <span class='badge badge-{{ isset($statusBadge[$data->level]) ? $statusBadge[$data->level] : 1 }} float-right'>{{ ucfirst($data->level_title) }}</span>
                                            <br><a href="{{ route($formRouteShow, $data->id) }}" class='badge badge-danger'><i class='fas fa-eye'></i> Lihat Detail</a>
                                        </div>
                                        
                                        <ul class='list-inline'>
                                            <li class='list-inline-item text-center'>
                                                @if($data->publisher_type == 'admin')
                                                    @foreach($admins as $dataInput)
                                                        @if($dataInput->id == $data->publisher_id)
                                                            <a href='' data-toggle='tooltip' class='mt-0' data-placement='top'
                                                                title='' data-original-title="{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}">
                                                                <img src="{{ asset('admintheme/images/users/'.$dataInput->image) }}" alt="{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}" class='avatar-md rounded-circle'>
                                                            </a> <br>
                                                            <span><strong>{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}</strong></span>
                                                        @endif
                                                    @endforeach
                                                @elseif($data->publisher_type == 'user')
                                                    @foreach($users as $dataInput)
                                                        @if($dataInput->id == $data->publisher_id)
                                                            <a href='' data-toggle='tooltip' class='mt-0' data-placement='top'
                                                                title='' data-original-title="{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}">
                                                                <img src="{{ asset('admintheme/images/users/'.$dataInput->image) }}" alt="{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}" class='avatar-md rounded-circle'>
                                                            </a> <br>
                                                            <span><strong>{{ ucfirst(strtolower($dataInput->firstname)).' '.ucfirst(strtolower($dataInput->lastname)) }}</strong></span>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <br>
                                                <small class='text-muted'>{{ date('l, d M Y',strtotime($data->date_start)) }}</small>
                                            </li>
                                            <li class='list-inline-item'>
                                                <h5 class='mt-1'>
                                                    <a href="#" class='text-dark'> </a>
                                                </h5>
                                                <h5 class='t-gray-1'>{{ \Illuminate\Support\Str::limit(ucfirst($data->title),29,'...') }}</h5>
                                                {!! \Illuminate\Support\Str::limit(ucfirst($data->description),33,'...') !!}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $i++;
                                if ($i % 2 == 0) {
                                    echo "<div class='w-100'></div>";
                                } 
                            ?>
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
                </div> <!-- container-fluid -->
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Kolaborasi internal</a>
                    <a href="{{ route($formMultiDeptCollaborationIndex) }}" class="btn btn-orange"><i class="fa fa-eye"></i> Kolaborasi multi departemen</a>
                    <a href="{{ route($formRouteBack) }}" class="btn btn-blue-lini">Kembali</a>
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
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Kolaborasi internal</a>
                    <a href="{{ route($formMultiDeptCollaborationIndex) }}" class="btn btn-orange"><i class="fa fa-eye"></i> Kolaborasi multi departemen</a>
                    <a href="{{ route($formRouteBack) }}" class="btn btn-blue-lini">Kembali</a>
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
