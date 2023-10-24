@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Pengajuan PR';
    $formRouteIndex = 'user-pr.index';
    $formRouteStore = 'user-pr.store';
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

<div class="card">
    <div class="card-header text-center">Project: <strong>{{ ucfirst($dataProject->name) }}</strong></div>

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
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="{{ route($formRouteStore) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                        @csrf
                        <!-- data -->
                        <input type="text" name="project_id" value="{{ $dataProject->id }}" hidden>
                        <!-- data -->
                        <div class="row">
                            <div class="col-md form-group">
                                <label>Nama barang/jasa <small class="c-red">*</small></label>
                                <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' has-error' : '' }}" value="{{ old('name') ?? '' }}" placeholder="Nama barang/jasa" required>
                            </div>
                            <div class="col-md form-group">
                                <label>Nomor PR </label>
                                <input type="text" name="number" class="form-control{{ $errors->has('number') ? ' has-error' : '' }}" value="{{ old('number') ?? '' }}" required>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
                                <label for="category_id">Kategori barang/jasa</label>
                                <select id="category_id" name="category_id" class="form-control select2" required>
                                    @if (!empty(old('category_id')) || !empty($dataPR->category_id))
                                        @foreach($dataCategory as $dataCat)
                                            @if($dataCat->id == old('category_id'))
                                                <option value="{{ $dataCat->id }}">{{ ucwords($dataCat->name)}}</option>
                                            @elseif($dataCat->id == $dataPR->category_id)
                                                <option value="{{ $dataCat->id }}">{{ ucwords($dataCat->name)}}</option>
                                            @endif
                                        @endforeach
                                        @foreach($dataCategory as $dataCat)
                                            @if($dataCat->id != old('category_id'))
                                                <option value="{{ $dataCat->id }}">{{ ucwords($dataCat->name)}}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="0">Pilih kategori</option>
                                        @foreach($dataCategory as $dataCat)
                                            <option value="{{ $dataCat->id }}">{{ ucwords($dataCat->name)}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                <label for="date">Tanggal</label>
                                <input type="date" class="form-control" name="date" value="{{ old('date') ? old('date') : $dataProject->date }}" min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group">
                                <label>Satuan<small class="c-red">*</small></label>
                                <input type="text" name="unit" class="form-control{{ $errors->has('unit') ? ' has-error' : '' }}" value="{{ old('unit') ?? '' }}" placeholder="cth: site" required>
                            </div>
                            <div class="col-md form-group">
                                <label>Jumlah<small class="c-red">*</small></label>
                                <input type="number" name="amount" class="form-control{{ $errors->has('amount') ? ' has-error' : '' }}" value="{{ old('amount') ?? '' }}" required>
                            </div>
                            <div class="col-md form-group">
                                <label>Budget<small class="c-red">*</small></label>
                                <input type="number" name="budget" class="form-control{{ $errors->has('budget') ? ' has-error' : '' }}" value="{{ old('budget') ?? '' }}" required>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                                <label>Keterangan </label>
                                <textarea id="note" name="note" class="form-control" cols="10" rows="9">{{ old('note') }}</textarea>
                            </div>
                            <div class="col-md form-group{{ $errors->has('alasan') ? ' has-error' : '' }}">
                                <label>Penjelasan/alasan pembelian </label>
                                <textarea id="alasan" name="alasan" class="form-control" cols="10" rows="9">{{ old('alasan') }}</textarea>
                            </div>
                            <div class="w-100"></div>
                            <div class="form-group">
                                <label for=""></label>
                                <input type="submit" class="btn btn-info" name="submit" value="Simpan">
                                <a href="{{ route($formRouteIndex) }}" class="btn btn-secondary">Batal</a>
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
