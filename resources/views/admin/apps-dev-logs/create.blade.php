@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Tambah log aplikasi';
    $formRouteIndex = 'apps-dev-logs.index';
    $formRouteStore = 'apps-dev-logs.store';
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
    <div class="card-header text-center text-uppercase bb-orange"><strong>{{ ucfirst($pageTitle) }}</strong></div>


    <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- hidden data -->
        @if(isset($requestDepartment))
            <input type="text" name="did" value="{{ $requestDepartment }}" hidden>
        @endif
        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md">
                    <div class="form-group">
                        <label>Nama log <small class="c-red">*</small></label>
                        <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ old('name') ?? '' }}" placeholder="Nama log" required>
                    </div>
                </div>
                <div class="col-md">
                    <div class="row">
                        <div class="col-md form-group">
                            <label>Jam mulai</label>
                            <div class="input-group">
                                <input id="timepicker3" name="event_start" type="text" class="form-control" value="{{ old('event_start') ?? '' }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                </div>
                            </div><!-- input-group -->
                        </div>
                        <div class="col-md form-group">
                            <label>Jam selesai</label>
                            <div class="input-group">
                                <input id="timepicker" name="event_end" type="text" class="form-control" value="{{ old('event_start') ?? '' }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                </div>
                            </div><!-- input-group -->
                        </div>
                    </div>
                </div>
                <div class="w-100"></div>
                <div class="col-md">
                    <label for="">Departement </label>
                    <select name="department_id" class="form-control select2{{ $errors->has('department_id') ? ' has-error' : '' }}" required>
                        <?php
                            if(old('department_id') != null) {
                                $department_id = old('department_id');
                            }elseif(isset($requestDepartment)){
                                $department_id = $requestDepartment;
                            }else{
                                $department_id = null;
                            }
                        ?>
                        @if ($department_id != null)
                            @foreach ($departmensDatas as $dataOne)
                                @if ($dataOne->id == $department_id)
                                    <option value='{{ strtolower($dataOne->id) }}'>{{ ucwords(strtolower($dataOne->name)) }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="0">Pilih departemen</option>
                        @endif
                        @foreach($departmensDatas as $dataOne)
                            <option value="{{ strtolower($dataOne->id) }}">{{ ucwords($dataOne->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md">
                    <label for="">Status </label>
                    <select name="status" class="form-control select2{{ $errors->has('status') ? ' has-error' : '' }}" required>
                        @if (!empty(old('status')))
                            @foreach ($appsStatusDatas as $dataTwo)
                                @if ($dataTwo->id == old('status'))
                                    <option value='{{ strtolower($dataTwo->id) }}'>{{ ucwords(strtolower($dataTwo->name)) }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="0">Pilih status</option>
                        @endif
                        @foreach($appsStatusDatas as $dataTwo)
                            <option value="{{ strtolower($dataTwo->id) }}">{{ ucwords(strtolower($dataTwo->name)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md form-group">
                    <label for="">Prosentase </label>
                    <input type="number" name="percentage" class="form-control{{ $errors->has('percentage') ? ' has-error' : '' }}" value="{{ old('percentage') ?? '' }}" placeholder="Hanya diisi jika belum done">
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group">
                    <label for="">Programmer </label>
                    <select id="programmer_id" name="programmer_id" class="form-control select2{{ $errors->has('programmer_id') ? ' has-error' : '' }}" required>
                        @if (!empty(old('programmer_id')))
                            @foreach ($programmersDatas as $dataThree)
                                @if ($dataThree->id == old('programmer_id'))
                                    <option value='{{ strtolower($dataThree->id) }}'>{{ ucwords(strtolower($dataThree->firstname)).' '.ucwords(strtolower($dataThree->lastname)) }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="0">Pilih programmer</option>
                        @endif
                        @foreach($programmersDatas as $dataThree)
                            <option value="{{ strtolower($dataThree->id) }}">{{ ucwords(strtolower($dataThree->firstname)).' '.ucwords(strtolower($dataThree->lastname)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                    <label for="date">Tanggal mulai</label>
                    <input type="date" class="form-control" name="date" value="{{ old('date') }}">
                    @if ($errors->has('date'))
                        <small class="form-text text-muted">
                            <strong>{{ $errors->first('date') }}</strong>
                        </small>
                    @endif
                </div>
                <div class="col-md form-group{{ $errors->has('done_date') ? ' has-error' : '' }}">
                    <label for="done_date">Tanggal selesai</label>
                    <input type="date" class="form-control" name="done_date" value="{{ old('done_date') }}">
                    @if ($errors->has('done_date'))
                        <small class="form-text text-muted">
                            <strong>{{ $errors->first('done_date') }}</strong>
                        </small>
                    @endif
                </div>
                <div class="col-md-12 form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                    <label>Note</label>
                    <textarea name="note" class="form-control" cols="10" rows="7">{{ old('note') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="col-md">
                <button type="submit" class="btn btn-orange" name="submit">Tambah</button>
                @if(isset($requestDepartment))
                    <a href="{{ route($formRouteIndex,'did='.$requestDepartment) }}" type="button" class="btn btn-blue-lini">Kembali</a>
                @else
                    <a href="{{ route($formRouteIndex) }}" type="button" class="btn btn-blue-lini">Kembali</a>
                @endif
            </div>
        </div>
    </form>

</div> <!-- container-fluid -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'solution' );
</script>
<script src="{{ asset('admintheme/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
@endsection
