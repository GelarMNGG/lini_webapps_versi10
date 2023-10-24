@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Ringkasan pembayaran';

    //back
    $formRouteBack = 'user-projects-ca.index';

    //form route
    $formRouteIndex = 'user-projects-expense.index';

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
                        <div class="img-fluid mr-2" style="display:inline-block; line-height:50px; vertical-align:top;">
                            <img src="{{ asset('img/'.$companyInfo->logo) }}" alt="logo {{ $companyInfo->name }}" height="57">
                        </div>
                        <div class="col-md-8" style="display:inline-block;">
                            <p style="line-height:1rem;">
                                <small><strong>PT {{ strtoupper($companyInfo->name) }}</strong></small>
                                <br><small>{{ ucfirst($companyInfo->address) }}</small>
                                <br><small>Telp. {{ ucfirst($companyInfo->phone) }}</small>
                                | <small>Email. {{ ucfirst($companyInfo->email) }}</small>
                            </p>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md">
                                    <div class="text-center"><h5>SURAT PERMOHONAN PEMBAYARAN (SPP)</h5></div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td width="30%"><small>No.</small></td>
                                                    <td width="30%">
                                                        <small>Tanggal: {{ date('l, m F Y') }}</small>
                                                    </td>
                                                    <td width="30%"><small>Project ID: {{ $projectTaskInfo->project_id }}</small>  </td>
                                                </tr>
                                                <tr>
                                                    <td width="30%"><small>Project: {{ ucwords($projectTaskInfo->project_name) }}</small></td>
                                                    <td width="30%">
                                                        <small>Task: {{ $projectTaskInfo->number }}</small>
                                                    </td>
                                                    <td width="30%"><small>COA:</small>  </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">
                                                        <div class="text-center">
                                                            <small><strong>Mohon dibayarkan kepada</strong></small>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">
                                                        <div class="row">
                                                            <div class="col-md">
                                                                <small>Nama: {{ ucfirst($dataTech->firstname).' '.ucfirst($dataTech->lastname) }}</small>
                                                            </div>
                                                            <div class="col-md">
                                                                <small>Perusahaan: PT {{ strtoupper($companyInfo->name) }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">
                                                        <small>Untuk pembayaran: Pembayaran atas pekerjaan {{ strtoupper($projectTaskInfo->name) }}</small>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <small>No rek: </small>
                                                            </div>
                                                            <div class="col-md">
                                                                <small>Jumlah: Rp. {{ number_format($projectCashAdvance->amount) }}</small>
                                                                <br><small>Terbilang: </small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row m-0">
                                <div class="col-md">
                                    <div class="clearfix">
                                        <h5 class="small text-dark mb-5">Diajukan oleh:</h5>
                                        <small>
                                            {{ isset($dataQC->firstname) ? ucwords($dataQC->firstname).' '.ucwords($dataQC->lastname) : 'Belum ada data' }}
                                            <br>{{ isset($projectExpensesReport->submitted_at) ? date('l, d F Y',strtotime($projectExpensesReport->submitted_at)) : '-' }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5 class="small text-dark mb-5">
                                        Disetujui oleh Supervisor:
                                    </h5>
                                    <small>
                                        {{ isset($dataPM->firstname) ? ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) : 'Belum ada data' }}
                                        <br>{{ isset($projectExpensesReport->approved_at) ? date('l, d F Y',strtotime($projectExpensesReport->approved_at)) : '-' }}
                                    </small>
                                </div>
                                <div class="col-md">
                                    <h5 class="small text-dark mb-5">Disetujui oleh Dept Head:</h5>
                                    <small>
                                        {{ isset($dataDeptHead->firstname) ? ucwords($dataDeptHead->firstname).' '.ucwords($dataDeptHead->lastname) : 'Belum ada data' }}
                                        <br>{{ isset($projectExpensesReport->approved_by_pm_at) ? date('l, d F Y',strtotime($projectExpensesReport->approved_by_pm_at)) : '-'}}
                                    </small>
                                </div>
                            </div>
                            <hr>
                            <div class="row m-0 mb-5">
                                <div class="col-md">
                                    <div class="clearfix">
                                        <h5 class="small text-dark mb-5">Diketahui oleh Finance Dept:</h5>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <h5 class="small text-dark mb-5">Nama: {{ isset($projectTaskInfo->qce_firstname) ? ucwords($projectTaskInfo->qce_firstname).' '.ucwords($projectTaskInfo->qce_lastname) : 'Belum ada data' }}</h5>
                                </div>
                                <div class="col-md">
                                    <h5 class="small text-dark mb-5">Tgl: {{ isset($projectExpensesReport->approved_by_pm_at) ? date('l, d F Y',strtotime($projectExpensesReport->approved_by_pm_at)) : '-'}}</h5>
                                </div>
                            </div>
                            <hr>
                            <div class="d-print-none">
                                <div class="float-right">
                                    <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>
                                    
                                    <a href="#" type="button" class="btn btn-danger">Kirim ke finance</a>

                                    <a href="{{ route($formRouteBack,'project_id='.$projectTaskInfo->project_id.'&task_id='.$projectTaskInfo->id) }}" type="button" class="btn btn-secondary">Kembali</a>

                                </div>
                                <div class="clearfix"></div>
                                
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
