@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Log pembuatan aplikasi';
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $statusAppsBadge    = array('','info','purple','danger','success');
    //route
    $formRouteIndex = 'apps-dev-logs.index';
    $formRouteCreate = 'apps-dev-logs.create';
    $formRouteEdit = 'apps-dev-logs.edit';
    $formRouteDestroy = 'apps-dev-logs.destroy';
    //report
    $formReportRouteCreate = 'apps-dev-logs-report.create';
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

<div class="card">
    <div class="card-header text-center text-uppercase bb-orange">
        <div class='badge badge-info float-left'>{{ count($appsDevLogsDatas) }}</div>
        @if (isset($requestProgrammer) || isset($requestDepartment) || isset($requestStatus))
            @if(isset($requestProgrammer))
                @if(isset($requestStatus))
                    <a href="{{ route ($formRouteIndex,'sid='.$requestStatus.'&pid='.$requestProgrammer.'&skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
                @elseif(isset($requestDepartment))
                    <a href="{{ route ($formRouteIndex,'did='.$requestDepartment.'&pid='.$requestProgrammer.'&skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
                @else
                    <a href="{{ route ($formRouteIndex,'pid='.$requestProgrammer.'&skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
                @endif
            @elseif(isset($requestStatus))
                @if(isset($requestProgrammer))
                    <a href="{{ route ($formRouteIndex,'sid='.$requestStatus.'&pid='.$requestProgrammer.'&skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
                @elseif(isset($requestDepartment))
                    <a href="{{ route ($formRouteIndex,'did='.$requestDepartment.'&sid='.$requestStatus.'&skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
                @else
                <a href="{{ route ($formRouteIndex,'sid='.$requestStatus.'&skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
                @endif
            @else
                <a href="{{ route ($formRouteIndex,'did='.$requestDepartment.'&skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
            @endif
        @else
            <a href="{{ route ($formRouteIndex,'skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
        @endif
    </div>

    @if (isset($appsDevLogsDatas))
    <div class="card-body bg-gray-lini-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama task</th>
                                    <th>Tanggal mulai</th>
                                    <th>Tanggal selesai</th>
                                    @if(Auth::user()->department_id == 5)
                                        <th>Departement</th>
                                    @endif
                                    <th>Programmer</th>
                                    <th>Status</th>
                                    @if(Auth::user()->department_id == 5)
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($appsDevLogsDatas as $dataLog)
                                <?php $ia = 1; ?>
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>
                                            <?php 
                                                if ($dataLog->status == 1) {
                                                    $cssTitle = 'info';
                                                }elseif($dataLog->status == 0){
                                                    $cssTitle = 'danger';
                                                }elseif($dataLog->status > 1 && $dataLog->status < 4){
                                                    $cssTitle = 'warning';
                                                }else{
                                                    $cssTitle = 'dark';
                                                }
                                            ?>
                                            <span class="text-{{ $cssTitle }}"><strong>{{ ucfirst($dataLog->name) }}</strong></span>
                                        </td>
                                        <td>
                                            @if($dataLog->date != null)
                                                <span class="text-info">{{ date('l, d F Y', strtotime($dataLog->date)) }}</span>
                                                | <small>{{ $dataLog->event_end ? date('H:i a', strtotime($dataLog->event_end)) : 'Jam tidak tersedia' }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($dataLog->done_date != null)
                                                <span class="text-success">{{ date('l, d F Y', strtotime($dataLog->done_date)) }}</span>
                                                | <small>{{ $dataLog->event_end ? date('H:i a', strtotime($dataLog->event_end)) : 'Jam tidak tersedia' }}</small>
                                            @else
                                                <span class="text-danger">-</span>
                                            @endif
                                        </td>
                                        @if(Auth::user()->department_id == 5)
                                            <td>
                                                @if (isset($dataLog->department_id))
                                                    @foreach ($departmensDatas as $dataOne)
                                                        @if ($dataOne->id == $dataLog->department_id)
                                                            <span class="text-danger"><small>[{{ ucwords(strtolower($dataOne->name)) }}]</small></span>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            @if (isset($dataLog->programmer_id))
                                                @foreach ($programmersDatas as $dataThree)
                                                    @if ($dataThree->id == $dataLog->programmer_id)
                                                        <small><span class="text-info">{{ ucwords(strtolower($dataThree->firstname)).' '.ucwords(strtolower($dataThree->lastname)) }}</span></small>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            <?php if($ia == 4){ $ia = 1;} ?>
                                            @if ($dataLog->status > 0)
                                                @foreach ($appsStatusDatas as $dataTwo)
                                                    @if ($dataTwo->id == $dataLog->status)
                                                        <span class="badge badge-{{ $statusAppsBadge[$dataLog->status] }}">
                                                            {{ ucwords(strtolower($dataTwo->name)) }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @endif
                                            <?php
                                                if ($dataLog->percentage <= 10) {
                                                    $badge = 'danger';
                                                }elseif($dataLog->percentage <= 35){
                                                    $badge = 'warning';
                                                }elseif($dataLog->percentage <= 85){
                                                    $badge = 'info';
                                                }else{
                                                    $badge = 'success';
                                                }
                                            ?>
                                            @if ($dataLog->status == 4)
                                                <span class="badge badge-success">
                                                    100<small>%</small>
                                                </span>
                                            @else
                                                <span class="badge badge-{{ $badge }}">
                                                    {{ $dataLog->percentage }}<small>%</small>
                                                </span>
                                            @endif
                                        </td>
                                        @if(Auth::user()->department_id == 5)
                                            <td>
                                                <!-- IT department feature -->
                                                @if(Auth::user()->department_id == 5)
                                                    <form action="{{ route($formRouteDestroy, $dataLog->id) }}" method="POST" style="display:inline;">
                                                        @method('DELETE')
                                                        @csrf

                                                        <a href="{{ route($formRouteEdit, $dataLog->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i></a>

                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                                    </form>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- container-fluid -->
    </div>
    <div class="card-body">
        <div class="col-md">
            <a href="{{ route($formReportRouteCreate) }}" class="btn btn-orange" type="button"><i class="fa fa-plus"></i> Create apps development report</a>
        </div>
    </div>
    @else
    <div class="card-body bg-gray-lini-2">
        <div class="alert alert-warning">Belum ada data.</div>
    </div>
    @endif
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
