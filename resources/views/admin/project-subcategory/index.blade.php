@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar sub kategori laporan'; 
    $dashboardLink  = 'admin-projects.index';
    $formRouteCreate = 'admin-projects-subcategory.create';
    $formRouteEdit = 'admin-projects-subcategory.edit';
    $formRouteDestroy = 'admin-projects-subcategory.destroy';
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
    <div class="card-header text-center text-uppercase bb-orange"><strong>{{ ucfirst($pageTitle) }}</strong></div>

    <div class="card-body">
        <div class="flash-message">
        @if (isset($projectReportSubcategorys))
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <a href="{{ route($formRouteCreate) }}" class="btn btn-info mb-3"><i class="fa fa-plus"></i> Tambah sub kategori</a>
                        <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($projectReportSubcategorys as $data)
                                    <tr>
                                        <td>
                                            {{ $i }}
                                        </td>
                                        <td>{{ ucfirst($data->name) }}</td>
                                        <td>
                                            @if ($data->status == 1)
                                                <span class="text-success">Active</span>
                                            @else
                                                <span class="text-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST">
                                            @method('DELETE')
                                            @csrf
                                                <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
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
            <a href="{{ route($formRouteCreate) }}" class="btn btn-info mb-3"><i class="fa fa-plus"></i> Tambah sub kategori</a>
            <div class="alert alert-warning">Belum ada data.</div>
        @endif
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
