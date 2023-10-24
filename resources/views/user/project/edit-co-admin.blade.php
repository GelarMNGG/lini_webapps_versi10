@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'project';
    $formRouteIndex = 'user-projects.index';
    $formRouteUpdate= 'user-projects.update';
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
        <strong>{{ ucfirst($pageTitle) }}</strong>
    </div>
    <form action="{{ route($formRouteUpdate, $project->id) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        @method('PUT')

        <div class="card-body bg-gray-lini-2">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- hidden data -->
                        <input type="text" name="status" value="{{ $project->status }}" hidden>

                        <div class="row">
                            <!-- project -->
                            @if($userDepartment == 1)
                                <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name">Nama <small class="c-red">*</small></label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : $project->name }}" data-parsley-minlength="3" required>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md mt-2 form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                                    <label for="number">Nomor <small class="c-red">*</small></label>
                                    <input type="text" class="form-control" name="number" value="{{ strtoupper($project->number) }}" data-parsley-minlength="3" readonly>
                                </div>
                                <div class="col-md mt-2 form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                                    <label for="location">Lokasi </label>
                                    <input type="text" class="form-control" name="location" value="{{ old('location') ? old('location') : $project->location }}" data-parsley-minlength="3">
                                </div>
                                <div class="col-md mt-2 form-group{{ $errors->has('pm_id') ? ' has-error' : '' }}">
                                    <label>Project Category <small class="c-red">*</small></label>
                                    <select name="procat_id" class="form-control select2" required>
                                    <?php 
                                        //qct_id
                                        if(old('procat_id') != null) {
                                            $procat_id = old('procat_id');
                                        }elseif(isset($project->procat_id)){
                                            $procat_id = $project->procat_id;
                                        }else{
                                            $procat_id = 1;
                                        }
                                    ?>
                                        @if($procat_id != null)
                                            @foreach($dataCategories as $dataKategori)
                                                @if($dataKategori->id == $procat_id)
                                                    <option value="{{ $dataKategori->id }}">{{ strtoupper($dataKategori->name) }}</option>
                                                @endif
                                            @endforeach
                                            @foreach($dataCategories as $dataKategori)
                                                @if($dataKategori->id != $procat_id)
                                                    <option value="{{ $dataKategori->id }}">{{ strtoupper($dataKategori->name) }}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="0">Pilih kategori</option>
                                            @foreach($dataCategories as $dataKategori)
                                                <option value="{{ $dataKategori->id }}">{{ strtoupper($dataKategori->name) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md mt-2 form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                                    <label for="amount">Amount <small class="c-red">*</small></label>
                                    <input type="number" class="form-control" name="amount" value="{{ old('amount') ? old('amount') : $project->amount }}" required>
                                </div>
                                <div class="col-md mt-2 form-group{{ $errors->has('pm_id') ? ' has-error' : '' }}">
                                    <label>Project Manager</label>
                                    <select name="pm_id" class="form-control select2" required>
                                    <?php 
                                        //qct_id
                                        if(old('pm_id') != null) {
                                            $pm_id = old('pm_id');
                                        }elseif(isset($project->pm_id)){
                                            $pm_id = $project->pm_id;
                                        }else{
                                            $pm_id = null;
                                        }
                                    ?>
                                        @if($pm_id != null)
                                            @foreach($dataUsers as $dataPM)
                                                @if($dataPM->id == $pm_id)
                                                    <option value="{{ $dataPM->id }}">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname)}}</option>
                                                @endif
                                            @endforeach
                                            @foreach($dataUsers as $dataPM)
                                                @if($dataPM->id != $pm_id)
                                                    <option value="{{ $dataPM->id }}">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname)}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="0">Pilih PM</option>
                                            @foreach($dataUsers as $dataPM)
                                                <option value="{{ $dataPM->id }}">{{ ucwords($dataPM->firstname).' '.ucwords($dataPM->lastname)}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md mt-2 form-group{{ $errors->has('pm_id') ? ' has-error' : '' }}">
                                    <label>Customer</label>
                                    <select name="customer_id" class="form-control select2" required>
                                    <?php 
                                        //qct_id
                                        if(old('customer_id') != null) {
                                            $customer_id = old('customer_id');
                                        }elseif(isset($project->customer_id)){
                                            $customer_id = $project->customer_id;
                                        }else{
                                            $customer_id = null;
                                        }
                                    ?>
                                        @if($customer_id != null)
                                            @foreach($dataCustomers as $dataCustomer)
                                                @if($dataCustomer->id == $customer_id)
                                                    <option value="{{ $dataCustomer->id }}">{{ ucwords($dataCustomer->firstname).' '.ucwords($dataCustomer->lastname)}}</option>
                                                @endif
                                            @endforeach
                                            @foreach($dataCustomers as $dataCustomer)
                                                @if($dataCustomer->id != $customer_id)
                                                    <option value="{{ $dataCustomer->id }}">{{ ucwords($dataCustomer->firstname).' '.ucwords($dataCustomer->lastname)}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            <option value="0">Pilih customers</option>
                                            @foreach($dataCustomers as $dataCustomer)
                                                <option value="{{ $dataCustomer->id }}">{{ ucwords($dataCustomer->firstname).' '.ucwords($dataCustomer->lastname)}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <!-- project -->
                            @endif
                        </div>
                    </div>
                </div>
            </div> <!-- container-fluid -->
        </div>
        <div class="card-body">
            <div class="col-md">
                <input type="submit" class="btn btn-orange" name="submit" value="Ubah">
                <a href="{{ route($formRouteIndex) }}" class="btn btn-secondary">Batal</a>
            </div>
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
