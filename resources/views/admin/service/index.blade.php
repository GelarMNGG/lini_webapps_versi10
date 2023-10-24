@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar services';
    $formRouteCreate = 'service.create';
    $formRouteEdit = 'service.edit';
    $formRouteDestroy = 'service.destroy';
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
    <div class="card-header text-center">{{ ucfirst($pageTitle) }}</div>

    <div class="card-body">
        @if (isset($services))
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <a href="{{ route($formRouteCreate) }}" class="btn btn-info mb-3"><i class="fa fa-plus"></i> Tambah service</a>
                        <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                <th>Icon</th>
                                <th>Nama</th>
                                <th>Image</th>
                                <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($services as $data)
                                    <tr>
                                        <td>
                                            <img src="{{ asset('img/services/icon/'.$data->icon) }}" alt="logo" class="rounded-circle avatar-md">
                                        </td>
                                        <td>{{ ucwords($data->name) }}</td>
                                        <td>
                                            @foreach($serviceImages as $dataImg)
                                                @if($dataImg->service_id == $data->id)
                                                    <img src="{{ asset('img/services/'.$dataImg->image) }}" alt="{{ $dataImg->image }}" class="avatar-xl rounded">
                                                @endif
                                            @endforeach
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- container-fluid -->
        @else
            <a href="{{ route($formRouteCreate) }}" class="btn btn-info mb-3"><i class="fa fa-plus"></i> Tambah service</a>
            <div class="alert alert-warning">Belum ada data.</div>
        @endif
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable(
            "order":[]
        );
    } );
</script>
@endsection
