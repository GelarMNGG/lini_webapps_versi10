@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Tambah pengeluaran';
    $formRouteIndex = 'expenses-tech.index';
    $formRouteStore = 'expenses-tech.store';
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
        <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTask->project_name }}</span></strong>
        <br><small>Task:</small> <strong><span class="text-danger text-uppercase">{{ $projectTask->name }}</span></strong>
        <br><small>No task:</small> <strong><span class="text-warning text-uppercase">{{ $projectTask->number }}</span></strong>
    </div>

    <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- hidden data -->
        <input type="text" name="project_id" value="{{ $projectTask->project_id }}" hidden>
        <input type="text" name="task_id" value="{{ $projectTask->id }}" hidden>

        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md form-group">
                    <label>Nama pengeluaran <small class="c-red">*</small></label>
                    <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ old('name') ?? '' }}" placeholder="Nama pengeluaran" required>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group">
                    <label>Jumlah pengeluaran <small class="c-red">*</small></label>
                    <input type="number" name="amount" class="form-control{{ $errors->has('amount') ? ' has-error' : '' }}" value="{{ old('amount') ?? '' }}" placeholder="jumlah pengeluaran" required>
                </div>
                <div class="col-md form-group">
                    <label for="">Kode produk </label>
                    <select name="code" class="form-control select2{{ $errors->has('code') ? ' has-error' : '' }}" required>
                        <?php
                            if(old('code') != null) {
                                $code = old('code');
                            }else{
                                $code = null;
                            }
                        ?>
                        @if ($code != null)
                            @foreach ($productCodes as $dataOne)
                                @if ($dataOne->id == $code)
                                    <option value='{{ strtolower($dataOne->id) }}'>{{ strtoupper($dataOne->code).'-'.ucwords(strtolower($dataOne->description)) }}</option>
                                @endif
                            @endforeach
                        @else
                            <option value="0">Pilih kode</option>
                        @endif
                        @foreach($productCodes as $dataOne)
                            <option value="{{ strtolower($dataOne->id) }}">{{ strtoupper($dataOne->code).'-'.ucwords(strtolower($dataOne->description)) }}</option>
                        @endforeach
                    </select>
                </div>
                <?php /*
                <div class="col-md-6">
                    <div class="{{ $errors->has('image') ? ' has-error' : '' }}">
                        <h4 class="header-title">Bukti pembelian <small class="c-red">*</small></h4>
                        <input type="file" name="image" class="dropify" data-max-file-size="2M" data-default-file="{{ asset('img/expenses/tech/default.png') }}" required/>
                    </div>
                </div>
                */ ?>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md mt-2 mb-2">
                <button type="submit" class="btn btn-orange" name="submit"><i class="fa fa-plus"></i> Tambah</button>
                
                <a href="{{ route($formRouteIndex,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-blue-lini">Kembali</a>
                
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
