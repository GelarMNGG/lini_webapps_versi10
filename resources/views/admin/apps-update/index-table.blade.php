@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Apps update list';
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $statusAppsBadge    = array('','info','purple','danger','success');
    //route
    $formRouteIndex = 'apps-update.index';
    $formRouteCreate = 'apps-update.create';
    $formRouteEdit = 'apps-update.edit';
    $formRouteDestroy = 'apps-update.destroy';
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
        <div class='badge badge-info float-left'>{{ count($appsUpdateDatas) }}</div>
    </div>

    @if (count($appsUpdateDatas) > 0)
        <div class="card-body bg-gray-lini-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Judul update</th>
                                        <th>Updater</th>
                                        <th>Penerima</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach ($appsUpdateDatas as $data1)
                                    <?php $ia = 1; ?>
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ ucwords($data1->title) }}</td>
                                            <td>
                                                <span class="text-info">{{ ucwords(strtolower($data1->updater_firstname)).' '.ucwords(strtolower($data1->updater_lastname)) }}</span> 
                                            </td>
                                            <td>{{ ucwords($data1->cat_name) }}</td>
                                            <td>{!! $data1->created_at != null ? date('l, d F Y',strtotime($data1->created_at)) : '<span class="text-danger">Belum ada data</span>' !!}</td>
                                            <td>
                                                @if(Auth::user()->company_id == 1 && Auth::user()->department_id == 5)
                                                    <form action="{{ route($formRouteDestroy, $data1->id) }}" method="POST" style="display:inline;">
                                                        @method('DELETE')
                                                        @csrf

                                                        <a href="{{ route($formRouteEdit, $data1->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i></a>

                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                                    </form>
                                                @endif
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
            <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah update</a>
        </div>
    @else
        <div class="card-body bg-gray-lini-2">
            <div class="alert alert-warning">Belum ada data.</div>
        </div>
        <div class="card-body">
            <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah update</a>
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
