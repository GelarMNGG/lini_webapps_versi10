@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar pengajuan WFH to WFO'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    //form route
    $formRouteIndex = 'user-wfh-to-wfo.index';
    $formRouteCreate = 'user-wfh-to-wfo.create';
    $formRouteShow = 'user-wfh-to-wfo.show';
    $formRouteEdit = 'user-wfh-to-wfo.edit';

    $formRouteUpdate = 'user-wfh-to-wfo.update';
    $formRouteDestroy = 'user-wfh-to-wfo.destroy';
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

    @if ($requestDatas != null)

        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="row m-0">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <?php $separator=1; ?>
                    @foreach($requestDatas as $data)
                        <div class="col-sm p-2">
                            <div class="bg-card-box br-5 p-2">
                                <strong>
                                    @if(isset($data->employee_id))
                                        @if(isset($data->employee_type))
                                            @if($data->employee_type == 'admin')
                                                @foreach($requesterAdmins as $requesterAdmin)
                                                    @if($requesterAdmin->id == $data->employee_id)
                                                        <span>{{ ucwords($requesterAdmin->firstname).' '.ucwords($requesterAdmin->lastname) }}</span>
                                                    @endif
                                                @endforeach
                                            @else
                                                @foreach($requesterUsers as $requesterUser)
                                                    @if($requesterUser->id == $data->employee_id)
                                                        <span>{{ ucwords($requesterUser->firstname).' '.ucwords($requesterUser->lastname) }}</span>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @else
                                            <span>Belum ada data</span>
                                        @endif
                                    @else
                                        <span>Belum ada data</span>
                                    @endif
                                </strong>

                                <?php
                                    if ($data->status == 2) {
                                        $cssBadge = 'success';
                                    }else{
                                        $cssBadge = 'danger';
                                    }
                                ?>
                                <span class="badge badge-{{ $cssBadge }} float-right">{{ ucfirst($data->status_name) }}</span>
                                <br><span class="text-danger text-uppercase">{{ isset($data->dept_name) ? ucwords($data->dept_name) : '-' }}</span>

                                <br>
                                <span class="small">Requested by:</span>
                                @if(isset($data->leader_id))
                                    @foreach($requesterAdmins as $requesterAdmin)
                                        @if($requesterAdmin->id == $data->leader_id)
                                            <span>{{ ucwords($requesterAdmin->firstname).' '.ucwords($requesterAdmin->lastname) }}</span>
                                            <small>({{ ucwords($requesterAdmin->title) }})</small>
                                        @endif
                                    @endforeach
                                @endif
                                <br><span class="text-info">{{ $data->date !== null ? date('l, d F Y',strtotime($data->date)) : 'Belum ada data' }}</span>

                                | <span class="text-info">{{ $data->clock_in !== null ? date('H:i A',strtotime($data->clock_in)) : 'Belum ada data' }}</span>

                                - <span class="text-info">{{ $data->clock_out !== null ? date('H:i A',strtotime($data->clock_out)) : 'Belum ada data' }}</span>

                                <div class="mt-1">

                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#requestModal{{ $data->id }}"><i class="fas fa-eye"></i> </button>
                                    
                                    @if($data->status != 2)
                                        <form action="{{ route($formRouteUpdate, $data->id) }}" style="display:inline-block" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <!-- hidden -->
                                            <input type="hidden" name="status" value="2">
                                            <button type="submit" class="btn btn-info">Approve</button>  
                                        </form>

                                        <form action="{{ route($formRouteDestroy, $data->id) }}" style="display:inline-block" method="POST">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                        </form>
                                    @endif
                                    
                                </div>

                            </div>
                        </div>
                        <!-- Modal -->
                            <div class="modal fade" id="requestModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="projectMinutes" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                    <div class="modal-content-img">
                                        <div class="modal-body text-center">
                                        <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                            <div class="alert alert-warning" id="projectMinutes">
                                                <h5>
                                                    Nama Karyawan: 
                                                    <span class="text-muted">
                                                        <strong>
                                                            @if(isset($data->employee_id))
                                                                @if(isset($data->employee_type))
                                                                    @if($data->employee_type == 'admin')
                                                                        @foreach($requesterAdmins as $requesterAdmin)
                                                                            @if($requesterAdmin->id == $data->employee_id)
                                                                                <span>{{ ucwords($requesterAdmin->firstname).' '.ucwords($requesterAdmin->lastname) }}</span>
                                                                            @endif
                                                                        @endforeach
                                                                    @else
                                                                        @foreach($requesterUsers as $requesterUser)
                                                                            @if($requesterUser->id == $data->employee_id)
                                                                                <span>{{ ucwords($requesterUser->firstname).' '.ucwords($requesterUser->lastname) }}</span>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                @else
                                                                    <span>Belum ada data</span>
                                                                @endif
                                                            @else
                                                                <span>Belum ada data</span>
                                                            @endif
                                                        </strong>
                                                    </span>
                                                    <br><span class="text-danger text-uppercase">{{ isset($data->dept_name) ? ucwords($data->dept_name) : '-' }}</span>
                                                    <br><span class="small">Requested by:</span>
                                                    @if(isset($data->leader_id))
                                                        @foreach($requesterAdmins as $requesterAdmin)
                                                            @if($requesterAdmin->id == $data->leader_id)
                                                                <span>{{ ucwords($requesterAdmin->firstname).' '.ucwords($requesterAdmin->lastname) }}</span>
                                                                <small>({{ ucwords($requesterAdmin->title) }})</small>
                                                            @endif
                                                        @endforeach
                                                    @endif

                                                    <br>
                                                    <br> WFH to WFO pada <br>
                                                    <span class="text-danger">
                                                        <small>@if(isset($data->date)) {{ date('l, d F Y', strtotime($data->date))}} @endif</small>
                                                    </span>

                                                    | <span class="text-info">{{ $data->clock_in !== null ? date('H:i A',strtotime($data->clock_in)) : 'Belum ada data' }}</span>

                                                    - <span class="text-info">{{ $data->clock_out !== null ? date('H:i A',strtotime($data->clock_out)) : 'Belum ada data' }}</span>
                                                </h5>
                                                <small>Keperluan:</small>
                                                <span class="text-muted"><small>{!! ucfirst($data->description) !!}</small></span>
                                            </div>
                                        </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- Modal -->
                        <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                        <?php $separator++; ?>
                    @endforeach
                    <div class="col-md-12">
                        <?php 
                            //$requestDatas->setPath('user-wfh-to-wfo');
                            #{{ $requestDatas->links() }}
                        ?>
                        <?php $paginator = $requestDatas; ?>
                        @include('includes.paginator')
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($dashboardLink) }}" class="btn btn-blue-lini">Kembali</a>
                </div>
            </div>
        </div>

    @else
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div style="display:inline-block">
                    <strong><span class="text-info text-uppercase">{{ $pageTitle }}</span></strong>
                </div>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>
            
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($dashboardLink) }}" class="btn btn-blue-lini">Kembali</a>
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
