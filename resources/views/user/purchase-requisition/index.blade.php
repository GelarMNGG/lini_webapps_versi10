@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar Purchase Requisition';
    //back
    $formRouteBack = 'user-projects.show';

    //form route
    $formRouteCreate = 'user-pr.create';
    $formRouteEdit = 'user-pr.edit';
    $formRouteDestroy = 'user-pr.destroy';
    $formRouteShow = 'user-pr.show';
    //additional setting
    $statusBadge    = array('dark','danger','info','success','purple','pink','warning');
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
    <div class="card-header text-center">
        Proyek: <strong><span class="text-info">{{ strtoupper($infoTaskProject->project_name) }}</span></strong>
        <br>Task: <strong><span class="text-danger">{{ isset($infoTaskProject->name) ? strtoupper($infoTaskProject->name) : 'Belum ada task' }}</span></strong>
    </div>

    <div class="card-body">
        @if (isset($dataPR))
        <div class="container-fluid">
            <div class="mb-2"> 
                <a href="{{ route($formRouteCreate,'project_id='.$infoTaskProject->project_id.'&task_id='.$infoTaskProject->id) }}" class="btn btn-info"><i class="fa fa-plus"></i> Tambah PR</a>
                <a href="{{ route($formRouteBack, $infoTaskProject->project_id) }}" class="btn btn-secondary">Kembali</a>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                <th>#</th>
                                <th>Nama barang/jasa</th>
                                <th>Jumlah</th>
                                <th>Tanggal dibuat</th>
                                <th>Tanggal disubmit</th>
                                <th>Tanggal diapprove</th>
                                <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($dataPR as $data)
                                    <tr>
                                        <td> {{ $i }} </td>
                                        <td>{{ ucwords($data->name) }}
                                            @foreach($prStatus as $dataStatus)
                                                @if($dataStatus->id == $data->status)
                                                    <span class="badge badge-{{ $statusBadge[$dataStatus->id] }}">{{ ucwords($dataStatus->name) }}</span>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>{{ ucwords($data->amount) }}</td>
                                        <td>{{ date('l, d F Y', strtotime($data->date)) }}</td>
                                        <td>{{ $data->date_submitted ? date('l, d F Y', strtotime($data->date_submitted)) : 'Belum disubmit' }}</td>
                                        <td>{{ $data->date_approved ? date('l, d F Y', strtotime($data->date_approved)) : 'Belum diapprove' }}</td>
                                        <td>
                                            @if($data->status < 2)
                                            <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>
                                            @endif
                                            <a href="{{ route($formRouteShow, $data->id.'?project_id='.$infoTaskProject->project_id.'&task_id='.$infoTaskProject->id) }}" class='btn btn-icon waves-effect waves-light btn-warning t-white'> <i class='fas fa-eye' title='Lihat'></i> Lihat PR</a>
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
        @else
            <div class="alert alert-warning">Belum ada data.</div>
        @endif

        <div class="col-md">
        <?php 
            if ($draftCount > $approvedCount || $reviewedCount > $approvedCount || $reviewedCount != 0) {
                $css_info = 'danger';
            }else{
                $css_info = 'success';
            }
        ?>
            <div class="alert alert-{{ $css_info }} mt-3">
                Draft: <strong>{{ $draftCount }}</strong> | Reviewed: <strong>{{ $reviewedCount }}</strong> | Approved: <strong>{{ $approvedCount }}</strong>
            </div>
        </div>

    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable(
            // "order":[]
        );
    } );
</script>
@endsection
