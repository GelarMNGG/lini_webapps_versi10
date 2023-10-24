@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle     = 'Daftar Purchase Requisition';
    $formRouteEdit = 'admin-pr.edit';
    $formRouteShow = 'admin-pr.show';
    //project
    $formProjectRouteEdit= 'admin-projects.edit';
    $formProjectRouteProgress= 'admin-projects.progress';
    $formProjectRouteShow= 'admin-projects.show';
    //project task
    $formProjectTaskEdit= 'admin-projects-task.edit';
    //other setting
    $statusBadge    = array('dark','info','danger','success','warning','purple','pink');
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
    <div class="card-header text-center text-uppercase bb-orange"><strong>{{ ucfirst($pageTitle) }}</strong></div>

    @if (isset($prDatas))
        <div class="card-body bg-gray-lini-2">
            <div class="row">
                <?php $separator=1; ?>
                @foreach ($prDatas as $data)
                    <div class="col-sm p-2">
                        <div class="bg-card-box br-5 p-2">
                            <span class="text-danger"><strong>{{ strtoupper($data->project_name) }}</strong></span>
                            @if($data->status == 2)
                                <span class="badge badge-danger float-right">Minta approval</span>
                            @else
                                @foreach($prStatus as $dataStatus)
                                    @if($dataStatus->id == $data->status)
                                    <span class="badge badge-{{ $statusBadge[$dataStatus->id] }} float-right">{{ ucwords($dataStatus->name) }}</span>
                                    @endif
                                @endforeach
                            @endif
                            
                            <br>Task: <span class="text-info">{{ ucwords($data->task_name ?? '-') }}</span>
                            <br>Nama: <span class="text-success">{{ ucwords($data->name) ?? '-' }}</span>
                            
                            <br>Diajukan: {{ $data->date_submitted ? date('l, d F Y', strtotime($data->date_submitted)) : '-' }}
                            
                            <br>Disetujui: {{ $data->date_approved ? date('l, d F Y', strtotime($data->date_approved)) : '-' }}
                            
                            <br>Teknisi: 
                            @if($data->tech_id != null)
                                @foreach($dataTeknisis as $dataTeknisi)
                                    @if($dataTeknisi->id == $data->tech_id)
                                        <span class="text-success">{{ ucwords($dataTeknisi->firstname).' '.ucwords($dataTeknisi->lastname) }}</span>
                                    @endif
                                @endforeach
                            @else
                                <span class="text-danger">-</span>
                            @endif

                            <div>
                                <?php /*
                                    @if($data->status < 2)
                                        <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white mb-1'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>
                                    @endif
                                */ ?>

                                <?php /*
                                    <a href="#" class='btn btn-icon waves-effect waves-light btn-info mb-1'> <i class='fas fa-bullhorn' title='Lihat'></i> Publish PR</a>
                                */ ?>

                                <?php /*
                                    <a href="{{ route('admin-wo.show', $data->id) }}" class='btn btn-icon waves-effect waves-light btn-danger mb-1'> <i class='fas fa-id-badge' title='wo'></i> Terbitkan WO</a>
                                */ ?>

                                @if($data->status == 3)
                                    @if($data->tech_id != null)
                                        <a href="{{ route($formProjectTaskEdit, $data->task_id) }}" class='btn btn-icon waves-effect waves-light btn-info mb-1'> <i class='fas fa-user' title='Edit'></i> Edit Teknisi</a>
                                    @else
                                        <a href="{{ route($formProjectTaskEdit, $data->task_id) }}" class='btn btn-icon waves-effect waves-light btn-success mb-1'> <i class='fas fa-user' title='Tunjuk'></i> Tunjuk Teknisi</a>
                                    @endif
                                @endif

                                <a href="{{ route($formRouteShow, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-warning mb-1'> <i class='fas fa-eye' title='Lihat'></i> Lihat PR</a>

                                <?php /*
                                    @if($data->project_status == 3)
                                        <a href="{{ route($formProjectRouteShow, $data->project_id) }}" class='btn btn-icon waves-effect waves-light btn-warning mb-1'> <i class='fas fa-eye' title='Lihat'></i> Lihat laporan proyek</a>
                                    @else
                                        <a href="{{ route($formProjectRouteProgress, $data->project_id) }}" class='btn btn-icon waves-effect waves-light btn-warning mb-1'> <i class='fas fa-eye' title='Lihat'></i> Lihat progress</a>

                                        <a href="#" class='btn btn-icon waves-effect waves-light btn-warning mb-1'> <i class='fas fa-eye' title='Lihat'></i> Lihat laporan expenses</a>
                                    @endif
                                */ ?>
                            </div>
                        </div>
                    </div>
                    <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                    <?php $separator++; ?>
                @endforeach 
                <div class="col-12">
                    <?php 
                        #$projects->setPath('teamuser?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                    ?>
                    <?php $paginator = $prDatas; ?>
                    @include('includes.paginator')
                </div>
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
        $('table.display').DataTable(
            // "order":[]
        );
    } );
</script>
@endsection
