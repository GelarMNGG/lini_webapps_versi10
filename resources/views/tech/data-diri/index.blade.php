@extends('layouts.dashboard-form-wizard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Input data';
    $formRouteIndex = 'tech-input-data-diri.index';
    $formRouteStore = 'tech-input-data-diri.store';
    $formRouteUpdate = 'tech-input-data-diri.update';

    //family route
    $formRouteFamilyStore = 'tech-input-data-keluarga.store';
    $formRouteFamilyUpdate = 'tech-input-data-keluarga.update';
    
    //education route
    $formRouteEducationCreate = 'tech-input-data-pendidikan.create';
    $formRouteEducationStore = 'tech-input-data-pendidikan.store';
    $formRouteEducationEdit = 'tech-input-data-pendidikan.edit';
    $formRouteEducationDestroy = 'tech-input-data-pendidikan.destroy';
    
    //document upload
    $formRouteUploadCreate = 'tech-input-doc.create';
    $formRouteUploadStore = 'tech-input-doc.store';
    $formRouteUploadEdit = 'tech-input-doc.edit';
    $formRouteUploadDestroy = 'tech-input-doc.destroy';
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
    <div id="progressbarwizard">
        <div class="card-header text-center text-uppercase bb-orange">
            <?php
                //css setting
                //$profileCSS = 'disabled'; 
                $familyCSS = 'disabled'; 
                $educationCSS = 'disabled'; 
                $uploadCSS = 'disabled'; 
                $finishCSS = 'disabled'; 
                $finishStatus = '';
                $progressCount = '0';

                if (isset($techPersonalData)) {
                    $familyCSS = 'tab';
                    $progressCount = '1';
                }
                if (isset($techFamilyInfo)) {
                    $educationCSS = 'tab';
                    $progressCount = '2';
                }
                if (count($educationDatas) > 0) {
                    $uploadCSS = 'tab';
                    $progressCount = '3';
                }
                if (count($documentDatas) >= $documentsTypesCount) {
                    $finishCSS = 'tab';
                    $finishStatus = ' active';
                    $progressCount = '4';
                }
            ?>
            <strong>{{ ucfirst($pageTitle) }}</strong>
            @if($progressCount == 4)
                <br><span class="text-success"><strong>[{{ $progressCount }}/4] - Done</strong></span>
            @else
                <br><span class="text-danger"><strong>[{{ $progressCount }}/4]</strong></span>
            @endif
        </div>
        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-xl-12">
                    <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-1 small">
                        @if(count($documentDatas) >= $documentsTypesCount)
                            <li class="nav-item">
                                <a href="#finish-2" data-toggle="{{ $finishCSS }}" class="nav-link rounded-0 pt-2 pb-2{{ $finishStatus }}">
                                    <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                    <span class="d-none d-sm-inline">Proses input selesai</span>
                                </a>
                            </li>
                        @endif
                        @if(count($educationDatas) > 0)
                            <li class="nav-item">
                                <a href="#dokumen-2" data-toggle="{{ $uploadCSS }}" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-face-profile mr-1"></i>
                                    <span class="d-none d-sm-inline">Upload dokumen</span>
                                    <br>
                                    @if(count($documentDatas) >= $documentsTypesCount)
                                        <span class="mt-0 text-success"><i class="mdi mdi-check-all"></i></span>
                                    @else
                                        <span class="text-danger"><strong>(4/4)</strong></span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        @if(isset($techFamilyInfo))
                            <li class="nav-item">
                                <a href="#pendidikan-2" data-toggle="{{ $educationCSS }}" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-face-profile mr-1"></i>
                                    <span class="d-none d-sm-inline">Data pendidikan</span>
                                    <br>
                                    @if(isset($educationDatas))
                                        <span class="mt-0 text-success"><i class="mdi mdi-check-all"></i></span>
                                    @else
                                        <span class="text-danger"><strong>(3/4)</strong></span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        @if(isset($techPersonalData))
                            <li class="nav-item">
                                <a href="#keluarga-2" data-toggle="{{ $familyCSS }}" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-face-profile mr-1"></i>
                                    <span class="d-none d-sm-inline">Data keluarga</span>
                                    <br>
                                    @if(isset($techFamilyInfo))
                                        <span class="mt-0 text-success"><i class="mdi mdi-check-all"></i></span>
                                    @else
                                        <span class="text-danger"><strong>(2/4)</strong></span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="#account-2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                <i class="mdi mdi-account-circle mr-1"></i>
                                <span class="d-none d-sm-inline">Data diri </span>
                                <br>
                                @if(isset($techPersonalData))
                                    <span class="mt-0 text-success"><i class="mdi mdi-check-all"></i></span>
                                @else
                                    <span class="text-danger"><strong>(1/4)</strong></span>
                                @endif
                            </a>
                        </li>
                    </ul>
                
                    <div class="tab-content border-0 mb-0 plr-0">
                
                        <div id="bar" class="progress mb-3">
                            <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:{{ ($progressCount/4) * 100 }}%">
                            {{ ($progressCount/4) * 100 }}% Selesai (success)
                            </div>
                        </div>
                        @if(count($documentDatas) >= $documentsTypesCount)
                            <div class="tab-pane active" id="finish-2">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="text-center">
                                            <h2 class="mt-0"><i class="mdi mdi-check-all"></i></h2>
                                            <h3 class="mt-0">Terima kasih !</h3>

                                            <p class="w-75 mb-2 mx-auto">Anda telah melengkapi data diri Anda sebagai salah satu prasyarat untuk mengikuti proses seleksi calon teknisi di PT Lima Inti Sinergi.</p>

                                            <div class="mb-3">
                                                <div class="custom-control custom-checkbox">
                                                    <a href="{{ route('tech-test-training.index') }}" class="btn btn-orange">Lanjut tes psikologi</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->
                            </div>
                        @endif
                        <!-- personal data -->
                        <div class="tab-pane" id="account-2">
                            <div class="row">
                                <div class="col-12">
                                    @if(isset($techPersonalData))
                                        <form class="w-100" action="{{ route($formRouteUpdate, $techPersonalData->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')

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
                                                        <input type="number" class="form-control" name="emergency_call" value="{{ old('emergency_call') ? old('emergency_call') : $techPersonalData->emergency_call }}" required>
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
                                                    <div class="col-md form-group{{ $errors->has('date_of_birth') ? ' has-error' : '' }}">
                                                        <?php 
                                                            $now = date('Y-m-d');
                                                            $dateMin = strtotime($now.' -45 year');
                                                        ?>
                                                        <label for="date_of_birth">Tanggal lahir</label>
                                                        <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth') ? old('date_of_birth') : date('Y-m-d', strtotime($techPersonalData->date_of_birth)) }}" max="{{ date('Y-m-d',$dateMin) }}" required>
                                                    </div>
                                                    <div class="col-md form-group{{ $errors->has('province') ? ' has-error' : '' }}">
                                                        <label for="">Propinsi <small class="c-red">*</small></label>
                                                        <select id="province" name="province" class="form-control select2" required>  
                                                            <?php
                                                                if(old('province') != null) {
                                                                    $province = old('province');
                                                                }elseif(isset($techPersonalData->province)){
                                                                    $province = $techPersonalData->province;
                                                                }else{
                                                                    $province = null;
                                                                }
                                                            ?> 
                                                            @if ($province != null)
                                                                @foreach ($provinceDatas as $data3)
                                                                    @if ($data3->id == $province)
                                                                        <option value='{{ strtolower($data3->id) }}'>{{ ucwords($data3->name) }}</option>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <option value="0">Pilih propinsi</option>
                                                            @endif
                                                            @foreach($provinceDatas as $data3)
                                                                <option value="{{ strtolower($data3->id) }}">{{ ucwords($data3->name) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                                                        <label for="">Kota <small class="c-red">*</small></label>
                                                        <select id="city" name="city" class="form-control" required>
                                                            <?php
                                                                if(old('city') != null) {
                                                                    $city = old('city');
                                                                }elseif(isset($techPersonalData->city)){
                                                                    $city = $techPersonalData->city;
                                                                }else{
                                                                    $city = null;
                                                                }
                                                            ?>
                                                            @if ($city != null)
                                                                @foreach ($cityDatas as $data4)
                                                                    @if ($data4->id == $city)
                                                                        <option value='{{ strtolower($data4->id) }}'>{{ ucwords($data4->name) }}</option>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <option value="0">Pilih kota</option>
                                                            @endif
                                                            @foreach($cityDatas as $data4)
                                                                @if($data4->code == $province)
                                                                    <option value="{{ strtolower($data4->id) }}">{{ ucwords($data4->name) }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="w-100"></div>
                                                    <div class="col-md form-group{{ $errors->has('religion') ? ' has-error' : '' }} ">
                                                        <label for="">Agama</label>
                                                        <select name="religion" class="form-control" placeholder="Pilih agama" required>
                                                            <?php 
                                                                if (old('religion') != NULL) {
                                                                    $statusReligion = old('religion');
                                                                }elseif(isset($techPersonalData->religion)){
                                                                    $statusReligion = $techPersonalData->religion;
                                                                }else{
                                                                    $statusReligion = NULL;
                                                                }
                                                            ?>
                                                            @if($statusReligion != NULL)
                                                                @foreach($procReligions as $religion)
                                                                    @if($religion->id == $statusReligion)
                                                                        <option value="{{ $religion->id }}">{{ ucwords($religion->name)}}</option>
                                                                    @endif
                                                                @endforeach
                                                                @foreach($procReligions as $religion)
                                                                    @if($religion->id != $statusReligion)
                                                                        <option value="{{ $religion->id }}">{{ ucwords($religion->name)}}</option>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <option value="0">Pilih salah satu</option>
                                                                @foreach($procReligions as $religion)
                                                                    <option value="{{ $religion->id }}">{{ ucwords($religion->name)}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="col-md form-group{{ $errors->has('gender') ? ' has-error' : '' }} ">
                                                        <label for="">Jenis kelamin</label>
                                                        <select name="gender" class="form-control" placeholder="Pilih Jenis Kelamin" required>
                                                            <?php 
                                                                if (old('gender') != NULL) {
                                                                    $statusGender = old('gender');
                                                                }elseif(isset($techPersonalData->gender)){
                                                                    $statusGender = $techPersonalData->gender;
                                                                }else{
                                                                    $statusGender = NULL;
                                                                }
                                                            ?>
                                                            @if($statusGender != NULL)
                                                                @foreach($procGenders as $gender)
                                                                    @if($gender->id == $statusGender)
                                                                        <option value="{{ $gender->id }}">{{ ucwords($gender->name)}}</option>
                                                                    @endif
                                                                @endforeach
                                                                @foreach($procGenders as $gender)
                                                                    @if($gender->id != $statusGender)
                                                                        <option value="{{ $gender->id }}">{{ ucwords($gender->name)}}</option>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <option value="0">Pilih salah satu</option>
                                                                @foreach($procGenders as $gender)
                                                                    <option value="{{ $gender->id }}">{{ ucwords($gender->name)}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="col-md form-group{{ $errors->has('marital_status') ? ' has-error' : '' }}">
                                                        <label for="">Status</label>
                                                        <select name="marital_status" class="form-control" placeholder="Pilih Jenis Kelamin" required>
                                                            <?php 
                                                                if (old('marital_status') != NULL) {
                                                                    $statusMarital = old('marital_status');
                                                                }elseif(isset($techPersonalData->marital_status)){
                                                                    $statusMarital = $techPersonalData->marital_status;
                                                                }else{
                                                                    $statusMarital = NULL;
                                                                }
                                                            ?>
                                                            @if($statusMarital != NULL)
                                                                @foreach($procMaritalStatus as $marital_status)
                                                                    @if($marital_status->id == $statusMarital)
                                                                        <option value="{{ $marital_status->id }}">{{ ucwords($marital_status->name)}}</option>
                                                                    @endif
                                                                @endforeach
                                                                @foreach($procMaritalStatus as $marital_status)
                                                                    @if($marital_status->id != $statusMarital)
                                                                        <option value="{{ $marital_status->id }}">{{ ucwords($marital_status->name)}}</option>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <option value="0">Pilih salah satu</option>
                                                                @foreach($procMaritalStatus as $marital_status)
                                                                    <option value="{{ $marital_status->id }}">{{ ucwords($marital_status->name)}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="w-100"></div>
                                                    <div class="col-md form-group{{ $errors->has('ktp') ? ' has-error' : '' }}">
                                                        <label for="ktp">No KTP</label>
                                                        <input type="number" class="form-control" name="ktp" value="{{ old('ktp') ? old('ktp') : $techPersonalData->ktp }}" data-parsley-minlength="3" required>
                                                    </div>
                                                    <div class="col-md form-group">
                                                        <label for="npwp">No NPWP</label>
                                                        <input type="number" class="form-control" name="npwp" value="{{ old('npwp') ? old('npwp') : $techPersonalData->npwp }}">
                                                    </div>
                                                    <div class="w-100"></div>
                                                    <div class="col-md form-group{{ $errors->has('bpjs_health') ? ' has-error' : '' }}">
                                                        <label for="bpjs_health">No BPJS kesehatan</label>
                                                        <input type="number" class="form-control" name="bpjs_health" value="{{ old('bpjs_health') ? old('bpjs_health') : $techPersonalData->bpjs_health }}" data-parsley-minlength="3" required>
                                                    </div>
                                                    <div class="col-md form-group">
                                                        <label for="bpjs_ketenagakerjaan">No BPJS ketenagakerjaan</label>
                                                        <input type="number" class="form-control" name="bpjs_ketenagakerjaan" value="{{ old('bpjs_ketenagakerjaan') ? old('bpjs_ketenagakerjaan') : $techPersonalData->bpjs_ketenagakerjaan }}">
                                                    </div>
                                                    <div class="w-100"></div>
                                                    <div class="col-md form-group{{ $errors->has('recitation_place') ? ' has-error' : '' }}">
                                                        <label for="">Tempat mengaji <small class="c-red">*</small></label>
                                                        <textarea type="text" class="form-control" name="recitation_place" required>{{ old('recitation_place') ? old('recitation_place') : $techPersonalData->recitation_place }}</textarea>
                                                    </div>
                                                    <div class="col-md form-group{{ $errors->has('ustad') ? ' has-error' : '' }}">
                                                        <label for="">Nama ustad <small class="c-red">*</small></label>
                                                        <textarea type="text" class="form-control" name="ustad" required>{{ old('ustad') ? old('ustad') : $techPersonalData->ustad }}</textarea>
                                                    </div>
                                                    <div class="w-100"></div>
                                                    <div class="col-md form-group{{ $errors->has('last_book_read') ? ' has-error' : '' }}">
                                                        <?php 
                                                            $now = date('Y-m-d');
                                                            $dateMin = strtotime($now);
                                                        ?>
                                                        <label for="last_book_read">Kapan terakhir baca buku? <small class="c-red">*</small></label>
                                                        <input type="date" class="form-control" name="last_book_read" value="{{ old('last_book_read') ? old('last_book_read') : date('Y-m-d', strtotime($techPersonalData->last_book_read)) }}" max="{{ date('Y-m-d',$dateMin) }}" required>
                                                    </div>
                                                    <div class="col-md form-group{{ $errors->has('book_title') ? ' has-error' : '' }}">
                                                        <label for="">Judul buku</label>
                                                        <textarea type="text" class="form-control" name="book_title">{{ old('book_title') ? old('book_title') : $techPersonalData->book_title }}</textarea>
                                                    </div>
                                                    <div class="col-md form-group{{ $errors->has('book_summary') ? ' has-error' : '' }}">
                                                        <label for="">Isi dari buku</label>
                                                        <textarea type="text" class="form-control" name="book_summary">{{ old('book_summary') ? old('book_summary') : $techPersonalData->book_summary }}</textarea>
                                                    </div>
                                                    <div class="w-100"></div>
                                                    <div class="col-md form-group{{ $errors->has('ktp_address') ? ' has-error' : '' }}">
                                                        <label for="">Alamat sesuai KTP</label>
                                                        <textarea type="text" class="form-control" name="ktp_address" cols="10" rows="5" minlength="10">{{ old('ktp_address') ? old('ktp_address') : $techPersonalData->ktp_address }}</textarea>
                                                    </div>
                                                    <div class="col-md form-group{{ $errors->has('current_address') ? ' has-error' : '' }}">
                                                        <label for="">Alamat sekarang</label>
                                                        <textarea type="text" class="form-control" name="current_address" cols="10" rows="5" minlength="10">{{ old('current_address') ? old('current_address') : $techPersonalData->current_address }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col-md">
                                                    <button type="submit" class="btn btn-orange" name="submit">Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    @else
                                        <form class="w-100" action="{{ route($formRouteStore) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row m-0 plr-0">
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
                                                    <input type="text" class="form-control" name="contact_name" value="{{ old('contact_name') ?? old('contact_name') }}" required>
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('relationship') ? ' has-error' : '' }}">
                                                    <label for="relationship">Relationship</label>
                                                    <input type="text" class="form-control" name="relationship" value="{{ old('relationship') ?? old('relationship') }}" required>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col-md form-group{{ $errors->has('date_of_birth') ? ' has-error' : '' }}">
                                                    <?php 
                                                        $now = date('Y-m-d');
                                                        $dateMin = strtotime($now.' -45 year');
                                                    ?>
                                                    <label for="date_of_birth">Tanggal lahir</label>
                                                    <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth') ?? old('date_of_birth') }}" max="{{ date('Y-m-d',$dateMin) }}" required>
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('province') ? ' has-error' : '' }}">
                                                    <label for="">Propinsi <small class="c-red">*</small></label>
                                                    <select id="province" name="province" class="form-control select2" required>  
                                                        @if (!empty(old('province'))))
                                                            <option value="{{ old('province') }}">{{ ucfirst(strtolower(old('name'))) }}</option>
                                                        @else
                                                            <option value="0">Pilih propinsi</option>
                                                        @endif
                                                        @foreach($provinceDatas as $provinceData)
                                                            <option value='{{ $provinceData->id }}'>{{ ucfirst(strtolower($provinceData->name)) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                                                    <label for="">Kota <small class="c-red">*</small></label>
                                                    <select id="city" name="city" class="form-control" required>
                                                        <?php
                                                            if(old('city') != null) {
                                                                $city = old('city');
                                                            }else{
                                                                $city = null;
                                                            }
                                                        ?>
                                                        @if ($city != null)
                                                            <option value="{{ $city }}">{{ ucfirst(strtolower(old('city'))) }}</option>
                                                        @else
                                                            <option value="0">Pilih kota</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col-md form-group{{ $errors->has('religion') ? ' has-error' : '' }} ">
                                                    <label for="">Agama</label>
                                                    <select name="religion" class="form-control" placeholder="Pilih agama" required>
                                                        <?php 
                                                            if (old('religion') != NULL) {
                                                                $statusReligion = old('religion');
                                                            }else{
                                                                $statusReligion = NULL;
                                                            }
                                                        ?>
                                                        @if($statusReligion != NULL)
                                                            @foreach($procReligions as $religion)
                                                                @if($religion->id == $statusReligion)
                                                                    <option value="{{ $religion->id }}">{{ ucwords($religion->name)}}</option>
                                                                @endif
                                                            @endforeach
                                                            @foreach($procReligions as $religion)
                                                                @if($religion->id != $statusReligion)
                                                                    <option value="{{ $religion->id }}">{{ ucwords($religion->name)}}</option>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <option value="0">Pilih salah satu</option>
                                                            @foreach($procReligions as $religion)
                                                                <option value="{{ $religion->id }}">{{ ucwords($religion->name)}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('gender') ? ' has-error' : '' }} ">
                                                    <label for="">Jenis kelamin</label>
                                                    <select name="gender" class="form-control" placeholder="Pilih jenis kelamin" required>
                                                        <?php 
                                                            if (old('gender') != NULL) {
                                                                $statusGender = old('gender');
                                                            }else{
                                                                $statusGender = NULL;
                                                            }
                                                        ?>
                                                        @if($statusGender != NULL)
                                                            @foreach($procGenders as $gender)
                                                                @if($gender->id == $statusGender)
                                                                    <option value="{{ $gender->id }}">{{ ucwords($gender->name)}}</option>
                                                                @endif
                                                            @endforeach
                                                            @foreach($procGenders as $gender)
                                                                @if($gender->id != $statusGender)
                                                                    <option value="{{ $gender->id }}">{{ ucwords($gender->name)}}</option>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <option value="0">Pilih salah satu</option>
                                                            @foreach($procGenders as $gender)
                                                                <option value="{{ $gender->id }}">{{ ucwords($gender->name)}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('marital_status') ? ' has-error' : '' }}">
                                                    <label for="">Status</label>
                                                    <select name="marital_status" class="form-control" placeholder="Pilih status nikah" required>
                                                        <?php 
                                                            if (old('marital_status') != NULL) {
                                                                $statusMarital = old('marital_status');
                                                            }else{
                                                                $statusMarital = NULL;
                                                            }
                                                        ?>
                                                        @if($statusMarital != NULL)
                                                            @foreach($procMaritalStatus as $marital_status)
                                                                @if($marital_status->id == $statusMarital)
                                                                    <option value="{{ $marital_status->id }}">{{ ucwords($marital_status->name)}}</option>
                                                                @endif
                                                            @endforeach
                                                            @foreach($procMaritalStatus as $marital_status)
                                                                @if($marital_status->id != $statusMarital)
                                                                    <option value="{{ $marital_status->id }}">{{ ucwords($marital_status->name)}}</option>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <option value="0">Pilih salah satu</option>
                                                            @foreach($procMaritalStatus as $marital_status)
                                                                <option value="{{ $marital_status->id }}">{{ ucwords($marital_status->name)}}</option>
                                                            @endforeach
                                                        @endif
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
                                                <div class="col-md form-group{{ $errors->has('recitation_place') ? ' has-error' : '' }}">
                                                    <label for="">Tempat mengaji <small class="c-red">*</small></label>
                                                    <textarea type="text" class="form-control" name="recitation_place" required>{{ old('recitation_place') ?? old('recitation_place') }}</textarea>
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('ustad') ? ' has-error' : '' }}">
                                                    <label for="">Nama ustad <small class="c-red">*</small></label>
                                                    <textarea type="text" class="form-control" name="ustad" required>{{ old('ustad') ?? old('ustad') }}</textarea>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col-md form-group{{ $errors->has('last_book_read') ? ' has-error' : '' }}">
                                                    <?php 
                                                        $now = date('Y-m-d');
                                                        $dateMin = strtotime($now);
                                                    ?>
                                                    <label for="last_book_read">Kapan terakhir baca buku? <small class="c-red">*</small></label>
                                                    <input type="date" class="form-control" name="last_book_read" value="{{ old('last_book_read') ?? old('last_book_read') }}" max="{{ date('Y-m-d',$dateMin) }}" required>
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('book_title') ? ' has-error' : '' }}">
                                                    <label for="">Judul buku <small class="c-red">*</small></label>
                                                    <textarea type="text" class="form-control" name="book_title" placeholder="Judul buku" required>{{ old('book_title') ?? old('book_title') }}</textarea>
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('book_summary') ? ' has-error' : '' }}">
                                                    <label for="">Isi dari buku  <small class="c-red">*</small></label>
                                                    <textarea type="text" class="form-control" name="book_summary" required>{{ old('book_summary') ?? old('book_summary') }}</textarea>
                                                </div>
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
                                            <div class="col-md mt-2 mb-2">
                                                <button type="submit" class="btn btn-orange" name="submit">Simpan</button>
                                            </div>
                                        </form>
                                    @endif
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div>
                        <!-- personal data end -->
                        <!-- family info -->
                        <div class="tab-pane" id="keluarga-2">
                            <div class="row">
                                <div class="col-12">
                                    @if(isset($techFamilyInfo))
                                        <form class="w-100" action="{{ route($formRouteFamilyUpdate, $techFamilyInfo->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="row m-0">
                                                <div class="w-100"></div>
                                                <div class="col-md form-group{{ $errors->has('father') ? ' has-error' : '' }}">
                                                    <label for="father">Ayah</label>
                                                    <input type="text" class="form-control" name="father" value="{{ old('father') ? old('father') : $techFamilyInfo->father }}" required>
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('father_profession') ? ' has-error' : '' }}">
                                                    <label for="father_profession">Pekerjaan</label>
                                                    <input type="text" class="form-control" name="father_profession" value="{{ old('father_profession') ? old('father_profession') : $techFamilyInfo->father_profession }}" required>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col-md form-group{{ $errors->has('mother') ? ' has-error' : '' }}">
                                                    <label for="mother">Ibu</label>
                                                    <input type="text" class="form-control" name="mother" value="{{ old('mother') ? old('mother') : $techFamilyInfo->mother }}" data-parsley-minlength="3" required>
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('mother_profession') ? ' has-error' : '' }}">
                                                    <label for="mother_profession">Pekerjaan</label>
                                                    <input type="text" class="form-control" name="mother_profession" value="{{ old('mother_profession') ? old('mother_profession') : $techFamilyInfo->mother_profession }}" required>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col-md form-group{{ $errors->has('spouse') ? ' has-error' : '' }}">
                                                    <label for="spouse">Suami/istri</label>
                                                    <input type="text" class="form-control" name="spouse" value="{{ old('spouse') ? old('spouse') : $techFamilyInfo->spouse }}" data-parsley-minlength="3">
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('spouse_profession') ? ' has-error' : '' }}">
                                                    <label for="spouse_profession">Pekerjaan</label>
                                                    <input type="text" class="form-control" name="spouse_profession" value="{{ old('spouse_profession') ? old('spouse_profession') : $techFamilyInfo->spouse_profession }}">
                                                </div>
                                            </div>
                                        
                                            <div class="col-md">
                                                <button type="submit" class="btn btn-orange" name="submit">Simpan</button>
                                            </div>
                                        </form>
                                    @else
                                        <form class="w-100" action="{{ route($formRouteFamilyStore) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row m-0">
                                                <div class="w-100"></div>
                                                <div class="col-md form-group{{ $errors->has('father') ? ' has-error' : '' }}">
                                                    <label for="">Ayah</label>
                                                    <input type="text" class="form-control" name="father" value="{{ old('father') ?? old('father') }}" required>
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('father_profession') ? ' has-error' : '' }}">
                                                    <label for="father_profession">Pekerjaan</label>
                                                    <input type="text" class="form-control" name="father_profession" value="{{ old('father_profession') ?? old('father_profession') }}" required>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col-md form-group{{ $errors->has('mother') ? ' has-error' : '' }}">
                                                    <label for="mother">Ibu</label>
                                                    <input type="text" class="form-control" name="mother" value="{{ old('mother') ?? old('mother') }}" data-parsley-minlength="3" required>
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('mother_profession') ? ' has-error' : '' }}">
                                                    <label for="mother_profession">Pekerjaan</label>
                                                    <input type="text" class="form-control" name="mother_profession" value="{{ old('mother_profession') ?? old('mother_profession') }}" required>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col-md form-group{{ $errors->has('spouse') ? ' has-error' : '' }}">
                                                    <label for="spouse">Suami/istri</label>
                                                    <input type="text" class="form-control" name="spouse" value="{{ old('spouse') ?? old('spouse') }}" data-parsley-minlength="3">
                                                </div>
                                                <div class="col-md form-group{{ $errors->has('spouse_profession') ? ' has-error' : '' }}">
                                                    <label for="spouse_profession">Pekerjaan</label>
                                                    <input type="text" class="form-control" name="spouse_profession" value="{{ old('spouse_profession') ?? old('spouse_profession') }}">
                                                </div>
                                            </div>
                                        
                                            <div class="col-md">
                                                <button type="submit" class="btn btn-orange" name="submit">Simpan</button>
                                            </div>
                                        </form>
                                    @endif
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div>
                        <!-- family info end -->
                        <!-- education -->
                        <div class="tab-pane" id="pendidikan-2">
                            <div class="row">
                                <div class="col-12">
                                @if (count($educationDatas) > 0) 
                                    <div class="row m-0">
                                        @foreach($educationDatas as $data)
                                            <div class="col-6 p-2">
                                                <div class="bg-card-box br-5 p-2">
                                                    <span class="text-danger"><strong>{{ isset($data->level_name) ? strtoupper($data->level_name) : 'Belum ada data' }}</strong></span>

                                                    <strong>{{ isset($data->name) ? '| '.ucwords($data->name) : '' }}</strong>
                                                    <strong>{{ isset($data->year) ? '| '.ucwords($data->year) : '' }}</strong>
                                                
                                                    <form action="{{ route($formRouteEducationDestroy, $data->id) }}" class="float-right" style="display:inline" method="POST">
                                                        @method('DELETE')
                                                        @csrf
                                                        <a href="{{ route($formRouteEducationEdit, $data->id) }}" class='btn badge badge-info float-right' style="display:inline;"> <i class='fas fa-edit' title='Edit'></i> Ubah</a>

                                                        <br><button type="submit" class="btn badge badge-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                                    </form>

                                                    <br> <strong>{!! isset($data->city_name) ? "<span class='text-success'><small>Kota:</small></span> ".ucwords($data->city_name) : '' !!}</strong>

                                                    <strong>{!! isset($data->province_name) ? "<span class='text-success'><small>Propinsi:</small></span> ".ucwords($data->province_name) : 'Belum ada data' !!}</strong>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="col-12">
                                            {{ $educationDatas->links() }}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <a href="{{ route($formRouteEducationCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah data pendidikan</a>
                                    </div>
                                @else
                                    <form class="w-100" action="{{ route($formRouteEducationStore) }}" method="POST" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row m-0">
                                            <div class="w-100"></div>
                                            <div class="col-md form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                                <label for="name">Nama sekolah</label>
                                                <input type="text" class="form-control" name="name" value="{{ old('name') ?? old('name') }}" placeholder="Nama sekolah" required>
                                            </div>
                                            <div class="col-md form-group{{ $errors->has('level') ? ' has-error' : '' }}">
                                                <label for="level">Tingkat</label>
                                                <select name="level" class="form-control select2{{ $errors->has('level') ? ' has-error' : '' }}" required>
                                                    <?php
                                                        if(old('level') != null) {
                                                            $level = old('level');
                                                        }else{
                                                            $level = null;
                                                        }
                                                    ?>
                                                    @if ($level != null)
                                                        @foreach ($educationLevels as $dataOne)
                                                            @if ($dataOne->id == $level)
                                                                <option value='{{ strtolower($dataOne->id) }}'>{{ strtoupper($dataOne->name) }}</option>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <option value="0">Pilih tingkat</option>
                                                    @endif
                                                    @foreach($educationLevels as $dataOne)
                                                        <option value="{{ strtolower($dataOne->id) }}">{{ strtoupper($dataOne->name) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col-md form-group{{ $errors->has('province') ? ' has-error' : '' }}">
                                                <label for="">Propinsi <small class="c-red">*</small></label>
                                                <select id="provinceEducation" name="province" class="form-control select2" required>  
                                                    @if (!empty(old('province')))
                                                        <option value="{{ old('province') }}">{{ ucfirst(strtolower(old('name'))) }}</option>
                                                    @else
                                                        <option value="0">Pilih propinsi</option>
                                                    @endif
                                                    @foreach($provinceDatas as $provinceData)
                                                        <option value='{{ $provinceData->id }}'>{{ ucfirst(strtolower($provinceData->name)) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                                                <label for="">Kota <small class="c-red">*</small></label>
                                                <select id="cityEducation" name="city" class="form-control" required>
                                                    <?php
                                                        if(old('city') != null) {
                                                            $city = old('city');
                                                        }else{
                                                            $city = null;
                                                        }
                                                    ?>
                                                    @if ($city != null)
                                                        <option value="{{ $city }}">{{ ucfirst(strtolower(old('city'))) }}</option>
                                                    @else
                                                        <option value="0">Pilih kota</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="col-md form-group{{ $errors->has('year') ? ' has-error' : '' }}">
                                                <label for="year">Tahun</label>
                                                <select name="year" class="form-control select2{{ $errors->has('year') ? ' has-error' : '' }}" required>
                                                    <?php
                                                        if(old('year') != null) {
                                                            $year = old('year');
                                                        }else{
                                                            $year = null;
                                                        }
                                                        //year
                                                        $currentDate = new DateTime();
                                                        $currentYear = $currentDate->format('Y');
                                                        $tenYearsAgo = $currentDate->format('Y');
                                                    ?>
                                                    @if ($year != null)
                                                        <option value='{{ $year }}'>{{ $year }}</option>
                                                    @else
                                                        <option value="0">Pilih tahun</option>
                                                    @endif

                                                    @for($i=1986; $i < $currentYear + 1; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md">
                                            <button type="submit" class="btn btn-orange" name="submit">Simpan</button>
                                        </div>
                                    </form>
                                @endif
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div>
                        <!-- education end -->
                        <!-- upload document -->
                        <div class="tab-pane" id="dokumen-2">
                            <div class="row">
                                @if(count($documentDatas) < 5)
                                    <div class="col-md-12">
                                        <div class="alert alert-warning">
                                            <span>Anda harus mengupload <strong>{{ count($docTypes) }} 
                                                (@foreach($docTypes as $docType)
                                                    {{ ucwords($docType->name)}}
                                                @endforeach)</strong> untuk dapat melanjutkan ke proses berikutnya.
                                            </span> 
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12">
                                @if (count($documentDatas) > 0)
                                    <div class="row m-0">
                                        @foreach($documentDatas as $data)
                                            <div class="col-6 p-2">
                                                <div class="bg-card-box br-5 p-2">
                                                    <span class="text-danger text-uppercase"><strong>{{ isset($data->doc_name) ? strtoupper($data->doc_name) : 'Belum ada data' }}</strong></span>

                                                    <form action="{{ route($formRouteUploadDestroy, $data->id) }}" class="float-right" style="display:inline" method="POST">
                                                        @method('DELETE')
                                                        @csrf
                                                        <a href="{{ route($formRouteUploadEdit, $data->id) }}" class='btn badge badge-info float-right' style="display:inline;"> <i class='fas fa-edit' title='Edit'></i> Ubah</a>

                                                        <br><button type="submit" class="btn badge badge-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                                    </form>

                                                    @if(isset($data->image))
                                                        <br><strong><a type="button" class="text-info" data-toggle="modal" data-target="#docModal{{ $data->id }}">Lihat dokumen </a></strong>
                                                    @endif

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="docModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="projectMinutes" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                                            <div class="modal-content-img">
                                                                <div class="modal-body text-center">
                                                                <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                                    <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/upload-doc/tech/'.$data->image) }}"  />
                                                                    <div class="alert alert-warning" id="projectMinutes">
                                                                        <h5>
                                                                            Tipe dokumen: <span class="text-muted">{{ ucfirst($data->doc_name) }}</span>
                                                                        </h5>
                                                                    </div>
                                                                </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="col-12">
                                            {{ $documentDatas->links() }}
                                        </div>
                                    </div>
                                    @if(count($documentDatas) < 5)
                                        <div class="col-md-12">
                                            <a href="{{ route($formRouteUploadCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Upload dokumen [{{ count($documentDatas) }}/5]</a>
                                        </div>
                                    @endif
                                @else
                                    <form class="w-100" action="{{ route($formRouteUploadStore) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row m-0">
                                            <div class="col-md form-group{{ $errors->has('doc_type') ? ' has-error' : '' }} ">
                                                <label for="">Tipe dokumen</label>
                                                <select name="doc_type" class="form-control" placeholder="Tipe dokumen" required>
                                                    <?php 
                                                        if (old('doc_type') != NULL) {
                                                            $dataTypes = old('doc_type');
                                                        }else{
                                                            $dataTypes = NULL;
                                                        }
                                                    ?>
                                                    @if($dataTypes != NULL)
                                                        @foreach($docTypes as $docType)
                                                            @if($docType->id == $dataTypes)
                                                                <option value="{{ $docType->id }}">{{ ucwords($docType->name)}}</option>
                                                            @endif
                                                        @endforeach
                                                        @foreach($docTypes as $docType)
                                                            @if($docType->id != $dataTypes)
                                                                <option value="{{ $docType->id }}">{{ ucwords($docType->name)}}</option>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <option value="0">Pilih salah satu</option>
                                                        @foreach($docTypes as $docType)
                                                            <option value="{{ $docType->id }}">{{ ucwords($docType->name)}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col-md{{ $errors->has('image') ? ' has-error' : '' }}">
                                                <div class="card-box">
                                                    <h4 class="header-title mb-3">Dokumen</h4>
                                                    <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/upload-doc/tech/default.png') }}" />
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md mt-2 mb-2">
                                            <button type="submit" class="btn btn-orange" name="submit">Simpan</button>
                                        </div>
                                    </form>
                                @endif
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div>
                        <!-- upload document end -->
                    </div> <!-- tab-content -->
                </div>
            </div>
        </div>
        <div class="card-body">
            <ul class="list-inline mb-0 wizard">
                <li class="previous list-inline-item">
                    <a href="javascript: void(0);" class="btn btn-blue-lini">Previous</a>
                </li>
                <li class="next list-inline-item float-right">
                    <a href="javascript: void(0);" class="btn btn-blue-lini">Next</a>
                </li>
            </ul>
        </div>
    </div>
</div> <!-- card -->

@endsection

@section ('script')
<script>
    function ucwords (str) {
        return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
            return $1.toUpperCase();
        });
    }

    $('#province').on('change',function(){ 
        var stateID = $(this).val();  
        if(stateID){
            $.ajax({
                type:"GET",
                url:"{{ url('tech/tech-get-city-list') }}?code="+stateID,
                success:function(res){        
                    if(res){
                        $("#city").empty();
                        $.each(res,function(key,value){
                            $("#city").append('<option value="'+value.id+'">'+ucwords(value.name)+'</option>');
                        });
                    }else{
                        $("#city").empty();
                    }
                }
            });
        }else{
            $("#city").empty();
        }
    });
    $('#provinceEducation').on('change',function(){ 
        var stateID = $(this).val();  
        if(stateID){
            $.ajax({
                type:"GET",
                url:"{{ url('tech/tech-get-city-list') }}?code="+stateID,
                success:function(res){        
                    if(res){
                        $("#cityEducation").empty();
                        $.each(res,function(key,value){
                            $("#cityEducation").append('<option value="'+value.id+'">'+ucwords(value.name)+'</option>');
                        });
                    }else{
                        $("#cityEducation").empty();
                    }
                }
            });
        }else{
            $("#cityEducation").empty();
        }
    });
</script>
<!-- Plugins js-->
<script src="assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>

<!-- Init js-->
<script src="assets/js/pages/form-wizard.init.js"></script>
@endsection
