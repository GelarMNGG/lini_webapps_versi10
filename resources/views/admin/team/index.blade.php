@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar admin'; 
    $dashboardLink  = 'admin.index';
    $formRouteCreate = 'team.create';
    $formRouteEdit = 'team.edit';
    $formRouteDestroy = 'team.destroy';
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

    @if (isset($teams))
        <div class="card-body bg-gray-lini-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                                <thead>
                                    <tr>
                                    <th>Photo</th>
                                    <th>Nama</th>
                                    <th>Departemen</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($teams as $data)
                                        @if (Auth::user()->id != $data->id)
                                        <tr>
                                            <td>
                                                <img src="{{ asset('admintheme/images/users/'.$data->image) }}" alt="user-image" class="rounded-circle avatar-md">
                                            </td>
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
                                                @foreach($departments as $department)
                                                    @if($department->id == $data->department_id)
                                                        {{ ucwords($department->name) }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @if ($data->active == 1)
                                                    <span class="text-success">Active</span>
                                                @else
                                                    <span class="text-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>

                                                <form action="{{ route($formRouteDestroy, $data->id) }}" class="display:inline-block" method="POST">
                                                    @method('DELETE')
                                                    @csrf

                                                    @if ($data->mobile !== null)
                                                        <a href='https://wa.me/{{ $data->mobile }}?text=Haloo ' target='_blank' class='btn btn-icon waves-effect waves-light btn-success t-white mt-1'> <i class='fab fa-whatsapp' title='Whatsapp'></i></a>
                                                    @endif

                                                    <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white mt-1'> <i class='fas fa-edit' title='Edit'></i></a>
                                                    
                                                    <button type="submit" class="btn btn-danger mt-1" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                                </form>
                                            </td>
                                        </tr>
                                        @endif
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
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah admin</a>
            </div>
        </div>
    @else
        <a href="{{ route($formRouteCreate) }}" class="btn btn-info mb-3"><i class="fa fa-plus"></i> Tambah admin</a>
        <div class="alert alert-warning">Belum ada data.</div>
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
