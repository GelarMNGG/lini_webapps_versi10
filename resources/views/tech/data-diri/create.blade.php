@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Input data diri';
    $formRouteIndex = 'tech-input-data-diri.index';
    $formRouteStore = 'tech-input-data-diri.store';
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
        <strong class="card-header text-center text-uppercase">{{ ucfirst($pageTitle) }}</strong>
    </div>

    <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-md alert alert-warning">
                    <div class="row">
                        <div class="col-md">
                            <label for="">Nomor rekening</label>
                        </div>
                        <div class="w-100"></div>
                        <div class="col-md form-group">
                            <input type="text" class="form-control" name="norek" value="{{ $tech->norek }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route($profileLink) }}" class="btn btn-orange">Ubah</a>
                        </div>
                    </div>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('emergency_call') ? ' has-error' : '' }}">
                    <label for="emergency_call">Emergency Call</label>
                    <input type="number" class="form-control" name="emergency_call" value="{{ old('emergency_call') ?? old('emergency_call') }}" required>
                </div>
                <div class="col-md form-group{{ $errors->has('contact_name') ? ' has-error' : '' }}">
                    <label for="contact_name">Contact name</label>
                    <input type="text" class="form-control" name="contact_name" value="{{ old('contact_name') ? old('contact_name') : $techPersonalData->contact_name }}" required>
                </div>
                <div class="col-md form-group{{ $errors->has('relationship') ? ' has-error' : '' }}">
                    <label for="relationship">Relationship</label>
                    <input type="text" class="form-control" name="relationship" value="{{ old('relationship') ? old('relationship') : $techPersonalData->relationship }}" required>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                    <label for="city">Kota</label>
                    <input type="number" class="form-control" name="city" value="{{ old('city') ?? old('city') }}" required>
                </div>
                <div class="col-md form-group{{ $errors->has('date_of_birth') ? ' has-error' : '' }}">
                    <?php 
                        $now = date('Y-m-d');
                        $dateMin = strtotime($now.' -45 year');
                    ?>
                    <label for="date_of_birth">Tanggal lahir</label>
                    <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth') ?? old('date_of_birth') }}" max="{{ date('Y-m-d',$dateMin) }}" required>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('gender') ? ' has-error' : '' }} ">
                    <label for="">Jenis kelamin</label>
                    <select name="gender" class="form-control" placeholder="Pilih Jenis Kelamin" required>
                        <option value="0">Pilih salah satu</option>
                        <?php 
                            if (old('gender') != NULL) {
                                $statusGender = old('gender');
                            }else{
                                $statusGender = NULL;
                            }
                        ?>
                        @foreach($procGenders as $gender)
                            @if($statusGender != NULL)
                                @if($gender->id == $statusGender)
                                    <option value="{{ $gender->id }}">{{ ucwords($gender->name)}}</option>
                                @endif
                            @else
                                <option value="{{ $gender->id }}">{{ ucwords($gender->name)}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md form-group{{ $errors->has('marital_status') ? ' has-error' : '' }}">
                    <label for="">Status</label>
                    <select name="marital_status" class="form-control" placeholder="Pilih Jenis Kelamin" required>
                        <option value="0">Pilih salah satu</option>
                        <?php 
                            if (old('marital_status') != NULL) {
                                $statusNikah = old('marital_status');
                            }else{
                                $statusNikah = NULL;
                            }
                        ?>
                        @foreach($procMaritalStatus as $dataMarital)
                            @if($statusNikah != NULL)
                                @if($dataMarital->id == $statusNikah)
                                    <option value="{{ $dataMarital->id }}">{{ ucwords($dataMarital->name)}}</option>
                                @endif
                            @else
                                <option value="{{ $dataMarital->id }}">{{ ucwords($dataMarital->name)}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('ktp') ? ' has-error' : '' }}">
                    <label for="ktp">No KTP</label>
                    <input type="number" class="form-control" name="ktp" value="{{ old('ktp') ?? old('ktp') }}" data-parsley-minlength="3" required>
                </div>
                <div class="col-md form-group">
                    <label for="npwp">No NPWP</label>
                    <input type="number" class="form-control" name="npwp" value="{{ old('npwp') ?? old('npwp') }}">
                </div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('bpjs_health') ? ' has-error' : '' }}">
                    <label for="bpjs_health">No BPJS kesehatan</label>
                    <input type="number" class="form-control" name="bpjs_health" value="{{ old('bpjs_health') ?? old('bpjs_health') }}" data-parsley-minlength="3" required>
                </div>
                <div class="col-md form-group">
                    <label for="bpjs_ketenagakerjaan">No BPJS ketenagakerjaan</label>
                    <input type="number" class="form-control" name="bpjs_ketenagakerjaan" value="{{ old('bpjs_ketenagakerjaan') ?? old('bpjs_ketenagakerjaan') }}">
                </div>
                <div class="w-100"></div>
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('ktp_address') ? ' has-error' : '' }}">
                    <label for="">Alamat sesuai KTP</label>
                    <textarea type="text" class="form-control" name="ktp_address" cols="10" rows="5" minlength="10">{{ old('ktp_address') ?? old('ktp_address') }}</textarea>
                </div>
                <div class="col-md form-group{{ $errors->has('current_address') ? ' has-error' : '' }}">
                    <label for="">Alamat sekarang</label>
                    <textarea type="text" class="form-control" name="current_address" cols="10" rows="5" minlength="10">{{ old('current_address') ?? old('current_address') }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="col-md mt-2 mb-2">
                <button type="submit" class="btn btn-orange" name="submit">Simpan</button>
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
