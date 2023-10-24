@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'detail proyek';
    $formRouteIndex = 'admin-projects.index';
    $formRouteUpdate= 'admin-projects.update';
    //task
    $formTaskStore= 'admin-projects-task.store';
    $formTaskEdit= 'admin-projects-task.edit';
    $formTaskDestroy= 'admin-projects-task.destroy';
    //template
    $formTemplateIndex = 'admin-projects-template.index';
    $formTemplateShow = 'admin-projects-template.show';
    //procurement
    $back = 'admin-pr.index';
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
        <small>Nama proyek:</small> <strong><span class="text-info">{{ strtoupper($project->name) }}</span></strong>
        <br><small>Kategori:</small> <strong><span class="text-danger">{{ isset($project->procat_name) ? strtoupper($project->procat_name) : 'Belum dikategorikan' }}</span></strong>
    </div>

    <div class="card-body bg-gray-lini-2">
        <div class="row">
            <!-- project -->
            @if($userDepartment == 1)
                
                <!-- project data -->
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
                <div class="progress w-100 mb-3">

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
                    <div class="col-md mt-2 form-group">
                        <label for="name">Nama <small class="c-red">*</small></label>
                        <input type="text" class="form-control" name="name" value="{{ strtoupper($project->name) }}" data-parsley-minlength="3" readonly>
                    </div>
                    <div class="col-md mt-2 form-group">
                        <label for="number">Nomor <small class="c-red">*</small></label>
                        <input type="text" class="form-control" name="number" value="{{ strtoupper($project->number) }}" data-parsley-minlength="3" readonly>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-md mt-2 form-group">
                        <label for="location">Lokasi </label>
                        <input type="text" class="form-control" name="location" value="{{ old('location') ? old('location') : $project->location }}" data-parsley-minlength="3" readonly>
                    </div>
                    <div class="col-md mt-2 form-group">
                        <label for="amount">Amount <small class="c-red">*</small></label>
                        <input type="number" class="form-control" name="amount" value="{{ old('amount') ? old('amount') : $project->amount }}" readonly>
                        <small class="form-text text-muted">
                            <strong>Rp. {{ old('amount') ? number_format(old('amount')) : number_format($project->amount) }}</strong>
                        </small>
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
                    <div class="col-md mt-2 form-group">
                        <label for="">Customer</label>
                        @if ($project->customer_id)
                            @foreach($dataCustomers as $dataCustomer)
                                @if($dataCustomer->id == $project->customer_id)
                                    <a href="#" class="form-control">{{ ucwords($dataCustomer->firstname).' '.ucwords($dataCustomer->lastname)}}</a>
                                @endif
                            @endforeach
                        @else
                            <input class="form-control" value="Not available" readonly>
                        @endif
                    </div>
                </div>
                <div class="w-100"><hr></div>
                <!-- task data -->
                
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

                                    <span class="text-danger">{{ strtoupper($data->name) }}</span>

                                    <br>PC: 
                                    @if($data->pc_id != null)
                                        @foreach($dataUsers as $dataPC)
                                            @if($dataPC->id == $data->pc_id)
                                                <span class="text-success">{{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname) }}</span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="text-danger">-</span>
                                    @endif

                                    <br>QCD: 
                                    @if($data->qcd_id != null)
                                        @foreach($dataUsers as $dataQCD)
                                            @if($dataQCD->id == $data->qcd_id)
                                                <span class="text-success">{{ ucwords($dataQCD->firstname).' '.ucwords($dataQCD->lastname) }}</span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="text-danger">-</span>
                                    @endif

                                    <br>Teknisi: 
                                    @if($data->tech_id != null)
                                        @foreach($dataTechs as $dataTech)
                                            @if($dataTech->id == $data->tech_id)
                                                <span class="text-success">{{ ucwords($dataTech->firstname).' '.ucwords($dataTech->lastname) }}</span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="text-danger">-</span>
                                    @endif

                                    @if($data->template_id !== null)
                                    <br>
                                        @foreach($projectTemplateDatas as $taskTemplate)
                                            @if($taskTemplate->task_id == $data->id)
                                                {{ ucfirst($taskTemplate->name) }}
                                            @endif
                                        @endforeach
                                    @endif

                                    @if($data->date_start)
                                        <br>Mulai: <span class="text-success">{{ date('l d F Y', strtotime($data->date_start)) }}</span><br>
                                    @endif

                                    @if($data->date_end)
                                        <br>Selesai: <span class="text-info">{{ date('l d F Y', strtotime($data->date_end)) }}</span>
                                    @endif

                                    <div>
                                        <form action="{{ route($formTaskDestroy, $data->id) }}" method="POST">
                                        @method('DELETE')
                                        @csrf
                                            <!-- hidden data -->
                                            <input name="project_id" value="{{ $project->id }}" hidden>  

                                            <a href="{{ route($formTemplateIndex, 'project_id='.$project->id.'&task_id='.$data->id) }}" class='btn btn-icon waves-effect waves-light btn-success t-white mt-1 mb-1'> <i class='fas fa-cogs'></i></a>
                                            
                                            <a href="{{ route($formTaskEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white mt-1 mb-1'> <i class='fas fa-edit' title='Edit'></i></a>

                                            <button type="submit" class="btn btn-danger mt-1 mb-1" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>
                                            
                                        </form>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @else
                    <div class="col-md">
                        <div class="alert alert-warning">Belum ada data.</div>
                        <form action="{{ route($formTaskStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                            @csrf
                            <!-- hidden data -->
                            <input name="project_id" value="{{ $project->id }}" hidden>

                            @if($project->pm_id != null)
                                <input name="pm_id" value="{{ $project->pm_id }}" hidden>
                            @endif
                            
                            <div class="row">
                                <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Nama <small class="c-red">*</small></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') ?? old('name') }}" data-parsley-minlength="3" required>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md mt-2 form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                                    <label for="number">Nomor <small class="c-red">*</small></label>
                                    <input type="text" class="form-control" name="number" value="{{ old('number') ?? old('number') }}" data-parsley-minlength="3" required>
                                </div>
                                <div class="col-md mt-2 form-group{{ $errors->has('budget') ? ' has-error' : '' }}">
                                    <label for="budget">Budget</label>
                                    <input type="number" class="form-control" name="budget" value="{{ old('budget') ?? old('budget') }}">
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md">
                                    <div class="form-group">
                                        <label for=""></label>
                                        <input type="submit" class="btn btn-orange" name="submit" value="Buat task">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
                    
            @endif
            <!-- project -->
            <!-- procurement -->
            <?php /*
            @if($userDepartment == 9)
                <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name">Nama <small class="c-red">*</small></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : $project->name }}" data-parsley-minlength="3" required>
                </div>
                <div class="w-100"></div>
                <div class="col-md mt-2 form-group{{ $errors->has('tech_id') ? ' has-error' : '' }}">
                    <label for="">Pilih Teknisi</label>
                    <select id="tech_id" name="tech_id" class="form-control select2" required>
                        @if (old('tech_id') || $project->tech_id)
                            @foreach($dataTechs as $dataTeknisi)
                                @if($dataTeknisi->id == old('tech_id') || $dataTeknisi->id == $project->tech_id)
                                    <option value="{{ $dataTeknisi->id }}">{{ ucwords($dataTeknisi->firstname).' '.ucwords($dataTeknisi->lastname)}}</option>
                                @endif
                            @endforeach
                            @foreach($dataTechs as $dataTeknisi)
                                @if($dataTeknisi->id != old('tech_id') || $dataTeknisi->id != $project->tech_id)
                                    <option value="{{ $dataTeknisi->id }}">{{ ucwords($dataTeknisi->firstname).' '.ucwords($dataTeknisi->lastname)}}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="0">Pilih Teknisi</option>
                            @foreach($dataTechs as $dataTeknisi)
                                <option value="{{ $dataTeknisi->id }}">{{ ucwords($dataTeknisi->firstname).' '.ucwords($dataTeknisi->lastname)}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="w-100"></div>
                <div class="col-md">
                    <div class="form-group">
                        <label for=""></label>
                        <input type="submit" class="btn btn-info" name="submit" value="Ubah">
                        <a href="{{ route($back) }}" class="btn btn-secondary">Batal</a>
                    </div>
                </div>
            @endif
            */ ?>
            <!-- procurement -->
        </div>
    </div>
    <div class="card-body">
        <div class="col-md">
            <button class="btn btn-orange" type="button" data-toggle="collapse" data-target="#addTask" aria-expanded="false" aria-controls="addTask"> Tambah task </button>

            <a href="{{ route($formRouteIndex, 'status='.$project->status) }}" class="btn btn-blue-lini">Batal</a>

            <form id="addTask" class="collapse" action="{{ route($formTaskStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                @csrf
                <!-- hidden data -->
                <input name="project_id" value="{{ $project->id }}" hidden>

                @if($project->pm_id != null)
                    <input name="pm_id" value="{{ $project->pm_id }}" hidden>
                @endif

                <div class="row">
                    <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name">Nama <small class="c-red">*</small></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') ?? old('name') }}" data-parsley-minlength="3" required>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-md mt-2 form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                        <label for="number">Nomor <small class="c-red">*</small></label>
                        <input type="text" class="form-control" name="number" value="{{ old('number') ?? old('number') }}" data-parsley-minlength="3" required>
                    </div>
                    <div class="col-md mt-2 form-group{{ $errors->has('budget') ? ' has-error' : '' }}">
                        <label for="budget">Budget</label>
                        <input type="number" class="form-control" name="budget" value="{{ old('budget') ?? old('budget') }}">
                    </div>
                    <div class="w-100"></div>
                    <div class="col-md">
                        <div class="form-group">
                            <label for=""></label>
                            <input type="submit" class="btn btn-orange" name="submit" value="Tambah">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
