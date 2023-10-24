@extends('layouts.dashboard-form-wizard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'test & training';
    $formRouteIndex = 'tech-test-training.index';
    $formRouteStore = 'tech-test-training.store';
    $formRouteUpdate = 'tech-test-training.update';

    //psychology test route
    $formPsychologyTestStore = "tech-psychology-test.store";
    
    //competency test route
    $formCompetencyTestStore = "tech-competency-test.store";

    //video corporate culture
    $formProcVideoStore = "tech-proc-video.store";

    //assessment test route
    $formAssessmentTestStore = "tech-assessment-test.store";

    //test training by category
    $formRouteTestByCategoryCreate = 'tech-test-training-category.create';

    //logout
    $formLogout = "tech.logout";

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
                $tab2css = 'disabled'; 
                $tab3css = 'disabled'; 
                $tab4css = 'disabled'; 
                $finishCSS = 'disabled'; 
                $finishStatus = '';
                $progressCount = 0;
                $totalCount = 4;

                //psychology result
                if (isset($testPsychologyResult) && strtoupper($testPsychologyResult->result) == 'INTJ') {
                    $psychologyPass = 1; //pass
                }else{
                    $psychologyPass = 0;
                }
                //competency result
                if (isset($testCompetencyResult) && $testCompetencyResult->result == 100) {
                    $competencyPass = 1; //pass
                }else{
                    $competencyPass = 0;
                }
                //super pass
                if (isset($testPsychologyResult) && $testPsychologyResult->status == 1) {
                    $superPass = 1; //pass
                }else{
                    $superPass = 0;
                }
                ///set psychologyPass value and compentencypas value to 1
                ///revised on jun 30
                $psychologyPass = 1;
                $competencyPass = 1;
                ///revised end

                if (isset($testPsychologyResult)) {
                    $tab2css = 'tab';
                    $progressCount = '1';
                }
                if (isset($testCompetencyResult)) {
                    $tab2acss = 'tab';
                    $progressCount = '2';
                }
                if (isset($videoCorpCultureDatas) && $videoCorpCultureCount > 0 && isset($testPsychologyResult) && ($competencyPass > 0 && $psychologyPass > 0 || $superPass > 0)) {
                    $tab3css = 'tab';
                    $progressCount = '3';
                }

                if ($testAssessmentResult == $testTrainingCatCount) {
                    $finishCSS = 'tab';
                    $finishStatus = ' active';
                    $progressCount = '4';
                }
            ?>
            <strong>{{ ucfirst($pageTitle) }}</strong>
            @if($progressCount == 5)
                <br><span class="text-success"><strong>[{{ $progressCount }}/{{ $totalCount }}] - Done</strong></span>
            @else
                <br><span class="text-danger"><strong>[{{ $progressCount }}/{{ $totalCount }}]</strong></span>
            @endif
        </div>

        <div class="card-body bg-gray-lini-2">
            <div class="row m-0">
                <div class="col-xl-12">
                    <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-1 small">
                        @if($testAssessmentResult == $testTrainingCatCount)
                            <li class="nav-item">
                                <a href="#finish-2" data-toggle="{{ $finishCSS }}" class="nav-link rounded-0 pt-2 pb-2{{ $finishStatus }}">
                                    <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                    <span class="d-none d-sm-inline">Finish</span>
                                    <br>
                                </a>
                            </li>
                        @endif
                        @if(isset($videoTrainingDatas) && $videoCorpCultureViews > 0)
                            <li class="nav-item">
                                <a href="#tab-tiga-2" data-toggle="{{ $tab3css }}" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-face-profile mr-1"></i>
                                    <span class="d-none d-sm-inline">Training</span>
                                    <br>
                                    @if(isset($videoCorpCultureDatas) && $videoTrainingViews == $testTrainingCatCount)
                                        <span class="mt-0 text-success"><i class="mdi mdi-check-all"></i></span>
                                    @else
                                        <span class="text-danger"><strong>({{ $progressCount }}/{{ $totalCount }})</strong></span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        @if(isset($videoCorpCultureDatas) && $videoCorpCultureCount > 0 && isset($testCompetencyResult) && ($competencyPass > 0 && $psychologyPass > 0 || $superPass > 0))
                            <li class="nav-item">
                                <a href="#tab-dua-2" data-toggle="{{ $tab2acss }}" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-face-profile mr-1"></i>
                                    <span class="d-none d-sm-inline">Visi Misi LINI</span>
                                    <br>
                                    @if(isset($videoCorpCultureDatas) && $videoCorpCultureCount > 0)
                                        <span class="mt-0 text-success"><i class="mdi mdi-check-all"></i></span>
                                    @else
                                        <span class="text-danger"><strong>({{ $progressCount }}/{{ $totalCount }})</strong></span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        @if(isset($testPsychologyResult))
                            <li class="nav-item">
                                <a href="#competency-2" data-toggle="{{ $tab2css }}" class="nav-link rounded-0 pt-2 pb-2">
                                    <i class="mdi mdi-account-circle mr-1"></i>
                                    <span class="d-none d-sm-inline">Tes kompetensi </span>
                                    <br>
                                    @if(isset($testCompetencyResult))
                                        <span class="mt-0 text-success"><i class="mdi mdi-check-all"></i></span>
                                    @else
                                        <span class="text-danger"><strong>({{ $progressCount }}/{{ $totalCount }})</strong></span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="#account-2" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                <i class="mdi mdi-account-circle mr-1"></i>
                                <span class="d-none d-sm-inline">Tes psikologi </span>
                                <br>
                                @if(isset($testPsychologyResult))
                                    <span class="mt-0 text-success"><i class="mdi mdi-check-all"></i></span>
                                @else
                                    <span class="text-danger"><strong>({{ $progressCount }}/{{ $totalCount }})</strong></span>
                                @endif
                            </a>
                        </li>
                    </ul>
                
                    <div class="tab-content border-0 mb-0 plr-0">
                
                        <div id="bar" class="progress mb-3">
                            <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:{{ ($progressCount/$totalCount) * 100 }}%">
                            {{ ($progressCount/$totalCount) * 100 }}% Selesai (success)
                            </div>
                        </div>

                        @if($testAssessmentResult == $testTrainingCatCount)
                            <div class="tab-pane active" id="finish-2">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="text-center">
                                            <h2 class="mt-0"><i class="mdi mdi-check-all"></i></h2>
                                            <h3 class="mt-0">Selamat!</h3>

                                            <p class="w-75 mb-2 mx-auto">Anda telah berhasil menyelesaikan rangkaian seleksi calon teknisi PT Lima Inti Sinergi dengan baik.</p>

                                            <div class="mb-3">
                                                <div class="custom-control custom-checkbox">
                                                    <a href="#" class="btn btn-orange">Tim Lini akan segera menghubungi Anda</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->
                            </div>
                        @endif
                        <!-- tab-1 -->
                        <div class="tab-pane" id="account-2">
                            <div class="row">
                                <div class="col-12">
                                    @include('includes.tech.psychology-test')
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div>
                        <!-- tab-1 end -->
                        <!-- tab-1a -->
                        <div class="tab-pane" id="competency-2">
                            <div class="row">
                                <div class="col-12">
                                    @include('includes.tech.competency-test')
                                </div> <!-- end col -->
                            </div> <!-- end row -->
                        </div>
                        <!-- tab-1a end -->
                        <!-- tab-2 -->
                        <div class="tab-pane" id="tab-dua-2">
                            <div class="row">
                                <div class="col-12">
                                    @include('includes.tech.video-corporate')
                                </div>
                            </div> <!-- end row -->
                        </div>
                        <!-- tab-2 end -->
                        <!-- tab-3 -->
                        <div class="tab-pane" id="tab-tiga-2">
                            @include('includes.tech.video-training')
                        </div>
                        <!-- tab-3 end -->
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
<script type=text/javascript>

    $(document).ready(function() {
        // Gets the video src from the data-src on each button
        var $videoSrc;
        $(".video-btn").click(function() {
            $videoSrc = $(this).attr("data-src");
            console.log("button clicked" + $videoSrc);
        });

        // when the modal is opened autoplay it
        $("#myModal").on("shown.bs.modal", function(e) {
            console.log("modal opened" + $videoSrc);
            $("#video").attr(
            "src",
            //$videoSrc + "?autoplay=1&showinfo=0&modestbranding=1&rel=0&mute=1"
            $videoSrc + "?autoplay=1&mute=1"
            );
        });

        // stop playing the video when close the modal
        $("#myModal").on("hide.bs.modal", function(e) {
            $("#myModal iframe").attr("src", $("#myModal iframe").attr("src", $videoSrc));
        });
    });

    $(function() {
        $(this).bind("contextmenu", function(e) {
            e.preventDefault();
        });
    });

    function killCopy(e){ return false } 
    function reEnable(){ return true } 
    document.onselectstart=new Function ("return false"); 
    if (window.sidebar)
    { 
        document.onmousedown=killCopy; 
        document.onclick=reEnable; 
    }
    if (window.history && window.history.pushState) {
        window.history.pushState('forward', null, './#forward');
    }
</script>
@endsection
