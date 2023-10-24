@extends('layouts.dashboard-form-wizard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'test & training';
    $formRouteIndex = 'tech-test-training.index';

    //assessment test route
    $formAssessmentTestStore = "tech-test-training-category.store";

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
            <br><span class="text-success"><strong>{{ $categoryDatas->name }} </strong></span>
        </div>

        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-xl-12">
                    @if($testAssessmentResult > 0)
                        <div class="mb-3 text-center">
                            <div class="custom-control custom-checkbox alert alert-warning">
                                <p class="w-75 mb-2 mx-auto">Selamat, Anda telah berhasil menyelesaikan tes asesmen dengan baik.</p>
                                <p><strong>Tim Lini akan segera menghubungi Anda!</strong></p>
                            </div>
                        </div>
                    @else
                        @if($categoryDatas->sort_order == 1)
                            <div class="mb-3 text-center">
                                <div class="custom-control custom-checkbox alert alert-warning small">
                                    <p class="w-75 mb-2 mx-auto">Pilih jawaban dengan mengeklik salah satu jawaban yang tersedia. Klik Kirim jika Anda telah selesai mengisi semua pertanyaan.</p>
                                    <p><strong>Selamat mengerjakan!</strong></p>
                                </div>
                            </div>
                        @endif
                        @if(count($testAssessmentDatas) > 0)
                            <div class="col-md-12">
                                <form class="w-100" action="{{ route($formAssessmentTestStore) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <!-- hidden data -->
                                    <input type="hidden" name="question_count" value="{{ count($testAssessmentDatas) }}">
                                    <input type="hidden" name="question_cat" value="{{ $categoryDatas->id }}">
                                    <!-- assessment question start -->
                                    @foreach($testAssessmentDatas as $dataAssessment)
                                        <div class="bg-card-box br-5 p-2 mb-2">
                                            <h4>{{ isset($dataAssessment->question) ? ucfirst($dataAssessment->question) : 'Belum ada data' }}</h4>
                                            <div class="row">
                                                @foreach($testAssessmentChoicesDatas as $testChoicesData)
                                                    @if($testChoicesData->question_id == $dataAssessment->id)
                                                        <div class="col-md">
                                                            <input type="radio" id="" name="{{ $dataAssessment->id }}" value="a">
                                                            <small>{{ isset($testChoicesData->answer_a) ? ucfirst($testChoicesData->answer_a) : '-' }}</small>
                                                        </div>
                                                        <div class="col-md">
                                                            <input type="radio" id="" name="{{ $dataAssessment->id }}" value="b">
                                                            <small>{{ isset($testChoicesData->answer_b) ? ucfirst($testChoicesData->answer_b) : '-' }}</small>
                                                        </div>
                                                        @if(isset($testChoicesData->answer_c))
                                                        <div class="w-100"></div>
                                                        <div class="col-md">
                                                            <input type="radio" id="" name="{{ $dataAssessment->id }}" value="c">
                                                            <small>{{ ucfirst($testChoicesData->answer_c) }}</small>
                                                        </div>
                                                        @endif
                                                        @if(isset($testChoicesData->answer_d))
                                                            <div class="col-md">
                                                                <input type="radio" id="" name="{{ $dataAssessment->id }}" value="d">
                                                                <small>{{ ucfirst($testChoicesData->answer_d) }}</small>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                    <!-- assessment question end -->
                                    <div class="w-100"></div>
                                    <div class="col-md">
                                        <button type="submit" class="btn btn-orange" name="submit">Kirim</button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-danger">Data belum tersedia. Cobalah beberapa saat lagi.</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(count($testAssessmentDatas) < 1)
                <a class="btn btn-blue-lini" href="{{ route($formRouteIndex) }}">Kembali</a>
            @endif
        </div>
    </div>
</div> <!-- card -->

@endsection

@section ('script')

@endsection
