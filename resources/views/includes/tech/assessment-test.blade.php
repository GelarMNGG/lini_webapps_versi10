@if($testAssessmentResult > 0)
    <div class="mb-3 text-center">
        <div class="custom-control custom-checkbox alert alert-warning">
            <p class="w-75 mb-2 mx-auto">Selamat, Anda telah berhasil menyelesaikan tes asesmen dengan baik.</p>
            <p><strong>Tim Lini akan segera menghubungi Anda!</strong></p>
        </div>
    </div>
@else
    <div class="mb-3 text-center">
        <div class="custom-control custom-checkbox alert alert-warning">
            <p class="w-75 mb-2 mx-auto">Pilih jawaban dengan mengeklik salah satu jawaban yang tersedia. Klik Kirim jika Anda telah selesai mengisi semua pertanyaan.</p>
            <p><strong>Selamat mengerjakan!</strong></p>
        </div>
    </div>
    <div class="col-md-12">
        <form class="w-100" action="{{ route($formAssessmentTestStore) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- hidden data -->
            <input type="hidden" name="question_count" value="{{ count($testAssessmentDatas) }}">
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
@endif