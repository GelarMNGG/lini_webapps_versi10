@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar Purchase Requisition';
    $formRouteCreate = 'user-pr.create';
    $formRouteEdit = 'user-pr.edit';
    $formRouteDestroy = 'user-pr.destroy';
    $formRouteShow = 'user-pr.show';
    $statusBadge    = array('dark','info','success','danger','purple','pink','warning');
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
    <div class="card-header text-center">{{ ucfirst($pageTitle) }}</div>

    <div class="card-body">
        @if (isset($dataPR))
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                <th>#</th>
                                <th>Nama project</th>
                                <th>Nama barang/jasa</th>
                                <th>Status</th>
                                <th>Jumlah</th>
                                <th>Tanggal</th>
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
                                        <td><strong>{{ ucfirst($data->project_name) }}</strong></td>
                                        <td>{{ ucwords($data->name) }}</td>
                                        <td>
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
                                            <a href="{{ route($formRouteShow, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-warning t-white'> <i class='fas fa-eye' title='Lihat'></i> Lihat PR</a>
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
