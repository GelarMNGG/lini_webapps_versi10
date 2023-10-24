@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar projects';
    $formRouteDashboard = 'admin-projects.dashboard';
    $formRouteCreate = 'admin-projects.create';
    $formRouteShow = 'admin-projects.show';
    $formRouteEdit = 'admin-projects.edit';
    $formRouteDestroy = 'admin-projects.destroy';
    $formRouteProgress = 'admin-projects.progress';

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
        <strong><span class="text-uppercase">{{ ucfirst($pageTitle) }}</span></strong> ({{ $projectsCount }})
    </div>
    @if (isset($projects))
        <div class="card-body bg-gray-lini-2">
            <div class="row">
                <?php $separator = 1; ?>
                @foreach ($projects as $data)
                    <div class="col-6 p-2">
                        <div class="bg-card-box br-5 p-2">
                            <span class="text-danger">{{ strtoupper($data->name) }}</span> <span class="text-info">({{ $data->taskCount }} task)</span>
                            <br>PM: 
                            @if($data->pm_id != null)
                                @foreach($users as $dataPM)
                                    @if($dataPM->id == $data->pm_id)
                                        <span class="text-success">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) }}</span>
                                    @endif
                                @endforeach
                            @else
                                <span class="text-danger">-</span>
                            @endif
                            
                            <br>Mulai: 
                            @if($data->date != null)
                                <span class="text-info">{{ date('l, d F Y',strtotime($data->date)) }}</span>
                            @else
                                <span class="text-danger">-</span>
                            @endif

                            <br>Nilai: 
                            @if($data->amount != null)
                                Rp. {{ number_format($data->amount) }}
                            @else
                                <span class="text-danger">0</span>
                            @endif

                            <div class="btn-project-box">
                                <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST">
                                @method('DELETE')
                                @csrf
                                    <a href="{{ route($formRouteShow, $data->id) }}" class="btn btn-icon btn-warning"> <i class='fas fa-eye' title='Show'></i> Show</a>
            
                                    <!-- progress -->
                                    <a href="{{ route($formRouteProgress, $data->id) }}" class="btn btn-success mt-1 mb-1"><i class="fas fa-eye"></i> Log</a>
            
                                    <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white mt-1 mb-1'> <i class='fas fa-edit' title='Edit'></i></a>
                                    @if($data->taskCount < 1)
                                        <button type="submit" class="btn btn-danger mt-1 mb-1" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                    @endif
                                </form>
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
                    <?php $paginator = $projects; ?>
                    @include('includes.paginator')
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah project</a>
            </div>
        </div>
    @else
    <div class="card-body bg-gray-lini-2">
        <div class="alert alert-warning">Belum ada data.</div>
    </div>
    <div class="card-body">
        <div class="col-md">
            <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah project</a>
        </div>
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
