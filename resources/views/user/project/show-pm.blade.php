@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'detail proyek';
    $formRouteIndex = 'user-projects.index';
    $formRouteUpdate= 'user-projects.update';
    //task
        $formTaskStore= 'user-projects-task.store';
        $formTaskEdit= 'user-projects-task.edit';
        $formTaskDestroy= 'user-projects-task.destroy';
    //expense
        $formRouteExpensesIndex = 'user-projects-expense.index';
    //cash advance
        $formRouteCashAdvanceIndex = 'user-projects-ca.index';
    //PR
        $formPRIndex = 'user-pr.index';
    //template
        $formTemplateIndex = 'user-projects-template.index';
        $formTemplateShow = 'user-projects-template.show';
        $formTemplateEdit = 'user-projects-template.edit';
        $formTemplateDestroy = 'user-projects-template.destroy';
    //report all
        $formReportAllShow = 'user-projects-report-qc.show';
    //payment summary
        $formRoutePaymentSummaryIndex = 'user-project-payment-summary.index';
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
    <div class="card-header text-center bb-orange">
        <small>Nama proyek: </small><strong><span class="text-info">{{ strtoupper($project->name) }}</span></strong>
        <br><small>Kategori: </small><strong><span class="text-danger">{{ isset($project->procat_name) ? strtoupper($project->procat_name) : 'Belum dikategorikan' }}</span></strong>
    </div>
    <div class="card-body bg-gray-lini-2">
        <div class="row">
            <!-- project -->
            @if($userDepartment == 1)
                <div class="col-md">
                    <!-- project data -->
                    <div id="project">
                        <?php
                            #default
                            $projectStatus = 0;
                            $progressValue = 0;
                            $progressMax   = $taskCount * 4;
                            #taskcount
                            $totalProgressedTask = $taskStatus0 * 0 + $taskStatus1 + $taskStatus2 * 2 + $taskStatus3 * 3 + $taskStatus4 * 4;
                            #set conditions and value 
                            if (isset($totalProgressedTask)) {
                                $projectStatus = $totalProgressedTask;
                                $progressValue = $projectStatus;
                            }
                        ?>
                        <div class="progress mb-3">

                            @if($progressMax < 1)
                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ $progressValue }}" aria-valuemin="0" aria-valuemax="{{ $progressMax }}" style="width:0%">
                                    
                                </div>
                            @else
                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ $progressValue }}" aria-valuemin="0" aria-valuemax="{{ $progressMax }}" style="width:{{ ($progressValue/$progressMax) * 100}}%">
                                    {{ ($progressValue/$progressMax) * 100}}% Selesai (success)
                                </div>
                            @endif

                        </div>
                        <div class="row">
                            <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name">Nama <small class="c-red">*</small></label>
                                <input type="text" class="form-control" name="name" value="{{ strtoupper($project->name) }}" data-parsley-minlength="3" readonly>
                            </div>
                            <div class="col-md mt-2 form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                                <label for="number">Nomor <small class="c-red">*</small></label>
                                <input type="text" class="form-control" name="number" value="{{ strtoupper($project->number) }}" data-parsley-minlength="3" readonly>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md mt-2 form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                                <label for="location">Lokasi </label>
                                <input type="text" class="form-control" name="location" value="{{ old('location') ? old('location') : $project->location }}" data-parsley-minlength="3" readonly>
                            </div>
                            <div class="col-md mt-2 form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                <label for="amount">Amount <small class="c-red">*</small></label>
                                <input type="number" class="form-control" name="amount" value="{{ old('amount') ? old('amount') : $project->amount }}" readonly>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md mt-2 form-group">
                                <label for="">Project Manager</label>
                                @if ($project->pm_id)
                                    @foreach($dataUsers as $dataPM)
                                        @if($dataPM->id == $project->pm_id)
                                            <a href="#" class="form-control">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname)}}</a>
                                        @endif
                                    @endforeach
                                @else
                                    <input class="form-control" value="Not available" readonly>
                                @endif
                            </div>

                        </div>
                    </div>
                    <hr>
                    <!-- task data -->
                    <div id="task">
                        @if (count($projectTaskDatas) > 0)
                        <div class="col-md-12">
                            <div class="row">
                                @foreach ($projectTaskDatas as $data)
                                    <div class="col-md-4 p-2">
                                        <div class="bg-card-box br-5 p-2">
                                            <?php
                                                if ($data->status == 4) {
                                                    $badge = 'success';
                                                }elseif($data->status <= 1){
                                                    $badge = 'danger';
                                                }else{
                                                    $badge = 'info';
                                                }
                                            ?>
                                            <span class="badge badge-{{ $badge }} float-right">
                                                @if($data->status == 0)
                                                    new
                                                @else
                                                    @foreach($dataTaskStatus as $taskStatus)
                                                        @if($taskStatus->id == $data->status)
                                                            {{ $taskStatus->name }}
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </span>

                                            <span class="text-danger"><strong>{{ strtoupper($data->name) }}</strong></span>

                                            <br><small>PC:</small>
                                            @if($data->pc_id != null)
                                                @foreach($dataUsers as $dataPC)
                                                    @if($dataPC->id == $data->pc_id)
                                                        <span class="text-success">{{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname) }}</span>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="text-danger">-</span>
                                            @endif

                                            <br><small>QCD:</small> 
                                            @if($data->qcd_id != null)
                                                @foreach($dataUsers as $dataQCD)
                                                    @if($dataQCD->id == $data->qcd_id)
                                                        <span class="text-success">{{ ucwords($dataQCD->firstname).' '.ucwords($dataQCD->lastname) }}</span>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="text-danger">-</span>
                                            @endif

                                            <br><small>QCE:</small> 
                                            @if($data->qce_id != null)
                                                @foreach($dataUsers as $dataQCE)
                                                    @if($dataQCE->id == $data->qce_id)
                                                        <span class="text-success">{{ ucwords($dataQCE->firstname).' '.ucwords($dataQCE->lastname) }}</span>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="text-danger">-</span>
                                            @endif

                                            <br><small>QCT:</small> 
                                            @if($data->qct_id != null)
                                                @foreach($dataUsers as $dataQCT)
                                                    @if($dataQCT->id == $data->qct_id)
                                                        <span class="text-success">{{ ucwords($dataQCT->firstname).' '.ucwords($dataQCT->lastname) }}</span>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="text-danger">-</span>
                                            @endif

                                            <br><small>Teknisi: </small>
                                            @if($data->tech_id != null)
                                                @foreach($dataTechs as $dataTech)
                                                    @if($dataTech->id == $data->tech_id)
                                                        <span class="text-success">{{ ucwords($dataTech->firstname).' '.ucwords($dataTech->lastname) }}</span>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span class="text-danger">-</span>
                                            @endif

                                            <br>
                                            @if($data->date_start)
                                                <small>Mulai:</small> <span class="text-success">{{ date('l d F Y', strtotime($data->date_start)) }}</span><br>
                                            @endif

                                            @if($data->date_end)
                                                <small>Selesai:</small> <span class="text-info">{{ date('l d F Y', strtotime($data->date_end)) }}</span>
                                            @endif

                                            <div>
                                                <!-- edit task -->
                                                <a href="{{ route($formTaskEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white mt-1 mb-1'> <i class='fas fa-edit' title='Edit'></i></a>
                                                <!-- view expense -->
                                                <a href="{{ route($formRouteExpensesIndex,'project_id='.$project->id.'&task_id='.$data->id) }}" class="btn btn-danger mt-1 mb-1"><i class="fas fa-file-invoice-dollar"  title='Pengeluaran'></i></a>
                                                <!-- view cash advance -->
                                                <a href="{{ route($formRouteCashAdvanceIndex,'project_id='.$project->id.'&task_id='.$data->id) }}" class="btn btn-pink mt-1 mb-1"><i class="fas fa-money-check-alt"  title='Cash advance'></i></a>
                                                <!-- view PR -->
                                                <a href="{{ route($formPRIndex, 'project_id='.$project->id.'&task_id='.$data->id) }}" class='btn btn-icon waves-effect waves-light btn-warning t-white mt-1 mb-1'> <i class='fas fa-donate' title='Purchase Requisition (PR)'></i></a>
                                                <!-- view template -->
                                                <a href="{{ route($formTemplateIndex, 'project_id='.$project->id.'&task_id='.$data->id) }}" class='btn btn-icon waves-effect waves-light btn-success t-white mt-1 mb-1'> <i class='fas fa-cogs'  title='Template'></i></a>
                                                <!-- view report all -->
                                                <a href="{{ route($formReportAllShow, $data->id.'?project_id='.$project->id) }}" class='btn btn-icon waves-effect waves-light btn-warning t-white mt-1 mb-1'> <i class='fas fa-eye'  title='Template'></i></a>
                                                <!-- payment summary report -->
                                                <a href="{{ route($formRoutePaymentSummaryIndex,'project_id='.$project->id.'&task_id='.$data->id) }}" class="btn btn-secondary mt-1 mb-1"><i class="fas fa-file-signature" title='Summary Pembayaran'></i></a>
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                            <div class="alert alert-warning">Belum ada data.</div>
                        @endif

                        <div class="col-12">
                            <?php 
                                #$projectTaskDatas->setPath('user-projects?project_id='.$infoTaskProject->project_id.'&task_id='.$infoTaskProject->id);
                            ?>
                            {{ $projectTaskDatas->links() }}
                        </div>
                    </div>
                </div>
            @endif
            <!-- project -->
        </div>
    </div>
    <div class="card-body">
        <div class="col-md">
            <button type="button" class="btn btn-orange" data-toggle="collapse" data-target="#add_task" aria-expanded="false" aria-controls="add_task"><i class="fas fa-plus"></i> Tambah task</button>

            <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini">kembali</a>

            <div class="collapse" id="add_task">
                <form action="{{ route($formTaskStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                    @csrf
                    <!-- hidden data -->
                    <input name="project_id" value="{{ $project->id }}" hidden>
                    
                    <div class="row">
                        <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name">Nama <small class="c-red">*</small></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') ?? old('name') }}" data-parsley-minlength="3" required>
                        </div>
                        <div class="w-100"></div>
                        <div class="col-md mt-2 form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                            <?php $task_number = $taskNumber->number; $task_number++; ?>
                            <label for="number">Nomor<small class="c-red">*</small></label>
                            <input type="text" class="form-control" name="number" value="{{ $task_number }}" readonly>
                        </div>
                        <div class="col-md mt-2 form-group{{ $errors->has('budget') ? ' has-error' : '' }}">
                            <label for="budget">Budget</label>
                            <input type="number" class="form-control" name="budget" value="{{ old('budget') ?? old('budget') }}">
                        </div>
                        <div class="w-100"></div>
                        <div class="col-md">
                            <div class="form-group">
                                <label for=""></label>
                                <input type="submit" class="btn btn-info" name="submit" value="Tambah task">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body bg-gray-lini-2">
        <div class="row mt-2">
            <div class="w-100"><small>Keterangan:</small></div>
            <div class="col-md-4">
                <small>
                    <br><i class="fas fa-file-invoice-dollar icon-width"></i> Pengeluaran
                    <br><i class="fas fa-money-check-alt icon-width"></i> Lihat cash advance
                </small>
            </div>
            <div class="col-md-4">
                <small>
                    <br><i class='fas fa-donate icon-width'></i> Purchase Requisition (PR)
                    <br><i class='fas fa-cogs icon-width'></i> Template
                </small>
            </div>
            <div class="col-md-4">
                <small>
                    <br><i class="fas fa-file-signature icon-width"></i> Summary pembayaran
                </small>
            </div>
        </div>
    </div>
</div> <!-- card -->
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
