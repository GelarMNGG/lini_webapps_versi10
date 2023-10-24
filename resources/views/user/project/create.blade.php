@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'tambah project';
    $formRouteIndex = 'user-projects.index';
    $formRouteStore= 'user-projects.store';
    $formRouteDashboard= 'user-projects.dashboard';
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

    <form action="{{ route($formRouteStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
        @csrf
        <div class="card-body bg-gray-lini-2">
            <div class="row">
                <div class="col-md mt-2 form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name">Nama <small class="c-red">*</small></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') ?? old('name') }}" data-parsley-minlength="3" required>
                </div>
                <div class="w-100"></div>
                <div class="col-md mt-2 form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                    <?php $project_number = $projectNumber->number; $project_number++; ?>
                    <label for="number">Nomor<small class="c-red">*</small></label>
                    <input type="text" class="form-control" name="number" value="{{ strtoupper($project_number) }}" readonly>
                </div>
                <div class="col-md mt-2 form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                    <label for="location">Lokasi </label>
                    <input type="text" class="form-control" name="location" value="{{ old('location') ?? old('location') }}" data-parsley-minlength="3">
                </div>
                <div class="col-md mt-2 form-group{{ $errors->has('pm_id') ? ' has-error' : '' }}">
                    <label>Project Category <small class="c-red">*</small></label>
                    <select name="procat_id" class="form-control select2" required>
                    <?php 
                        //qct_id
                        if(old('procat_id') != null) {
                            $procat_id = old('procat_id');
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
                    <input type="number" class="form-control" name="amount" value="{{ old('amount') ?? old('amount') }}" required>
                </div>
                <div class="col-md mt-2 form-group{{ $errors->has('pm_id') ? ' has-error' : '' }}">
                    <label>Project Manager</label>
                    <select name="pm_id" class="form-control select2" required>
                    <?php 
                        //qct_id
                        if(old('pm_id') != null) {
                            $pm_id = old('pm_id');
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
            </div>

        </div>
        <div class="card-body">
            <div class="col-md">
                <input type="submit" class="btn btn-orange" name="submit" value="Tambah">
                <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini">Batal</a>
            </div>
        </div>
    </form>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('description');
</script>
@endsection
