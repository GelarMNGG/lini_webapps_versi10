@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar peminjaman alat'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');

    //form project route
    $formRouteProjectIndex = 'user-projects.index';
    $formRouteProjectShow = 'user-projects.show';
    
    //project tool
    $formProjectToolUpdate = 'user-project-tool.update';
    $formProjectToolReport = 'user-project-tool.report';
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

                    @foreach($dataTools as $data)
                        <div class="col-6 p-2">
                            <div class="bg-card-box br-5 p-2">

                                <strong><span class="text-uppercase">{{ $data->code}}</span> | {{ isset($data->name) ? ucwords($data->name) : 'Belum ada data' }}</strong>

                                @if($data->status == 2)
                                    <span class="badge badge-danger float-right">Request approval</span>
                                @else
                                    @if($data->status < 3)
                                        <span class="badge badge-danger float-right">{{ ucwords($data->status_name) }}</span>
                                    @else
                                        <span class="badge badge-success float-right">{{ ucwords($data->status_name) }}</span>
                                    @endif
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
                                    <span class="text-danger">Data tidak tersedia</span>
                                @endif

                                <div class="mt-1">
                                    @if($data->status == 2)
                                        
                                        <form action="{{ route($formProjectToolUpdate, $data->id) }}" style="display:inline-block" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <!-- hidden data -->
                                            <input type="text" name="project_id" value="{{ $projectTask->project_id }}" hidden>
                                            <input type="text" name="task_id" value="{{ $projectTask->id }}" hidden>
                                            <input type="text" name="tool_name" value="{{ $data->name }}" hidden>
                                            <input type="text" name="status" value="3" hidden>

                                            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane" title='Kirim'></i> Setujui</button>  
                                        </form>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                    <div class="col-12">
                        <?php 
                            $dataTools->setPath('user-project-tool?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                        ?>
                        {{ $dataTools->links() }}
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="col-md mt-2 mb-2">
                    @if($dataReportCount > 0)
                        <a href="{{ route($formProjectToolReport,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange{{ $dataFinishedCount > 0 ? '' : ' disabled' }}"><i class="fa fa-eye"></i> Lihat laporan</a>
                    @else
                        <a href="{{ route($formProjectToolReport,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange{{ $dataFinishedCount > 0 ? '' : ' disabled' }}"><i class="fa fa-magic"></i> Buat laporan</a>
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
