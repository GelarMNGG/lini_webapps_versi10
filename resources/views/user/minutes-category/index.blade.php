@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar kategori aktivitas'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $formRouteIndex = 'user-minutes-category.index';
    $formRouteCreate = 'user-minutes-category.create';
    $formRouteStore = 'user-minutes-category.store';
    $formRouteEdit = 'user-minutes-category.edit';
    $formRouteDestroy = 'user-minutes-category.destroy';

    //user minutes
    $formMinutesIndex = 'user-minutes.index';
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

    @if (count($minuteCategory) > 0)
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ count($minuteCategory) }}</div>
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
                    @foreach($minuteCategory as $data)
                        <div class="col-sm p-2">
                            <div class="bg-card-box br-5 p-2">
                            <strong>{{ isset($data->name) ? ucwords($data->name) : 'Belum ada data' }}</strong>

                                <div class="mt-1">
                                    <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST">
                                        @method('DELETE')
                                        @csrf

                                        <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i></a>

                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                        <?php $separator++; ?>
                    @endforeach
                    <div class="col-12">
                        <?php 
                            #$minuteCategory->setPath('minutes-tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                        ?>
                        <?php $paginator = $minuteCategory; ?>
                        @include('includes.paginator')
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah kategori</a>
                    <a href="{{ route($formMinutesIndex) }}" class="btn btn-orange mt-1"><i class="fa fa-eye"></i> Lihat aktivitas</a>
                </div>
            </div>
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ sizeof($minuteCategory) }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>

            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah kategori</a>
                    <a href="{{ route($formMinutesIndex) }}" class="btn btn-orange mt-1"><i class="fa fa-eye"></i> Lihat aktivitas</a>
                </div>
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
