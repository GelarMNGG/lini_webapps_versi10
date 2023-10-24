@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar cash advance'; 
    $statusBadge    = array('','danger','warning','info','success','purple','pink','dark');
    //form project route
    $formRouteIndex = 'project-ca-tech.index';
    $formRouteCreate = 'project-ca-tech.create';
    $formRouteStore = 'project-ca-tech.store';
    $formRouteEdit = 'project-ca-tech.edit';
    $formRouteUpdate = 'project-ca-tech.update';
    $formRouteDestroy = 'project-ca-tech.destroy';
    //back
    $formRouteProjectIndex = 'project-tech.index';
    $formRouteProjectShow = 'project-tech.show';
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

<div class="card mt-2">
    <div class="card-header text-center bb-orange">
        <small>Proyek:</small> <strong><span class="text-info">{{ isset($projectTask->project_name) ? strtoupper($projectTask->project_name) : '' }}</span></strong>
        <br><small>Task:</small> <strong><span class="text-danger">{{ isset($projectTask->name) ? strtoupper($projectTask->name) : 'Belum ada task' }}</span></strong>
    </div>

    <div class="card-body bg-gray-lini-2">
        @if (isset($dataCashAdvance) && count($dataCashAdvance) > 0)
            <div class="row m-0">
                <?php $separator=1; ?>
                @foreach($dataCashAdvance as $data)
                    <div class="col-sm p-2">
                        <div class="bg-card-box br-5 p-2">

                            <div class="img-ca-box">
                                @if(isset($data->image))

                                    <button type="button" class="btn badge-pill text-dark" data-toggle="modal" style="position:absolute;" data-target="#cat_id_modal{{ $data->id }}"><i class="fas fa-eye"></i> </button>

                                    <img class="img-ca" src="{{ asset('img/cash-advance/tech/'.$data->image) }}">
                                @else
                                    <div class="p-5 text-center"><img class="img-ca-2" src="{{ asset('img/cash-advance/tech/default.png') }}"></div>
                                @endif
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="cat_id_modal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="projectCashAdvanceImage" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                    <div class="modal-content-img">
                                        <div class="modal-body text-center">
                                        <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                            <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/cash-advance/tech/'.$data->image) }}"  />
                                            <div class="alert alert-warning" id="projectCashAdvanceImage">
                                                <h5>
                                                    Bukti transfer: <span class="text-muted">{{ ucfirst($data->name) }} - Rp. {{ number_format($data->amount)}}</span>
                                                </h5>
                                            </div>
                                        </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <br><strong>{{ isset($data->name) ? ucwords($data->name) : 'Belum ada data' }}</strong>
                            @foreach($dataCAStatus as $projectStatus)
                                @if($projectStatus->id == $data->status)
                                    <div class="badge badge-{{ $statusBadge[$data->status] }} float-right">{{ $projectStatus->name }}</div>
                                @endif
                            @endforeach

                            <br>
                            @if($data->reject_status > 0)
                                <span class="text-danger">{{ $data->rejected_at ? date('l, d F Y',strtotime($data->rejected_at)) : '-' }}</span>
                            @else
                                @if($data->status == 2)
                                    <span class="text-info">{{ $data->submitted_at ? date('l, d F Y',strtotime($data->submitted_at)) : '-' }}</span>
                                @elseif($data->status == 3)
                                    <span class="text-success">{{ $data->approved_at ? date('l, d F Y',strtotime($data->approved_at)) : '-' }}</span>
                                @elseif($data->status == 4)
                                    <span class="text-success">{{ $data->approved_by_pm_at ? date('l, d F Y',strtotime($data->approved_by_pm_at)) : '-' }}</span>
                                @else
                                    <span class="text-danger">{{ $data->created_at ? date('l, d F Y',strtotime($data->created_at)) : '-' }}</span>
                                @endif
                            @endif

                            <br>Rp. {{ number_format($data->amount)}}

                            <div>
                                @if($data->status == 1)
                                    <a href="{{ route($formRouteEdit, $data->id) }}" class="btn btn-info"><i class="fas fa-edit"></i> Edit</a>

                                    <form action="{{ route($formRouteUpdate, $data->id) }}" style="display:inline-block" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <!-- hidden data -->
                                        <input type="text" name="project_id" value="{{ $projectTask->project_id }}" hidden>
                                        <input type="text" name="task_id" value="{{ $projectTask->id }}" hidden>
                                        <input type="text" name="status" value="2" hidden>

                                        <button type="submit" class="btn btn-pink"><i class="fas fa-paper-plane" title='Kirim'></i> Kirim</button>  
                                    </form>

                                    <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST" style="display:inline">
                                    @method('DELETE')
                                    @csrf
                                        <!-- hidden data -->
                                        <input type="text" name="project_id" value="{{ $projectTask->project_id }}" hidden>
                                        <input type="text" name="task_id" value="{{ $projectTask->id }}" hidden>

                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                    </form>
                                @elseif($data->status == 4)
                                    <span class="text-success">Cash advance telah disetujui</span>
                                @else
                                    <span class="text-info">Permohonan sedang direview</span>
                                @endif

                            </div>

                        </div>
                    </div>
                    <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                    <?php $separator++; ?>
                @endforeach
                <div class="w-100"></div>
                <div class="col-md">
                    <?php 
                        $dataCashAdvance->setPath('project-ca-tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                    ?>
                    <?php $paginator = $dataCashAdvance; ?>
                    @include('includes.paginator')
                </div>

                @if($dataCashAdvanceCount->total_dana > 0)
                <div class="col-md-12">
                    <div class="alert alert-warning mt-2">
                        @if($dataCashAdvanceCount->total_dana > $dataCashAdvanceCount->total_pengeluaran)
                            <div class="alert alert-danger text-center">Saldo: <span class="text-success">Rp {{ number_format($dataCashAdvanceCount->total_dana - $dataCashAdvanceCount->total_pengeluaran) }}</span></div>
                        @elseif($dataCashAdvanceCount->total_dana < $dataCashAdvanceCount->total_pengeluaran)
                            <div class="alert alert-danger text-center">Saldo: <span class="text-danger">Rp {{ number_format($dataCashAdvanceCount->total_dana - $dataCashAdvanceCount->total_pengeluaran) }}</span></div>
                        @else
                            <div class="alert alert-danger text-center">Saldo: <span class="text-info">-</span></div>
                        @endif
                        <!-- total pengeluaran -->
                        Total pengajuan dana:<strong> {{ $dataCashAdvanceCount->total_dana > 0 ? 'Rp. '.number_format($dataCashAdvanceCount->total_dana) : '-' }}</strong>
                        <div class="float-right">Total pengeluaran:
                            <strong>{{ $dataCashAdvanceCount->total_pengeluaran > 0 ? 'Rp. '.number_format($dataCashAdvanceCount->total_pengeluaran) : '-' }}</strong>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        @else
            <div class="alert alert-warning">Belum ada data.</div>
        @endif
    </div>
    <div class="card-body">
        <div class="col-md">
            <a href="{{ route($formRouteCreate,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Ajukan lagi</a>
            
            <a href="{{ route($formRouteProjectShow, $projectTask->id) }}" class="btn btn-blue-lini mt-1">Kembali</a>
        </div>
    </div>
</div>

@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
