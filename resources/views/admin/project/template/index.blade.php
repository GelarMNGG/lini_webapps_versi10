@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar template';

    //back
    $formRouteIndex = 'admin-projects.index';
    $formRouteShow = 'admin-projects.show';

    //form template
    $formTemplateIndex = 'admin-projects-template.index';
    $formTemplateCreate = 'admin-projects-template.create';
    $formTemplateEdit = 'admin-projects-template.edit';
    $formTemplateDestroy = 'admin-projects-template.destroy';
    $formTemplateShow = 'admin-projects-template.show';

    //image
    #$formImageCreate = 'admin-projects-image.show';
    $formImageReport = 'admin-projects-image.report';

    //additional setting
    $statusBadge    = array('dark','danger','info','success','purple','pink','warning');
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
    <div class="card-header text-center bb-orange">
        @if(isset($infoTaskProject->project_name))
            <small>Proyek: </small><strong><span class="text-info">{{ isset($infoTaskProject->project_name) ? strtoupper($infoTaskProject->project_name) : '' }}</span></strong>
            <br><small>Task:</small> <strong><span class="text-danger">{{ isset($infoTaskProject->name) ? strtoupper($infoTaskProject->name) : 'Belum ada task' }}</span></strong>
        @else
            <span class="text-uppercase text-info">Template yang tersedia <strong>({{ $projectTemplateCount }})</strong></span>
        @endif
    </div>

    <div class="card-body bg-gray-lini-2">
        <div class="col-md">
            <strong class="text-info">Template pada proyek ini</strong>
        </div>
        <div class="row">
            @if(isset($project))
                <!-- template for current project -->
                @if (count($projectTemplateDatas) > 0)
                    <?php $separator=1; ?>
                    @foreach ($projectTemplateDatas as $projectTemplateData)
                        <div class="col-sm p-2">
                            <div class="bg-card-box br-5 p-2">
                                <span class="text-danger"><strong>{{ isset($projectTemplateData->name) ? strtoupper($projectTemplateData->name) : 'Belum ada data' }}</strong></span>

                                @if($projectTemplateData->imageCount > 0)
                                    <span class="text-success">({{ $projectTemplateData->imageCount }} gambar terupload)</span>
                                @endif

                                <br><span class="text-info">{{ isset($projectTemplateData->task_name) ? strtoupper($projectTemplateData->task_name) : 'Belum ada data' }}</span>

                                <br>Dibuat oleh: 
                                @if($projectTemplateData->publisher_id != null)
                                    @if($projectTemplateData->publisher_type == 'admin')
                                        @foreach($dataadmins as $dataPM)
                                            @if($dataPM->id == $projectTemplateData->publisher_id)
                                                <span class="text-success">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) }}</span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="text-success">You</span>
                                    @endif

                                @else
                                    <span class="text-danger">-</span>
                                @endif

                                <div>
                                    <form action="{{ route($formTemplateDestroy, $projectTemplateData->id) }}" method="POST" style="display:inline-block;">
                                        @method('DELETE')
                                        @csrf
                                        <!-- hidden data -->
                                        <input name="project_id" value="{{ $project->id }}" hidden>
                                        <input name="task_id" value="{{ $projectTemplateData->task_id }}" hidden>

                                        <a href="{{ route($formTemplateEdit, $projectTemplateData->id) }}" class='btn btn-icon waves-effect waves-light btn-info mt-1 mb-1'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>

                                        @if($projectTemplateData->imageCount < 1)
                                            <button type="submit" class="btn btn-danger mt-1 mb-1" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>
                                        @endif
                                        @if($projectTemplateData->approvedImageCount > 0)
                                            <a href="{{ route($formImageReport, $projectTemplateData->template_id.'?project_id='.$project->id.'&task_id='.$infoTaskProject->id) }}" class="btn btn-warning mt-1 mb-1"><i class="fas fa-file-signature"></i> Lihat laporan foto</a>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                        <?php $separator++; ?>
                    @endforeach
                @else
                    <div class="card-body">
                        <div class="alert alert-warning">Belum ada data.</div>
                    </div>
                @endif
                
            @endif
        </div>
        <hr>
    </div>    
    <div class="card-body bg-gray-lini-2">
        <div class="col-md"><strong class="text-info">Template yang tersedia</strong></div>
        <div class="row">
            @if (count($allProjectTemplates) > 0)
                <?php $separator1=1; ?>
                @foreach ($allProjectTemplates as $allProjectTemplate)
                    <div class="col-sm p-2">
                        <div class="bg-card-box br-5 p-2">
                            <span class="text-danger"><strong>{{ isset($allProjectTemplate->name) ? strtoupper($allProjectTemplate->name) : 'Belum ada data' }}</strong></span> 
                            
                            <span class="text-info">({{ $allProjectTemplate->subcatCount }} subkategori)</span>
                            
                            <br>
                            @if($allProjectTemplate->publisher_id != null)
                                @if($allProjectTemplate->publisher_type == 'admin')
                                    @foreach($dataadmins as $dataPM)
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
                                
                            <br>
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
                                @if(Auth::admin()->id == $allProjectTemplate->publisher_id)
                                    <a href="{{ route($formTemplateEdit, $allProjectTemplate->id) }}" class='btn btn-icon waves-effect waves-light btn-info mt-1 mb-1'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>
                                @endif
                                */?>
                            </form>
                        </div>
                    </div>  
                    <?php if($separator1 % 2 == 0){echo "<div class='w-100'></div>";} ?>
                    <?php $separator1++; ?>      
                @endforeach
                <div class="col-12">
                    <?php 
                        $allProjectTemplates->setPath('admin-projects-template?project_id='.$infoTaskProject->project_id.'&task_id='.$infoTaskProject->id);
                    ?>
                    <?php $paginator = $allProjectTemplates; ?>
                    @include('includes.paginator')
                </div>
            @else
                <div class="alert alert-warning">Belum ada data.</div>
            @endif
        </div>

    </div>
    <div class="card-body">
        <div class="col-md">
            <a href="{{ route($formRouteShow,$project->id) }}" class="btn btn-blue-lini">Kembali</a>
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
