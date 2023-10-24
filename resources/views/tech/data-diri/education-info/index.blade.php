@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Input data pendidikan';
    $formRouteIndex = 'tech-input-data-pendidikan.index';
    $formRouteCreate = 'tech-input-data-pendidikan.create';
    $formRouteStore = 'tech-input-data-pendidikan.store';
    $formRouteEdit = 'tech-input-data-pendidikan.edit';
    $formRouteDestroy = 'tech-input-data-pendidikan.destroy';

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

    @if (count($educationDatas) > 0)
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ count($educationDatas) }}</div>
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
                    @foreach($educationDatas as $data)
                        <div class="col-sm p-2">
                            <div class="bg-card-box br-5 p-2">
                                <span class="text-danger"><strong>{{ isset($data->level_name) ? strtoupper($data->level_name) : 'Belum ada data' }}</strong></span>

                                <strong>{{ isset($data->name) ? '| '.ucwords($data->name) : '' }}</strong>
                                <strong>{{ isset($data->year) ? '| '.ucwords($data->year) : '' }}</strong>
                            
                                <form action="{{ route($formRouteDestroy, $data->id) }}" class="float-right" style="display:inline" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <a href="{{ route($formRouteEdit, $data->id) }}" class='btn badge badge-info float-right' style="display:inline;"> <i class='fas fa-edit' title='Edit'></i> Ubah</a>

                                    <br><button type="submit" class="btn badge badge-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                </form>

                                <br> <strong>{!! isset($data->city_name) ? "<span class='text-success'><small>Kota:</small></span> ".ucwords($data->city_name) : '' !!}</strong>

                                <strong>{!! isset($data->province_name) ? "<span class='text-success'><small>Propinsi:</small></span> ".ucwords($data->province_name) : 'Belum ada data' !!}</strong>
                            </div>
                        </div>
                        <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                        <?php $separator++; ?>
                    @endforeach
                    <div class="col-12">
                        <?php $paginator = $educationDatas; ?>
                        @include('includes.paginator')
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah data pendidikan</a>
                </div>
            </div>
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ count($educationDatas) }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>

            <div class="card-body">
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah data pendidikan</a>
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
