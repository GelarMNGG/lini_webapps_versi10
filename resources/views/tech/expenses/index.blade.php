@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar pengeluaran'; 
    $statusBadge    = array('','danger','warning','info','success','purple','pink','dark');
    //form route
    $formRouteIndex = 'expenses-tech.index';
    $formRouteCreate = 'expenses-tech.create';
    $formRouteStore = 'expenses-tech.store';
    $formRouteReport = 'expenses-tech.report';
    $formRouteEdit = 'expenses-tech.edit';
    $formRouteUpdate = 'expenses-tech.update';
    $formRouteDestroy = 'expenses-tech.destroy';

    //tools report
    $formRouteExpenseReport = 'tech.projectreportexpenseshow';

    //form project route
    $formRouteProjectIndex = 'project-tech.index';
    $formRouteProjectShow = 'project-tech.show';

    //image/receipt upload
    $formExpenseImageStore = 'expenses-image-upload.store';
    $formExpenseImageDestroy = 'expenses-image-upload.destroy';
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

    @if (count($dataExpenses) > 0)
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <small>Proyek:</small> <strong><span class="text-info">{{ isset($projectTask->project_name) ? strtoupper($projectTask->project_name) : '' }}</span></strong>
                <br><small>Task:</small> <strong><span class="text-danger">{{ isset($projectTask->name) ? strtoupper($projectTask->name) : 'Belum ada task' }}</span></strong>
            </div>

            @if (isset($dataExpenses))
                <div class="bg-gray-lini-2">
                    <div class="row m-0">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <?php $separator=1; ?>
                        @foreach($dataExpenses as $data)
                            <div class="col-sm p-2">
                                <div class="bg-card-box br-5 p-2">
                                    <div class="col-md">
                                        @if(isset($dataExpensesImages) || count($dataExpensesImages) > 0)
                                            @foreach($dataExpensesImages as $dataImage)
                                                @if($dataImage->expense_id == $data->id)
                                                    <button type="button" class="btn" data-toggle="modal"  data-target="#expenseModal{{ $dataImage->id }}">
                                                        <img class="img-crop img-thumbnail" src="{{ asset('img/expenses/tech/'.$dataImage->image) }}">
                                                        <form action="{{ route($formExpenseImageDestroy, $dataImage->id) }}" method="POST">
                                                            @method('DELETE')
                                                            @csrf
                                                            <button type="submit" class="btn text-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')" style="position:relative; left: -29px; top: -38px;"><i class="fas fa-times" title='Delete'></i></button>  
                                                        </form>
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
                                
                                    <strong>{{ isset($data->name) ? ucwords($data->name) : 'Belum ada data' }}</strong>
                                    @foreach($dataExpensesStatus as $projectStatus)
                                        @if($projectStatus->id == $data->status)
                                            @if($data->rejected_at != null)
                                                <div class="badge badge-{{ $statusBadge[$data->status] }} float-right">ditolak</div>
                                            @else
                                                <div class="badge badge-{{ $statusBadge[$data->status] }} float-right">{{ $projectStatus->name }}</div>
                                            @endif
                                        @endif
                                    @endforeach

                                    | @if($data->status == 1 && $data->rejected_at != null)
                                        <span class="text-info">{{ $data->rejected_at ? date('l, d F Y',strtotime($data->rejected_at)) : '-' }}</span>
                                    @elseif($data->status == 2)
                                        <span class="text-info">{{ $data->submitted_at ? date('l, d F Y',strtotime($data->submitted_at)) : '-' }}</span>
                                    @elseif($data->status == 3)
                                        <span class="text-info">{{ $data->approved_at ? date('l, d F Y',strtotime($data->approved_at)) : '-' }}</span>
                                    @elseif($data->status == 4)
                                        <span class="text-success">{{ $data->approved_by_pm_at ? date('l, d F Y',strtotime($data->approved_by_pm_at)) : '-' }}</span>
                                    @else
                                        <span class="text-danger">{{ $data->created_at ? date('l, d F Y',strtotime($data->created_at)) : '-' }}</span>
                                    @endif

                                    <br>Rp. {{ number_format($data->amount)}}
                                    @if($data->status == 1 && $data->rejected_at != null)
                                        <br><span class="text-danger">Rejected</span> note: 
                                        <span class="text-info">{{ ucfirst($data->reject_note) }}</span>
                                    @endif

                                    <br><span><small>Kode pengeluaran: <span class="text-danger">{{ ucwords($data->code_id) ? '['.ucwords($data->code_id).'] '.ucwords($data->code_name) : '-'}}</span></small></span>

                                    <div class="mt-1">
                                        @if($dataReportCount < 1)
                                            @if($data->rejected_at != null && $data->status < 3)
                                                <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#uploadModal{{ $data->id }}"> Upload kwitansi ulang</button>
                                            @elseif($data->status < 2)
                                                <button type="button" class="btn btn-success mb-1" data-toggle="modal" data-target="#uploadModal{{ $data->id }}"> Upload kwitansi</button>   
                                            @endif

                                            <!-- modal upload start -->
                                                <div class="modal fade" id="uploadModal{{ $data->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header no-bd">
                                                                <h5 class="modal-title">
                                                                    <span class="fw-mediumbold text-danger"> Tutup</span> 
                                                                </h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span class="text-danger" aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form class="w-100" action="{{ route($formExpenseImageStore) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <!-- hidden data -->
                                                                <input type="text" name="expense_id" value="{{ $data->id }}" hidden>
                                                                <input type="text" name="task_id" value="{{ $data->task_id }}" hidden>
                                                                <input type="text" name="project_id" value="{{ $data->project_id }}" hidden>
                                                                <!-- hidden data end -->
                                                                <div class="row">
                                                                    <div class="col-md{{ $errors->has('image') ? ' has-error' : '' }}">
                                                                        <div class="card-box">
                                                                            <h4 class="header-title mb-3"><small>Kwitansi untuk </small><br><span class="text-info">{{ ucfirst($data->name) }}</span></h4>
                                                                            <input type="file" name="image" class="dropify" data-max-file-size="7M" data-default-file="{{ asset('img/expenses/tech/default.png') }}" />
                                                                        </div>
                                                                    </div>
                                                                    <div class="w-100"></div>
                                                                    <div class="col-md mt-2 mb-2">
                                                                        <button id="btn_upload" type="submit" class="btn btn-orange" name="submit">Upload</button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <!-- modal upload start end -->

                                            @if(isset($data->approved_by_pm_at))
                                                <form action="{{ route($formRouteUpdate, $data->id) }}" style="display:inline-block" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <!-- hidden data -->
                                                    <input type="text" name="status" value="5" hidden>
                                                    <input type="text" name="project_id" value="{{ $data->project_id }}" hidden>
                                                    <input type="text" name="task_id" value="{{ $data->task_id }}" hidden>
                                                    <!-- email -->
                                                    <input type="text" name="expense_id" value="{{ $data->id }}" hidden>
                                                    <input type="text" name="task_name" value="{{ $projectTask->name }}" hidden>
                                                    <input type="text" name="name" value="{{ $data->name }}" hidden>
                                                    <input type="text" name="amount" value="{{ $data->amount }}" hidden>
                                                    <input type="text" name="code" value="{{ $data->code }}" hidden>

                                                    <button type="submit" class="btn btn-danger mb-1"><i class="fas fa-paper-plane" title='Kirim'></i> Kirim ke Odoo</button>  
                                                </form>
                                            @endif
                                            @if($data->status == 1)
                                                @if($data->expenses_files_count > 0)
                                                    <form action="{{ route($formRouteUpdate, $data->id) }}" style="display:inline-block" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <!-- hidden data -->
                                                        <input type="text" name="status" value="2" hidden>
                                                        <input type="text" name="project_id" value="{{ $data->project_id }}" hidden>
                                                        <input type="text" name="task_id" value="{{ $data->task_id }}" hidden>
                                                        <!-- email -->
                                                        <input type="text" name="expense_id" value="{{ $data->id }}" hidden>
                                                        <input type="text" name="task_name" value="{{ $projectTask->name }}" hidden>
                                                        <input type="text" name="name" value="{{ $data->name }}" hidden>
                                                        <input type="text" name="amount" value="{{ $data->amount }}" hidden>
                                                        <input type="text" name="code" value="{{ $data->code }}" hidden>

                                                        <button type="submit" class="btn btn-pink mb-1"> Minta approval</button>  
                                                    </form>
                                                @endif
                                                <form action="{{ route($formRouteDestroy, $data->id) }}" style="display:inline-block" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                    <button type="submit" class="btn btn-danger mb-1" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                                </form>
                                            @endif
                                        @endif

                                    </div>

                                </div>
                            </div>
                            <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                            <?php $separator++; ?>
                        @endforeach
                        <div class="col-md">
                            <?php 
                                $dataExpenses->setPath('expenses-tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                            ?>
                            <?php $paginator = $dataExpenses; ?>
                            @include('includes.paginator')
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">Belum ada data.</div>
            @endif

            <div class="card-body">
                <div class="col-md">
                    @if($dataReportCount > 0)
                        <a href="{{ route($formRouteExpenseReport,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange{{ $dataFinishedCount > 0 ? '' : ' disabled' }}"><i class="fa fa-magic"></i> Lihat laporan pengeluaran</a>

                        @if($dataFinishedCount < 1)
                            <a href="{{ route($formRouteCreate,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah expenses</a>
                        @endif
                    @else    
                        <a href="{{ route($formRouteCreate,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah expenses</a>

                        @if($dataExpenseCount > 0)
                            <a href="{{ route($formRouteReport,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange{{ $dataFinishedCount > 0 ? '' : ' disabled' }}"><i class="fa fa-magic"></i> Buat laporan pengeluaran</a>
                        @endif
                    @endif
                    <a class="btn btn-blue-lini" href="{{ route($formRouteProjectShow, $projectTask->id) }}">Kembali</a>
                </div>
            </div>
            
        </div>
    @else
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-info float-left'>{{ count($dataExpenses) }}</div>
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
                    <a href="{{ route($formRouteCreate,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange"><i class="fa fa-plus"></i> Tambah expenses</a>
                    
                    <a href="{{ route($formRouteProjectShow, $projectTask->id) }}" class="btn btn-blue-lini">Kembali</a>
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
