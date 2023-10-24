@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'edit project';
    $formRouteIndex = 'user-projects.index';
    $formRouteUpdate= 'user-projects.update';
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

<div class="card mt-2">
    <div class="card-header text-center text-uppercase bb-orange">
        <strong>{{ ucfirst($pageTitle) }}</strong>
    </div>

    <form action="{{ route($formRouteUpdate, $project->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method('PUT')
        <div class="card-body bg-gray-lini-2">
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
                            <!-- the data status -->
                            <input class="form-control" type="text" name="status" value="{{ $project->status }}" hidden>
                            <div class="row">
                                <div class="w-100"></div>
                                <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Nama <small class="c-red">*</small></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : $project->name }}" data-parsley-minlength="3" required readonly>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md mt-2 form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                    <label for="amount">Amount <small class="c-red">*</small></label>
                                    <input type="number" class="form-control" name="amount" value="{{ old('amount') ? old('amount') : $project->amount }}" required>
                                </div>
                                <div class="col-md mt-2 form-group{{ $errors->has('budget') ? ' has-error' : '' }}">
                                    <label for="budget">Budget <small class="c-red">*</small></label>
                                    <input type="number" class="form-control" name="budget" value="{{ old('budget') ? old('budget') : $project->budget }}" required>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md mt-2 form-group{{ $errors->has('pc_id') ? ' has-error' : '' }}">
                                    <label for="">Pilih PC</label>
                                    <select id="pc_id" name="pc_id" class="form-control select2" required>
                                        @if (old('pc_id') || $project->pc_id)
                                            @foreach($dataProjectCoordinators as $dataPC)
                                                @if($dataPC->id == old('pc_id') || $dataPC->id == $project->pc_id)
                                                    <option value="{{ $dataPC->id }}">{{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname)}}</option>
                                                @endif
                                            @endforeach
                                            @foreach($dataProjectCoordinators as $dataPC)
                                                @if($dataPC->id != old('pc_id') || $dataPC->id != $project->pc_id)
                                                    <option value="{{ $dataPC->id }}">{{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname)}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="0">Pilih PC</option>
                                            @foreach($dataProjectCoordinators as $dataPC)
                                                <option value="{{ $dataPC->id }}">{{ ucwords($dataPC->firstname).' '.ucwords($dataPC->lastname)}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md mt-2 form-group{{ $errors->has('ad_id') ? ' has-error' : '' }}">
                                    <label for="">Pilih Admin doc</label>
                                    <select id="ad_id" name="ad_id" class="form-control select2" required>
                                        @if (old('ad_id') || $project->ad_id)
                                            @foreach($dataAdminDocuments as $dataAD)
                                                @if($dataAD->id == old('ad_id') || $dataAD->id == $project->ad_id)
                                                    <option value="{{ $dataAD->id }}">{{ ucwords($dataAD->firstname).' '.ucwords($dataAD->lastname)}}</option>
                                                @endif
                                            @endforeach
                                            @foreach($dataAdminDocuments as $dataAD)
                                                @if($dataAD->id != old('ad_id') || $dataAD->id != $project->ad_id)
                                                    <option value="{{ $dataAD->id }}">{{ ucwords($dataAD->firstname).' '.ucwords($dataAD->lastname)}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="0">Pilih PC</option>
                                            @foreach($dataAdminDocuments as $dataAD)
                                                <option value="{{ $dataAD->id }}">{{ ucwords($dataAD->firstname).' '.ucwords($dataAD->lastname)}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                    </div>
                </div>
            </div> <!-- container-fluid -->
        </div>
        <div class="card-body">
            <input type="submit" class="btn btn-orange" name="submit" value="Ubah">
            <a href="{{ route($formRouteIndex, 'status='.$project->status) }}" class="btn btn-blue-lini">Batal</a>
        </div>
    </form>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'description' );
</script>
@endsection
