@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar jabatan'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $formRouteIndex = 'user-teamusertitle.index';
    $formRouteCreate = 'user-teamusertitle.create';
    $formRouteStore = 'user-teamusertitle.store';
    $formRouteEdit = 'user-teamusertitle.edit';
    $formRouteDestroy = 'user-teamusertitle.destroy';

    //admin minutes
    $formTeamUserIndex = 'user-teamuser.index';
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

    @if (count($userLevels) > 0)
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ count($userLevels) }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="row m-0">
                    <?php $separator=1; ?>
                    @foreach($userLevels as $data)
                        <div class="col-sm p-2">
                            <div class="bg-card-box br-5 p-2">
                            <strong>{{ isset($data->name) ? ucwords($data->name) : 'Belum ada data' }}</strong>

                                <div class="mt-1">
                                    <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST">
                                        @method('DELETE')
                                        @csrf

                                        @if($data->department_id != 0)
                                            <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i></a>

                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')" disabled><i class="fas fa-times" title='Delete'></i></button>  
                                        @else
                                            <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white disabled'> <i class='fas fa-edit' title='Edit'></i></a>

                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')" disabled><i class="fas fa-times" title='Delete'></i></button>  
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                        <?php $separator++; ?>
                    @endforeach
                    <div class="w-100"></div>
                    <div class="col-sm">
                        <?php 
                            #$userLevels->setPath('minutes-tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                            #{{ $userLevels->links() }}
                        ?>
                        <?php $paginator = $userLevels; ?>
                        @include('includes.paginator')
                    </div>
                </div>
            </div>
            <div class="card-body">
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah jabatan</a>
                <a href="{{ route($formTeamUserIndex) }}" class="btn btn-orange"><i class="fa fa-eye"></i> Lihat semua user</a>
            </div>
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>0</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>

            <div class="card-body">
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah jabatan</a>
                <a href="{{ route($formTeamUserIndex) }}" class="btn btn-orange"><i class="fa fa-eye"></i> Lihat semua user</a>
            </div>
        </div> <!-- container-fluid -->
    @endif
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
