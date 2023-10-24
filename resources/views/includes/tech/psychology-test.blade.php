@if(isset($testPsychologyResult))
    <div class="col-md mb-3 text-center">
        <div class="custom-control custom-checkbox alert alert-warning">
            <p class="w-75 mb-2 mx-auto">Selamat, Anda telah berhasil menyelesaikan tes psikologi dengan baik.</p>
            <p><strong>Silahkan melanjutkan ke proses selanjutnya.</strong></p>
        </div>
    </div>
@else
    <div class="row">
        <div class="mb-3 text-center">
            <div class="custom-control custom-checkbox alert alert-warning">
                <p class="mb-2 mx-auto">Tidak ada jawaban benar dan salah dalam setiap pertanyaan pada tes psikologi berikut. Jawab dengan jawaban yang sebenar-benarnya dan paling sesuai dengan diri Anda.</p>
                <p>-</p>
                <p>Pilih jawaban dengan klik salah satu jawaban yang tersedia. Dan klik <strong>Kirim</strong> jika Anda telah selesai mengisi semua pertanyaan.</p>
                <p><strong>Selamat mengerjakan!</strong></p>
            </div>
        </div>
    </div>

    <div class="row">
        <form class="w-100" action="{{ route($formPsychologyTestStore) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- psychology question -->
            @foreach($testPsychologyDatas as $dataPsychology)
                <div class="bg-card-box br-5 p-2 mb-2">
                    <h4>{{ isset($dataPsychology->question) ? ucfirst($dataPsychology->question) : 'Belum ada data' }}</h4>
                    <div class="row">
                        @foreach($testPsychologyChoicesDatas as $testChoicesData)
                            @if($testChoicesData->question_id == $dataPsychology->id)
                                <div class="col-md">
                                    <input type="radio" id="" name="{{ $dataPsychology->id }}" value="a">
                                    <small>{{ isset($testChoicesData->answer_a) ? ucfirst($testChoicesData->answer_a) : '-' }}</small>
                                </div>
                                <div class="col-md">
                                    <input type="radio" id="" name="{{ $dataPsychology->id }}" value="b">
                                    <small>{{ isset($testChoicesData->answer_b) ? ucfirst($testChoicesData->answer_b) : '-' }}</small>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
            <!-- psychology question end -->
            <div class="w-100"></div>
            <div class="w-100">
                <button type="submit" class="btn btn-orange" name="submit">Kirim</button>
            </div>
        </form>
    </div>
@endif