@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar troubleshooting'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $formRouteIndex = 'troubleshooting.index';
    $formRouteCreate = 'troubleshooting.create';
    $formRouteStore = 'troubleshooting.store';
    $formRouteShow = 'troubleshooting.show';
    $formRouteEdit = 'troubleshooting.edit';
    $formRouteDestroy = 'troubleshooting.destroy';
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

    @if ($countData > 0)
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-info float-left'>{{ $countData }}</div>
                <span class="text-uppercase"><strong>{{ ucfirst($pageTitle) }}</strong></span>
            </div>

            <div class="card-body bg-gray-lini-2">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-box table-responsive">
                                <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Status</th>
                                            <th>Pengarang</th>
                                            <th>Jabatan</th>
                                            <th>Masalah</th>
                                            <th>Solusi</th>
                                            <th>View</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>
                                        @foreach ($troubleshootings as $data)

                                        <?php 
                                            if($data->status == 3):
                                                $statusCSS = "text-info";
                                            elseif($data->status == 2):
                                                $statusCSS = "text-success";
                                            else:
                                                $statusCSS = "text-danger";
                                            endif
                                        ?>
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>
                                                <?php
                                                    if(old('status') != null) {
                                                        $status = old('status');
                                                    }elseif(isset($data->status)){
                                                        $status = $data->status;
                                                    }else{
                                                        $status = null;
                                                    }
                                                ?>
                                                @if ($status != null)
                                                        @if ($status == 2)
                                                            <span class="text-danger">Need your approval</span>
                                                        @else
                                                            @foreach ($statusDatas as $dataOne)
                                                                @if($dataOne->id == $status)
                                                                    <span class="{{ $statusCSS }}">{{ ucwords(strtolower($dataOne->name)) }}</span>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if($data->publisher_type == 'tech')
                                                    @foreach ($techDatas as $data3)
                                                        @if ($data3->id == $data->publisher_id)
                                                            <span class="text-info">{{ ucwords($data3->firstname).' '.ucwords($data3->lastname) }}</span>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    @foreach ($userDatas as $data4)
                                                        @if ($data4->id == $data->publisher_id)
                                                            <span class="text-info">{{ ucwords($data4->firstname).' '.ucwords($data4->lastname) }}</span>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if($data->publisher_type == 'tech')
                                                    <span class="text-dark">Technician</span>
                                                @else
                                                    @foreach ($userLevelDatas as $data5)
                                                        @if ($data5->role == $data->user_level || $data5->id == $data->user_level)
                                                            <span class="text-dark">{{ ucwords($data5->name) }}</span>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                {{ ucwords($data->title) }}
                                            </td>
                                            <td>
                                                {!! ucwords($data->solution) !!}
                                            </td>
                                            <td>
                                                {{ ucwords($data->view) }}
                                            </td>
                                            <td>
                                                <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                    <a href="{{ route($formRouteShow, $data->id) }}" class="btn btn-warning t-white"> <i class='fas fa-eye' title='Show'></i> Show</a>
                                                    <a href="{{ route($formRouteEdit, $data->id) }}" class="btn btn-info t-white"> <i class='fas fa-edit' title='Edit'></i> Edit</a>
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
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah troubleshooting</a>
            </div>
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-info float-left'>{{ $countData }}</div>
                <span class="text-uppercase"><strong>{{ ucfirst($pageTitle) }}</strong></span>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>
            <div class="card-body">
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah troubleshooting</a>
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
