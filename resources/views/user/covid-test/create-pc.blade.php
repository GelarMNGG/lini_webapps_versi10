@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Pengajuan tes covid';
    $formRouteIndex = 'user-covid-test.index';
    $formRouteStore = 'user-covid-test.store';
    //back
    $formRouteBack = 'user-projects.show';
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
        <strong>{{ strtoupper($pageTitle) }}</strong>
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
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="{{ route($formRouteStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf
                        
                        <!-- hidden data -->
                        <input type="text" name="project_id" value="{{ $projectTaskInfo->project_id }}" hidden>
                        <input type="text" name="task_id" value="{{ $projectTaskInfo->id }}" hidden>
                        <!-- data -->
                        <div class="row">
                            <div class="col-md form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label>Nama yang akan dites<small class="c-red">*</small></label>
                                <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ old('name') ? old('name') : ucwords($projectTaskInfo->tech_firstname).' '.ucwords($projectTaskInfo->tech_lastname) }}" placeholder="Nama" required>
                            </div>
                            <div class="col-md form-group{{ $errors->has('nik') ? ' has-error' : '' }}">
                                <label>NIK  <small class="c-red">*</small></label>
                                <input type="text" name="nik" class="form-control{{ $errors->has('nik') ? ' has-error' : '' }}" value="{{ old('nik') ?? '' }}" required>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('requester_name') ? ' has-error' : '' }}">
                                <label>Nama pemohon<small class="c-red">*</small></label>
                                <input type="text" name="requester_name" class="form-control{{ $errors->has('requester_name') ? ' has-error' : '' }}" value="{{ old('requester_name') ? old('requester_name') : ucwords($projectTaskInfo->pc_firstname).' '.ucwords($projectTaskInfo->pc_lastname) }}" placeholder="Nama pemohon" required>
                            </div>
                            <div class="col-md form-group{{ $errors->has('project_name') ? ' has-error' : '' }}">
                                <label>Nama proyek<small class="c-red">*</small></label>
                                <input type="text" name="project_name" class="form-control{{ $errors->has('project_name') ? ' has-error' : '' }}" value="{{ old('project_name') ? old('project_name') : $projectTaskInfo->project_name }}" placeholder="cth: site" required>
                            </div>
                            <div class="col-md form-group{{ $errors->has('project_number') ? ' has-error' : '' }}">
                                <label>ID proyek</label>
                                <input type="text" name="project_number" class="form-control{{ $errors->has('project_number') ? ' has-error' : '' }}" value="{{ old('project_number') ? old('project_number') : $projectTaskInfo->project_id }}" placeholder="">
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md">
                                <label for="">Departemen <small class="c-red">*</small></label>
                                <select id="department_id" name="department_id" class="form-control select2{{ $errors->has('department_id') ? ' has-error' : '' }}">
                                    @foreach ($departments as $data2)
                                        @if ($data2->id == $dataDepartment)
                                            <option value="{{ $data2->id }}">{{ ucwords(strtolower($data2->name)) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="">Jabatan <small class="c-red">*</small></label>
                                <input type="title" class="form-control" name="title" value="{{ old('title') ? old('title') : 'Project Coordinator' }}" required>
                            </div>

                            <div class="col-md form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                <label for="date">Tanggal <small class="c-red">*</small></label>
                                <input type="date" class="form-control" name="date" value="{{ old('date') ?? old('date') }}" min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('destination') ? ' has-error' : '' }}">
                                <label>Tujuan/nama site <small class="c-red">*</small></label>
                                <textarea id="destination" name="destination" class="form-control" cols="10" rows="5">{{ old('destination') }}</textarea>
                            </div>
                            <div class="col-md form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                <label>Alamat teknisi <small class="c-red">*</small></label>
                                <textarea id="address" name="address" class="form-control" cols="10" rows="5">{{ old('address') }}</textarea>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group">
                                <label for=""></label>
                                <input type="submit" class="btn btn-orange" name="submit" value="Ajukan">
                                <a href="{{ route($formRouteBack,$projectTaskInfo->project_id) }}" class="btn btn-blue-lini">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> <!-- container-fluid -->
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $('.datepicker').datepicker({
        dateFormat:'mm/dd/yy',
        startDate: new Date()
    });
</script>
@endsection
