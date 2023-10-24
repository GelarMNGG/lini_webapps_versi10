@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Laporan pengembalian alat';
    //form route
    $formRouteIndex = 'project-tool-tech.index';
    $formRouteUpdate= 'project-tool-tech.update';
    //save report
    $formRouteReport = 'tech.projectreporttool';
?>
@endsection

@section('content')

    <div class="content mt-2">
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="flash-message">
                @foreach (['danger','warning','success','info'] as $msg)
                    @if (Session::has('alert-'.$msg))
                        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                    @endif
                @endforeach
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card-box">
                        <span class="logo float-left">
                            <img src="{{ asset('img/'.$companyInfo->logo) }}" alt="logo {{ $companyInfo->name }}" height="57">
                        </span>
                        <div class="panel-heading text-center text-uppercase">
                            <h3>{{ $pageTitle }}</h3>
                            <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectToolsReport->project_name }}</span></strong>
                        </div>
                        <hr>
                        <div class="panel-body">
                            <div class="clearfix">
                                <div class="float-left">
                                    <span>Nama teknisi: {{ ucwords($userProfile->firstname).' '.ucwords($userProfile->lastname) }}</span>
                                    <br>Task: <span class="text-uppercase">{{ $projectToolsReport->task_name }}</span>
                                </div>
                                <div class="float-right">
                                    <h4>No task #<strong><span class="text-uppercase">{{ $projectToolsReport->task_number }}</span></strong> </h4>
                                    <span>Tanggal: {{ date('l, d F Y', strtotime($projectToolsReport->submitted_at)) }}</span>
                                </div>
                            </div>
                            <hr>
                            <!-- end row -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama barang</th>
                                                    <th>Kode alat</th>
                                                    <th>Tanggal penggunaan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach($dataReportTools as $data)
                                                    <tr>
                                                        <td>{{ $i }}</td>
                                                        <td>{{ ucfirst($data->name) }}</td>
                                                        <td>{{ strtoupper($data->code ?? 'Tidak ada') }}</td>
                                                        <td>{{ date('d F Y', strtotime($data->request_submitted)) }} - {{ $data->report_submitted !== null ? date('d F Y', strtotime($data->report_submitted)) : date('d F Y') }}</td>
                                                        <?php $i++; ?>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-xl-6 col-6">
                                    
                                </div>
                                <div class="col-xl-3 col-6 offset-xl-3 pr-3">
                                    
                                </div>
                            </div>
                            <div class="row m-0 mb-5">
                                <div class="col-xl-6 col-6">
                                    <div class="clearfix">
                                        <h5 class="small text-dark text-uppercase mb-5">dibuat</h5>
                                        <small>
                                            {{ ucwords($userProfile->firstname).' '.ucwords($userProfile->lastname) }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-6">
                                    <h5 class="small text-dark text-uppercase mb-5">Disetujui, Kepala departemen</h5>
                                    <small>
                                        {{ $approverProfile ? ucwords($approverProfile->firstname).' '.ucwords($approverProfile->lastname) : 'Belum ada data' }}
                                    </small>
                                </div>
                            </div>
                            <hr>
                            <div class="d-print-none">
                                <div class="float-right">
                                    <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>
                                    <a href="{{ route($formRouteIndex,'task_id='.$projectToolsReport->task_id) }}" type="button" class="btn btn-secondary">Kembali</a>
                                </div>
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-md mt-3">
                                        @if($projectToolsReport->status == 1)
                                            <div class="alert alert-warning">Laporan Anda sedang direview oleh pejabat terkait.</div>
                                        @else
                                            <div class="alert alert-success">Laporan Anda telah disetujui.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <!-- end row -->        
            
        </div> <!-- container-fluid -->

    </div> <!-- content -->

@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'description' );
</script>
@endsection
