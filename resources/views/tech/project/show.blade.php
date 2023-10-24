@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'detail proyek';
    $formRouteDashboard = 'project-tech.dashboard';
    $formRouteIndex = 'project-tech.index';
    $formRouteCreate = 'project-tech.create';
    $formRouteEdit = 'project-tech.edit';
    $formRouteShow = 'project-tech.show';
    $formRouteProgress = 'project-tech.progress';

    //minutes
    $formMinutesCreate = 'minutes-tech.index';
    
    //pr
    $formRoutePrShow = 'tech-pr.show';

    //tool route
    $formRouteToolIndex = 'project-tool-tech.index';
    $formRouteToolCreate = 'project-tool-tech.create';

    //expense route
    $formRouteExpensesIndex = 'expenses-tech.index';
    $formRouteExpensesCreate = 'expenses-tech.create';

    //cash advance route
    $formRouteCashAdvanceIndex = 'project-ca-tech.index';
    $formRouteCashAdvanceCreate = 'project-ca-tech.create';

    //report route
    $formRouteReportIndex = 'report-tech.index';
    $formRouteReportCreate = 'report-tech.create';

    //payment summary
    $formRoutePaymentSummaryIndex = 'payment-summary-tech.index';
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

<div class="card mt-2">
    <div class="card-header text-center bb-orange">
        <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTask->project_name }}</span></strong>
        <br><small>Task:</small> <strong><span class="text-danger text-uppercase">{{ $projectTask->name }}</span></strong>
        <br><small>No task:</small> <strong><span class="text-warning text-uppercase">{{ $projectTask->number }}</span></strong>
    </div>
    <div class="card-body">
        @if ($errors->any())
        <div class="col-md">
            <div class="alert alert-danger">
                <small class="form-text">
                    <strong>{{ $errors->first() }}</strong>
                </small>
            </div>
        </div>
        @endif
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="container">
                            <div class="row">
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
                                    <label>QC Document</label>
                                    <?php 
                                        //qct_id
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
                                <div class="col-md mt-2">
                                    <label>Nilai jasa</label>
                                    @if($projectTask->task_budget != null)
                                        <input class="form-control" value="Rp. {{ number_format($projectTask->task_budget)}}" readonly>
                                    @else
                                        <input class="form-control" value="Belum ada data" readonly>
                                    @endif
                                </div>
                                <div class="w-100"></div>
                                <hr>
                                <div class="col-md mt-3 text-center">
                                    <div class="form-group">

                                        <div class="box-second text-center">
                                            <a href="{{ route($formMinutesCreate,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}"><img class="icon-login" src="{{ asset('admintheme/images/icon/tech-aktifitas.png') }}"></a>
                                            <a href="{{ route($formMinutesCreate,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}">Aktifitas</a>
                                        </div>
                                        <div class="box-second text-center">
                                            <a href="{{ route($formRouteToolIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}"><img class="icon-login" src="{{ asset('admintheme/images/icon/tech-alat.png') }}"></a>
                                            <a href="{{ route($formRouteToolIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}">Alat</a>
                                        </div>
                                        <div class="box-second text-center">
                                            <a href="{{ route($formRouteExpensesIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}"><img class="icon-login" src="{{ asset('admintheme/images/icon/tech-pengeluaran.png') }}"></a>
                                            <a href="{{ route($formRouteExpensesIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}">Pengeluaran</a>
                                        </div>
                                        <div class="box-second text-center">
                                            <a href="{{ route($formRouteCashAdvanceIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}"><img class="icon-login" src="{{ asset('admintheme/images/icon/tech-cash-advance.png') }}"></a>
                                            <a href="{{ route($formRouteCashAdvanceIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}">Cash advance</a>
                                        </div>
                                        <div class="box-second text-center">
                                            <a href="{{ route($formRouteReportIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}"><img class="icon-login" src="{{ asset('admintheme/images/icon/tech-laporan.png') }}"></a>
                                            <a href="{{ route($formRouteReportIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}">Laporan</a>
                                        </div>
                                        <div class="box-second text-center">
                                            <a href="{{ route($formRoutePaymentSummaryIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}"><img class="icon-login" src="{{ asset('admintheme/images/icon/tech-summary.png') }}"></a>
                                            <a href="{{ route($formRoutePaymentSummaryIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}">Summary pembayaran</a>
                                        </div>
                                        <div class="box-second text-center box-back">
                                            <a href="{{ route($formRouteIndex, $projectTask->id) }}">Kembali</a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- container-fluid -->
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
