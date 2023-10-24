@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar template laporan';
    //back
    $formRouteBack = 'user-projects.show';

    //form template
    $formTemplateCreate = 'user-projects-template.create';
    $formTemplateEdit = 'user-projects-template.edit';
    $formTemplateDestroy = 'user-projects-template.destroy';
    $formTemplateShow = 'user-projects-template.show';

    //select image
    $formImageIndex = 'user-projects-image.index';
    $formImageCreate = 'user-projects-image.create';
    $formImageEdit = 'user-projects-image.edit';
    $formImageShow = 'user-projects-image.show';
    $formImageReport = 'user-projects-image.report';

    //report
    $formReportCreate = 'user-projects-report.create';
    $formReportShow = 'user-projects-report.show';

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
        <small>Proyek:</small> <strong><span class="text-info">{{ isset($infoTaskProject->project_name) ? strtoupper($infoTaskProject->project_name) : '' }}</span></strong>
        <br><small>Task:</small> <strong><span class="text-danger">{{ isset($infoTaskProject->name) ? strtoupper($infoTaskProject->name) : 'Belum ada task' }}</span></strong>
    </div>

    <div class="bg-gray-lini-2">
        <div class="card-body">
            <!-- template for current project -->
            @if (count($projectTemplateDatas) > 0)
                <div class="container-fluid">
                    <div class="row">
                        <?php $separator=1; ?>
                        @foreach ($projectTemplateDatas as $projectTemplateData)
                            <div class="col-sm p-2">
                                <div class="bg-card-box br-5 p-2">
                                    <span class="text-danger"><strong>{{ isset($projectTemplateData->name) ? strtoupper($projectTemplateData->name) : 'Belum ada data' }}</strong></span>
                                    <br><span class="text-info">{{ isset($projectTemplateData->task_name) ? ucwords($projectTemplateData->task_name) : 'Belum ada data' }}</span>

                                    <br>Dibuat oleh: 
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

                                    <div>
                                        <!-- gambar -->
                                        <a href="{{ route($formImageShow, $projectTemplateData->template_id.'?project_id='.$projectTemplateData->project_id.'&task_id='.$projectTemplateData->task_id) }}" class='btn btn-icon waves-effect waves-light btn-info mt-1 mb-1'> <i class='fas fa-eye' title='View'></i> Approve foto</a>
                                                            
                                        @if($projectTemplateData->imgApprovedCount > 0)
                                            <a href="{{ route($formImageReport, $projectTemplateData->template_id.'?project_id='.$infoTaskProject->project_id.'&task_id='.$infoTaskProject->id) }}" class='btn btn-icon waves-effect waves-light btn-warning mt-1 mb-1'> <i class='fas fa-images' title='Edit'></i> Lihat laporan</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                            <?php $separator++; ?>
                        @endforeach
                    </div>
                </div> <!-- container-fluid -->
            @else
                <div class="alert alert-warning">Belum ada data.</div>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="col-md mt-2">
            <div class="form-group">
                <a href="{{ route($formRouteBack, $infoTaskProject->id) }}" class="btn btn-blue-lini">Kembali</a>
            </div>
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
