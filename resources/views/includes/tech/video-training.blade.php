@if($testAssessmentResult < $testTrainingCatCount)
    @if(isset($videoTrainingDatas))
        <div class="row m-0">
            <?php $i = 0; ?>
            @foreach($videoTrainingCategories as $videoTrainingCatData)
                <?php
                    if ($i == 0) {
                        $step = '';
                    } else{
                        $step = 'step-'.$i;
                    }
                    $i++;
                ?>
                @if($videoTrainingCatData->test_count < 1)
                    <?php
                        //category count
                        $catCount = count($videoTrainingCategories);
                        $testCatCount = $videoTrainingCatData->test_catcount;
                        $currentCat = $testCatCount - $catCount;
                        //default iteration
                        $i2 = $i - 1;
                        if ($i2 == 0) {
                            $testCount = 0;
                        }else{
                            $testCount = $videoTrainingCatData->test_count;
                        }
                    ?>
                    @if($testCount < 1 && $i == $testCatCount + 1)
                        <div id="{{ $step }}" class="col-md-12 mb-3">
                            <div class="card card-1">
                                <div class="card-header card-1-header">
                                    <span class="header-number step-1">{{ $i }}</span>
                                    <span class="header-title">{{ ucfirst($videoTrainingCatData->name) }}</span>
                                </div>
                                <div class="card-body">
                                    @if($videoTrainingCatData->video_viewcount > 0)
                                        <div class="alert alert-danger small">Video tidak tersedia/Anda telah menyaksikan video materi training. Silahkan melanjutkan dengan klik tombol tes dibawah ini.</div>
                                    @else
                                        @if(isset($videoTrainingCatData->video))
                                            <button type="button" class="btn video-btn" data-toggle="modal" data-src="{{ asset('video/training/'.$videoTrainingCatData->video) }}" data-target="#myModal">
                                                <img class="col-md img-thumbnail mb-1" class="img-responsive" src="{{ asset('video/training/'.$videoTrainingCatData->thumbnail) }}"/>
                                            </button>
                                        @else
                                            <div class="alert alert-warning">Video belum tersedia.</div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="alert alert-warning small">
                                <span>Jika Anda sudah memahami materi yang disampaikan melalui video di atas, silahkan melanjutkan dan mengikuti tes dengan klik tombol tes <strong>{{ ucfirst($videoTrainingCatData->name) }}</strong> di bawah ini. </span> 
                                <span class="text-danger"><span class="text-uppercase">Peringatan</span>: Anda tidak dapat melihat materi lagi, setelah Anda klik tombol tes ini.</span>
                            </div>
                            <a href="{{ route($formRouteTestByCategoryCreate,'cid='.$videoTrainingCatData->id) }}" type="button" class="btn btn-danger text-uppercase"><small><strong>tes {{ ucfirst($videoTrainingCatData->name) }}</strong></small></a>
                        </div>
                    @else
                        <div class="col-md-12">
                            <div class="card card-1">
                                <div class="card-header card-1-header">
                                    <span class="header-number-nonaktif step-1">{{ $i }}</span>
                                    <span class="header-title-nonaktif">{{ ucfirst($videoTrainingCatData->name) }}</span>
                                    @if($videoTrainingCatData->test_result == 100)
                                        <span class="test-value text-success float-right">{{ ucfirst($videoTrainingCatData->test_result) }}</span>
                                    @else
                                        <span class="test-value text-danger float-right">{{ ucfirst($videoTrainingCatData->test_result) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="col-md-12">
                        <div class="card card-1">
                            <div class="card-header card-1-header">
                                <span class="header-number-nonaktif step-1">{{ $i }}</span>
                                <span class="header-title-nonaktif">{{ ucfirst($videoTrainingCatData->name) }}</span>
                                @if($videoTrainingCatData->test_result == 100)
                                    <span class="test-value text-success float-right">{{ ucfirst($videoTrainingCatData->test_result) }}</span>
                                @else
                                    <span class="test-value text-danger float-right">{{ ucfirst($videoTrainingCatData->test_result) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

        </div>
        
        <?php /*
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-1">
                        <div class="card-header card-1-header">
                            <span class="header-number step-1">1</span>
                            <span class="header-title">Mau kirim kemana?</span>
                        </div>
                        <div class="card-body">
                        <button type="button" class="btn video-btn" data-toggle="modal" data-src="{{ asset('video/corporate-culture/trip-site.mp4') }}" data-target="#myModal">
                            <img class="col-md img-thumbnail mb-1" class="img-responsive" src="{{ asset('video/corporate-culture/visi.png') }}"/>
                        </button>

                        </div>
                    </div>
                </div>

                <div id="second-step" class="col-md-12">
                
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <div class="card card-1">
                                <div class="card-header card-1-header">
                                    <span class="header-number-nonaktif step-1">2</span>
                                    <span class="header-title-nonaktif">Apa yang Anda kirim?</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="card card-1">
                                <div class="card-header card-1-header">
                                    <span class="header-number-nonaktif step-1">3</span>
                                    <span class="header-title-nonaktif">Detil tambahan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="second-step-contents" class="col-md" style="display:none">
                    <div class="card card-1">
                        <div class="card-header card-1-header">
                            <span class="header-number step-1">2</span>
                            <span class="header-title">Apa yang Anda kirim?</span>
                        </div>
                        <div class="card-body">
                            <div class="row text-center justify-content-center">
                                
                            </div>
                        </div>
                    </div>
                </div>

                <div id="third-step" class="col-md-12" style="display:none">
                    <button type="button" class="btn btn-warning text-uppercase"><small>Selanjutnya</small></button>
                    <div class="row">
                        <div class="col-md-12 mt-3 mb-3">
                            <div class="card card-1">
                                <div class="card-header card-1-header">
                                    <span class="header-number-nonaktif step-1">3</span>
                                    <span class="header-title-nonaktif">Detil tambahan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="third-step-contents" class="col-md" style="display:none">
                    <div class="card card-1">
                        <div class="card-header card-1-header">
                            <span class="header-number step-1">3</span>
                            <span class="header-title">Detil tambahan</span>
                        </div>
                        <div class="card-body">
                            @php $dti = 0 @endphp
                        </div>
                    </div>
                </div>
            </div>
        */ ?>

        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <!-- 16:9 aspect ratio -->
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="" id="video"
                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="custom-control custom-checkbox alert alert-warning text-center">
            <p class="w-75 mb-2 mx-auto">Belum ada data.</p>
            <p><strong>Silahkan coba beberapa saat lagi.</strong></p>
        </div>
    @endif
@else
    <?php $i = 1; ?>
    @foreach($videoTrainingCategories as $videoTrainingCatData)
        <div class="col-md-12">
            <div class="card card-1">
                <div class="card-header card-1-header">
                    <span class="header-number-nonaktif step-1">{{ $i }}</span>
                    <span class="header-title-nonaktif">{{ ucfirst($videoTrainingCatData->name) }}</span>
                    @if($videoTrainingCatData->test_result == 100)
                        <span class="test-value text-success float-right">{{ ucfirst($videoTrainingCatData->test_result) }}</span>
                    @else
                        <span class="test-value text-danger float-right">{{ ucfirst($videoTrainingCatData->test_result) }}</span>
                    @endif
                </div>
            </div>
        </div>
    <?php $i++; ?>
    @endforeach
    <hr>
    <div class="custom-control custom-checkbox alert alert-warning text-center">
        <p class="w-75 mb-2 mx-auto">Anda telah menyelesaikan proses ini.</p>
        <p><strong>Silahkan melanjutkan ke proses selanjutnya.</strong></p>
    </div>
@endif