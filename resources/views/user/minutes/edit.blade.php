@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Aktivitas harian';
    $formRouteIndex = 'user-minutes.index';
    $formRouteUpdate = 'user-minutes.update';
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


    <form action="{{ route($formRouteUpdate, $userMinute->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method ('PUT')

        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama aktivitas <small class="c-red">*</small></label>
                        <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ old('name') ? old('name') : ucfirst($userMinute->name) }}" placeholder="Nama aktivitas" required>
                    </div>
                    @if(Auth::user()->company_id == 1 && Auth::user()->department_id == 1)
                    <div class="row m-0 form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        <label>Deskripsi masalah</label>
                        <textarea name="description" class="form-control" cols="10" rows="11" required>{{ old('description') ? old('description') : ucfirst($userMinute->description) }}</textarea>
                    </div>
                    @else
                    <div class="row">
                        <div class="col-md form-group">
                            <label for="">Kategori </label>
                            <select id="minute_cat" name="minute_cat" class="form-control select2{{ $errors->has('minute_cat') ? ' has-error' : '' }}" required>
                                <?php
                                    if(old('minute_cat') != null) {
                                        $minute_cat = old('minute_cat');
                                    }elseif(isset($userMinute->minute_cat)){
                                        $minute_cat = $userMinute->minute_cat;
                                    }else{
                                        $minute_cat = null;
                                    }
                                ?>
                                @if ($minute_cat != null)
                                    @foreach ($minutesCats as $dataCat)
                                        @if ($dataCat->id == $minute_cat)
                                            <option value='{{ strtolower($dataCat->id) }}'>{{ ucwords(strtolower($dataCat->name)) }}</option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="0">Pilih kategori</option>
                                @endif
                                @foreach($minutesCats as $dataCat)
                                    <option value="{{ strtolower($dataCat->id) }}">{{ ucwords($dataCat->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md form-group">
                            <label for="">Peminta layanan </label>
                            <select id="department_id" name="department_id" class="form-control select2{{ $errors->has('department_id') ? ' has-error' : '' }}" required>
                                <?php
                                    if(old('department_id') != null) {
                                        $department_id = old('department_id');
                                    }elseif(isset($userMinute->department_id)){
                                        $department_id = $userMinute->department_id;
                                    }else{
                                        $department_id = null;
                                    }
                                ?>
                                @if ($department_id != null)
                                    @foreach ($departmentDatas as $departmentData)
                                        @if ($departmentData->id == $department_id)
                                            <option value='{{ strtolower($departmentData->id) }}'>{{ ucwords(strtolower($departmentData->name)) }}</option>
                                        @endif
                                    @endforeach
                                @else
                                    <option value="0">Pilih departemen</option>
                                @endif
                                @foreach($departmentDatas as $departmentData)
                                    <option value="{{ strtolower($departmentData->id) }}">{{ ucwords($departmentData->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row m-0 form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        <label>Deskripsi masalah</label>
                        <textarea name="description" class="form-control" cols="10" rows="7" required>{{ old('description') ? old('description') : ucfirst($userMinute->description) }}</textarea>
                    </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md form-group">
                            <label>Jam mulai</label>
                            <div class="input-group">
                                <input id="timepicker3" name="event_start" type="text" class="form-control" value="{{ old('event_start') ? old('event_start') : date('G:i A', strtotime($userMinute->event_start)) }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                </div>
                            </div><!-- input-group -->
                        </div>
                        <div class="col-md form-group">
                            <label>Jam selesai</label>
                            <div class="input-group">
                                <input id="timepicker" name="event_end" type="text" class="form-control" value="{{ old('event_end') ? old('event_end') : date('G:i A', strtotime($userMinute->event_end)) }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="mdi mdi-clock-outline"></i></span>
                                </div>
                            </div><!-- input-group -->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md form-group">
                            <label>Tanggal</label>
                            <input type="date" class="form-control{{ $errors->has('date') ? ' has-error' : '' }}" name="date" value="{{ old('date') ? old('date') : date('Y-m-d', strtotime($userMinute->date)) }}" min="{{ date('Y-m-d', strtotime($userMinute->date)) }}" placeholder="dd/mm/yyyy">
                            @if ($errors->has('date'))
                                <small class="form-text text-muted">
                                    <strong>{{ $errors->first('date') }}</strong>
                                </small>
                            @endif
                        </div>
                    </div>
                    <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                        <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/minutes/user/'.$userMinute->image) }}"  />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md form-group">
                    <label for="">Published </label>
                    <select name="published" class="form-control select2{{ $errors->has('published') ? ' has-error' : '' }}">
                        <?php
                            if(old('published') != null) {
                                $published = old('published');
                            }elseif(isset($userMinute->published)){
                                $published = $userMinute->published;
                            }else{
                                $published = null;
                            }
                        ?>
                        @if ($published != null))
                            @if ($published == 1)
                                <option value='1'>Published</option>
                            @else
                                <option value="0">Draft</option>
                            @endif
                        @else
                            <option value='1'>Published</option>
                            <option value="0">Draft</option>
                        @endif
                    </select>
                </div>
                <div class="col-md form-group">
                    <label for="">Status </label>
                    <select id="status" name="status" class="form-control select2{{ $errors->has('department_id') ? ' has-error' : '' }}">
                        <?php
                            if(old('status') != null) {
                                $status = old('status');
                            }elseif(isset($userMinute->status)){
                                $status = $userMinute->status;
                            }else{
                                $status = null;
                            }
                        ?>
                        @if ($status != null)
                            @if ($status == 1)
                                <option value='1'>Done</option>
                            @else
                                <option value="0">In progress</option>
                            @endif
                        @else
                            <option value="0">In progress</option>
                            <option value='1'>Done</option>
                        @endif
                    </select>
                </div>
                <div class="col-md form-group">
                    <label for="">Prosentase </label>
                    <input type="text" name="percentage" class="form-control{{ $errors->has('percentage') ? ' has-error' : '' }}" value="{{ old('percentage') ? old('percentage') : $userMinute->percentage }}" placeholder="Hanya diisi jika belum done">
                </div>
            </div>   
        </div>
        <div class="card-body">
            <div class="col-md">
                <input type="submit" class="btn btn-orange" name="submit" value="Ubah">
                <a href="{{ route($formRouteIndex,'skin='.$skin) }}" class="btn btn-blue-lini">Batal</a>
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
