@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar WFH to WFO Anda';

    $formRouteIndex  = 'admin-wfh-to-wfo.index';
    $formRouteCreate = 'admin-wfh-to-wfo.create';
    $formRouteEdit = 'admin-wfh-to-wfo.edit';
    $formRouteDestroy = 'admin-wfh-to-wfo.destroy';
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

<div class="card mt-2">
    <div class="card-header text-center text-uppercase bb-orange">
        <div class='badge badge-info float-left'>{{ count($requestDatas) }}</div>
        <strong>{{ ucfirst($pageTitle) }}</strong>
    </div>

    @if (isset($requestDatas))
    <div class="card-body bg-gray-lini-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Pemberi tugas</th>
                                    <th>Tanggal WFH - WFO</th>
                                    <th>Keperluan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($requestDatas as $data)
                                    <?php 
                                        if($data->status == 2){
                                            $css = 'success';
                                        }else{
                                            $css = 'danger';
                                        }
                                    ?>
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>
                                            <strong>
                                                @if(isset($data->leader_id))
                                                    @foreach($requesterAdmins as $requesterAdmin)
                                                        @if($requesterAdmin->id == $data->leader_id)
                                                            <span>{{ ucwords($requesterAdmin->firstname).' '.ucwords($requesterAdmin->lastname) }}</span>
                                                            <br><span class="small">{{ ucwords($requesterAdmin->title) }}</span>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <span>Belum ada data</span>
                                                @endif
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="text-{{ $css }}">{{ isset($data->date) ? date('l, d F Y', strtotime($data->date)) : '-'}}</span>
                                            
                                            <br>Jam: <span class="text-info">{{ $data->clock_in !== null ? date('H:i A',strtotime($data->clock_in)) : 'Belum ada data' }}</span>

                                            - <span class="text-info">{{ $data->clock_out !== null ? date('H:i A',strtotime($data->clock_out)) : 'Belum ada data' }}</span>
                                        </td>
                                        <td>
                                            {!! ucfirst($data->description) !!}
                                        </td>
                                        <td>
                                            <?php
                                                if ($data->status == 2) {
                                                    $cssBadge = 'success';
                                                }else{
                                                    $cssBadge = 'danger';
                                                }
                                            ?>
                                            <span class="text-{{ $cssBadge }}">{{ ucfirst($data->status_name) }}</span>
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
        </div>
    </div>
    @else
    <div class="card-body bg-gray-lini-2">
        <div class="alert alert-warning">Belum ada data.</div>
    </div>
    <div class="card-body">
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
