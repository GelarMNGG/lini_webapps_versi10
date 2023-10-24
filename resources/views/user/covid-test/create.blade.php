@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Pengajuan tes covid';
    $formRouteIndex = 'user-covid-test.index';
    $formRouteStore = 'user-covid-test.store';
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
                        
                        <!-- data -->
                        <div class="row">
                            <div class="col-md form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label>Nama yang akan dites<small class="c-red">*</small></label>
                                <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ old('name') ?? '' }}" placeholder="Nama" required>
                            </div>
                            <div class="col-md form-group{{ $errors->has('nik') ? ' has-error' : '' }}">
                                <label>NIK  <small class="c-red">*</small></label>
                                <input type="text" name="nik" class="form-control{{ $errors->has('nik') ? ' has-error' : '' }}" value="{{ old('nik') ?? '' }}" required>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('requester_name') ? ' has-error' : '' }}">
                                <label>Nama pemohon<small class="c-red">*</small></label>
                                <input type="text" name="requester_name" class="form-control{{ $errors->has('requester_name') ? ' has-error' : '' }}" value="{{ old('requester_name') ?? '' }}" placeholder="Nama pemohon" required>
                            </div>
                            <div class="col-md form-group{{ $errors->has('project_name') ? ' has-error' : '' }}">
                                <label>Nama proyek<small class="c-red">*</small></label>
                                <input type="text" name="project_name" class="form-control{{ $errors->has('project_name') ? ' has-error' : '' }}" value="{{ old('project_name') ?? '' }}" placeholder="cth: site" required>
                            </div>
                            <div class="col-md form-group{{ $errors->has('project_number') ? ' has-error' : '' }}">
                                <label>ID proyek</label>
                                <input type="text" name="project_number" class="form-control{{ $errors->has('project_number') ? ' has-error' : '' }}" value="{{ old('project_number') ?? '' }}" placeholder="">
                            </div>

                            <div class="w-100"></div>
                            <div class="col-md">
                                <label for="">Departemen <small class="c-red">*</small></label>
                                <select id="department_id" name="department_id" class="form-control select2{{ $errors->has('department_id') ? ' has-error' : '' }}" required>
                                    <?php
                                        if(old('department_id') != null) {
                                            $department_id = old('department_id');
                                        }else{
                                            $department_id = null;
                                        }
                                    ?>
                                    @if ($department_id != null)
                                        @foreach ($departments as $data2)
                                            @if ($data2->id == $department_id)
                                                <option value="{{ $department_id ?? $data2->id }}">{{ ucwords(strtolower($data2->name)) }}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="0">Pilih departemen</option>
                                    @endif
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ ucwords($department->name)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="">Jabatan <small class="c-red">*</small></label>
                                <input type="title" class="form-control" name="title" value="{{ old('title') ?? old('title') }}" required>
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
                                <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini">Batal</a>
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
