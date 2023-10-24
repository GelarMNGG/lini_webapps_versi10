@if($videoTrainingViews < 1)
    @if(isset($videoTrainingDatas))
    <div class="row m-0 text-center">
        <img class="col-md img-thumbnail mb-1" data-toggle="modal" data-target="#corporateCulture4" class="img-responsive" src="{{ asset('video/corporate-culture/visi.png') }}" alt="video" onclick="playVid4()" />
        <img class="col-md img-thumbnail ml-1 mb-1" data-toggle="modal" data-target="#corporateCulture5" class="img-responsive" src="{{ asset('video/corporate-culture/trip.png') }}" alt="video" onclick="playVid5()" />
        <img class="col-md img-thumbnail ml-1 mb-1" data-toggle="modal" data-target="#corporateCulture6" class="img-responsive" src="{{ asset('video/corporate-culture/trip2.png') }}" alt="video" onclick="playVid6()" />
    </div>
    <!-- Training Modal 1 -->
    <div class="modal fade" id="corporateCulture4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" onclick="pauseVid4()">
        <div class="modal-dialog video-center" >
            <div class="modal-content">
                <button type="button" class="close close-img text-danger" data-dismiss="modal" onclick="pauseVid4()">X</button>
                <div class="embed-responsive embed-responsive-16by9">
                    <video id="corporateCultureVideo4" class="embed-responsive-item" controls="controls">
                        <source src="{{ asset('video/corporate-culture/visi-misi.mp4') }}" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>
    </div>
    <!-- Training Modal 2 -->
    <div class="modal fade" id="corporateCulture5" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" onclick="pauseVid5()">
        <div class="modal-dialog" role="document">
            <div class="modal-content video-center">
                <button type="button" class="close close-img text-danger" data-dismiss="modal" onclick="pauseVid5()">X</button>
                <div class="embed-responsive embed-responsive-16by9">
                    <video id="corporateCultureVideo5" class="embed-responsive-item" controls="controls" poster="{{ asset('video/corporate-culture/trip2.png') }}">
                        <source src="{{ asset('video/corporate-culture/trip-site.mp4') }}" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>
    </div>
    <!-- Training Modal 3 -->
    <div class="modal fade" id="corporateCulture6" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" onclick="pauseVid6()">
        <div class="modal-dialog" role="document">
            <div class="modal-content video-center">
                <button type="button" class="close close-img text-danger" data-dismiss="modal" onclick="pauseVid6()">X</button>
                <div class="embed-responsive embed-responsive-16by9">
                    <video id="corporateCultureVideo6" class="embed-responsive-item" controls="controls" poster="{{ asset('video/corporate-culture/trip2.png') }}">
                        <source src="{{ asset('video/corporate-culture/trip-site.mp4') }}" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 text-center p-3">
        <form action="{{ route($formProcVideoStore) }}" style="display:inline-block" method="POST">
            @csrf

            <!-- hidden data -->
            <input type="text" name="video_type" value="2" hidden>
            <input type="text" name="status" value="1" hidden>

            <button type="submit" class="btn btn-orange"> Lanjut <strong>tes</strong> paska training!</button>  
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