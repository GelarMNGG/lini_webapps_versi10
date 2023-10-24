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
<div class="flash-message">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<div class="card">
    <div class="card-header text-center">
        {{ ucfirst($pageTitle) }} (<strong><span class="text-info text-uppercase">{{ $projectsCount }}</span></strong>)
    </div>
    <div class="card-body">
        @if (isset($projects))
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <a href="{{ route($formRouteCreate) }}" class="btn btn-info mb-3"><i class="fa fa-plus"></i> Tambah project</a>
                        <a href="{{ route($formRouteDashboard) }}" class="btn btn-secondary mb-3">Kembali</a>
                        <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                <th>#</th>
                                <th>Nama project</th>
                                <th>Project manager</th>
                                <th>Tanggal mulai</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($projects as $data)
                                    <tr>
                                        <td> {{ $i }} </td>
                                        <td>{{ ucwords($data->name) }} ({{ $data->taskCount }} task)</td>
                                        <td>
                                            @if($data->pm_id != null)
                                                @foreach($users as $dataPM)
                                                    @if($dataPM->id == $data->pm_id)
                                                        <span class="text-success">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) }}</span>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="text-danger">Belum ada PM</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data->date != null)
                                                <span class="text-info">{{ date('l, d F Y',strtotime($data->date)) }}</span>
                                            @else
                                                <span class="text-danger">Belum ada data</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($data->amount != null)
                                                Rp. {{ number_format($data->amount) }}
                                            @else
                                                <span class="text-danger">Belum ada data</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST">
                                            @method('DELETE')
                                            @csrf
                                                <a href="{{ route($formRouteShow, $data->id) }}" class="btn btn-icon btn-warning"> <i class='fas fa-eye' title='Show'></i> Show</a>

                                                <!-- progress -->
                                                <a href="{{ route($formRouteProgress, $data->id) }}" class="btn btn-success mt-1 mb-1"><i class="fas fa-eye"></i> Log</a>

                                                <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white mt-1 mb-1'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>
                                                @if($data->taskCount < 1)
                                                    <button type="submit" class="btn btn-danger mt-1 mb-1" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                                @endif
                                            </form>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- container-fluid -->
        @else
            <a href="{{ route($formRouteCreate) }}" class="btn btn-info mb-3"><i class="fa fa-plus"></i> Tambah project</a>
            <div class="alert alert-warning">Belum ada data.</div>
        @endif
    </div>
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
