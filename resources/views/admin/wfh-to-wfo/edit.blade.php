@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Edit pengajuan WFH to WFO';
    $formRouteIndex = 'admin-wfh-to-wfo.index';
    $formRouteUpdate = 'admin-wfh-to-wfo.update';
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
        <strong><span>{{ strtoupper($pageTitle) }}</span></strong>
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
                    <form action="{{ route($formRouteUpdate, $requestData->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf
                        @method('PUT')
                        
                        <!-- data -->
                        <div class="row">
                            <div class="col-md form-group{{ $errors->has('employee_id') ? ' has-error' : '' }}">
                                <label for="">Karyawan <small class="c-red">*</small></label>
                                <select id="employee_id" name="employee_id" class="form-control select2" required>
                                    <?php 
                                        //qct_id
                                        if(old('employee_id') != null) {
                                            $employeeData = old('employee_id');
                                        }elseif(isset($requestData->employee_id)){
                                            if ($requestData->employee_type == 'admin') {
                                                $employeeData = 'lid';
                                            }else{
                                                $employeeData = $requestData->employee_id;
                                            }
                                        }else{
                                            $employeeData = null;
                                        }
                                    ?>
                                    @if ($employeeData !== null))
                                        @if($employeeData == 'lid')
                                            <option value="lid">Diri sendiri</option>
                                            @foreach($users as $data77)
                                                <option value="{{ $data77->id }}">{{ ucwords($data77->firstname).' '.ucwords($data77->lastname)}}</option>
                                            @endforeach
                                        @else
                                            @foreach($users as $data77)
                                                @if($data77->id == $employeeData)
                                                    <option value="{{ $data77->id }}">{{ ucwords($data77->firstname).' '.ucwords($data77->lastname)}}</option>
                                                @endif
                                            @endforeach
                                            @foreach($users as $data77)
                                                @if($data77->id != $employeeData)
                                                    <option value="{{ $data77->id }}">{{ ucwords($data77->firstname).' '.ucwords($data77->lastname)}}</option>
                                                @endif
                                            @endforeach
                                            <option value="lid">Diri sendiri</option>
                                        @endif
                                    @else
                                        <option value="lid">Diri sendiri</option>
                                        @foreach($users as $data77)
                                            <option value="{{ $data77->id }}">{{ ucwords($data77->firstname).' '.ucwords($data77->lastname)}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                <label for="date">Tanggal <small class="c-red">*</small></label>
                                <input type="date" class="form-control" name="date" value="{{ old('date') ? old('date') : date('Y-m-d',strtotime($requestData->date)) }}" min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md form-group">
                                <label>Jam masuk <small class="c-red">*</small></label>
                                <div class="input-group">
                                    <input id="timepicker3" name="clock_in" type="text" class="form-control" value="{{ old('clock_in') ? old('clock_in') : date('G:i A', strtotime($requestData->clock_in)) }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                    </div>
                                </div><!-- input-group -->
                            </div>
                            <div class="col-md form-group">
                                <label>Jam keluar <small class="c-red">*</small></label>
                                <div class="input-group">
                                    <input id="timepicker" name="clock_out" type="text" class="form-control" value="{{ old('clock_out') ? old('clock_out') : date('G:i A', strtotime($requestData->clock_out)) }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                    </div>
                                </div><!-- input-group -->
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label>Keperluan WFO <small class="c-red">*</small></label>
                                <textarea id="description" name="description" class="form-control" cols="10" rows="5" required>{{ old('description') ? old('description') : ucfirst($requestData->description) }}</textarea>
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
