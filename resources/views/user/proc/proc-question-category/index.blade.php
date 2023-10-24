@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar kategori pertanyaan'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $formRouteIndex = 'user-proc-question-category.index';
    $formRouteCreate = 'user-proc-question-category.create';
    $formRouteStore = 'user-proc-question-category.store';
    $formRouteEdit = 'user-proc-question-category.edit';
    $formRouteDestroy = 'user-proc-question-category.destroy';

    
    $formCategoryIndex = 'user-proc-question.index';
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

@if (count($dataCategory) > 0)
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ count($dataCategory) }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="row m-0">
                    @foreach($dataCategory as $data)
                        <div class="col-6 p-2">
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
                    @endforeach
                    <div class="col-12">
                        <?php 
                            #$minuteCategory->setPath('minutes-tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                        ?>
                        {{ $dataCategory->links() }}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah kategori</a>
                    <a href="{{ route($formCategoryIndex) }}" type="button" class="btn btn-blue-lini">Kembali</a>
                </div>
            </div>
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ sizeof($dataCategory) }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>

            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah kategori</a>
                    <a href="{{ route($formCategoryIndex) }}" type="button" class="btn btn-blue-lini">Kembali</a>
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
