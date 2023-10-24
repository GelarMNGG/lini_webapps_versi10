@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar template';
    //back
    $formRouteBack = 'admin-projects.show';

    //form route
    $formRouteCreate = 'admin-projects-template.create';
    $formTemplateEdit = 'admin-projects-template.edit';
    $formTemplateDestroy = 'admin-projects-template.destroy';
    $formTemplateShow = 'admin-projects-template.show';

    //additional setting
    $statusBadge    = array('dark','danger','info','success','purple','pink','warning');
?>
@endsection

@section('content')
<div class="flash-message">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<div class="card">
    <div class="card-header text-center">
        @if(isset($infoTaskProject->project_name))
            Proyek: <strong><span class="text-info">{{ isset($infoTaskProject->project_name) ? strtoupper($infoTaskProject->project_name) : '' }}</span></strong>
            <br>Task: <strong><span class="text-danger">{{ isset($infoTaskProject->name) ? strtoupper($infoTaskProject->name) : 'Belum ada task' }}</span></strong>
        @else
            <span class="text-uppercase text-info">Template yang tersedia <strong>({{ $projectTemplateCount }})</strong></span>
        @endif
    </div>

    <div class="card-body">

        @if(isset($project))
            <!-- template for current project -->
            <div class="col-md mb-1"><strong class="text-info">Template pada proyek ini</strong></div>
            @if (count($projectTemplateDatas) > 0)
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="" class="display table table-bordered table-bordered dt-responsive">
                                    <thead>
                                        <tr>
                                        <th>#</th>
                                        <th>Nama template</th>
                                        <th>Task</th>
                                        <th>Dibuat oleh</th>
                                        <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>
                                        @foreach ($projectTemplateDatas as $projectTemplateData)
                                            <tr>
                                                <td> {{ $i }} </td>
                                                <td>{{ isset($projectTemplateData->name) ? ucwords($projectTemplateData->name) : 'Belum ada data' }}</td>
                                                <td>{{ isset($projectTemplateData->task_name) ? strtoupper($projectTemplateData->task_name) : 'Belum ada data' }}</td>
                                                <td>
                                                    @if($projectTemplateData->publisher_id != null)
                                                        @if($projectTemplateData->publisher_type == 'user')
                                                            @foreach($dataUsers as $dataPM)
                                                                @if($dataPM->id == $projectTemplateData->publisher_id)
                                                                    <span class="text-success">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) }}</span>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <span class="text-success">You</span>
                                                        @endif

                                                    @else
                                                        <span class="text-danger">Belum ada data</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    
                                                    <form action="{{ route($formTemplateDestroy, $projectTemplateData->id) }}" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                        <!-- hidden data -->
                                                        <input name="project_id" value="{{ $project->id }}" hidden>
                                                        <input name="task_id" value="{{ $projectTemplateData->task_id }}" hidden>

                                                        <a href="{{ route($formTemplateEdit, $projectTemplateData->id) }}" class='btn btn-icon waves-effect waves-light btn-info mt-1 mb-1'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>
                                                        
                                                        
                                                        <button type="submit" class="btn btn-danger mt-1 mb-1" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>
                                                    </form>

                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- container-fluid -->
            @else
                <div class="alert alert-warning">Belum ada data.</div>
            @endif
            <hr>
            <!-- all available template -->
            <div class="col-md mb-1"><strong class="text-info">Template yang tersedia</strong></div>
        @endif
        @if (count($allProjectTemplates) > 0)
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                                <thead>
                                    <tr>
                                    <th>#</th>
                                    <th>Nama template</th>
                                    <th>Dibuat oleh</th>
                                    <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach ($allProjectTemplates as $allProjectTemplate)
                                        <tr>
                                            <td> {{ $i }} </td>
                                            <td>{{ isset($allProjectTemplate->name) ? strtoupper($allProjectTemplate->name) : 'Belum ada data' }} ({{ $allProjectTemplate->subcatCount }} subkategori)</td>
                                            <td>
                                                @if($allProjectTemplate->publisher_id != null)
                                                    @if($allProjectTemplate->publisher_type == 'user')
                                                        @foreach($dataUsers as $dataPM)
                                                            @if($dataPM->id == $allProjectTemplate->publisher_id)
                                                                <span class="text-success">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) }}</span>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <span class="text-success">You</span>
                                                    @endif

                                                @else
                                                    <span class="text-danger">Belum ada data</span>
                                                @endif
                                            </td>
                                            <td>
                                            <?php 
                                                $taskCount = count($projectTaskDatas);
                                            ?>
                                                <form action="{{ route($formTemplateDestroy, $allProjectTemplate->id) }}" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                    @if(isset($project->id))
                                                        <!-- hidden data -->
                                                        <input name="project_id" value="{{ $infoTaskProject->project_id }}" hidden>

                                                        <a href="{{ route($formTemplateShow, $allProjectTemplate->id.'?project_id='.$project->id.'&task_id='.$infoTaskProject->id) }}" class='btn btn-icon waves-effect waves-light btn-warning t-white mt-1 mb-1'> <i class='fas fa-eye' title='Show'></i> Show</a>
                                                    @else
                                                        <a href="{{ route($formTemplateShow, $allProjectTemplate->id) }}" class='btn btn-icon waves-effect waves-light btn-warning t-white mt-1 mb-1'> <i class='fas fa-eye' title='Show'></i> Show</a>
                                                    @endif

                                                    <?php /*
                                                    @if(Auth::user()->id == $allProjectTemplate->publisher_id)
                                                        <a href="{{ route($formTemplateEdit, $allProjectTemplate->id) }}" class='btn btn-icon waves-effect waves-light btn-info mt-1 mb-1'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>
                                                    @endif
                                                    */?>
                                                </form>
                                                
                                            </td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> <!-- container-fluid -->
        @else
            <div class="alert alert-warning">Belum ada data.</div>
        @endif

        <!-- back button -->
        <div class="col-md mt-2">
            @if(isset($project))
                <a href="{{ route($formRouteBack, $project->id) }}" type="button" class="btn btn-secondary">Kembali</a>
            @else
                <a href="{{ route($formRouteBack) }}" type="button" class="btn btn-secondary">Kembali</a>
            @endif

        </div>

    </div>
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
