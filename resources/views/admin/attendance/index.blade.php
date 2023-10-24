@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar absensi'; 
    $formRouteIndex = 'attendance.index';
    $formRouteCreate = 'attendance.create';
    $formClockinRouteEdit = 'user-clockin.edit';
    $formClockoutRouteEdit = 'user-clockout.edit';
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

    <div class="card mt-2">
        <div class="card-header text-center bb-orange">
            <div>{{ date('l, d F Y') }}</div>
            <div class="display-3">{{ date('H:i A') }}</div>
        </div>

        <div class="card-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card-box text-center" style="margin-bottom:0">
                            <a href="{{ route('user-clockin.create') }}" class="btn btn-info clock-button{{ $clockinCount > 0 ? ' disabled' : '' }}">Clock In</a>
                            <a href="{{ route('user-clockout.create') }}" class="btn btn-info clock-button{{ $clockoutCount > 0 || $clockinCount < 1 ? ' disabled' : ''}}">Clock Out</a>
                        </div>
                    </div>
                </div>
            </div> <!-- container-fluid -->
        </div>
    </div> <!-- card -->
    <div class="card">
        <div class="card-header text-center">
            <div>Attendance log</div>
        </div>

        @if(isset($attendances) && count($attendances) > 0)
            <div class="card-body bg-gray-lini-2">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card-box table-responsive">
                                <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Clock in</th>
                                            <th>Clock out</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>
                                        @foreach ($attendances as $data)
                                        <tr>
                                            <td>{{ date('l, d F Y', strtotime($data->date) ) }}</td>
                                            <td>
                                                {{!empty($data->clockin) ? date('H:i A', strtotime($data->clockin)) : '-' }}

                                                @if(!empty($data->clockin) && date('Y-m-d') == date('Y-m-d',strtotime($data->date)))
                                                    <!-- amendment -->
                                                    @if($clockinAmendmentCount > 0)
                                                        <br><span class="text-info">Clock in sedang direview</span>
                                                    @else
                                                        <a href="{{ route($formClockinRouteEdit, $data->id) }}" class='btn btn-link waves-effect waves-light'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>
                                                    @endif
                                                    <!-- amendment -->
                                                @endif
                                            </td>
                                            <td>
                                                {{!empty($data->clockout) ? date('H:i A', strtotime($data->clockout)) : '-' }}

                                                @if(!empty($data->clockout) && date('Y-m-d') == date('Y-m-d',strtotime($data->date)))
                                                    <!-- amendment -->
                                                    @if($clockoutAmendmentCount > 0)
                                                        <br><span class="text-info">Clock out sedang direview</span>
                                                    @else
                                                        <a href="{{ route($formClockinRouteEdit, $data->id) }}" class='btn btn-link waves-effect waves-light'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>
                                                    @endif
                                                    <!-- amendment -->
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
        @else
        <div class="card-body bg-gray-lini-2">
            <div class="alert alert-warning">Belum ada data.</div>
        </div>
        @endif
    </div> <!-- card -->
    
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable(
            "order": []
        );
    } );
</script>
@endsection
