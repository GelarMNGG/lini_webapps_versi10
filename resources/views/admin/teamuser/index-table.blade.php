@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar staff';

    $formRouteIndex  = 'teamuser.index';
    $formRouteCreate = 'teamuser.create';
    $formRouteEdit = 'teamuser.edit';
    $formRouteDestroy = 'teamuser.destroy';
    //title
    $formTitleIndex = 'teamusertitle.index';
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

<div class="card">
    <div class="card-header text-center text-uppercase bb-orange">
        <div class='badge badge-info float-left'>{{ count($teamusers) }}</div>
        <a href="{{ route ($formRouteIndex,'skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
        <strong>{{ ucfirst($pageTitle) }}</strong>
    </div>

    @if (isset($teamusers))
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
                                    <th>Jabatan</th>
                                    <th>Departemen</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($teamusers as $data)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>
                                            <span class="text-info">
                                                @if(isset($data->firstname))
                                                    {{ ucwords($data->firstname).' '.ucwords($data->lastname) }}
                                                @else
                                                    {{ ucwords($data->name) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            @if(isset($data->role_name))
                                                {{ ucfirst($data->role_name) }}
                                            @else
                                                {{ ucfirst($data->level_name) != null ? ucfirst($data->level_name) : 'Staff' }}
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-danger">{{ strtoupper($data->department_name) }}</small>
                                        </td>
                                        <td>
                                            @if($data->active == 1)
                                                    <div class="badge badge-success float-right">Active</div>
                                            @else
                                                <div class="badge badge-danger float-right">Inactive</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($data->mobile !== null)
                                                <a href='https://wa.me/{{ $data->mobile }}?text=Haloo ' target='_blank' class='btn btn-icon waves-effect waves-light btn-success t-white mt-1'> <i class='fab fa-whatsapp' title='Whatsapp'></i></a>
                                            @endif
                                            <form action="{{ route($formRouteDestroy, $data->id) }}" style="display:inline-block;" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white mt-1'> <i class='fas fa-edit' title='Edit'></i></a>
                                                <button type="submit" class="btn btn-danger mt-1" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
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
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah staff</a>
                <a href="{{ route($formTitleIndex) }}" class="btn btn-orange mt-1"><i class="fa fa-eye"></i> Lihat jabatan</a>
            </div>
        </div>
    @else
    <div class="card-body bg-gray-lini-2">
        <div class="alert alert-warning">Belum ada data.</div>
    </div>
    <div class="card-body">
        <div class="col-md">
            <a href="{{ route($formRouteCreate) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah staff</a>
            <a href="{{ route($formTitleIndex) }}" class="btn btn-orange mt-1"><i class="fa fa-eye"></i> Lihat jabatan</a>
        </div>
    </div>
    @endif
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable({
            order: [[ 0, 'desc' ], [ 5, 'asc' ]]
        });
    } );
</script>
@endsection
