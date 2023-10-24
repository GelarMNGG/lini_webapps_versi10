@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'edit task';
    $formRouteIndex = 'user-projects.show';
    $formRouteUpdate= 'user-projects-task.update';
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
    <div class="card-header text-center bb-orange">
        Nama proyek: <strong><span class="text-info">{{ strtoupper($taskData->project_name) }}</span></strong>
        <br>Nama task: <strong><span class="text-danger">{{ isset($taskData->name) ? strtoupper($taskData->name) : 'Tidak ada data' }}</span></strong>
    </div>
    <form action="{{ route($formRouteUpdate, $taskData->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method('PUT')
        <div class="card-body bg-gray-lini-2">
            
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- hidden data -->
                        <input type="text" name="status" value="{{ $taskData->status }}" hidden>
                        <input name="project_id" value="{{ $taskData->project_id }}" hidden>

                        <div class="row">
                            <div class="progress w-100 mb-3">
                                <?php
                                    #default
                                    $taskStatus = 0;
                                    $progressValue = 0;
                                    $progressMax   = 4;
                                    #set conditions and value 
                                    if (isset($taskData->status)) {
                                        $taskStatus = $taskData->status;
                                        $progressValue = $taskStatus;
                                    }
                                ?>
                                <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="{{ $progressValue }}" aria-valuemin="0" aria-valuemax="{{ $progressMax }}" style="width:{{ ($progressValue/$progressMax) * 100}}%">
                                    {{ ($progressValue/$progressMax) * 100}}% Selesai 
                                    @foreach($dataTaskStatus as $taskStatusName)
                                        @if($taskStatusName->id == $taskData->status)
                                            (tahap: {{ $taskStatusName->name }})
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- project -->
                            @if(Auth::user()->user_level == 3 && $userDepartment == 1)
                                <div class="w-100"></div>
                                <div class="col-md mt-2 form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                                    <label for="number">Nomor <small class="c-red">*</small></label>
                                    <input type="text" class="form-control" name="number" value="{{ old('number') ? old('number') : strtoupper($taskData->number) }}" readonly>
                                </div>
                                <div class="col-md mt-2 form-group{{ $errors->has('budget') ? ' has-error' : '' }}">
                                    <label for="budget">Budget</label>
                                    <input type="number" class="form-control" name="budget" value="{{ old('budget') ? old('budget') : $taskData->budget }}" readonly>
                                </div>
                                <div class="w-100"></div>
                                
                                <div class="col-md mt-2">
                                    <label>Project Coordinator</label>
                                    <select name="pc_id" class="form-control select2" required>
                                    <?php 
                                        //qct_id
                                        if(old('pc_id') != null) {
                                            $pc_id = old('pc_id');
                                        }elseif(isset($taskData->pc_id)){
                                            $pc_id = $taskData->pc_id;
                                        }else{
                                            $pc_id = null;
                                        }
                                    ?>
                                        @if($pc_id != null)
                                            @foreach($dataPCs as $dataPC)
                                                @if($dataPC->id == $pc_id)
                                                    <option value="{{ $dataPC->id }}">{{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname)}}</option>
                                                @endif
                                            @endforeach
                                            @foreach($dataPCs as $dataPC)
                                                @if($dataPC->id != $pc_id)
                                                    <option value="{{ $dataPC->id }}">{{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname)}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="0">Pilih PC</option>
                                            @foreach($dataPCs as $dataPC)
                                                <option value="{{ $dataPC->id }}">{{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname)}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md mt-2">
                                    <label>QC Document</label>
                                    <select name="qcd_id" class="form-control select2" required>
                                    <?php 
                                        //qct_id
                                        if(old('qcd_id') != null) {
                                            $qcd_id = old('qcd_id');
                                        }elseif(isset($taskData->qcd_id)){
                                            $qcd_id = $taskData->qcd_id;
                                        }else{
                                            $qcd_id = null;
                                        }
                                    ?>
                                        @if($qcd_id != null)
                                            @foreach($dataQCDs as $dataQCD)
                                                @if($dataQCD->id == $qcd_id)
                                                    <option value="{{ $dataQCD->id }}">{{ ucwords($dataQCD->firstname).' '.ucwords($dataQCD->lastname)}}</option>
                                                @endif
                                            @endforeach
                                            @foreach($dataQCDs as $dataQCD)
                                                @if($dataQCD->id != $qcd_id)
                                                    <option value="{{ $dataQCD->id }}">{{ ucwords($dataQCD->firstname).' '.ucwords($dataQCD->lastname)}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="0">Pilih QCD</option>
                                            @foreach($dataQCDs as $dataQCD)
                                                <option value="{{ $dataQCD->id }}">{{ ucwords($dataQCD->firstname).' '.ucwords($dataQCD->lastname)}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md mt-2">
                                    <label>QC Tools</label>
                                    <select name="qct_id" class="form-control select2" required>
                                    <?php 
                                        //qct_id
                                        if(old('qct_id') != null) {
                                            $qct_id = old('qct_id');
                                        }elseif(isset($taskData->qct_id)){
                                            $qct_id = $taskData->qct_id;
                                        }else{
                                            $qct_id = null;
                                        }
                                    ?>
                                        @if($qct_id != null)
                                            @foreach($dataQCTs as $dataQCT)
                                                @if($dataQCT->id == $qct_id)
                                                    <option value="{{ $dataQCT->id }}">{{ ucwords($dataQCT->firstname).' '.ucwords($dataQCT->lastname)}}</option>
                                                @endif
                                            @endforeach
                                            @foreach($dataQCTs as $dataQCT)
                                                @if($dataQCT->id != $qct_id)
                                                    <option value="{{ $dataQCT->id }}">{{ ucwords($dataQCT->firstname).' '.ucwords($dataQCT->lastname)}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="0">Pilih QCT</option>
                                            @foreach($dataQCTs as $dataQCT)
                                                <option value="{{ $dataQCT->id }}">{{ ucwords($dataQCT->firstname).' '.ucwords($dataQCT->lastname)}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md mt-2">
                                    <label>QC Expenses</label>
                                    <select name="qce_id" class="form-control select2" required>
                                    <?php 
                                        //qce_id
                                        if(old('qce_id') != null) {
                                            $qce_id = old('qce_id');
                                        }elseif(isset($taskData->qce_id)){
                                            $qce_id = $taskData->qce_id;
                                        }else{
                                            $qce_id = null;
                                        }
                                    ?>
                                        @if($qce_id != null)
                                            @foreach($dataQCEs as $dataQCE)
                                                @if($dataQCE->id == $qce_id)
                                                    <option value="{{ $dataQCE->id }}">{{ ucwords($dataQCE->firstname).' '.ucwords($dataQCE->lastname)}}</option>
                                                @endif
                                            @endforeach
                                            @foreach($dataQCEs as $dataQCE)
                                                @if($dataQCE->id != $qce_id)
                                                    <option value="{{ $dataQCE->id }}">{{ ucwords($dataQCE->firstname).' '.ucwords($dataQCE->lastname)}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="0">Pilih QCT</option>
                                            @foreach($dataQCEs as $dataQCE)
                                                <option value="{{ $dataQCE->id }}">{{ ucwords($dataQCE->firstname).' '.ucwords($dataQCE->lastname)}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md mt-2">
                                    <label>Teknisi</label>
                                    <select name="tech_id" class="form-control select2">
                                    <?php 
                                        //tech_id
                                        if(old('tech_id') != null) {
                                            $tech_id = old('tech_id');
                                        }elseif(isset($taskData->tech_id)){
                                            $tech_id = $taskData->tech_id;
                                        }else{
                                            $tech_id = null;
                                        }
                                    ?>
                                        @if($tech_id != null)
                                            @foreach($dataTechs as $dataTech)
                                                @if($dataTech->id == $tech_id)
                                                    <option value="{{ $dataTech->id }}">{{ ucwords($dataTech->firstname).' '.ucwords($dataTech->lastname)}}</option>
                                                @endif
                                            @endforeach
                                            @foreach($dataTechs as $dataTech)
                                                @if($dataTech->id != $tech_id)
                                                    <option value="{{ $dataTech->id }}">{{ ucwords($dataTech->firstname).' '.ucwords($dataTech->lastname)}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="0">Pilih Teknisi</option>
                                            @foreach($dataTechs as $dataTech)
                                                <option value="{{ $dataTech->id }}">{{ ucwords($dataTech->firstname).' '.ucwords($dataTech->lastname)}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="w-100 mt-2"></div>
                                <div class="col-md mt-2">
                                    <label>Tanggal mulai</label>
                                    <?php 
                                        //date start
                                        if(old('date_start') != null) {
                                            $dateStart = old('date_start');
                                        }elseif(isset($taskData->date_start)){
                                            $dateStart = date('Y-m-d',strtotime($taskData->date_start));
                                        }else{
                                            $dateStart = null;
                                        }
                                        //date end
                                        if(old('date_end') != null) {
                                            $dateEnd = old('date_end');
                                        }elseif(isset($taskData->date_end)){
                                            $dateEnd = date('Y-m-d',strtotime($taskData->date_end));
                                        }else{
                                            $dateEnd = null;
                                        }
                                    ?>
                                    <input type="date" class="form-control{{ $errors->has('date_start') ? ' has-error' : '' }}" name="date_start" value="{{ $dateStart }}" placeholder="dd/mm/yyyy">
                                    @if ($errors->has('task_date'))
                                        <small class="form-text text-muted">
                                            <strong>{{ $errors->first('date_start') }}</strong>
                                        </small>
                                    @endif
                                </div>
                                <div class="col-md mt-2">
                                    <label>Tanggal selesai</label>
                                    <input type="date" class="form-control{{ $errors->has('date_end') ? ' has-error' : '' }}" name="date_end" value="{{ $dateEnd }}" max-date="{{ date('Y-m-d') }}" placeholder="dd/mm/yyyy">
                                    @if ($errors->has('date_end'))
                                        <small class="form-text text-muted">
                                            <strong>{{ $errors->first('date_end') }}</strong>
                                        </small>
                                    @endif
                                </div>
                            @endif
                            <!-- project -->
                        </div>
                    </div>
                </div>
            </div> <!-- container-fluid -->
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for=""></label>
                <input type="submit" class="btn btn-orange" name="submit" value="Simpan">
                <a href="{{ route($formRouteIndex, $taskData->project_id) }}" class="btn btn-blue-lini">Batal</a>

                <button type="button" class="btn btn-orange" data-toggle="collapse" data-target="#updateTaskStatus" aria-expanded="false" aria-controls="updateTaskStatus">Update status</button>
            </div>
        </div>
    </form>
    <div class="collapse" id="updateTaskStatus">
        <div class="card-body">
            <label>Update status</label>
            <form action="{{ route($formRouteUpdate, $taskData->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                @csrf
                @method('PUT')
                <!-- hidden data -->
                <input name="project_id" value="{{ $taskData->project_id }}" hidden>

                <div class="row m-0 alert alert-warning">
                    <div class="col-md-8">
                        <select name="update_status_task" class="form-control select2" required>

                            @if($taskData->status)

                                @foreach($dataTaskStatus as $dataTS)
                                    @if($dataTS->id == $taskData->status)
                                        <option value="{{ $dataTS->id }}">{{ ucwords($dataTS->name)}}</option>
                                    @endif
                                @endforeach

                                @foreach($dataTaskStatus as $dataTS)
                                    @if($dataTS->id != $taskData->status)
                                        <option value="{{ $dataTS->id }}">{{ ucwords($dataTS->name) }}</option>
                                    @endif
                                @endforeach
                            @else
                                <option value="0">Pilih status</option>
                                @foreach($dataTaskStatus as $dataTS)
                                    <option value="{{ $dataTS->id }}">{{ ucwords($dataTS->name) }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md">
                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-check' title='done'> </i> Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


</div> <!-- container-fluid -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'description' );
</script>
@endsection
