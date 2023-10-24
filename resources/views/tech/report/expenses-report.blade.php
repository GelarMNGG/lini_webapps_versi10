@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Laporan pengeluaran';
    //form route
    $formRouteIndex = 'expenses-tech.index';
    $formRouteUpdate= 'expenses-tech.update';
    //save report
    $formRouteReport = 'tech.projectreportexpense';
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
                @if ($errors->any())
                    <p class="alert alert-danger">
                        <small class="form-text">
                            <strong>{{ $errors->first() }}</strong>
                        </small>
                    </p>
                @endif
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card-box">
                        <span class="logo float-left">
                            <img src="{{ asset('img/'.$companyInfo->logo) }}" alt="logo {{ $companyInfo->name }}" height="57">
                        </span>
                        <div class="panel-heading text-center text-uppercase">
                            <h3>{{ $pageTitle }}</h3>
                            <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectExpensesReport->project_name }}</span></strong>
                        </div>
                        <hr>
                        <div class="panel-body">
                            <div class="clearfix">
                                <div class="float-left">
                                    <span>Nama teknisi: {{ ucwords($userProfile->firstname).' '.ucwords($userProfile->lastname) }}</span>
                                    <br>Task: <span class="text-uppercase">{{ $projectExpensesReport->task_name }}</span>
                                </div>
                                <div class="float-right">
                                    <h4>No task #<strong><span class="text-uppercase">{{ $projectExpensesReport->number }}</span></strong> </h4>
                                    <span>Tanggal: {{ date('l, d F Y') }}</span>
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
                                                    <th>Nama barang/jasa</th>
                                                    <th>Tanggal</th>
                                                    <th>Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach($dataReportExpenses as $techExpense)
                                                    <tr>
                                                        <td>{{ $i }}</td>
                                                        <td>{{ ucfirst($techExpense->name) }}</td>
                                                        <td>{{ date('d F Y', strtotime($techExpense->created_at)) }}</td>
                                                        <td width="121px">Rp. {{ number_format($techExpense->amount ?? 0) }}</td>
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
                                    <p class="text-right"><b>Total:</b> <strong>Rp. {{ number_format($dataReportExpensesCount->total_amount) }}</strong></p>
                                </div>
                            </div>
                            <div class="row m-0 mb-5">
                                <div class="col-md">
                                    <div class="clearfix">
                                        <h5 class="small text-dark text-uppercase mb-5">dibuat, Teknisi</h5>
                                        <small>
                                            {{ isset($projectTaskInfo->tech_firstname) ? ucwords($projectTaskInfo->tech_firstname).' '.ucwords($projectTaskInfo->tech_lastname) : 'Belum ada data' }}
                                            <br>{{ isset($projectExpensesReport->submitted_at) ? date('l, d F Y',strtotime($projectExpensesReport->submitted_at)) : '-' }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5 class="small text-dark text-uppercase mb-5">QC Expenses</h5>
                                    <small>
                                        {{ isset($projectTaskInfo->qce_firstname) ? ucwords($projectTaskInfo->qce_firstname).' '.ucwords($projectTaskInfo->qce_lastname) : 'Belum ada data' }}
                                        <br>{{ isset($projectExpensesReport->approved_at) ? date('l, d F Y',strtotime($projectExpensesReport->approved_at)) : '-' }}
                                    </small>
                                </div>
                                <div class="col-md">
                                    <h5 class="small text-dark text-uppercase mb-5">Project Manager</h5>
                                    <small>
                                        {{ isset($projectTaskInfo->pm_firstname) ? ucwords($projectTaskInfo->pm_firstname).' '.ucwords($projectTaskInfo->pm_lastname) : 'Belum ada data' }}
                                        <br>{{ isset($projectExpensesReport->approved_by_pm_at) ? date('l, d F Y',strtotime($projectExpensesReport->approved_by_pm_at)) : '-'}}
                                    </small>
                                </div>
                            </div>
                            <hr>
                            <div class="d-print-none">
                                <div class="float-right">
                                    <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>

                                    <a href="{{ route($formRouteIndex,'project_id='.$projectExpensesReport->project_id.'&task_id='.$projectExpensesReport->task_id) }}" type="button" class="btn btn-secondary">Kembali</a>
                                    
                                    @if($projectExpensesReport->status == 2)
                                        @if($projectExpensesReport->canceled_at == null)
                                            <form action="{{ route($formRouteReport, 'id='.$projectExpensesReport->id.'&project_id='.$projectExpensesReport->project_id.'&task_id='.$projectExpensesReport->task_id) }}" style="display:inline-block" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                                @csrf
                                                @method('PUT')

                                                <!-- hidden data -->
                                                <input type="text" name="canceled_at" value="1" hidden>

                                                <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Cancel laporan</button>
                                            </form>
                                        @endif
                                    @endif

                                    @if($projectExpensesReport->status == 1)
                                        <form action="{{ route($formRouteReport, 'id='.$projectExpensesReport->id.'&project_id='.$projectExpensesReport->project_id.'&task_id='.$projectExpensesReport->task_id) }}" style="display:inline-block" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                            @csrf
                                            @method('PUT')

                                            <!-- hidden data -->
                                            <input type="text" name="status" value="2" hidden>

                                            <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Kirim laporan</button>
                                        </form>
                                    @endif

                                </div>
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-md mt-3">
                                        @if($projectExpensesReport->status == 4)
                                            <div class="alert alert-success">Laporan Anda telah disetujui PM.</div>
                                        @elseif($projectExpensesReport->status == 3)
                                            <div class="alert alert-success">Laporan Anda telah disetujui QC Expenses.</div>
                                        @elseif($projectExpensesReport->status == 2)
                                            @if($projectExpensesReport->canceled_at != null)
                                                <div class="alert alert-warning">Permohonan <strong>pembatalan laporan</strong> Anda sedang direview oleh pejabat terkait.</div>
                                            @else
                                                <div class="alert alert-warning">Laporan Anda sedang direview oleh pejabat terkait.</div>
                                            @endif
                                        @else
                                            <div class="alert alert-warning">Laporan belum disubmit.</div>
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
