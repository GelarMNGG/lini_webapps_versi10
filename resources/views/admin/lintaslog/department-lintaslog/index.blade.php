@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar departement lintaslog'; 
    // $dashboardLink  = 'admin.index';
    $formRouteCreate = 'department-lintaslog.create';
    $formRouteEdit = 'department-lintaslog.edit';
    $formRouteDestroy = 'department-lintaslog.destroy';
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
    @if (isset($departments))
    <div class="card-body bg-gray-lini-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>ID Departemen</th>
                                <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($departments as $data)
                                    <tr>
                                        <td>
                                            {{ $i }}
                                        </td>
                                        <td>{{ ucwords($data->name) }}</td>
                                        <td>{{ ucwords($data->id) }}</td>
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
    </div>
    <div class="card-body">
        <div class="col-md">
            <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah departement</a>
        </div>
    </div>
    @else
        <div class="card-body bg-gray-lini-2">
            <div class="alert alert-warning">Belum ada data.</div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah departement</a>
            </div>
        </div>
    @endif
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
