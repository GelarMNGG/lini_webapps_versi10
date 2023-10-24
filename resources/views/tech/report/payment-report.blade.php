@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Ringkasan pembayaran';

    //back
    $formRouteBack = 'project-tech.show';

    //form route
    $formRouteIndex = 'expenses-tech.index';
    $formRouteUpdate= 'expenses-tech.update';

    //route CA
    $formRouteCA= 'project-ca-tech.index';

    //save report
    $formRouteReport = 'tech.projectreportexpense';

    //payment summary
    $formRoutePaymentSummaryCreate = 'payment-summary-tech.create';
    $formRoutePaymentSummaryDestroy = 'payment-summary-tech.destroy';
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
                            <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTaskInfo->project_name }}</span></strong>
                        </div>
                        <hr>
                        <div class="panel-body">
                            <div class="clearfix">
                                <div class="float-left">
                                    <h4>Task: <span class="text-uppercase">{{ $projectTaskInfo->name }}</span></h4>
                                    <span>Nama teknisi: {{ isset($projectTaskInfo->tech_firstname) ? ucwords($projectTaskInfo->tech_firstname).' '.ucwords($projectTaskInfo->tech_lastname) : 'Belum ada data' }}</span>
                                </div>
                                <div class="float-right">
                                    <h4>No #<span class="text-uppercase">{{ $projectTaskInfo->number }}</span></h4>
                                    <span>Tanggal: {{ date('l, d F Y') }}</span>
                                </div>
                            </div>
                            <hr>
                            <!-- end row -->
                            <div class="row mt-4">
                                <div class="col-md">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Total cash advance</th>
                                                    <th>Total pengeluaran</th>
                                                    <th>Jasa</th>
                                                    <th>Pembayaran</th>
                                                    <th>Saldo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>#</td>
                                                    <td>
                                                        Rp. {{ number_format($projectTotalCA ?? 0) }}
                                                        <br><a href="{{ route($formRouteCA,'project_id='.$projectTaskInfo->project_id.'&task_id='.$projectTaskInfo->id) }}" class="btn-link"><small>detail</small></a>
                                                    </td>
                                                    <td>
                                                        @if(isset($checkExpenseReport->submitted_at) &&$checkExpenseReport->submitted_at == null)
                                                            <span class="text-danger">Laporan pengeluaran belum disubmit</span>
                                                        @elseif(isset($checkExpenseReport->approved_at) &&$checkExpenseReport->approved_at == null)
                                                            <span class="text-danger">Laporan pengeluaran belum disetujui QC Expenses</span>
                                                        @elseif(isset($checkExpenseReport->approved_by_pm_at) &&$checkExpenseReport->approved_by_pm_at == null)
                                                            <span class="text-danger">Laporan pengeluaran belum disetujui PM</span>
                                                        @else
                                                            Rp. {{ number_format($projectTotalExpense ?? 0) }}
                                                        @endif
                                                        <br><a href="{{ route($formRouteIndex,'project_id='.$projectTaskInfo->project_id.'&task_id='.$projectTaskInfo->id) }}" class="btn-link"><small>detail</small></a>
                                                    </td>
                                                    <td>Rp. {{ number_format($projectTotalPR ?? 0) }}</td>
                                                    <td>Rp. {{ number_format($paymentSummaryLiniTotal ?? 0) }}</td>
                                                    <td>
                                                        <?php
                                                            //processing the data
                                                            $total = ($projectTotalExpense + $projectTotalPR + $paymentSummaryTotal) - ($projectTotalCA + $paymentSummaryLiniTotal) ;
                                                            
                                                            if ($total > 0) {
                                                                $cssTotal = 'text-info';
                                                            }else{
                                                                $cssTotal = 'text-danger';
                                                            }
                                                        ?>
                                                        @if($total > 0)
                                                            <span class="{{ $cssTotal }}"><strong>Rp. {{ number_format($total ?? 0) }}</strong></span>
                                                        @elseif($total < 0)
                                                            <span class="{{ $cssTotal }}"><strong>(Rp. {{ number_format($total ?? 0) }})</strong></span>
                                                        @else
                                                            <span class="text-info"><strong>LUNAS</strong></span>
                                                        @endif
                                                    </td>
                                                </tr>
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
                                    <!-- <p class="text-right"><b>Total:</b> <strong>Rp. </strong></p> -->
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
                                    <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light mt-1"><i class="fa fa-print"></i></a>

                                    @if($total < 0)
                                        <a href="{{ route($formRoutePaymentSummaryCreate,'project_id='.$projectTaskInfo->project_id.'&task_id='.$projectTaskInfo->id) }}" type="button" class="btn btn-danger mt-1">Upload bukti transfer</a>
                                    @endif

                                    <a href="{{ route($formRouteBack,$projectTaskInfo->id) }}" type="button" class="btn btn-secondary mt-1">Kembali</a>

                                </div>
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-md mt-3">
                                        @if(!isset($projectExpensesReport) || $projectTotalPR < 1)
                                            <div class="alert alert-danger">Penghitungan belum valid. Laporan belum lengkap.</div>
                                        @else
                                            @if($total > 0)
                                                <div class="alert alert-success">Nilai total yang akan ditransfer LINI adalah sebesar <span class="{{ $cssTotal }}"><strong>Rp. {{ number_format(abs($total) ?? 0) }}</strong></span>.</div>
                                            @elseif($total < 0)
                                                <div class="alert alert-warning">Anda harus mengembalikan uang penggunaan dana sebesar <span class="{{ $cssTotal }}"><strong>Rp. {{ number_format(abs($total) ?? 0) }}</strong></span></div>
                                            @else
                                                <div class="alert alert-info">Pembayaran telah <span class="text-info"><strong>LUNAS</strong></span></div>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                @if(count($paymentSummaryDatas) > 0)
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12"><label>Bukti transfer pengembalian dana </label></div>
                                        @foreach($paymentSummaryDatas as $paymentSummaryData)
                                            
                                            <div class="col-md">
                                                <button type="button" class="btn badge-pill text-dark button-img-report" data-toggle="modal" data-target="#cat_id_modal{{ $paymentSummaryData->id }}"><i class="fas fa-eye"></i> </button>

                                                <label>
                                                    <div class="img-report-box">
                                                        @if(preg_match("/\.(gif|png|jpg)$/", $paymentSummaryData->image))
                                                            <img name="image" class="img-fluid img-thumbnail" src="{{ asset('/img/projects/report/payment/tech/'.$paymentSummaryData->image) }}"  />
                                                        @else
                                                            <object width="400" height="500" type="application/pdf" data="{{ asset('/img/projects/report/payment/tech/'.$paymentSummaryData2->image) }}?#zoom=85&scrollbar=0&toolbar=0&navpanes=0">
                                                                <p>Your web browser not supported pdf file.</p>
                                                            </object>
                                                        @endif
                                                    </div>
                                                </label>

                                                <!-- Modal -->
                                                <div class="modal fade" id="cat_id_modal{{ $paymentSummaryData->id }}" tabindex="-1" role="dialog" aria-labelledby="projectImageModal" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                                        <div class="modal-content-img">
                                                            <div class="modal-body text-center">
                                                            <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                                @if(preg_match("/\.(gif|png|jpg)$/", $paymentSummaryData->image))
                                                                    <img name="image" class="img-fluid img-thumbnail" src="{{ asset('/img/projects/report/payment/tech/'.$paymentSummaryData->image) }}"  />
                                                                @else
                                                                    <iframe src="http://docs.google.com/gview?url={{ asset('/img/projects/report/payment/tech/'.$paymentSummaryData->image) }}&embedded=true" style="width:718px; height:700px;" frameborder="0"></iframe>
                                                                @endif
                                                                <div class="alert alert-warning" id="projectImageModal">
                                                                    <h5>
                                                                        <span class="text-uppercase">{{ ucfirst($paymentSummaryData->title) }} </span>
                                                                        <br><span class="text-muted">{{ ucfirst($paymentSummaryData->description) }}</span>
                                                                    </h5>
                                                                </div>
                                                            </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- delete & payment status -->
                                                <div class="col-md mt-1 text-center">
                                                    <div class="approve-button">
                                                        <?php 
                                                            if (isset($paymentSummaryData->reject_status)) {
                                                                if ($paymentSummaryData->reject_status == 1) {
                                                                    $btnPaymentCSS = 'danger';
                                                                }elseif($paymentSummaryData->reject_status == 2 || $paymentSummaryData->reject_status == 3){
                                                                    $btnPaymentCSS = 'success';
                                                                }else{
                                                                    $btnPaymentCSS = 'info';
                                                                }
                                                            }
                                                        ?>
                                                        <button class="btn btn-{{ $btnPaymentCSS }}">
                                                            Rp. {{ number_format($paymentSummaryData->amount) }}
                                                            @if($paymentSummaryData->reject_status == 0)
                                                                @foreach($paymentSummaryStatus as $PSStatus)
                                                                    @if($PSStatus->id == $paymentSummaryData->status)
                                                                        <em>({{ $PSStatus->name }})</em>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <em>(Rejected)</em>
                                                            @endif
                                                            @if($paymentSummaryData->approved_by_pm_at !== null)
                                                                <small>{{ isset($paymentSummaryData->approved_by_pm_at) ? date('l, d F Y',strtotime($paymentSummaryData->approved_by_pm_at)) : '' }}</small>
                                                            @elseif($paymentSummaryData->approved_at !== null)
                                                                <small>{{ isset($paymentSummaryData->approved_at) ? date('l, d F Y',strtotime($paymentSummaryData->approved_at)) : '' }}</small>
                                                            @elseif($paymentSummaryData->submitted_at !== null)
                                                                <small>{{ isset($paymentSummaryData->submitted_at) ? date('l, d F Y',strtotime($paymentSummaryData->submitted_at)) : '' }}</small>
                                                            @else
                                                                <small></small>
                                                            @endif
                                                        </button>

                                                        <form action="{{ route($formRoutePaymentSummaryDestroy, $paymentSummaryData->id) }}" style="display:inline-block" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <!-- hidden data -->

                                                            <input type="text" name="project_id" value="{{ $projectTaskInfo->project_id }}" hidden>
                                                            <input type="text" name="task_id" value="{{ $projectTaskInfo->id }}" hidden>

                                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                                        </form>

                                                        <!-- reject note -->
                                                        @if (isset($paymentSummaryData->reject_note) && $paymentSummaryData->reject_status != 0)
                                                            <div class="alert alert-warning" style="margin-top:2px;">{{ $paymentSummaryData->reject_note }}</div>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @if(count($paymentSummaryLiniDatas) > 0)
                                    <hr>
                                    <div class="row">
                                        @foreach($paymentSummaryLiniDatas as $paymentSummaryData2)
                                            
                                            <div class="col-md-12"><label>Bukti transfer pembayaran </label></div>
                                            <div class="col-md">

                                                <button type="button" class="btn badge-pill text-dark button-img-report" data-toggle="modal" data-target="#cat_id_modal{{ $paymentSummaryData2->id }}"><i class="fas fa-eye"></i> </button>

                                                <label>
                                                    <div class="img-report-box">
                                                        @if(preg_match("/\.(gif|png|jpg)$/", $paymentSummaryData2->image))
                                                            <img name="image" class="img-fluid img-thumbnail" src="{{ asset('/img/projects/report/payment/tech/'.$paymentSummaryData2->image) }}"  />
                                                        @else
                                                            <object width="400" height="500" type="application/pdf" data="{{ asset('/img/projects/report/payment/tech/'.$paymentSummaryData2->image) }}?#zoom=85&scrollbar=0&toolbar=0&navpanes=0">
                                                                <p>Your web browser not supported pdf file.</p>
                                                            </object>
                                                        @endif
                                                    </div>
                                                </label>

                                                <!-- Modal -->
                                                <div class="modal fade" id="cat_id_modal{{ $paymentSummaryData2->id }}" tabindex="-1" role="dialog" aria-labelledby="projectImageModal" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                                        <div class="modal-content-img">
                                                            <div class="modal-body text-center">
                                                            <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                                @if(preg_match("/\.(gif|png|jpg)$/", $paymentSummaryData2->image))
                                                                    <img name="image" class="img-fluid img-thumbnail" src="{{ asset('/img/projects/report/payment/tech/'.$paymentSummaryData2->image) }}"  />
                                                                @else
                                                                    <iframe src="http://docs.google.com/gview?url={{ asset('/img/projects/report/payment/tech/'.$paymentSummaryData2->image) }}&embedded=true" style="width:718px; height:700px;" frameborder="0"></iframe>
                                                                @endif
                                                                <div class="alert alert-warning" id="projectImageModal">
                                                                    <h5>
                                                                        <span class="text-uppercase">{{ ucfirst($paymentSummaryData2->title) }} </span>
                                                                        <br><span class="text-muted">{{ ucfirst($paymentSummaryData2->description) }}</span>
                                                                    </h5>
                                                                </div>
                                                            </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- payment status -->
                                                <div class="col-md mt-1 text-center">
                                                    <div class="approve-button">
                                                        <button class="btn btn-danger">
                                                            Rp. {{ number_format($paymentSummaryData2->amount) }}
                                                            @foreach($paymentSummaryStatus as $PSStatus)
                                                                @if($PSStatus->id == $paymentSummaryData2->status)
                                                                    <em>({{ $PSStatus->name }})</em>
                                                                @endif
                                                            @endforeach
                                                            <small>{{ isset($paymentSummaryData2->submitted_at) ? date('l, d F Y',strtotime($paymentSummaryData2->submitted_at)) : '' }}</small>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

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
