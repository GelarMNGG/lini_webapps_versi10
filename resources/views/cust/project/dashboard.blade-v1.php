@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Dashboard proyek';
    $statusBadge    = array('dark','info','success','danger','purple','pink','warning');
    //form link
    $formRouteIndex = 'cust-projects.index';
    $formRouteEdit = 'cust-projects.edit';
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
        <!-- Start Content-->
        <div class="col-md text-center mt-3 mb-3">
            <!-- progressbar -->
            <ul id="progressbar">

                <li class="{{ $newCount > 0 ? 'active ' : '' }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-magic progress-icon"></i>
                    </div>
                    <div>
                        <strong>Persiapan</strong>
                        <br><h3 class="text-muted">{{ $newCount }}</h3>
                    </div>
                </li>
                <li class="{{ $onprogressCount > 0 ? 'active ' : '' }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-wrench progress-icon"></i>
                    </div>
                    <div class="progress-icon-box-1">
                    </div>
                    <div>
                        <strong>Dalam pengerjaan</strong>
                        <br><h3 class="text-muted">{{ $onprogressCount }}</h3>
                    </div>
                </li>
                <li class="{{ $reportingCount > 0 ? 'active ' : '' }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-file-alt progress-icon"></i>
                    </div>
                    <div>
                        <strong>Penyiapan laporan</strong>
                        <br><h3 class="text-muted">{{ $reportingCount }}</h3>
                    </div>
                </li>
                <li class="{{ $finishedCount > 0 ? 'active ' : '' }}justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-lock progress-icon"></i>
                    </div>
                    <div>
                        <strong>Selesai</strong>
                        <br><h3 class="text-muted">{{ $finishedCount }}</h3>
                    </div>
                </li>

            </ul>
        </div>
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="" class="display table dt-responsive nowrap">
                            <tbody>
                            <tr>
                                    <td width="25%">
                                        @foreach ($projects as $data)
                                            @if($data->status == 1)
                                                <div class="alert alert-warning">{{ ucwords($data->name) }} 
                                                <br> <a href="{{ route($formRouteEdit, $data->id) }}" class="text-success">Detail</a></div>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td width="25%">
                                        @foreach ($projects as $data)
                                            @if($data->status == 2)
                                                <div class="alert alert-warning">{{ ucwords($data->name) }} 
                                                <br> <a href="{{ route($formRouteEdit, $data->id) }}" class="text-success">Detail</a></div>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td width="25%">
                                        @foreach ($projects as $data)
                                            @if($data->status == 3)
                                                <div class="alert alert-warning">{{ ucwords($data->name) }} 
                                                <br> <a href="{{ route($formRouteEdit, $data->id) }}" class="text-success">Detail</a></div>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td width="25%">
                                        @foreach ($projects as $data)
                                            @if($data->status == 4)
                                                <div class="alert alert-warning">{{ ucwords($data->name) }} 
                                                <br> <a href="{{ route($formRouteEdit, $data->id) }}" class="text-success">Detail</a></div>
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td width="25%">
                                        @if($newCount > 0)
                                            <div class="text-center"><a href="{{ route($formRouteIndex, 'status=1') }}" class="btn btn-info"><i class="fas fa-eye"></i> Lihat semua</a></div>
                                        @endif
                                    </td>
                                    <td width="25%">
                                        @if($onprogressCount > 0)
                                            <div class="text-center"><a href="{{ route($formRouteIndex, 'status=2') }}" class="btn btn-info"><i class="fas fa-eye"></i> Lihat semua</a></div>
                                        @endif
                                    </td>
                                    <td width="25%">
                                        @if($reportingCount > 0)
                                            <div class="text-center"><a href="{{ route($formRouteIndex, 'status=3') }}" class="btn btn-info"><i class="fas fa-eye"></i> Lihat semua</a></div>
                                        @endif
                                    </td>
                                    <td width="25%">
                                        @if($finishedCount > 0)
                                            <div class="text-center"><a href="{{ route($formRouteIndex, 'status=4') }}" class="btn btn-info"><i class="fas fa-eye"></i> Lihat semua</a></div>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- end row -->        

        </div> <!-- container-fluid -->
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable(
            "order":[]
        );
    } );
</script>
@endsection
