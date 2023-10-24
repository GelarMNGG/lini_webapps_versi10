@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar cash advance'; 
    $statusBadge    = array('','danger','warning','info','success','purple','pink','dark');

    //form project route
    $formRouteIndex = 'user-projects-ca.index';
    $formRouteCreate = 'user-projects-ca.create';
    $formRouteEdit = 'user-projects-ca.edit';
    $formRouteUpdate = 'user-projects-ca.update';

    //back
    $formRouteProjectIndex = 'user-projects.index';
    $formRouteProjectShow = 'user-projects.show';

    //request payment
    $formRequestPaymentShow = 'user-projects-spp.show';
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

    @if (sizeof($dataCashAdvance) > 0)
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-danger float-left'>{{ sizeof($dataCashAdvance) }}</div>
                <div>
                    <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTask->project_name }}</span></strong>
                    <br><small>Task:</small> <strong><span class="text-danger text-uppercase">{{ $projectTask->name }}</span></strong>
                    <br><small>No task:</small> <strong><span class="text-warning text-uppercase">{{ $projectTask->number }}</span></strong>
                </div>
            </div>
            <div class="card-body bg-gray-lini-2">
                <div class="container-fluid">
                    <div class="row">
                        <?php $i = 1; ?>
                        @foreach ($dataCashAdvance as $data)
                            <div class="col-6 p-2">
                                <div class="bg-card-box br-5 p-2">
                                    <div class="img-ca-box mb-2">
                                        @if(isset($data->image))
                                            <button type="button" class="btn badge-pill text-dark" data-toggle="modal" style="position:absolute;" data-target="#covidModal{{ $data->id }}"><i class="fas fa-eye"></i> </button>

                                            <img class="img-ca" src="{{ asset('img/cash-advance/tech/'.$data->image) }}">
                                        @else
                                            <div class="p-5 text-center"><img class="img-ca-2" src="{{ asset('img/cash-advance/tech/default.png') }}"></div>
                                        @endif
                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="covidModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="covidImage" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                            <div class="modal-content-img">
                                                <div class="modal-body text-center">
                                                <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                    <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/cash-advance/tech/'.$data->image) }}"  />
                                                    <div class="alert alert-warning" id="covidImage">
                                                        <h5>
                                                            Bukti transfer: <span class="text-muted">{{ ucfirst($data->name) }} - Rp. {{ number_format($data->amount)}}</span>
                                                        </h5>
                                                    </div>
                                                </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @foreach($dataCAStatus as $projectStatus)
                                        @if($projectStatus->id == $data->status)
                                            <div class="badge badge-{{ $statusBadge[$data->status] }} float-right">{{ $projectStatus->name }}</div>
                                        @endif
                                    @endforeach

                                    <strong>{{ ucwords($data->name) }}</strong>

                                    <br>
                                    @if($data->status == 2)
                                        <span class="text-info">{{ $data->submitted_at ? date('l, d F Y',strtotime($data->submitted_at)) : '-' }}</span>
                                    @elseif($data->status == 3)
                                        <span class="text-success">{{ $data->approved_at ? date('l, d F Y',strtotime($data->approved_at)) : '-' }}</span>
                                    @elseif($data->status == 4)
                                        <span class="text-success">{{ $data->approved_by_pm_at ? date('l, d F Y',strtotime($data->approved_by_pm_at)) : '-' }}</span>
                                    @else
                                        <span class="text-danger">{{ $data->created_at ? date('l, d F Y',strtotime($data->created_at)) : '-' }}</span>
                                    @endif

                                    <br>Rp. {{ number_format($data->amount)}}

                                    <div>
                                        @if(Auth::user()->user_level == 3)
                                            @if($data->status == 2)
                                                
                                                <form action="{{ route($formRouteUpdate, $data->id) }}" style="display:inline-block" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <!-- hidden data -->
                                                    <input type="text" name="project_id" value="{{ $data->project_id }}" hidden>
                                                    <input type="text" name="task_id" value="{{ $data->task_id }}" hidden>
                                                    <input type="text" name="status" value="4" hidden>

                                                    <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane" title='Kirim'></i> Approve</button>  
                                                </form>

                                                <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#rejectNote{{ $i }}" aria-expanded="false" aria-controls="rejectNote{{ $i }}">Edit</button>

                                                <div class="collapse" id="rejectNote{{ $i }}">
                                                    <div class="card mt-1">
                                                        <form action="{{ route($formRouteUpdate, $data->id) }}" class="{{ $errors->has('reject_note') ? ' has-error' : '' }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <!-- hidden data -->
                                                            <input type="text" name="project_id" value="{{ $data->project_id }}" hidden>
                                                            <input type="text" name="task_id" value="{{ $data->task_id }}" hidden>
                                                            <input type="text" name="status" value="4" hidden>
                                                            <!-- <input type="text" name="reject_status" value="0" hidden> -->

                                                            <input type="number" name="amount" class="form-control mb-1" value="{{ $data->amount }}" placeholder="Amount">

                                                            <input type="text" name="reject_note" class="form-control mb-1" value="" placeholder="Alasan reject">

                                                            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane" title='Simpan'></i> Simpan</button>  
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        @else(Auth::user()->user_level == 6)
                                            @if($data->status == 4)
                                                <a href="{{ route($formRequestPaymentShow, $data->id.'?project_id='.$data->project_id.'&task_id='.$data->task_id) }}" class="btn btn-success"><i class="fas fa-edit" title='Create'></i> Buat SPP</a>

                                                @if($data->image != null)
                                                    <a href="{{ route($formRouteEdit, $data->id) }}" class="btn btn-info">Edit bukti transfer</a>
                                                @else
                                                    <a href="{{ route($formRouteCreate, 'id='.$data->id.'&project_id='.$data->project_id.'&task_id='.$data->task_id) }}" class="btn btn-warning">Upload bukti transfer</a>
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
                    @if(Auth::user()->user_level == 3)
                        <a href="{{ route($formRouteProjectShow,$projectTask->project_id) }}" type="button" class="btn btn-blue-lini mb-1 ml-1">Kembali</a>
                    @else
                        <a href="{{ route($formRouteProjectShow,$projectTask->id) }}" type="button" class="btn btn-blue-lini mb-1 ml-1">Kembali</a>
                    @endif
                </div>
            </div>
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-danger float-left'>{{ sizeof($dataCashAdvance) }}</div>
                <div>
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
                        <a href="{{ route($formRouteProjectShow,$projectTask->project_id) }}" type="button" class="btn btn-blue-lini mb-1 ml-1">Kembali</a>
                    @else
                        <a href="{{ route($formRouteProjectShow,$projectTask->id) }}" type="button" class="btn btn-blue-lini mb-1 ml-1">Kembali</a>
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
</script>
@endsection
