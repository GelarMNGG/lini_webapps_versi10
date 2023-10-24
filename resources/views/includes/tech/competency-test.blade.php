@if(isset($testCompetencyResult))
    <div class="col-md mb-3 text-center">
        <div class="custom-control custom-checkbox alert alert-warning">
            @if($competencyPass > 0 && $psychologyPass > 0 || $superPass > 0)
                <p class="w-75 mb-2 mx-auto">Selamat, Anda telah berhasil menyelesaikan tes kompetensi dengan baik.</p>
                <p><strong>Silahkan melanjutkan ke proses selanjutnya.</strong></p>
            @else
                <p class="w-75 mb-2 mx-auto">Selamat, Anda telah berhasil menyelesaikan tes psikologi dan tes kompetensi dengan baik.</p>
                <p><strong>Tim LINI sedang mereview hasil tes Anda.</strong></p>
            @endif
        </div>
    </div>
@else
    <div class="col-md">
        <div class="mb-3 text-center">
            <div class="custom-control custom-checkbox alert alert-warning">
                <p class="mb-2 mx-auto">Pilih jawaban dengan klik salah satu jawaban yang tersedia. Dan klik <strong>Kirim</strong> jika Anda telah selesai mengisi semua pertanyaan.</p>
                <p><strong>Selamat mengerjakan!</strong></p>
            </div>
        </div>
    </div>

    <div class="row">
        <form class="w-100" action="{{ route($formCompetencyTestStore) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- hidden data -->
            <input type="hidden" name="question_count" value="{{ count($testCompetencyDatas) }}">
            <!-- competency question -->
            @foreach($testCompetencyDatas as $dataCompetency)
                <div class="bg-card-box br-5 p-2 mb-2">
                    <h4>{{ isset($dataCompetency->question) ? ucfirst($dataCompetency->question) : 'Belum ada data' }}</h4>
                    <div class="row">
                        <div class="col-md">
                            <input type="radio" id="" name="{{ $dataCompetency->id }}" value="1">
                            <small>Ya</small>
                        </div>
                        <div class="col-md">
                            <input type="radio" id="" name="{{ $dataCompetency->id }}" value="0">
                            <small>Tidak</small>
                        </div>
                    </div>
                </div>
            @endforeach
            <!-- competency question end -->
            <div class="w-100"></div>
            <div class="w-100">
                <button type="submit" class="btn btn-orange" name="submit">Kirim</button>
            </div>
        </form>
    </div>
@endif