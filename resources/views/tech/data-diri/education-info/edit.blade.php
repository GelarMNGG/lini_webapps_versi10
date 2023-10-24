@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Ubah data pendidikan';
    $formRouteIndex = 'tech-input-data-pendidikan.index';
    $formRouteUpdate = 'tech-input-data-pendidikan.update';

    //route back
    $formRouteBack = 'tech-input-data-diri.index';
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


    <form class="w-100" action="{{ route($formRouteUpdate, $techEducationInfo->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="w-100"></div>
                <div class="col-md form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                    <label for="name">Nama sekolah</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') ? old('name') : $techEducationInfo->name }}" placeholder="Nama sekolah" required>
                </div>
                <div class="col-md form-group{{ $errors->has('level') ? ' has-error' : '' }}">
                    <label for="level">Tingkatan</label>
                    <select name="level" class="form-control select2{{ $errors->has('level') ? ' has-error' : '' }}" required>
                        <?php
                            if(old('level') != null) {
                                $level = old('level');
                            }elseif(isset($techEducationInfo->level)){
                                $level = $techEducationInfo->level;
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
                    <select id="province" name="province" class="form-control select2" required> 
                        <?php
                            if(old('province') != null) {
                                $province = old('province');
                            }elseif(isset($techEducationInfo->province)){
                                $province = $techEducationInfo->province;
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
                            }elseif(isset($techEducationInfo->city)){
                                $city = $techEducationInfo->city;
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
                <div class="col-md form-group{{ $errors->has('year') ? ' has-error' : '' }}">
                    <label for="year">Tahun</label>
                    <select name="year" class="form-control select2{{ $errors->has('year') ? ' has-error' : '' }}" required>
                        <?php
                            if(old('year') != null) {
                                $year = old('year');
                            }elseif(isset($techEducationInfo->year)){
                                $year = $techEducationInfo->year;
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
        </div>
        <div class="card-body">
            <div class="col-md">
                <button type="submit" class="btn btn-orange" name="submit">Ubah</button>
                <a href="{{ route($formRouteBack) }}" type="button" class="btn btn-blue-lini">Kembali</a>
            </div>
        </div>
    </form>

</div> <!-- container-fluid -->
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
</script>
<script src="{{ asset('admintheme/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
@endsection
