@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar kota'; 
    $formRouteCreate  = 'admin-cities.create';
    $formRouteEdit  = 'admin-cities.edit';
    $formRouteDestroy  = 'admin-cities.destroy';
?>
@endsection

@section('content')
<div class="flash-message mt-2">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
    @if ($errors->any())
        <p class="alert alert-danger">
            <small class="form-text">
                <strong>{{ $errors->first() }}</strong>
            </small>
        </p>
    @endif
</div>

<div class="card mt-2">
    <div class="card-header text-center text-uppercase bb-orange">
        <strong>{{ ucfirst($pageTitle) }}</strong>
    </div>

    <div class="card-body bg-gray-lini-2">
        @if (isset($provincesDatas))
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                <th>Propinsi</th>
                                <th>Kota</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($provincesDatas as $data)
                                <tr>
                                    <td><strong>{{ ucwords($data->name) }}</strong></td>
                                    <td>
                                        <div class="row">
                                            <?php $i = 0; ?>
                                            @foreach ($citiesDatas as $daftarKota)
                                                @if ($data->id == $daftarKota->code)
                                                <div class="col-md">
                                                    <div class="form-group">
                                                        <div class="form-control" style="display:table">
                                                            <form action="{{ route($formRouteDestroy, $daftarKota->id) }}" method="POST" style="display:inline">
                                                                @method('DELETE')
                                                                @csrf
                                                                <a href="{{ route($formRouteEdit, $daftarKota->id) }}" class='btn icon-button text-info'> <i class='fas fa-edit' title='Edit'></i></a>
                                                                
                                                                <button type="submit" class="btn icon-button text-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data pengiriman ini?')"><i class="fas fa-times" title='Delete'></i></button>
                                                            </form>
                                                            <div style="display:inline; word-wrap:break-word;">{{ ucwords($daftarKota->name) }}</div>
                                                        </div>
                                                    </div> 
                                                </div>
                                                @endif
                                            <?php 
                                                $i++;
                                                if ($i % 2 == 0) {
                                                    echo "<div class='w-100'></div>";
                                                }
                                            ?>
                                            @endforeach
                                            <div class="col-md">
                                                <a href="{{ route($formRouteCreate,'pid='.$data->id) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah kota</a>
                                            </div>
                                        </div>
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
            <div class="alert alert-warning">Belum ada data.</div>
        @endif
    </div>
    <div class="card-body">
        <div class="col-md">
            <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah kota</a>
        </div>
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
