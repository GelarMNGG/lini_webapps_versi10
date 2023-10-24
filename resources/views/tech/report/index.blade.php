@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar template laporan';
    //back
        $formRouteBack = 'project-tech.show';
    //form report
        $formReportShow = 'report-tech.show';
        $formReportIndex = 'report-tech.index';
    //additional setting
        $statusBadge    = array('dark','danger','info','success','purple','pink','warning');
    //report qc
        $formReportQcShow = 'tech-report-qc.show';
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

    @if (isset($projectTemplateDatas))
    <div class="card-body bg-gray-lini">
        <div class="row m-0">
            @if(sizeof($projectTemplateDatas) > 0)
                <?php $separator=1; ?>
                @foreach($projectTemplateDatas as $projectTemplateData)
                    <div class="col-sm p-2">
                        <div class="bg-card-box br-5 p-2">
                        {{ isset($projectTemplateData->name) ? ucwords($projectTemplateData->name) : 'Belum ada data' }}
                            <br><span class="text-info">{{ isset($projectTemplateData->task_name) ? strtoupper($projectTemplateData->task_name) : 'Belum ada data' }}</span> 
                            <div>
                                <a href="{{ route($formReportShow, $projectTemplateData->template_id.'?project_id='.$projectTemplateData->project_id.'&task_id='.$projectTemplateData->task_id) }}" class='btn btn-icon waves-effect waves-light btn-success mt-1 mb-1'> <i class='fas fa-edit' title='Edit'></i> Buat laporan</a>
                            </div>
                        </div>
                    </div>
                    <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                    <?php $separator++; ?>
                @endforeach
                <div class="col-md">
                    <?php 
                        $projectTemplateDatas->setPath('report-tech?project_id='.$infoTaskProject->project_id.'&task_id='.$infoTaskProject->id);
                    ?>
                    <?php $paginator = $projectTemplateDatas; ?>
                    @include('includes.paginator')
                </div>
            @endif
        </div>
    </div>
    @else
        <div class="alert alert-warning">Belum ada data.</div>
    @endif
    
    <div class="card-body">
        <div class="col-md">
            <a href="{{ route($formReportQcShow, $infoTaskProject->id.'?project_id='.$infoTaskProject->project_id) }}" class="btn btn-orange"><i class="fa fa-eye"></i> Lihat laporan proyek</a>
            <a href="{{ route($formRouteBack, $projectTemplateData->task_id) }}" class="btn btn-blue-lini">Kembali</a>
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
