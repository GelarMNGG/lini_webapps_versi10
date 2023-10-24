@extends('layouts.dashboard-form-wizard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Data teknisi';
    $formRouteIndex = 'user-tech.index';
    $formRouteCreate = 'user-tech.create';
    $formRouteEdit = 'user-tech.edit';
    $formRouteShow = 'user-tech.show';
    $formRouteDestroy = 'user-tech.destroy';

    //change psychology test result
    $formPsychologyTestResultUpdate = 'admin-test-psychology-result.update';

    //tech rating
    $formTechRatingStore = 'admin-tech-rating.store';
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
            <strong>{{ ucfirst($pageTitle) }}</strong>
            <br><span class="text-info"><strong>{{ isset($tech->firstname) ? ucwords($tech->firstname).' '.ucwords($tech->lastname) : 'Data tidak tersedia' }}</strong></span>
        </div>
        <div class="card-body bg-gray-lini-2">
            <div class="row">
                <div class="col-md p-2">
                    <div class="alert alert-warning">
                        <div class="float-right text-right">
                            @if($tech->active == 1)
                                <div class="badge badge-success">Active</div>
                            @else
                                <div class="badge badge-danger">Inactive</div>
                            @endif
                            <br>
                            @if(strtolower($techsData->test_psychology_result) == 'intj')
                                <div class="badge badge-success">Cocok</div>
                            @else
                                <div class="badge badge-danger">Tidak cocok</div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="mr-1">
                                    <img src="{{ asset('admintheme/images/users/'.$tech->image) }}" alt="user-image" class="rounded-circle avatar-xl">
                                </div>
                            </div>
                            <div class="col-md">
                                <span class="text-info">{{ ucwords($tech->firstname).' '.ucwords($tech->lastname) }}</span>
                                <br>
                                <small>
                                    @if(isset($ratingCountAlls) && count($ratingCountAlls) > 0)
                                        @foreach ($ratingCountAlls as $ratingCountAll)
                                            @if ($ratingCountAll->tech_id == $tech->id)
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
                                <br>
                                <small>Keahlian: 
                                    @if(isset($tech->skill_id))
                                        @foreach($techSkillsDatas as $skillData)
                                            @if($skillData->id == $tech->skill_id)
                                                <span>{{ $skillData->name }}</span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="text-danger">-</span>
                                    @endif
                                </small>
                                <br>
                                <small>Proyek terakhir: 
                                    @if(isset($tech->task_name))
                                        <span class="text-info">{{  ucwords($tech->task_name) }}</span>
                                    @else
                                        <span class="text-danger">Data belum tersedia.</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row m-0">
                <div class="col-xl-12">
                    <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-1 small">
                        <li class="nav-item">
                            <a href="#summary-2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                <i class="mdi mdi-face-profile mr-1"></i>
                                <span class="d-none d-sm-inline">Ringkasan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#account-2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                <i class="mdi mdi-account-circle mr-1"></i>
                                <span class="d-none d-sm-inline">Data diri </span>
                            </a>
                        </li>
                        <?php /*
                        <li class="nav-item">
                            <a href="#pendidikan-2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                <i class="mdi mdi-face-profile mr-1"></i>
                                <span class="d-none d-sm-inline">Data tes asesmen</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#keluarga-2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                <i class="mdi mdi-face-profile mr-1"></i>
                                <span class="d-none d-sm-inline">Data tes psikologi</span>
                            </a>
                        </li>
                        */ ?>
                    </ul>
                    <div class="tab-content border-0 mb-0 plr-0">
                        <!-- summary -->
                        <div class="tab-pane active" id="summary-2">
                            <div class="row">
                                <div class="col-12">
                                    <!-- psychology test result -->
                                        <div class="row alert alert-warning m-0 small mb-1">
                                            <div class="col-md">
                                                <label class="text-uppercase">Tes psikologi
                                                    @if(count($testPsychologyResults) < 1)
                                                        <span class="text-danger">| Belum ada data</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="w-100"></div>

                                            @if(isset($testPsychologyResults))
                                                @foreach($testPsychologyResults as $testPsychologyResult)
                                                    <div class="col-md mt-1">
                                                        <strong>Tanggal tes</strong>
                                                        <br>{{ isset($testPsychologyResult->created_at) ? date('l, d F Y',strtotime($testPsychologyResult->created_at)) : '-' }}
                                                    </div>
                                                    <div class="col-md mt-1">
                                                        <strong>Hasil</strong>
                                                        <br>
                                                        @if($testPsychologyResult->status == 0)
                                                            {!! '<span class="text-uppercase">'.$testPsychologyResult->result.'</span> | <span class="text-danger">Belum dinilai</span>' !!}

                                                            <div class="float-right">
                                                                <button type="button" class="bagde btn-danger" data-toggle="collapse" data-target="#beri_nilai" aria-expanded="false" aria-controls="beri_nilai">Nilai</button>
                                                            </div>
                                                        @else
                                                            {!! $testPsychologyResult->status == 1 ? '<span class="text-uppercase">'.$testPsychologyResult->result.'</span> <span class="text-danger">Tidak lulus</span>' : '<span class="text-success">Lulus</span>' !!}
                                                        @endif
                                                    </div>
                                                    <div class="col-md mt-1">
                                                        <strong>Penilai</strong>
                                                        <br>
                                                        @if($testPsychologyResult->assessor_type == 'admin')
                                                            @foreach($admins as $admin)
                                                                @if($admin->id == $testPsychologyResult->assessor_id)
                                                                    <span>{{ isset($admin->firstname) ? ucwords($admin->firstname).' '.ucwords($admin->lastname) : '-' }}</span>
                                                                @endif
                                                            @endforeach
                                                        @elseif($testPsychologyResult->assessor_type == 'user')
                                                            @foreach($users as $user)
                                                                @if($user->id == $testPsychologyResult->assessor_id)
                                                                    <span>{{ isset($user->firstname) ? ucwords($user->firstname).' '.ucwords($user->lastname) : '-' }}</span>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <span>-</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md mt-1">
                                                        <strong>Tanggal di nilai</strong>
                                                        <br>
                                                        @if(isset($testPsychologyResult->assessment_date))
                                                            <span>{{ date('l, d F Y', strtotime($testPsychologyResult->assessment_date)) }}</span>
                                                        @else
                                                            <span>-</span>
                                                        @endif
                                                    </div>

                                                    <!-- dropdown status test -->
                                                    <div class="w-100"></div>
                                                    <div class="collapse col-md" id="beri_nilai">
                                                        <form action="{{ route($formPsychologyTestResultUpdate,$testPsychologyResult->id) }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="row bg-gray-lini-2">
                                                                <div class="col-md mt-2 form-group">
                                                                    <label for="">Status </label>
                                                                    <select name="status" class="form-control select2{{ $errors->has('status') ? ' has-error' : '' }}" required>
                                                                        <?php
                                                                            if(old('status') != null) {
                                                                                $status = old('status');
                                                                            }elseif(isset($testPsychologyResult->status)){
                                                                                $status = $testPsychologyResult->status;
                                                                            }else{
                                                                                $status = null;
                                                                            }
                                                                        ?>
                                                                        @if ($status != null)
                                                                            @foreach ($testResultStatus as $data3)
                                                                                @if ($data3->id == $status)
                                                                                    <option value="{{ $data3->id }}">{{ ucwords(strtolower($data3->name)) }}</option>
                                                                                @endif
                                                                            @endforeach
                                                                            @foreach($testResultStatus as $data4)
                                                                                @if ($data4->id != $status)
                                                                                    <option value="{{ $data4->id }}">{{ ucwords(strtolower($data4->name)) }}</option>
                                                                                @endif
                                                                            @endforeach
                                                                        @else
                                                                            <option value="">Pilih status</option>
                                                                            @foreach($testResultStatus as $data2)
                                                                                <option value="{{ $data2->id }}">{{ ucwords(strtolower($data2->name)) }}</option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                                <div class="w-100"></div>
                                                                <div class="col-md">
                                                                    <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                                                                        <label>Note <small class="c-red">*</small></label>
                                                                        <textarea name="note" class="form-control" cols="10" rows="3" required>{{ old('note') ? old('note') : $testPsychologyResult->note }}</textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="w-100"></div>
                                                                <div class="col-md">
                                                                    <div class="form-group">
                                                                        <label for=""></label>
                                                                        <input type="submit" class="btn btn-orange" name="submit" value="Beri nilai" disabled>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md">
                                                                    <?php $psychologyType = ['intj','enfj','entp','esfp']; ?>
                                                                    <div class="alert alert-warning">
                                                                        Tipe kepribadian yang sesuai dengan pekerjaan teknisi adalah kepribadian <span>
                                                                        <strong>
                                                                        <?php $i=1; ?>
                                                                        @foreach($psychologyType as $psychoType)
                                                                            @if($i != count($psychologyType))
                                                                                {{ strtoupper($psychoType) }},
                                                                            @else
                                                                                {{ strtoupper($psychoType) }}.
                                                                            @endif
                                                                            <?php $i++; ?>
                                                                        @endforeach
                                                                        </strong></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <!-- dropdown status test end -->

                                                    @if(isset($testPsychologyResult->note))
                                                        <div class="w-100"></div>
                                                        <div class="col-md mt-1">
                                                            <span class="text-info">Note: </span>{!! ucfirst($testPsychologyResult->note) !!}
                                                        </div>
                                                    @endif
                                                    @if(isset($testPsychologyResult->result))
                                                        <div class="w-100"></div>
                                                        <div class="col-md mt-1">
                                                            @foreach($psychologyAnalisysDatas as $psychologyAnalisys)
                                                                @if(strpos(strtolower($psychologyAnalisys->name),strtolower($testPsychologyResult->result)) !== false)
                                                                    <p><span class="text-info">Deskripsi: </span>{!! ucfirst($psychologyAnalisys->description) !!}
                                                                    </p>
                                                                    <p><span class="text-info">Saran: </span>{!! ucfirst($psychologyAnalisys->recommendation) !!}
                                                                    </p>
                                                                    <p><span class="text-info">Profesi: </span><strong>{!! ucfirst($psychologyAnalisys->profession) !!}</strong></p>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <span>Belum ada data.</span>
                                            @endif
                                        </div>
                                    <!-- psychology test result -->
                                    <!-- competency -->
                                        <div class="row alert alert-warning m-0 small mb-1">
                                            <div class="col-md">
                                                <label class="text-uppercase">Tes competency
                                                    @if(count($testCompetencyResult) < 1)
                                                        <span class="text-danger">| Belum ada data</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="w-100"></div>

                                            @if(isset($testCompetencyResult))
                                                @foreach($testCompetencyResult as $testCompetencyResult)
                                                    <div class="col-md mt-1">
                                                        <strong>Tanggal tes</strong>
                                                        <br>{{ isset($testCompetencyResult->created_at) ? date('l, d F Y',strtotime($testCompetencyResult->created_at)) : '-' }}
                                                    </div>
                                                    <div class="col-md mt-1">
                                                        <strong>Hasil</strong>
                                                        <br>
                                                        @if($testCompetencyResult->result < 100)
                                                            <span class="text-uppercase">({{ $testCompetencyResult->result }}/100)</span> <span class="text-danger">Tidak lulus</span>
                                                        @else
                                                            <span class="text-success">({{ $testCompetencyResult->result }}/100)</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <span>Belum ada data.</span>
                                            @endif
                                        </div>
                                    <!-- competency end -->
                                    <!-- assessment test result -->
                                        <div class="row alert alert-warning m-0 small mb-1">
                                            <div class="col-md">
                                                <label class="text-uppercase">Tes assessment
                                                    @if(count($testAssessmentResult) < 1)
                                                        <span class="text-danger">| Belum ada data</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="w-100"></div>

                                            @if(isset($testAssessmentResult))
                                                @foreach($testAssessmentResult as $testAssessmentResult)
                                                    <div class="col-md mt-1">
                                                        <strong>Kategori</strong>
                                                        <br>{{ isset($testAssessmentResult->cat_name) ? strtoupper($testAssessmentResult->cat_name) : '-' }}
                                                    </div>
                                                    <div class="col-md mt-1">
                                                        <strong>Tanggal tes</strong>
                                                        <br>{{ isset($testAssessmentResult->created_at) ? date('l, d F Y, H:i A',strtotime($testAssessmentResult->created_at)) : '-' }}
                                                    </div>
                                                    <div class="col-md mt-1">
                                                        <strong>Hasil</strong>
                                                        <br>

                                                            {!! $testAssessmentResult->result < 100 ? "<span class='text-uppercase'>(".$testAssessmentResult->result."/100)</span> <span class='text-danger'>Tidak lulus</span>" : "<span class='text-success'>($testAssessmentResult->result/100)</span>" !!}
                                                    </div>
                                                    <div class="w-100"></div>
                                                @endforeach
                                            @else
                                                <span>Belum ada data.</span>
                                            @endif
                                        </div>
                                    <!-- assessment test result -->
                                    <!-- rating -->
                                        <div class="row alert alert-warning m-0 small mb-1">
                                            <div class="col-md">
                                                <label class="text-uppercase">Pemberi rating
                                                    @if(count($ratingDatas) < 1)
                                                        <span class="text-danger">| Belum ada data</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="w-100"></div>

                                            @if(isset($ratingDatas))
                                                @foreach($ratingDatas as $ratingData)
                                                    <div class="col-md mt-1">
                                                        <strong>Nama pemberi rating</strong>
                                                        <br>
                                                        @if($ratingData->giver_type == 'admin')
                                                            @foreach($admins as $userData)
                                                                @if($userData->id == $ratingData->giver_id)
                                                                    {{ ucwords($userData->firstname).' '.ucwords($userData->lastname)}}
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            @foreach($users as $userData)
                                                                @if($userData->id == $ratingData->giver_id)
                                                                    {{ ucwords($userData->firstname).' '.ucwords($userData->lastname)}}
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    <div class="col-md mt-1">
                                                        <strong>Departemen</strong>
                                                        <br>
                                                        @foreach($departmentDatas as $department)
                                                            @if($department->id == $ratingData->giver_department)
                                                                <span class="text-info">{{ ucwords($department->name) }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <div class="col-md mt-1">
                                                        <strong>Rating</strong>
                                                        <br>
                                                        @if($ratingData->one != 0)
                                                            @for ($ri = 0; $ri < 1; $ri++)
                                                                <span class="fa fa-star text-warning"></span>
                                                            @endfor
                                                        @elseif($ratingData->two != 0)
                                                            @for ($ri = 0; $ri < 2; $ri++)
                                                                <span class="fa fa-star text-warning"></span>
                                                            @endfor
                                                        @elseif($ratingData->three != 0)
                                                            @for ($ri = 0; $ri < 3; $ri++)
                                                                <span class="fa fa-star text-warning"></span>
                                                            @endfor
                                                        @elseif($ratingData->four != 0)
                                                            @for ($ri = 0; $ri < 4; $ri++)
                                                                <span class="fa fa-star text-warning"></span>
                                                            @endfor
                                                        @elseif($ratingData->five != 0)
                                                            @for ($ri = 0; $ri < 5; $ri++)
                                                                <span class="fa fa-star text-warning"></span>
                                                            @endfor
                                                        @else
                                                            <span class="text-danger">Belum rating</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md mt-1">
                                                        <strong>Tanggal</strong>
                                                        <br>{{ isset($ratingData->created_at) ? date('l, d F Y',strtotime($ratingData->created_at)) : '-' }}
                                                    </div>
                                                    <div class="w-100"></div>
                                                    <div class="col-md mt-1">
                                                        <strong>Note</strong>
                                                        <br>{!! isset($ratingData->giver_note) ? ucfirst($ratingData->giver_note) : '-' !!}
                                                    </div>
                                                    <div class="w-100"></div>
                                                @endforeach
                                            @else
                                                <span>Belum ada data.</span>
                                            @endif
                                            @if(isset($ratingGiverCount) && $ratingGiverCount < 1)
                                                <div class="w-100"><hr></div>
                                                <div class="col-md">
                                                    <button type="button" class="btn btn-orange mt-1" data-toggle="collapse" data-target="#custom_sort" aria-expanded="false" aria-controls="custom_sort">Berikan rating</button>

                                                    <div class="collapse" id="custom_sort">
                                                        <form action="{{ route($formTechRatingStore) }}" method="post" enctype="multipart/form-data">
                                                            @csrf

                                                            <!-- hidden data -->
                                                            <input type="hidden" name="tech_id" value="{{ $tech->id }}">
                                                            <div class="row bg-gray-lini-2">
                                                                <div class="col-md mt-2 form-group">
                                                                    <label for="">Rating </label>
                                                                    <select name="rating" class="form-control select2{{ $errors->has('rating') ? ' has-error' : '' }}" required>
                                                                        <?php
                                                                            if(old('rating') != null) {
                                                                                $rating = old('rating');
                                                                            }else{
                                                                                $rating = null;
                                                                            }
                                                                        ?>
                                                                        @if ($rating != null)
                                                                            <option value="{{ old('rating') }}">{{ ucwords(strtolower(old('rating'))) }}</option>
                                                                        @else
                                                                            <option value="">Pilih rating</option>
                                                                                <option value="1">1 bintang</option>
                                                                                <option value="2">2 bintang</option>
                                                                                <option value="3">3 bintang</option>
                                                                                <option value="4">4 bintang</option>
                                                                                <option value="5">5 bintang</option>
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                                <div class="w-100"></div>
                                                                <div class="col-md">
                                                                    <div class="form-group{{ $errors->has('giver_note') ? ' has-error' : '' }}">
                                                                        <label>Note <small class="c-red">*</small></label>
                                                                        <textarea name="giver_note" class="form-control" cols="10" rows="3" required>{{ old('giver_note') ?? old('giver_note') }}</textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="w-100"></div>
                                                                <div class="col-md">
                                                                    <div class="form-group">
                                                                        <label for=""></label>
                                                                        <input type="submit" class="btn btn-orange" name="submit" value="Rate" disabled>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    <!-- rating -->
                                </div>
                            </div>
                        </div>
                        <!-- summary end -->
                        <!-- personal data -->
                        <div class="tab-pane" id="account-2">
                            <div class="row">
                                <div class="col-12">
                                    @if(isset($techPersonalData))
                                        <!-- data tech -->
                                            <div class="row alert alert-warning m-0 small">
                                                <div class="col-md">
                                                    <strong>Nama panggilan</strong>
                                                    <br>{{ isset($tech->name) ? ucfirst($tech->name) : '-' }}
                                                </div>
                                                <div class="col-md">
                                                    <strong>Nama depan</strong>
                                                    <br>{{ isset($tech->firstname) ? ucwords($tech->firstname) : '-' }}
                                                </div>
                                                <div class="col-md">
                                                    <strong>Nama belakang</strong>
                                                    <br>{{ isset($tech->lastname) ? ucwords($tech->lastname) : '-' }}
                                                </div>
                                                <div class="col-md">
                                                    <strong>Nomor HP</strong>
                                                    <br>{{ isset($tech->mobile) ? $tech->mobile : '-' }}
                                                </div>
                                            </div>
                                            <div class="row alert alert-warning m-0 small">
                                                <div class="col-md-3">
                                                    <strong>Rekening</strong>
                                                    <br>{{ isset($tech->norek) ? ucwords($tech->norek) : '-' }}
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>Email</strong>
                                                    <br>{{ isset($tech->email) ? $tech->email : '-' }}
                                                </div>
                                                <div class="col-md">
                                                    <strong>Kota</strong>
                                                    <br>
                                                    {{ isset($techPersonalData->city) ? ucwords($techPersonalData->city_name).', ' : '' }}
                                                        
                                                        {{ isset($techPersonalData->province) ? ucwords($techPersonalData->province_name).'.' : '' }}
                                                </div>
                                            </div>
                                        <!-- data tech -->
                                        <hr>
                                        <!-- tech personal datas -->
                                            <div class="row alert small">
                                                <div class="col-md">
                                                    <strong>No KTP</strong>
                                                    <br>{{ isset($techPersonalData->ktp) ? $techPersonalData->ktp : '-' }}
                                                </div>
                                                <div class="col-md">
                                                    <strong>Emergency call</strong>
                                                    <br>{{ isset($techPersonalData->emergency_call) ? $techPersonalData->emergency_call : '-' }}
                                                </div>
                                                <div class="col-md">
                                                    <strong>Agama</strong>
                                                    <br>{{ isset($techPersonalData->religion) ? ucwords($techPersonalData->religion_name) : '-' }}
                                                </div>
                                            </div>
                                            <div class="row alert small">
                                                <div class="col-md">
                                                    <strong>Jenis kelamin</strong>
                                                    <br>{{ isset($techPersonalData->gender) ? ucwords($techPersonalData->gender_name) : '-' }}
                                                </div>
                                                <div class="col-md">
                                                    <strong>Status</strong>
                                                    <br>{{ isset($techPersonalData->marital_status) ? ucfirst($techPersonalData->marital_status_name) : '-' }}
                                                </div>
                                                <div class="col-md">
                                                    <strong>Tanggal lahir</strong>
                                                    <br>{{ isset($techPersonalData->date_of_birth) ? date('l, d F Y', strtotime($techPersonalData->date_of_birth)) : '-' }}
                                                </div>
                                            </div>
                                            <div class="row alert small">
                                                <div class="col-md">
                                                    <strong>No NPWP</strong>
                                                    <br>{{ isset($techPersonalData->npwp) ? $techPersonalData->npwp : '-' }}
                                                </div>
                                                <div class="col-md">
                                                    <strong>No BPJS kesehatan</strong>
                                                    <br>{{ isset($techPersonalData->bpjs_health) ? $techPersonalData->bpjs_health : '-' }}
                                                </div>
                                                <div class="col-md">
                                                    <strong>No BPJS ketenagakerjaan</strong>
                                                    <br>{{ isset($techPersonalData->bpjs_ketenagakerjaan) ? $techPersonalData->bpjs_ketenagakerjaan : '-' }}
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row alert small">
                                                <div class="col-md">
                                                    <strong>Alamat sesuai ktp</strong>
                                                    <br>{!! isset($techPersonalData->ktp_address) ? $techPersonalData->ktp_address : '-' !!}
                                                </div>
                                                <div class="col-md">
                                                    <strong>Alamat saat ini</strong>
                                                    <br>{!! isset($techPersonalData->current_address) ? $techPersonalData->current_address : '-' !!}
                                                </div>
                                            </div>
                                        <!-- tech personal datas end -->
                                        <hr>
                                        <!-- family datas -->
                                            <div class="row alert small">
                                                <div class="col-md">
                                                    <label class="text-info">Info keluarga</label>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col-md">
                                                    <strong>Suami/Istri</strong>
                                                    <br>{{ isset($techFamilyInfo->spouse) ? ucwords($techFamilyInfo->spouse) : '-' }} | {{ isset($techFamilyInfo->spouse_profession) ? ucwords($techFamilyInfo->spouse_profession) : '-' }}
                                                </div>
                                                <div class="col-md">
                                                    <strong>Ayah</strong>
                                                    <br>{{ isset($techFamilyInfo->father) ? ucwords($techFamilyInfo->father) : '-' }} | {{ isset($techFamilyInfo->father_profession) ? ucwords($techFamilyInfo->father_profession) : '-' }}
                                                </div>
                                                <div class="col-md">
                                                    <strong>Ibu</strong>
                                                    <br>{{ isset($techFamilyInfo->mother) ? ucwords($techFamilyInfo->mother) : '-' }} | {{ isset($techFamilyInfo->mother_profession) ? ucwords($techFamilyInfo->mother_profession) : '-' }}
                                                </div>
                                            </div>
                                        <!-- family datas end -->
                                        <hr>
                                        <!-- education datas -->
                                            <div class="row alert small">
                                                <div class="col-md">
                                                    <label class="text-info">Info pendidikan</label>
                                                </div>
                                                <div class="w-100"></div>
                                                @foreach($educationDatas as $data)
                                                    <div class="col-md-2">
                                                        <strong>Tingkat</strong>
                                                        <br><span>{{ isset($data->level_name) ? strtoupper($data->level_name) : '-' }}</span>
                                                    </div>
                                                    <div class="col-md">
                                                        <strong>Nama sekolah</strong>
                                                        <br><span>{{ isset($data->name) ? ucwords($data->name) : '' }}</span>
                                                    </div>
                                                    <div class="col-md">
                                                        <strong>Tahun</strong>
                                                        <br>{{ isset($data->year) ? ucwords($data->year) : '-' }}
                                                    </div>
                                                    <div class="col-md">
                                                        <strong>Kota</strong>
                                                        <br>{{ isset($data->city_name) ? ucwords($data->city_name) : '-' }}
                                                        {{ isset($data->province_name) ? "| ".ucwords($data->province_name) : '' }}
                                                    </div>
                                                    <div class="w-100"></div>
                                                @endforeach
                                            </div>
                                        <!-- education datas end -->
                                        <hr>
                                        <!-- document datas -->
                                            <div class="row alert small">
                                                <div class="col-md">
                                                    <label class="text-info">Dokumen pendukung</label>
                                                </div>
                                                <div class="w-100"></div>
                                                @foreach($documentDatas as $data)
                                                    <div class="col-md">
                                                        <strong><span>{{ isset($data->doc_name) ? strtoupper($data->doc_name) : '-' }}</span></strong>
                                                        <div class="float-right">
                                                            @if(isset($data->image))
                                                                <strong><a type="button" class="text-info" data-toggle="modal" data-target="#docModal{{ $data->id }}">Lihat dokumen </a></strong>
                                                            @endif
                                                        </div>
                                                    </div>

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
                                                    <div class="w-100"></div>
                                                @endforeach
                                            </div>
                                        <!-- document datas end -->
                                    @else
                                        <div class="alert alert-warning">Data belum tersedia.</div>
                                    @endif
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div>
                        <!-- personal data end -->
                        <!-- family info -->
                        <div class="tab-pane" id="keluarga-2">
                            <div class="row">
                                <div class="col-12">
                                    
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div>
                        <!-- family info end -->
                        <!-- education -->
                        <div class="tab-pane" id="pendidikan-2">
                            <div class="row">
                                <div class="col-12">
                                
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div>
                        <!-- education end -->
                        <!-- upload document -->
                        <div class="tab-pane" id="dokumen-2">
                            <div class="row">
                                <div class="col-12">
                                
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
                <li class="list-inline-item">
                    <a href="{{ route($formRouteIndex) }}" class="btn btn-blue-lini">Kembali</a>
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
<!-- Plugins js-->
<script src="assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>

<!-- Init js-->
<script src="assets/js/pages/form-wizard.init.js"></script>
@endsection
