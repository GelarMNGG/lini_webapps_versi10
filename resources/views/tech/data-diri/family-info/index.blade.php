@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Input data keluarga';
    $formRouteIndex = 'tech-input-data-keluarga.index';
    $formRouteCreate = 'tech-input-data-keluarga.create';
    $formRouteStore = 'tech-input-data-keluarga.store';
    $formRouteEdit = 'tech-input-data-keluarga.edit';
    $formRouteDestroy = 'tech-input-data-keluarga.destroy';

    //add category
    // $formCategoryIndex = 'admin-proc-question-category.index';

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

    @if (count($procFamily) > 0)
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ count($procFamily) }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="row m-0">
                    @foreach($procFamily as $data)
                        <div class="col-6 p-2">
                            <div class="bg-card-box br-5 p-2">

                                <!-- <br><span class="text-info">Keluarga : </span><strong>{{ isset($data->family_name) ? ucwords($data->family_name) : 'Belum ada data' }}</strong> -->
                                <br><span class="text-info">Keluarga : </span><strong>{{ isset($data->category_name) ? ucwords($data->category_name) : 'Belum ada data' }}</strong>
                                <br><span class="text-success">Pekerjaan : </span><strong>{{ isset($data->family_profession) ? ucwords($data->family_profession) : 'Belum ada data' }}</strong>
                                <br><span class="text-success">Alamat : </span><strong>{{ isset($data->family_address) ? ucwords($data->family_address) : 'Belum ada data' }}</strong>
        
                                <!-- <br><span class="text-danger">{{ isset($data->category_name) ? ucwords($data->category_name) : '' }}</span> -->
                                <div class="mt-1">
                                    <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST">
                                        @method('DELETE')
                                        @csrf

                                        <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>

                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                    </form>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah Keluarga</a>
                </div>
            </div>
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ count($procFamily) }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>

            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah Keluarga</a>
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
