@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'detail proyek';
    $formRouteIndex = 'user-projects.index';
    $formRouteUpdate= 'user-projects.update';
    //task
        $formTaskStore = 'user-projects-task.store';
        $formTaskEdit = 'user-projects-task.edit';
        $formTaskUpdate = 'user-projects-task.update';
        $formTaskDestroy = 'user-projects-task.destroy';
    //template
        $formTemplateIndex = 'user-projects-template.index';
        $formTemplateShow = 'user-projects-template.show';
        $formTemplateEdit = 'user-projects-template.edit';
        $formTemplateDestroy = 'user-projects-template.destroy';
    //expense route
        $formRouteExpensesIndex = 'user-projects-expense.index';
    //cash advance
        $formRouteCashAdvanceIndex = 'user-projects-ca.index';
    //payment summary
        $formRoutePaymentSummaryIndex = 'user-project-payment-summary.index';
    //PR
        $formPRIndex = 'user-pr.index';
    //PR
        $formToolsIndex = 'user-project-tool.index';
    //PR
        $formCovidCreate = 'user-covid-test.create';
    //project report qc
        $formQCProjectReportShow = 'user-projects-report-qc.show';
    //proejct version two
        $formVTProjectReportShow = 'user-projects-report-vt.show';
    //css setting
        $statusBadge    = array('dark','danger','warning','info','success','purple','pink');
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
        @foreach($projectTaskStatus as $projectStatus)
            @if($projectStatus->id == $projectTask->status)
                <div class="badge badge-{{ $statusBadge[$projectTask->status] }} float-right">{{ $projectStatus->name }}</div>
            @endif
        @endforeach
        <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTask->project_name }}</span></strong>
        <br><small>Task:</small> <strong><span class="text-danger text-uppercase">{{ $projectTask->name }}</span></strong>
        <br><small>No task:</small> <strong><span class="text-warning text-uppercase">{{ $projectTask->number }}</span></strong>
    </div>
    <div class="card-body bg-gray-lini-2">
        <div class="row">
            <div class="col-md mt-2">
                <label>Project Manager</label>
                <?php 
                    //qct_id
                    if(old('pm_id') != null) {
                        $pm_id = old('pm_id');
                    }elseif(isset($projectTask->pm_id)){
                        $pm_id = $projectTask->pm_id;
                    }else{
                        $pm_id = null;
                    }
                ?>
                @if($pm_id != null)
                    @foreach($dataPMs as $dataPM)
                        @if($dataPM->id == $pm_id)
                            <input class="form-control" value="{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname)}}" readonly>
                        @endif
                    @endforeach
                @else
                    <input class="form-control" value="Belum ada data" readonly>
                @endif
            </div>
            <div class="col-md mt-2">
                <label>Project Coordinator</label>
                <?php 
                    //qct_id
                    if(old('pc_id') != null) {
                        $pc_id = old('pc_id');
                    }elseif(isset($projectTask->pc_id)){
                        $pc_id = $projectTask->pc_id;
                    }else{
                        $pc_id = null;
                    }
                ?>
                @if($pc_id != null)
                    @foreach($dataPCs as $dataPC)
                        @if($dataPC->id == $pc_id)
                            <input class="form-control" value="{{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname)}}" readonly>
                        @endif
                    @endforeach
                @else
                    <input class="form-control" value="Belum ada data" readonly>
                @endif
            </div>
            <div class="col-md mt-2">
                <label>Teknisi</label>
                <?php 
                    //qct_id
                    if(old('tech_id') != null) {
                        $tech_id = old('tech_id');
                    }elseif(isset($projectTask->tech_id)){
                        $tech_id = $projectTask->tech_id;
                    }else{
                        $tech_id = null;
                    }
                ?>
                @if($tech_id != null)
                    @foreach($dataTechs as $dataTech)
                        @if($dataTech->id == $tech_id)
                            <input class="form-control" value="{{ ucwords($dataTech->firstname).' '.ucwords($dataTech->lastname)}}" readonly>
                        @endif
                    @endforeach
                @else
                    <input class="form-control" value="Belum ada data" readonly>
                @endif
            </div>
            <div class="w-100"></div>
            <div class="col-md mt-2">
                <label>QC Tools</label>
                <?php 
                    //qct_id
                    if(old('qct_id') != null) {
                        $qct_id = old('qct_id');
                    }elseif(isset($projectTask->qct_id)){
                        $qct_id = $projectTask->qct_id;
                    }else{
                        $qct_id = null;
                    }
                ?>
                @if($qct_id != null)
                    @foreach($dataQCTs as $dataQCT)
                        @if($dataQCT->id == $qct_id)
                            <input class="form-control" value="{{ ucwords($dataQCT->firstname).' '.ucwords($dataQCT->lastname)}}" readonly>
                        @endif
                    @endforeach
                @else
                    <input class="form-control" value="Belum ada data" readonly>
                @endif
            </div>
            <div class="col-md mt-2">
                <label>QC Expenses</label>
                <?php 
                    //qce_id
                    if(old('qce_id') != null) {
                        $qce_id = old('qce_id');
                    }elseif(isset($projectTask->qce_id)){
                        $qce_id = $projectTask->qce_id;
                    }else{
                        $qce_id = null;
                    }
                ?>
                @if($qce_id != null)
                    @foreach($dataQCEs as $dataQCE)
                        @if($dataQCE->id == $qce_id)
                            <input class="form-control" value="{{ ucwords($dataQCE->firstname).' '.ucwords($dataQCE->lastname)}}" readonly>
                        @endif
                    @endforeach
                @else
                    <input class="form-control" value="Belum ada data" readonly>
                @endif
            </div>
            <div class="col-md mt-2">
                <label>QC Documents</label>
                <?php 
                    //qcd_id
                    if(old('qcd_id') != null) {
                        $qcd_id = old('qcd_id');
                    }elseif(isset($projectTask->qcd_id)){
                        $qcd_id = $projectTask->qcd_id;
                    }else{
                        $qcd_id = null;
                    }
                ?>
                @if($qcd_id != null)
                    @foreach($dataQCDs as $dataQCD)
                        @if($dataQCD->id == $qcd_id)
                            <input class="form-control" value="{{ ucwords($dataQCD->firstname).' '.ucwords($dataQCD->lastname)}}" readonly>
                        @endif
                    @endforeach
                @else
                    <input class="form-control" value="Belum ada data" readonly>
                @endif
            </div>
            <div class="w-100 mt-2"></div>
            <div class="col-md mt-2">
                <label>Tanggal mulai</label>
                <?php 
                    //date start
                    if(old('date_start') != null) {
                        $dateStart = old('date_start');
                    }elseif(isset($projectTask->date_start)){
                        $dateStart = date('Y-m-d',strtotime($projectTask->date_start));
                    }else{
                        $dateStart = null;
                    }
                    //date end
                    if(old('date_end') != null) {
                        $dateEnd = old('date_end');
                    }elseif(isset($projectTask->date_end)){
                        $dateEnd = date('Y-m-d',strtotime($projectTask->date_end));
                    }else{
                        $dateEnd = null;
                    }
                ?>
                <input type="date" class="form-control{{ $errors->has('date_start') ? ' has-error' : '' }}" name="date_start" value="{{ $dateStart }}" placeholder="dd/mm/yyyy" readonly>
            </div>
            <div class="col-md mt-2">
                <label>Tanggal selesai</label>
                <input type="date" class="form-control{{ $errors->has('date_end') ? ' has-error' : '' }}" name="date_end" value="{{ $dateEnd }}" placeholder="dd/mm/yyyy" readonly>
            </div>
            <!-- project department -->
            @if(Auth::user()->department_id == 1)
                
                <!-- QC expense -->
                @if(Auth::user()->user_level == 6)
                    <div class="col-md mt-2">
                        <label>Nilai jasa</label>
                        @if($projectTask->task_budget != null)
                            <input class="form-control" value="Rp. {{ number_format($projectTask->task_budget)}}" readonly>
                        @else
                            <input class="form-control" value="Belum ada data" readonly>
                        @endif
                    </div>
                @endif
                <div class="w-100"></div>
                <!-- PC -->
                @if(Auth::user()->user_level == 2)
                    <div class="col-md alert alert-warning mt-2">
                        <form action="{{ route($formTaskUpdate, $projectTask->id) }}" style="display:inline-block" class="w-100" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                            @csrf
                            @method('PUT')

                            <!-- hidden data -->
                            <input type="text" name="update_status_task" value="1" hidden>

                            <div class="row">
                                <div class="col-12">
                                    <label>Update status task </label>
                                </div>
                                <div class="col-8">
                                    <select name="status" class="form-control select2{{ $errors->has('status') ? ' has-error' : '' }}" required>
                                        <?php
                                            if(old('status') != null) {
                                                $status = old('status');
                                            }elseif(isset($projectTask->status)){
                                                $status = $projectTask->status;
                                            }else{
                                                $status = null;
                                            }
                                        ?>
                                        @if ($status != null)
                                            @foreach ($projectTaskStatus as $data2)
                                                @if ($data2->id == $status)
                                                    <option value="{{ $status ?? $data2->id }}">{{ ucwords(strtolower($data2->name)) }}</option>
                                                @endif
                                            @endforeach
                                            @foreach($projectTaskStatus as $data2)
                                                @if ($data2->id != $status)
                                                    <option value="{{ $status ?? $data2->id }}">{{ ucwords(strtolower($data2->name)) }}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="0">Pilih status</option>
                                            @foreach($projectTaskStatus as $data2)
                                                <option value="{{ $data2->id }}">{{ ucwords(strtolower($data2->name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md">
                                    <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger w-100" name="submit"> Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="col-md">
            @if(Auth::user()->department_id == 1)
                @if(Auth::user()->user_level == 2)
                    <a href="{{ route($formCovidCreate,'pid='.$projectTask->project_id.'&tid='.$projectTask->id) }}" class="btn btn-orange mt-1"><i class="fas fa-file-signature"></i> Ajukan Test Covid-19</a>
                @endif
                @if(Auth::user()->user_level == 4)
                    <a href="{{ route($formTemplateIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange mt-1"><i class="fas fa-cogs"></i> Lihat template</a>
                    <!-- lihat laporan -->
                    <a href="{{ route($formQCProjectReportShow,$projectTask->id.'?project_id='.$projectTask->project_id) }}" class="btn btn-orange mt-1"><i class="fas fa-file-signature"></i> Lihat laporan</a>
                    <!-- lihat laporan new -->
                    <a href="{{ route($formVTProjectReportShow,$projectTask->id.'?project_id='.$projectTask->project_id) }}" class="btn btn-orange mt-1"><i class="fas fa-file-signature"></i> Report</a>
                @endif
                @if(Auth::user()->user_level == 5)
                    <a href="{{ route($formToolsIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange mt-1"><i class="fas fa-wrench"></i> Lihat laporan alat</a>
                @endif
                @if(Auth::user()->user_level == 6)
                    <a href="{{ route($formRouteExpensesIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange mt-1"><i class="fas fa-file-invoice-dollar"></i> Lihat pengeluaran</a>
                    <!-- cash advance -->
                    <a href="{{ route($formRouteCashAdvanceIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange mt-1"><i class="fas fa-money-check-alt"></i> Lihat cash advance</a>
                    <!-- payment summary report -->
                    <a href="{{ route($formRoutePaymentSummaryIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange mt-1"><i class="fas fa-file-signature"></i> Summary pembayaran</a>
                @endif
            @endif
            <a href="{{ route($formRouteIndex, $projectTask->id) }}" class="btn btn-blue-lini mt-1">Kembali</a>
        </div>
    </div>
</div> <!-- card -->
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
