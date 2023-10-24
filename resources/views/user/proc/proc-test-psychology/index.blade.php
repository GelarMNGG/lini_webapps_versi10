@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar pertanyaan tes psikologi'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $formRouteIndex = 'user-proc-test-psychology.index';
    $formRouteCreate = 'user-proc-test-psychology.create';
    $formRouteStore = 'user-proc-test-psychology.store';
    $formRouteEdit = 'user-proc-test-psychology.edit';
    $formRouteDestroy = 'user-proc-test-psychology.destroy';

    //add Question
    $formQuestionIndex = 'user-proc-question.index';

    //add Answer
    $formAnswerCreate = 'user-test-psychology-answers.create';
    $formAnswerEdit = 'user-test-psychology-answers.edit';

    //add Analisys
    $formAnalisysIndex = 'user-test-psychology-analisys.index';



?>
@endsection

@section('content')
<div class="flash-message mt-2">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

    @if (count($dataQuestions) > 0)
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ $dataQuestions->total() }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="row m-0">
                    @foreach($dataQuestions as $data)
                        <div class="col-6 p-2">
                            <div class="bg-card-box br-5 p-2">
                                <span class="text-info">Pertanyaan : </span><strong>{{ isset($data->question) ? ucfirst($data->question) : 'Belum ada data' }}</strong>
                                <br><span class="text-success">Jawaban : </span>
                                @if(isset($data->answer_a) || isset($data->answer_b))

                                <ol type="A">
                                    <li>{{ isset($data->answer_a) ? ucfirst($data->answer_a) : '-'}}</li>
                                    <li>{{ isset($data->answer_b) ? ucfirst($data->answer_b) : '-'}}</li>
                                </ol>
                                @endif

                                @if(isset($data->category_name))
                                    <span class="text-danger">{{ ucfirst($data->category_name) }}</span>
                                @endif

                                <div class="mt-1">
                                    <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST">
                                        @method('DELETE')
                                        @csrf
                                        @if(isset($data->answer_a) || isset($data->answer_b))
                                            <a href="{{ route($formAnswerEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fa fa-edit' title='Create'></i> Edit Jawaban</a>
                                        @else
                                            <a href="{{ route($formAnswerCreate,'qid='.$data->id) }}" class='btn btn-icon waves-effect waves-light btn-orange t-white'> <i class='fa fa-plus' title='Create'></i> Tambah Jawaban</a>
                                        @endif
                                        <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                    </form>
                                </div>

                            </div>
                        </div>
                    @endforeach
                    <div class="col-12">
                        <?php 
                            #$techMinutes->setPath('minutes-tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                        ?>
                        {{ $dataQuestions->links() }}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah Pertanyaan</a>
                    <a href="{{ route($formAnalisysIndex) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Lihat Kategori Analisis</a>
                    <a href="{{ route($formQuestionIndex) }}" type="button" class="btn btn-blue-lini">Kembali</a>
                </div>
            </div>
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ count($dataQuestions) }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>

            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah Pertanyaan</a>
                    <a href="{{ route($formAnalisysIndex) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Lihat Kategori Analisis</a>
                    <a href="{{ route($formQuestionIndex) }}" type="button" class="btn btn-blue-lini">Kembali</a>
                </div>
            </div>
        </div> <!-- container-fluid -->
    @endif
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
