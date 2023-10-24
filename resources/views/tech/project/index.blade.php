@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar proyek';
    $formRouteDashboard = 'project-tech.dashboard';
    $formRouteCreate = 'project-tech.create';
    $formRouteEdit = 'project-tech.edit';
    $formRouteShow = 'project-tech.show';
    $formRouteProgress = 'project-tech.progress';

    //setting
    $statusBadge    = array('dark','info','success','danger','purple','pink','warning');
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
        <span class="text-uppercase"><strong>{{ ucfirst($pageTitle) }}</strong></span>
    </div>
    @if (isset($dataTask) && $dataTask->total() > 0)
    <div class="card-body bg-gray-lini">
        <div class="row m-0">
            @if(sizeof($dataTask) > 0)
                <?php $separator=1; ?>
                @foreach($dataTask as $dataProject)
                    <div class="col-sm p-2">
                        <div class="bg-card-box br-5 p-2">
                            <span class="text-danger"><strong>{{ strtoupper($dataProject->project_name) }}</strong></span>
                            <br><span class="text-info">{{ $dataProject->name }}</span> 

                            @if($dataProject->pm_id != null)
                                @foreach($users as $dataPM)
                                    @if($dataPM->id == $dataProject->pm_id)
                                        <br>PM: <span class="text-success">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) }}</span>
                                    @endif
                                @endforeach
                            @else
                                <br><span class="text-danger">Belum ada PM</span>
                            @endif

                            @if($dataProject->pc_id != null)
                                @foreach($users as $dataPC)
                                    @if($dataPC->id == $dataProject->pc_id)
                                        <br>PC: <span class="text-success">{{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname) }}</span>
                                    @endif
                                @endforeach
                            @else
                                <br><span class="text-danger">Belum ada PC</span>
                            @endif
                            
                            @if($dataProject->qcd_id != null)
                                @foreach($users as $dataQCD)
                                    @if($dataQCD->id == $dataProject->qcd_id)
                                        <br>QCD: <span class="text-success">{{ ucwords($dataQCD->firstname).' '.ucwords($dataQCD->lastname) }}</span>
                                    @endif
                                @endforeach
                            @else
                                <br><span class="text-danger">Belum ada QCD</span>
                            @endif

                            @if($dataProject->qce_id != null)
                                @foreach($users as $dataQCE)
                                    @if($dataQCE->id == $dataProject->qce_id)
                                        <br>QCE: <span class="text-success">{{ ucwords($dataQCE->firstname).' '.ucwords($dataQCE->lastname) }}</span>
                                    @endif
                                @endforeach
                            @else
                                <br><span class="text-danger">Belum ada QCE</span>
                            @endif

                            <div>
                                <a href="{{ route($formRouteShow, $dataProject->id) }}" class='btn btn-icon waves-effect waves-light btn-warning t-white mt-1 mb-1'> <i class='fas fa-eye' title='Show'></i> Show </a>

                                <!-- progress -->
                                <a href="{{ route($formRouteProgress, $dataProject->id) }}" class="btn btn-success mt-1 mb-1"><i class="fas fa-eye"></i> Log</a>
                            </div>

                        </div>
                    </div>
                    <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                    <?php $separator++; ?>
                @endforeach
                <div class="col-md-12">
                    <?php $paginator = $dataTask; ?>
                    @include('includes.paginator')
                </div>
            @endif
        </div>
    </div>
    @else
    <div class="card-body bg-gray-lini">
        <div class="alert alert-warning">Belum ada data.</div>
    </div>
    @endif

</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable(
            //"order":[]
        );
    } );
</script>
@endsection
