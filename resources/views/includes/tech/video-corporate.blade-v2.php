@if($videoCorpCultureViews < 1)
    @if(isset($videoCorpCultureDatas))
        <?php /*
        <div class="row m-0 text-center">
            <?php $ic = 1; ?>
            @foreach($videoCorpCultureDatas as $videoCorpCultureData)
                @if($ic == 1)
                    <img class="col-md img-thumbnail mb-1" data-toggle="modal" data-target="#corporateCulture" class="img-responsive" src="{{ asset('video/corporate-culture/'.$videoCorpCultureData->thumbnail) }}" alt="video" onclick="playVid()" />

                    <!-- Corporate Culture Modal 1 -->
                    <div class="modal fade" id="corporateCulture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" onclick="pauseVid()">
                        <div class="modal-dialog" >
                            <div class="modal-content">
                                <button type="button" class="close close-img" data-dismiss="modal" onclick="pauseVid()">X</button>
                                <div class="embed-responsive embed-responsive-16by9">
                                    <video id="corporateCultureVideo" class="embed-responsive-item" controls="controls">
                                        <source src="{{ asset('video/corporate-culture/'.$videoCorpCultureData->video) }}" type="video/mp4">
                                    </video>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <img class="col-md img-thumbnail mb-1" data-toggle="modal" data-target="#corporateCulture{{ $ic }}" class="img-responsive" src="{{ asset('video/corporate-culture/'.$videoCorpCultureData->thumbnail) }}" alt="video" onclick="{{ $ic }}playVid()" />

                    <!-- Corporate Culture Modal 1 -->
                    <div class="modal fade" id="corporateCulture{{ $ic }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" onclick="{{ $ic }}pauseVid()">
                        <div class="modal-dialog" >
                            <div class="modal-content">
                                <button type="button" class="close close-img" data-dismiss="modal" onclick="{{ $ic }}pauseVid()">X</button>
                                <div class="embed-responsive embed-responsive-16by9">
                                    <video id="corporateCultureVideo" class="embed-responsive-item" controls="controls">
                                        <source src="{{ asset('video/corporate-culture/'.$videoCorpCultureData->video) }}" type="video/mp4">
                                    </video>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <?php $ic++; ?>
            @endforeach
        </div>
        */ ?>

        <div class="row m-0 text-center">
            <img class="col-md img-thumbnail mb-1" data-toggle="modal" data-target="#corporateCulture" class="img-responsive" src="{{ asset('video/corporate-culture/visi.png') }}" alt="video" onclick="playVid()" />
            <img class="col-md img-thumbnail ml-1 mb-1" data-toggle="modal" data-target="#corporateCulture2" class="img-responsive" src="{{ asset('video/corporate-culture/trip.png') }}" alt="video" onclick="playVid2()" />
            <img class="col-md img-thumbnail ml-1 mb-1" data-toggle="modal" data-target="#corporateCulture3" class="img-responsive" src="{{ asset('video/corporate-culture/benefit-teknisi-oss-thumbnail.png') }}" alt="video" onclick="playVid3()" />
        </div>
        <!-- Corporate Culture Modal 1 -->
        <div class="modal fade" id="corporateCulture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" onclick="pauseVid()">
            <div class="modal-dialog" >
                <div class="modal-content video-center">
                    <button type="button" class="close close-img text-danger" data-dismiss="modal" onclick="pauseVid()">X</button>
                    <div class="embed-responsive embed-responsive-16by9">
                        <video id="corporateCultureVideo" class="embed-responsive-item" controls="controls">
                            <source src="{{ asset('video/corporate-culture/visi-misi.mp4') }}" type="video/mp4">
                        </video>
                    </div>
                </div>
            </div>
        </div>
        <!-- Corporate Culture Modal 2 -->
        <div class="modal fade" id="corporateCulture2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" onclick="pauseVid2()">
            <div class="modal-dialog" role="document">
                <div class="modal-content video-center">
                    <button type="button" class="close close-img text-danger" data-dismiss="modal" onclick="pauseVid2()">X</button>
                    <div class="embed-responsive embed-responsive-16by9">
                        <video id="corporateCultureVideo2" class="embed-responsive-item" controls="controls" poster="{{ asset('video/corporate-culture/trip.png') }}">
                            <source src="{{ asset('video/corporate-culture/trip-site.mp4') }}" type="video/mp4">
                        </video>
                    </div>
                </div>
            </div>
        </div>
        <!-- Corporate Culture Modal 3 -->
        <div class="modal fade" id="corporateCulture3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" onclick="pauseVid3()">
            <div class="modal-dialog" role="document">
                <div class="modal-content video-center">
                    <button type="button" class="close close-img text-danger" data-dismiss="modal" onclick="pauseVid3()">X</button>
                    <div class="embed-responsive embed-responsive-16by9">
                        <video id="corporateCultureVideo3" class="embed-responsive-item" controls="controls" poster="{{ asset('video/corporate-culture/benefit-teknisi-oss-thumbnail.png') }}">
                            <source src="{{ asset('video/corporate-culture/benefit-teknisi-oss.mp4') }}" type="video/mp4">
                        </video>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 text-center p-3">
            <form action="{{ route($formProcVideoStore) }}" style="display:inline-block" method="POST">
                @csrf

                <!-- hidden data -->
                <input type="text" name="video_type" value="1" hidden>
                <input type="text" name="status" value="1" hidden>

                <button type="submit" class="btn btn-orange"> Apakah Anda ingin lanjut ke proses berikutnya?</button>  
            </form>
            <form action="{{ route($formLogout) }}" style="display:inline-block" method="POST">
                @csrf
                <!-- hidden data -->
                <input type="hidden" name="tech_id" value="{{ Auth::user()->id }}">
                <input type="hidden" name="active" value="0">

                <button type="submit" class="btn btn-blue-lini"> Tidak</button>  
            </form>
        </div>

    @else
        <div class="custom-control custom-checkbox alert alert-warning text-center">
            <p class="w-75 mb-2 mx-auto">Belum ada data.</p>
            <p>Silahkan coba beberapa saat lagi.</p>
        </div>
    @endif
@else
    <div class="custom-control custom-checkbox alert alert-warning text-center">
        <p class="w-75 mb-2 mx-auto">Anda telah menyelesaikan proses ini.</p>
        <p>Silahkan melanjutkan ke proses selanjutnya.</p>
    </div>
@endif
<?php /*
    <div class="row m-0 text-center">
        <img class="col-md img-thumbnail mb-1" data-toggle="modal" data-target="#corporateCulture" class="img-responsive" src="{{ asset('video/corporate-culture/visi.png') }}" alt="video" onclick="playVid()" />
        <img class="col-md img-thumbnail ml-1 mb-1" data-toggle="modal" data-target="#corporateCulture2" class="img-responsive" src="{{ asset('video/corporate-culture/trip.png') }}" alt="video" onclick="playVid2()" />
        <img class="col-md img-thumbnail ml-1 mb-1" data-toggle="modal" data-target="#corporateCulture3" class="img-responsive" src="{{ asset('video/corporate-culture/trip2.png') }}" alt="video" onclick="playVid3()" />
    </div>
    <!-- Corporate Culture Modal 1 -->
    <div class="modal fade" id="corporateCulture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" onclick="pauseVid()">
        <div class="modal-dialog" >
            <div class="modal-content">
                <button type="button" class="close close-img" data-dismiss="modal" onclick="pauseVid()">X</button>
                <div class="embed-responsive embed-responsive-16by9">
                    <video id="corporateCultureVideo" class="embed-responsive-item" controls="controls">
                        <source src="{{ asset('video/corporate-culture/visi-misi.mp4') }}" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>
    </div>
    <!-- Corporate Culture Modal 2 -->
    <div class="modal fade" id="corporateCulture2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" onclick="pauseVid2()">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <button type="button" class="close close-img text-danger" data-dismiss="modal" onclick="pauseVid2()">X</button>
                <div class="embed-responsive embed-responsive-16by9">
                    <video id="corporateCultureVideo2" class="embed-responsive-item" controls="controls" poster="{{ asset('video/corporate-culture/trip2.png') }}">
                        <source src="{{ asset('video/corporate-culture/trip-site.mp4') }}" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>
    </div>
    <!-- Corporate Culture Modal 3 -->
    <div class="modal fade" id="corporateCulture3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" onclick="pauseVid3()">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <button type="button" class="close close-img" data-dismiss="modal" onclick="pauseVid3()">X</button>
                <div class="embed-responsive embed-responsive-16by9">
                    <video id="corporateCultureVideo2" class="embed-responsive-item" controls="controls" poster="{{ asset('video/corporate-culture/trip2.png') }}">
                        <source src="{{ asset('video/corporate-culture/trip-site.mp4') }}" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>
    </div>
*/ ?>