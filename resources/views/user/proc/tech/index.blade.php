@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar teknisi';

    $formRouteIndex = 'user-tech.index';
    $formRouteCreate = 'user-tech.create';
    $formRouteEdit = 'user-tech.edit';
    $formRouteShow = 'user-tech.show';
    $formRouteDestroy = 'user-tech.destroy';
?>
@endsection

@section('content')
<div class="flash-message mt-2">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<div class="card mt-2">
    <div class="card-header text-center bb-orange">
        <strong>{{ strtoupper($pageTitle) }}</strong>
        <a href="{{ route ($formRouteIndex,'skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
    </div>

    @if (sizeof($techsDatas) > 0)
        <div class="card-body bg-gray-lini-2">
            <div class="container-fluid">
                <div class="row">
                    <?php $separator = 1; ?>
                    @foreach ($techsDatas as $data)
                        <div class="col-sm p-2">
                            <div class="bg-card-box br-5 p-2">
                                <div class="float-right text-right">
                                    @if($data->active == 1)
                                        <div class="badge badge-success">Active</div>
                                    @else
                                        <div class="badge badge-danger">Inactive</div>
                                    @endif
                                    <br>
                                    <?php 
                                        $psychologyType = ['intj','enfj','entp','esfp'];
                                    ?>
                                    @if(in_array(strtolower($data->test_psychology_result),$psychologyType))
                                        <div class="badge badge-success">Cocok</div>
                                    @else
                                        <div class="badge badge-danger">Tidak cocok</div>
                                    @endif
                                </div>
                                <div class="float-left mr-1">
                                    <img src="{{ asset('admintheme/images/users/'.$data->image) }}" alt="user-image" class="rounded-circle avatar-md">
                                </div>

                                <span class="text-info">
                                    @if(isset($data->firstname))
                                        {{ ucwords($data->firstname).' '.ucwords($data->lastname) }}
                                    @else
                                        {{ ucwords($data->name) }}
                                    @endif
                                </span>

                                <br>
                                <small>
                                    @if(isset($ratingCountAlls) && count($ratingCountAlls) > 0)
                                        @foreach ($ratingCountAlls as $ratingCountAll)
                                            @if ($ratingCountAll->tech_id == $data->id)
                                                <?php 
                                                    if ($ratingCountAll->count > 0) {
                                                        $ratingValue = $ratingCountAll->totalCount / $ratingCountAll->count; 
                                                    }else{
                                                        $ratingValue = 0;
                                                    }
                                                ?>
                                                @if ($ratingValue >= 5)
                                                    @for ($ri = 0; $ri < 5; $ri++)
                                                    <span class="fa fa-star text-warning"></span>
                                                    @endfor
                                                @elseif($ratingValue >= 4 && $ratingValue <= 5)
                                                    @for ($ri = 0; $ri < 4; $ri++)
                                                    <span class="fa fa-star text-warning"></span>
                                                    @endfor
                                                    @for ($ri = 0; $ri < 1; $ri++)
                                                    <span class="fa fa-star rating rating-checked"></span>
                                                    @endfor
                                                @elseif($ratingValue >= 3 && $ratingValue <= 4)
                                                    @for ($ri = 0; $ri < 3; $ri++)
                                                    <span class="fa fa-star text-warning"></span>
                                                    @endfor
                                                    @for ($ri = 0; $ri < 2; $ri++)
                                                    <span class="fa fa-star rating rating-checked"></span>
                                                    @endfor
                                                @elseif($ratingValue >= 2 && $ratingValue <= 3)
                                                    @for ($ri = 0; $ri < 2; $ri++)
                                                    <span class="fa fa-star text-warning"></span>
                                                    @endfor
                                                    @for ($ri = 0; $ri < 3; $ri++)
                                                    <span class="fa fa-star rating rating-checked"></span>
                                                    @endfor
                                                @elseif($ratingValue >= 1 && $ratingValue <= 2)
                                                    @for ($ri = 0; $ri < 1; $ri++)
                                                    <span class="fa fa-star text-warning"></span>
                                                    @endfor
                                                    @for ($ri = 0; $ri < 4; $ri++)
                                                    <span class="fa fa-star rating rating-checked"></span>
                                                    @endfor
                                                @else
                                                    @for ($ri = 0; $ri < 5; $ri++)
                                                    <span class="fa fa-star rating text-checked"></span>
                                                    @endfor
                                                @endif

                                                ({{ $ratingCountAll->count }} voter)
                                            @else
                                                @for ($ri = 0; $ri < 5; $ri++)
                                                    <span class="fa fa-star rating text-checked"></span>
                                                @endfor
                                            @endif
                                        @endforeach
                                    @else
                                        @for ($ri = 0; $ri < 5; $ri++)
                                            <span class="fa fa-star rating text-checked"></span>
                                        @endfor
                                    @endif
                                </small>

                                <small>
                                    <br> Keahlian: 
                                    @if(isset($data->skill_id))
                                        @foreach($techSkillsDatas as $skillData)
                                            @if($skillData->id == $data->skill_id)
                                                <span>{{ $skillData->name }}</span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="text-danger">-</span>
                                    @endif
                                    <br> Proyek terbaru: <span class="text-danger">-</span>
                                    <br> Tes psikologi tgl: {{ $data->test_psychology_date ? date('l, d F Y',strtotime($data->test_psychology_date)) : '-'}} 

                                        @if(isset($data->test_psychology_date))
                                            @if($data->test_psychology_status == 2)
                                                <span class="text-success">| Lulus seleksi</span>
                                            @elseif($data->test_psychology_status == 1)
                                                <span class="text-danger">| {{ strtoupper($data->test_psychology_result) }}</span>
                                            @else
                                                <span class="text-info">| {{ strtoupper($data->test_psychology_result) }}</span>
                                            @endif
                                        @endif
                                    <br> 
                                    Tes assessment: 
                                    <?php 
                                        if ($data->test_assessment_count == $testTrainingCatCount) {
                                            $assessmentCss = 'text-success';
                                        }elseif($data->test_assessment_count > $testTrainingCatCount/2 && $data->test_assessment_count < $testTrainingCatCount){
                                            $assessmentCss = 'text-info';
                                        }else{
                                            $assessmentCss = 'text-danger';
                                        }
                                    ?>
                                    <span class="{{ $assessmentCss}}">[{{ $data->test_assessment_count.'/'.$testTrainingCatCount }}]</span>
                                </small>

                                <div>
                                    @if ($data->mobile !== null)
                                        <a href='https://wa.me/{{ $data->mobile }}?text=Haloo ' target='_blank' class='btn btn-icon waves-effect waves-light btn-success t-white mb-1'> <i class='fab fa-whatsapp' title='Whatsapp'></i> {{ $data->mobile }}</a>
                                    @endif

                                    <form action="{{ route($formRouteDestroy, $data->id) }}" style="display:inline-block" method="POST">
                                        @method('DELETE')
                                        @csrf

                                        <a href="{{ route($formRouteShow, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-warning t-white mb-1'> <i class='fas fa-eye' title='Show'></i></a>

                                        <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white mb-1'> <i class='fas fa-edit' title='Edit'></i></a>

                                        <button type="submit" class="btn btn-danger mb-1" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                        <?php $separator++; ?>
                    @endforeach
                    <div class="w-100"></div>
                    <div class="col-sm">
                    <?php 
                        #$techsDatas->setPath('tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                    ?>
                    <?php $paginator = $techsDatas; ?>
                    @include('includes.paginator')
                </div>
                </div>
            </div> <!-- container-fluid -->
        </div>
        <div class="card-body">
            <div class="col-md">
                <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah teknisi</a>
            </div>
        </div>
    @else
    <div class="card-body bg-gray-lini-2">
        <div class="alert alert-warning">Belum ada data.</div>
    </div>
    <div class="card-body">
        <div class="col-md">
            <a href="{{ route($formRouteCreate) }}" class="btn btn-blue-lini"><i class="fa fa-plus"></i> Tambah teknisi</a>
        </div>
    </div>
    @endif
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
