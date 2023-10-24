@extends('layouts.dashboard-form')

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
    $formTemplateShow = '#';
    //procurement
    $back = 'admin-pr.index';
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
    <div class="card-header text-center">
        Nama proyek: <strong><span class="text-info">{{ ucwords($project->name) }}</span></strong>
        <br>Kategori: <strong><span class="text-danger">{{ isset($project->procat_name) ? strtoupper($project->procat_name) : 'Belum dikategorikan' }}</span></strong>
    </div>
    <div class="card-body">
        @if ($errors->any())
        <div class="col-md">
            <div class="alert alert-danger">
                <small class="form-text">
                    <strong>{{ $errors->first() }}</strong>
                </small>
            </div>
        </div>
        @endif
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <!-- project -->
                        @if($userDepartment == 1)
                            

                            <!-- test -->

                            <div class="container">
                                <ul class="nav nav-pills tabs-detail">
                                    <li class="btn btn-info mr-1"><a data-toggle="pill" href="#project">Detail Proyek</a></li>
                                    <li class="btn btn-info mr-1"><a data-toggle="pill" href="#task">Task</a></li>
                                    <li class="btn btn-info mr-1"><a data-toggle="pill" href="#template">Template</a></li>
                                </ul>
                                
                                <div class="tab-content">
                                    <!-- project data -->
                                    <div id="project" class="tab-pane fade in active show">
                                        <div class="progress mb-3">
                                            <?php
                                                #default
                                                $projectStatus = 0;
                                                $progressValue = 0;
                                                $progressMax   = 4;
                                                #set conditions and value 
                                                if (isset($project->status)) {
                                                    $projectStatus = $project->status;
                                                    $progressValue = $projectStatus;
                                                }
                                            ?>
                                            <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ $progressValue }}" aria-valuemin="0" aria-valuemax="{{ $progressMax }}" style="width:{{ ($progressValue/$progressMax) * 100}}%">
                                                {{ ($progressValue/$progressMax) * 100}}% Selesai (success)
                                            </div>
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
                                            <div class="col-md mt-2 form-group{{ $errors->has('budget') ? ' has-error' : '' }}">
                                                <label for="budget">Budget <small class="c-red">*</small></label>
                                                <input type="number" class="form-control" name="budget" value="{{ old('budget') ? old('budget') : $project->budget }}" readonly>
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
                                                <label for="">Project Coordinator</label>
                                                @if ($project->pc_id)
                                                    @foreach($dataUsers as $dataPC)
                                                        @if($dataPC->id == $project->pc_id)
                                                            <a href="#" class="form-control">{{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname)}}</a>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <input class="form-control" value="Not available" readonly>
                                                @endif
                                            </div>
                                            <div class="col-md mt-2 form-group">
                                                <label for="">QC Document</label>
                                                @if ($project->ad_id)
                                                    @foreach($dataUsers as $dataQCD)
                                                        @if($dataQCD->id == $project->ad_id)
                                                            <a href="#" class="form-control">{{ ucwords($dataQCD->firstname).' '.ucwords($dataQCD->lastname)}}</a>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <input class="form-control" value="Not available" readonly>
                                                @endif
                                            </div>
                                            <div class="col-md mt-2 form-group">
                                                <label for="">Technician</label>
                                                @if ($project->tech_id)
                                                    @foreach($dataTechs as $dataTech)
                                                        @if($dataTech->id == $project->tech_id)
                                                            <a href="#" class="form-control">{{ ucwords($dataTech->firstname).' '.ucwords($dataTech->lastname)}}</a>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <input class="form-control" value="Not available" readonly>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- task data -->
                                    <div id="task" class="tab-pane fade">
                                        @if (sizeof($projectTaskDatas) > 0)
                                            <div class="container-fluid">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="table-responsive">
                                                            <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                                                                <thead>
                                                                    <tr>
                                                                    <th>#</th>
                                                                    <th>Nama task</th>
                                                                    <th>Project manager</th>
                                                                    <th>Project coordinator</th>
                                                                    <th>QC Document</th>
                                                                    <th>Teknisi</th>
                                                                    <th>Template</th>
                                                                    <th>Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $i = 1; ?>
                                                                    @foreach ($projectTaskDatas as $data)
                                                                        <tr>
                                                                            <td> {{ $i }} </td>
                                                                            <td>{{ ucwords($data->name) }}</td>
                                                                            <td>
                                                                                @if($data->pm_id != null)
                                                                                    @foreach($users as $dataPM)
                                                                                        @if($dataPM->id == $data->pm_id)
                                                                                            <span class="text-success">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) }}</span>
                                                                                        @endif
                                                                                    @endforeach
                                                                                @else
                                                                                    <span class="text-danger">Belum ada PM</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($data->pc_id != null)
                                                                                    @foreach($users as $dataPC)
                                                                                        @if($dataPC->id == $data->pc_id)
                                                                                            <span class="text-success">{{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname) }}</span>
                                                                                        @endif
                                                                                    @endforeach
                                                                                @else
                                                                                    <span class="text-danger">Belum ada PC</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($data->ad_id != null)
                                                                                    @foreach($users as $dataQCD)
                                                                                        @if($dataQCD->id == $data->ad_id)
                                                                                            <span class="text-success">{{ ucwords($dataQCD->firstname).' '.ucwords($dataQCD->lastname) }}</span>
                                                                                        @endif
                                                                                    @endforeach
                                                                                @else
                                                                                    <span class="text-danger">Belum ada QC Document</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($data->tech_id != null)
                                                                                    @foreach($techs as $dataTech)
                                                                                        @if($dataTech->id == $data->tech_id)
                                                                                            <span class="text-success">{{ ucwords($dataTech->firstname).' '.ucwords($dataTech->lastname) }}</span>
                                                                                        @endif
                                                                                    @endforeach
                                                                                @else
                                                                                    <span class="text-danger">Belum ada teknisi</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($data->template_id !== null)
                                                                                    @foreach($projectTemplateDatas as $taskTemplate)
                                                                                        @if($taskTemplate->task_id == $data->id)
                                                                                            {{ ucfirst($taskTemplate->name) }}
                                                                                        @endif
                                                                                    @endforeach
                                                                                @else
                                                                                    <span class="text-danger">Belum ada template</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                <form action="{{ route($formTaskDestroy, $data->id) }}" method="POST">
                                                                                @method('DELETE')
                                                                                @csrf
                                                                                    <!-- hidden data -->
                                                                                    <input name="project_id" value="{{ $project->id }}" hidden>
                                                                                    
                                                                                    <a href="{{ route($formTaskEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white mt-1 mb-1'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>
                                                                                    <button type="submit" class="btn btn-danger mt-1 mb-1" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                                                                </form>
                                                                            </td>
                                                                        </tr>
                                                                        <?php $i++; ?>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                    <button class="btn btn-info" type="button" data-toggle="collapse" data-target="#addTask" aria-expanded="false" aria-controls="addTask">
                                                        Tambah task
                                                    </button>

                                                <form id="addTask" class="collapse" action="{{ route($formTaskStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
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
                                                            <label for="number">Nomor <small class="c-red">*</small></label>
                                                            <input type="text" class="form-control" name="number" value="{{ old('number') ?? old('number') }}" data-parsley-minlength="3" required>
                                                        </div>
                                                        <div class="col-md mt-2 form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                                            <label for="amount">Amount</label>
                                                            <input type="number" class="form-control" name="amount" value="{{ old('amount') ?? old('amount') }}">
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


                                            </div> <!-- container-fluid -->
                                        @else
                                            <div class="alert alert-warning">Belum ada data.</div>
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
                                                        <label for="number">Nomor <small class="c-red">*</small></label>
                                                        <input type="text" class="form-control" name="number" value="{{ old('number') ?? old('number') }}" data-parsley-minlength="3" required>
                                                    </div>
                                                    <div class="col-md mt-2 form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                                        <label for="amount">Amount</label>
                                                        <input type="number" class="form-control" name="amount" value="{{ old('amount') ?? old('amount') }}">
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
                                        @endif
                                    </div>
                                    <!-- template data -->
                                    <div id="template" class="tab-pane fade">
                                        @if (sizeof($projectTemplateDatas) > 0)
                                            <div class="container-fluid">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="table-responsive">
                                                            <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                                                                <thead>
                                                                    <tr>
                                                                    <th>#</th>
                                                                    <th>Nama template</th>
                                                                    <th>Dibuat oleh</th>
                                                                    <th>Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $i = 1; ?>
                                                                    @foreach ($projectTemplateDatas as $projectTemplateData)
                                                                        <tr>
                                                                            <td> {{ $i }} </td>
                                                                            <td>{{ isset($projectTemplateData->name) ? ucwords($projectTemplateData->name) : 'Belum ada data' }}</td>
                                                                            <td>
                                                                                @if($projectTemplateData->publisher_id != null)
                                                                                    @if($projectTemplateData->publisher_type == 'user')
                                                                                        @foreach($dataUsers as $dataPM)
                                                                                            @if($dataPM->id == $projectTemplateData->publisher_id)
                                                                                                <span class="text-success">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) }}</span>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    @else
                                                                                        <span class="text-success">You</span>
                                                                                    @endif

                                                                                @else
                                                                                    <span class="text-danger">Belum ada data</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                <form action="#" method="POST">
                                                                                @method('DELETE')
                                                                                @csrf
                                                                                    <!-- hidden data -->
                                                                                    <input name="project_id" value="{{ $project->id }}" hidden>
                                                                                    
                                                                                    <a href="#" class='btn btn-icon waves-effect waves-light btn-info t-white mt-1 mb-1'> <i class='fas fa-eye' title='Show'></i> Show</a>

                                                                                    <a href="#" class='btn btn-icon waves-effect waves-light btn-info t-white mt-1 mb-1'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>

                                                                                    <button type="submit" class="btn btn-danger mt-1 mb-1" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                                                                </form>
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
                                        @else
                                            <div class="alert alert-warning">Belum ada data.</div>
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
                                                        <label for="number">Nomor <small class="c-red">*</small></label>
                                                        <input type="text" class="form-control" name="number" value="{{ old('number') ?? old('number') }}" data-parsley-minlength="3" required>
                                                    </div>
                                                    <div class="col-md mt-2 form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                                        <label for="amount">Amount</label>
                                                        <input type="number" class="form-control" name="amount" value="{{ old('amount') ?? old('amount') }}">
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
                                        @endif


                                        <button class="btn btn-info" type="button" data-toggle="collapse" data-target="#allTemplates" aria-expanded="false" aria-controls="allTemplates">
                                            Pilih template
                                        </button>
                                        <button class="btn btn-info" type="button" data-toggle="collapse" data-target="#addTemplate" aria-expanded="false" aria-controls="addTemplate">
                                            Buat template
                                        </button>

                                        <!-- select a template -->
                                        <div class="collapse mt-1" id="allTemplates">
                                            @if (sizeof($allProjectTemplates) > 0)
                                                <div class="container-fluid">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="table-responsive">
                                                                <table id="" class="display table table-bordered table-bordered dt-responsive nowrap">
                                                                    <thead>
                                                                        <tr>
                                                                        <th>#</th>
                                                                        <th>Nama template</th>
                                                                        <th>Dibuat oleh</th>
                                                                        <th>Aksi</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php $i = 1; ?>
                                                                        @foreach ($allProjectTemplates as $allProjectTemplate)
                                                                            <tr>
                                                                                <td> {{ $i }} </td>
                                                                                <td>{{ isset($allProjectTemplate->name) ? ucwords($allProjectTemplate->name) : 'Belum ada data' }}</td>
                                                                                <td>
                                                                                    @if($allProjectTemplate->publisher_id != null)
                                                                                        @if($allProjectTemplate->publisher_type == 'user')
                                                                                            @foreach($dataUsers as $dataPM)
                                                                                                @if($dataPM->id == $allProjectTemplate->publisher_id)
                                                                                                    <span class="text-success">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname) }}</span>
                                                                                                @endif
                                                                                            @endforeach
                                                                                        @else
                                                                                            <span class="text-success">You</span>
                                                                                        @endif

                                                                                    @else
                                                                                        <span class="text-danger">Belum ada data</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    <form action="{{ route($formTaskDestroy, $data->id) }}" method="POST">
                                                                                    @method('DELETE')
                                                                                    @csrf
                                                                                        <!-- hidden data -->
                                                                                        <input name="project_id" value="{{ $project->id }}" hidden>
                                                                                        
                                                                                        <a href="{{ route($formTaskEdit, $allProjectTemplate->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white mt-1 mb-1'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>

                                                                                        <button type="submit" class="btn btn-danger mt-1 mb-1" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                                                                    </form>
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
                                            @else
                                                <div class="alert alert-warning">Belum ada data.</div>
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
                                                            <label for="number">Nomor <small class="c-red">*</small></label>
                                                            <input type="text" class="form-control" name="number" value="{{ old('number') ?? old('number') }}" data-parsley-minlength="3" required>
                                                        </div>
                                                        <div class="col-md mt-2 form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                                            <label for="amount">Amount</label>
                                                            <input type="number" class="form-control" name="amount" value="{{ old('amount') ?? old('amount') }}">
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
                                            @endif
                                        </div>
                                        <!-- select a template end -->





                                        <!-- add template -->
                                        <form id="addTemplate" class="collapse" action="{{ route($formTaskStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
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
                                                    <label for="number">Nomor <small class="c-red">*</small></label>
                                                    <input type="text" class="form-control" name="number" value="{{ old('number') ?? old('number') }}" data-parsley-minlength="3" required>
                                                </div>
                                                <div class="col-md mt-2 form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                                    <label for="amount">Amount</label>
                                                    <input type="number" class="form-control" name="amount" value="{{ old('amount') ?? old('amount') }}">
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
                                        <!-- add template end -->

                                    </div>
                                </div>
                            </div>


                            <div class="w-100"></div>


                            <!-- test -->





                            <div class="col-md mt-2">
                                <div class="form-group">
                                    <a href="{{ route($formRouteIndex, 'status='.$project->status) }}" class="btn btn-secondary">Batal</a>
                                </div>
                            </div>
                        <!-- project -->
                        <!-- procurement -->
                        @elseif($userDepartment == 9)
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
                        <!-- procurement -->
                    </div>
                </div>
            </div>
        </div> <!-- container-fluid -->
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')

@endsection
