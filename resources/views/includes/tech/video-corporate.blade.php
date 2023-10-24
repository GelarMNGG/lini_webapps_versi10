@if($videoCorpCultureViews < 1)
    @if(isset($videoCorpCultureDatas))
        <div class="row m-0 text-center">
            <?php $vc = 0; ?>
            @foreach($videoCorpCultureDatas as $videoCorpCultureData)
                <div class="col-md">
                    <button type="button" class="btn video-btn" data-toggle="modal" data-src="{{ asset('video/corporate-culture/'.$videoCorpCultureData->video) }}" data-target="#myModal">
                        <img class="col-md img-thumbnail mb-1" class="img-responsive" src="{{ asset('video/corporate-culture/'.$videoCorpCultureData->thumbnail) }}"/>
                    </button>
                </div>
                <?php 
                    $vc++;
                    if ($vc % 3 == 0) {
                        echo "<div class='w-100'></div>";
                    }
                ?>
            @endforeach
        </div>

        <div class="col-md-12 text-center p-3">
            <form action="{{ route($formProcVideoStore) }}" style="display:inline-block" method="POST">
                @csrf
                <!-- hidden data -->
                <input type="text" name="video_type" value="1" hidden>
                <input type="text" name="video_cat" value="1" hidden>
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
    <div class="custom-control custom-checkbox alert alert-warning text-center">
        <p class="w-75 mb-2 mx-auto">Anda telah menyelesaikan proses ini.</p>
        <p><strong>Silahkan melanjutkan ke proses selanjutnya.</strong></p>
    </div>
@endif