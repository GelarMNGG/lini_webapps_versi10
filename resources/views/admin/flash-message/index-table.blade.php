@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Flash message list';
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $statusAppsBadge    = array('','info','purple','danger','success');
    //route
    $formRouteIndex = 'flash-messages.index';
    $formRouteCreate = 'flash-messages.create';
    $formRouteEdit = 'flash-messages.edit';
    $formRouteDestroy = 'flash-messages.destroy';
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
        <div class='badge badge-info float-left'>{{ count($flashMessageDatas) }}</div>
    </div>

    @if (count($flashMessageDatas) > 0)
        <div class="card-body bg-gray-lini-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Pesan</th>
                                        <th>Penerima</th>
                                        <th>Info</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach ($flashMessageDatas as $data1)
                                    <?php $ia = 1; ?>
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>
                                                @if($data1->views > 0)
                                                    <span class="text-info">[<strong>{{ $data1->views }}</strong><small>x lagi]</small></span>
                                                @endif
                                                <strong>{{ ucfirst($data1->message) }}</strong>
                                            </td>
                                            <td>
                                                @if ($data1->receiver_type == 'admin')
                                                    @foreach ($adminsDatas as $dataTwo)
                                                        @if ($dataTwo->id == $data1->receiver_id)
                                                            <span class="text-info">{{ ucwords(strtolower($dataTwo->firstname)).' '.ucwords(strtolower($dataTwo->lastname)) }}</span>
                                                        @endif
                                                    @endforeach
                                                @elseif($data1->receiver_type == 'user')
                                                    @foreach ($usersDatas as $dataThree)
                                                        @if ($dataThree->id == $data1->receiver_id)
                                                            <span class="text-info">{{ ucwords(strtolower($dataThree->firstname)).' '.ucwords(strtolower($dataThree->lastname)) }}</span>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    @foreach ($techsDatas as $dataFour)
                                                        @if ($dataFour->id == $data1->receiver_id)
                                                            <span class="text-info">{{ ucwords(strtolower($dataFour->firstname)).' '.ucwords(strtolower($dataFour->lastname)) }}</span>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                {{ $data1->receiver_department_name }}
                                                <?php
                                                    if ($data1->level == 'danger') {
                                                        $levelName = 'penting';
                                                    }elseif($data1->level == 'warning'){
                                                        $levelName = 'sedang';
                                                    }else{
                                                        $levelName = 'normal';
                                                    }
                                                ?>
                                                <br><span class="badge badge-{{ $data1->level }}">
                                                    {{ strtoupper($levelName) }}
                                                </span>
                                            </td>
                                            <td>
                                                <!-- IT department feature -->
                                                @if(Auth::user()->department_id == 5)
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
            <div class="col-md">
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah pesan</a>
            </div>
        </div>
    @else
        <div class="card-body bg-gray-lini-2">
            <div class="alert alert-warning">Belum ada data.</div>
        </div>
        <div class="card-body">
            <div class="col-md">
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah pesan</a>
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
