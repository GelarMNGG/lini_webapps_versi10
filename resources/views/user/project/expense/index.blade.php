@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar pengeluaran'; 
    $statusBadge    = array('','danger','warning','info','success','purple','pink','dark');

    //form route
    $formRouteBack = 'user-projects.show';

    //form expense route
    $formRouteExpensesIndex = 'user-projects-expense.index';
    $formRouteExpensesUpdate = 'user-projects-expense.update';
    $formRouteExpenseShow = 'user-projects-expense.show';

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

    @if (sizeof($dataExpenses) > 0)
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-info float-left'>{{ sizeof($dataExpenses) }}</div>
                <div style="display:inline-block">
                    <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTask->project_name }}</span></strong>
                    <br><small>Task:</small> <strong><span class="text-danger text-uppercase">{{ $projectTask->name }}</span></strong>
                    <br><small>No task:</small> <strong><span class="text-warning text-uppercase">{{ $projectTask->number }}</span></strong>
                </div>
            </div>

            <div class="card-body bg-gray-lini-2">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="container-fluid">
                    <div class="row">
                        <?php $i = 1; ?>
                        @foreach ($dataExpenses as $data)
                            <div class="col-6 p-2">
                                <div class="bg-card-box br-5 p-2">
                                    @foreach($dataExpensesStatus as $projectStatus)
                                        @if($projectStatus->id == $data->status)
                                            <div class="badge badge-{{ $statusBadge[$data->status] }} float-right">{{ $projectStatus->name }}</div>
                                        @endif
                                    @endforeach
                                    <div class="col-md">
                                        @if(isset($dataExpensesImages) || count($dataExpensesImages) > 0)
                                            @foreach($dataExpensesImages as $dataImage)
                                                @if($dataImage->expense_id == $data->id)
                                                    <button type="button" class="btn" data-toggle="modal"  data-target="#expenseModal{{ $dataImage->id }}">

                                                        <img class="img-crop img-thumbnail" src="{{ asset('img/expenses/tech/'.$dataImage->image) }}">
                                                    </button>

                                                    <!-- modal image view -->
                                                        <div class="modal fade" id="expenseModal{{ $dataImage->id }}" tabindex="-1" role="dialog" aria-labelledby="projectExpenses" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                                                <div class="modal-content-img">
                                                                    <div class="modal-body text-center">
                                                                    <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                                        <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/expenses/tech/'.$dataImage->image) }}"  />
                                                                        <div class="alert alert-warning mt-1" id="projectExpenses">
                                                                            <h5>
                                                                                Bukti pengeluaran: <span class="text-muted">{{ ucfirst($data->name) }} - Rp. {{ number_format($data->amount)}}</span>
                                                                            </h5>
                                                                        </div>
                                                                    </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <!-- modal image view end -->
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    <span class="text-info"><strong>{{ ucwords($data->name) }}</strong></span>

                                    <br>
                                    @if($data->status == 2)
                                        <span class="text-info">{{ $data->submitted_at ? date('l, d F Y',strtotime($data->submitted_at)) : '-' }}</span>
                                    @elseif($data->status == 3)
                                        <span class="text-info">{{ $data->approved_at ? date('l, d F Y',strtotime($data->approved_at)) : '-' }}</span>
                                    @elseif($data->status == 4)
                                        <span class="text-success">{{ $data->approved_by_pm_at ? date('l, d F Y',strtotime($data->approved_by_pm_at)) : '-' }}</span>
                                    @else
                                        <span class="text-danger">{{ $data->created_at ? date('l, d F Y',strtotime($data->created_at)) : '-' }}</span>
                                    @endif

                                    <br>Rp. {{ number_format($data->amount)}}

                                    <div>
                                        @if(Auth::user()->user_level == 3)
                                            @if($data->status == 3)
                                                @if($data->expenses_files_count > 0)
                                                    <form action="{{ route($formRouteExpensesUpdate, $data->id) }}" style="display:inline-block" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <!-- hidden data -->
                                                        <input type="text" name="project_id" value="{{ $data->project_id }}" hidden>
                                                        <input type="text" name="task_id" value="{{ $data->task_id }}" hidden>
                                                        <input type="text" name="status" value="4" hidden>

                                                        <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane" title='Kirim'></i> Approve</button>  
                                                    </form>

                                                    <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#rejectNote{{ $i }}" aria-expanded="false" aria-controls="rejectNote{{ $i }}">Reject</button>

                                                    <div class="collapse" id="rejectNote{{ $i }}">
                                                        <div class="col-md card mt-1">
                                                            <form action="{{ route($formRouteExpensesUpdate, $data->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <!-- hidden data -->
                                                                <input type="text" name="project_id" value="{{ $data->project_id }}" hidden>
                                                                <input type="text" name="task_id" value="{{ $data->task_id }}" hidden>
                                                                <input type="text" name="status" value="1" hidden>

                                                                <input type="text" name="reject_note" class="form-control mb-1" value="" placeholder="Alasan reject">

                                                                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane" title='Kirim'></i> Reject</button>  
                                                            </form>
                                                        </div>
                                                    </div>

                                                @else
                                                    <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#rejectNote{{ $i }}" aria-expanded="false" aria-controls="rejectNote{{ $i }}">Reject</button>

                                                    <div class="collapse" id="rejectNote{{ $i }}">
                                                        <div class="col-md card mt-1">
                                                            <form action="{{ route($formRouteExpensesUpdate, $data->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <!-- hidden data -->
                                                                <input type="text" name="project_id" value="{{ $data->project_id }}" hidden>
                                                                <input type="text" name="task_id" value="{{ $data->task_id }}" hidden>
                                                                <input type="text" name="status" value="1" hidden>

                                                                <input type="text" name="reject_note" class="form-control mb-1" value="" placeholder="Alasan reject">

                                                                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane" title='Kirim'></i> Reject</button>  
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-danger">Tidak tersedia</span>
                                            @endif
                                        @else(Auth::user()->user_level == 6)
                                            @if($data->status > 1 && $data->status < 3)
                                                @if($data->expenses_files_count > 0)
                                                    <form action="{{ route($formRouteExpensesUpdate, $data->id) }}" style="display:inline-block" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <!-- hidden data -->
                                                        <input type="text" name="project_id" value="{{ $data->project_id }}" hidden>
                                                        <input type="text" name="task_id" value="{{ $data->task_id }}" hidden>
                                                        <input type="text" name="status" value="3" hidden>

                                                        <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane" title='Kirim'></i> Approve</button>  
                                                    </form>

                                                    <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#rejectNote{{ $i }}" aria-expanded="false" aria-controls="rejectNote{{ $i }}">Reject</button>

                                                    <div class="collapse alert" id="rejectNote{{ $i }}">
                                                        <div class="col-md card mt-1">
                                                            <form action="{{ route($formRouteExpensesUpdate, $data->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <!-- hidden data -->
                                                                <input type="text" name="project_id" value="{{ $data->project_id }}" hidden>
                                                                <input type="text" name="task_id" value="{{ $data->task_id }}" hidden>
                                                                <input type="text" name="status" value="1" hidden>

                                                                <input type="text" name="reject_note" class="form-control mb-1" value="" placeholder="Alasan reject">

                                                                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane" title='Kirim'></i> Reject</button>  
                                                            </form>
                                                        </div>
                                                    </div>
                                                    
                                                @else

                                                <button type="button" class="btn btn-danger" data-toggle="collapse" data-target="#rejectNote{{ $i }}" aria-expanded="false" aria-controls="rejectNote{{ $i }}">Reject</button>

                                                <div class="collapse alert" id="rejectNote{{ $i }}">
                                                    <div class="col-md card-box">
                                                        <form action="{{ route($formRouteExpensesUpdate, $data->id) }}" style="display:inline-block" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <!-- hidden data -->
                                                            <input type="text" name="project_id" value="{{ $data->project_id }}" hidden>
                                                            <input type="text" name="task_id" value="{{ $data->task_id }}" hidden>
                                                            <input type="text" name="status" value="1" hidden>

                                                            <input type="text" name="reject_note" class="form-control" value="" placeholder="Alasan reject">

                                                            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane" title='Kirim'></i> Kirim</button>  
                                                        </form>
                                                    </div>
                                                </div>
                                                @endif
                                            @endif
                                        @endif
                                    </div>

                                </div>
                            </div>
                            <?php $i++; ?>
                        @endforeach
                    </div>
                </div> <!-- container-fluid -->
            </div>
            <div class="card-body">
                <div class="col-md">

                    @if($dataReportCount > 0)
                        <a href="{{ route($formRouteExpenseShow, $projectTask->id.'?project_id='.$projectTask->project_id) }}" class="btn btn-orange mb-1"><i class="fa fa-eye"></i> Lihat laporan pengeluaran</a>
                    @endif

                    @if(Auth::user()->user_level == 3)
                        <a href="{{ route($formRouteBack,$projectTask->project_id) }}" type="button" class="btn btn-blue-lini mb-1 ml-1">Kembali</a>
                    @else
                        <a href="{{ route($formRouteBack,$projectTask->id) }}" type="button" class="btn btn-blue-lini mb-1 ml-1">Kembali</a>
                    @endif

                </div>
            </div>

        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-info float-left'>{{ sizeof($dataExpenses) }}</div>
                <div class="w-100"></div>
                <div style="display:inline-block">
                    <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTask->project_name }}</span></strong>
                    <br><small>Task:</small> <strong><span class="text-danger text-uppercase">{{ $projectTask->name }}</span></strong>
                    <br><small>No task:</small> <strong><span class="text-warning text-uppercase">{{ $projectTask->number }}</span></strong>
                </div>
            </div>
            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>
            <div class="card-body">
                <div class="col-md">
                    @if(Auth::user()->user_level == 3)
                        <a href="{{ route($formRouteBack,$projectTask->project_id) }}" type="button" class="btn btn-blue-lini mb-1 ml-1">Kembali</a>
                    @else
                        <a href="{{ route($formRouteBack,$projectTask->id) }}" type="button" class="btn btn-blue-lini mb-1 ml-1">Kembali</a>
                    @endif
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
    $('#staticBackdrop').on('shown.bs.modal', function () {
        $('#myInput').trigger('focus')
    })
</script>
@endsection
