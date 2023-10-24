@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar peminjaman alat'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    //form route
    $formRouteIndex = 'project-tool-tech.index';
    $formRouteCreate = 'project-tool-tech.create';
    $formRouteStore = 'project-tool-tech.store';
    $formRouteReport = 'project-tool-tech.report';
    $formRouteEdit = 'project-tool-tech.edit';
    $formRouteUpdate = 'project-tool-tech.update';
    $formRouteDestroy = 'project-tool-tech.destroy';
    //tools report
    $formRouteToolReport = 'tech.projectreporttoolshow';
    //form project route
    $formRouteProjectIndex = 'project-tech.index';
    $formRouteProjectShow = 'project-tech.show';
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

    @if (count($dataTools) > 0)
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-info float-left'>{{ count($dataTools) }}</div>
                <div class="text-center">
                    <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTask->project_name }}</span></strong>
                    <br><small>Task:</small> <strong><span class="text-danger text-uppercase">{{ $projectTask->name }}</span></strong>
                    <br><small>No task:</small> <strong><span class="text-warning text-uppercase">{{ $projectTask->number }}</span></strong>
                </div>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="row m-0">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <?php $separator=1; ?>
                    @foreach($dataTools as $data)
                        <div class="col-sm p-2">
                            <div class="bg-card-box br-5 p-2">

                                <strong><span class="text-uppercase">{{ $data->code}}</span> | {{ isset($data->name) ? ucwords($data->name) : 'Belum ada data' }}</strong>

                                @if($data->status < 3)
                                    <span class="badge badge-danger float-right">{{ ucwords($data->status_name) }}</span>
                                @else
                                    <span class="badge badge-success float-right">{{ ucwords($data->status_name) }}</span>
                                @endif
                                
                                <br>Diajukan: 
                                @if($data->request_submitted !== null)
                                    <span class="text-success">{{ date('l, d F Y', strtotime($data->request_submitted)) }}</span>
                                @else
                                    <span class="text-danger">Belum disubmit</span>
                                @endif

                                <br>Disetujui: 
                                @if($data->request_approved !== null)
                                    <span class="text-success">{{ date('l, d F Y', strtotime($data->request_approved)) }}</span>
                                @else
                                    <span class="text-danger">-</span>
                                @endif

                                <div class="mt-1">
                                    @if($data->status == 1)
                                        <a href="{{ route($formRouteEdit, $data->id) }}" class="btn btn-info"><i class="fas fa-edit"></i> Edit</a>
                                        
                                        <form action="{{ route($formRouteUpdate, $data->id) }}" style="display:inline-block" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <!-- hidden data -->
                                            <input type="text" name="task_id" value="{{ $projectTask->id }}" hidden>
                                            <input type="text" name="status" value="2" hidden>

                                            <button type="submit" class="btn btn-pink"><i class="fas fa-paper-plane" title='Kirim'></i> Kirim</button>  
                                        </form>

                                        <form action="{{ route($formRouteDestroy, $data->id) }}" style="display:inline-block" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                        </form>
                                    @endif
                                </div>

                            </div>
                        </div>
                        <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                        <?php $separator++; ?>
                    @endforeach
                    <div class="w-100"></div>
                    <div class="col-md">
                        <?php 
                            $dataTools->setPath('project-tool-tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                        ?>
                        <?php $paginator = $dataTools; ?>
                        @include('includes.paginator')
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="col-md">
                    @if($dataReportCount > 0)
                        <a href="{{ route($formRouteToolReport,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange{{ $dataFinishedCount > 0 ? '' : ' disabled' }}"><i class="fa fa-eye"></i> Lihat laporan</a>
                    @else
                        <a href="{{ route($formRouteCreate,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Ajukan lagi</a>
                        
                        <a href="{{ route($formRouteReport,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange{{ $dataFinishedCount > 0 ? '' : ' disabled' }}"><i class="fa fa-magic"></i> Buat laporan</a>
                    @endif
                    
                    <a href="{{ route($formRouteProjectShow, $projectTask->id) }}" class="btn btn-blue-lini">Kembali</a>
                </div>
            </div>
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-info float-left'>{{ count($dataTools) }}</div>
                <div class="card-header text-center">
                    <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTask->project_name }}</span></strong>
                    <br><small>Task:</small> <strong><span class="text-danger text-uppercase">{{ $projectTask->name }}</span></strong>
                    <br><small>No task:</small> <strong><span class="text-warning text-uppercase">{{ $projectTask->number }}</span></strong>
                </div>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>

            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Ajukan</a>
                    
                    <a href="{{ route($formRouteProjectShow, $projectTask->id) }}" class="btn btn-blue-lini">Kembali</a>
                </div>
            </div>
        </div>
    @endif
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
