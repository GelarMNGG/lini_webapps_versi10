@extends('layouts.dashboard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Edit data kota';
    $formRouteUpdate = 'admin-cities.update';
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

    <form class="w-100" action="{{ route($formRouteUpdate, $citiesData->id) }}" method="POST" enctype="multipart/form-data">
        <div class="card-body bg-gray-lini-2">
            @csrf
            @method('PUT')
            <div class="row m-0">
                <div class="col-md form-group kota-asal-box">
                    <label for="">Kota asal <small class="c-red">*</small></label>
                    <input name="name" class="form-control{{ $errors->any() ? ' has-error' : '' }}" value='{{ ucwords(strtolower($citiesData->name)) }}'>
                </div>
                <div class="col-md form-group{{ $errors->has('code') ? ' has-error' : '' }}">
                    <label for="">Propinsi tujuan <small class="c-red">*</small></label>
                    <select id="code" name="code" class="form-control select2" disabled>
                        @foreach ($provincesDatas as $data2)
                            @if ($data2->id == $citiesData->code)
                                <option value='{{ $data2->id }}'>{{ ucwords(strtolower($data2->name)) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <button type="submit" class="btn btn-orange" name="submit">Ubah data</button>
            <a href="#" type="button" onclick="javascript:history.go(-1);" class="btn btn-blue-lini">Kembali</a>
        </div>
    </form>
</div> <!-- container-fluid -->
@endsection

@section ('script')

@endsection
