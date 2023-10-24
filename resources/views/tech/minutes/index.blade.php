@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar aktivitas harian'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $formRouteIndex = 'minutes-tech.index';
    $formRouteCreate = 'minutes-tech.create';
    $formRouteStore = 'minutes-tech.store';
    $formRouteEdit = 'minutes-tech.edit';
    $formRouteDestroy = 'minutes-tech.destroy';
    
    //form back
    $formRouteBack = 'project-tech.show';
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
    @if ($techMinutes->total() > 0)
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-info float-left'>{{ count($techMinutes) }}</div>
                <small>Project:</small> <strong><span class="text-info text-uppercase">{{ $projectTask->project_name }}</span></strong>
                <br><small>Task:</small> <strong><span class="text-danger text-uppercase">{{ $projectTask->name }}</span></strong>
                <br><small>No task:</small> <strong><span class="text-warning text-uppercase">{{ $projectTask->number }}</span></strong>
            </div>
            <div class="card-body bg-gray-lini-2">
                <div class="row m-0">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <?php $separator=1; ?>
                    @foreach($techMinutes as $data)
                        <div class="col-sm p-2">
                            <div class="bg-card-box br-5 p-2">

                                <div class="img-ca-box">
                                    @if($data->images_count > 0)
                                    <div class="row">
                                        <?php $im=1; ?>
                                        @foreach($techMinutesImages as $techMinutesImage)
                                            @if($techMinutesImage->projmin_id == $data->id && $im <=3)
                                            <div class="col-md">
                                                <button type="button" class="btn badge-pill text-dark" data-toggle="modal" style="position:absolute;" data-target="#minutesModal{{ $techMinutesImage->id }}"><i class="fas fa-eye"></i> </button>
                                                <img class="img-ca" src="{{ asset('img/minutes/tech/'.$techMinutesImage->image) }}" style="height:177px;min-width:auto;object-fit:cover; overflow:hidden;">
                                                <!-- Modal -->
                                                    <div class="modal fade" id="minutesModal{{ $techMinutesImage->id }}" tabindex="-1" role="dialog" aria-labelledby="projectMinutes" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                                            <div class="modal-content-img">
                                                                <div class="modal-body text-center">
                                                                <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                                    <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/minutes/tech/'.$techMinutesImage->image) }}"  />
                                                                    <div class="alert alert-warning" id="projectMinutes">
                                                                        <h5>
                                                                            Foto aktifitas: <span class="text-muted">{{ ucfirst($data->name) }}</span>
                                                                        </h5>
                                                                    </div>
                                                                </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <!-- Modal end -->
                                            </div>
                                            <?php $im++; ?>
                                            @endif
                                        @endforeach
                                    </div>
                                    @else
                                        <div class="p-5 text-center"><img class="img-ca-2" src="{{ asset('img/minutes/tech/default.png') }}"></div>
                                    @endif
                                </div>
                                <br><strong>{{ isset($data->name) ? ucwords($data->name).' ('.$data->images_count.' gambar)' : 'Belum ada data' }}</strong>
                                <br><span class="text-info">@if(isset($data->date)) {{ date('l, d F Y', strtotime($data->date))}} @endif</span>
                                | <span class="text-info">{{ date('H:i A', strtotime($data->event_start)) .' - '. date('H:i A', strtotime($data->event_end)) }}</span>

                                <div class="mt-1">
                                    <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST">
                                        @method('DELETE')
                                        @csrf

                                        <a href="{{ route($formRouteEdit, $data->id) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i> Ubah</a>

                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i> Hapus</button>  
                                    </form>
                                </div>

                            </div>
                        </div>
                        <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                        <?php $separator++; ?>
                    @endforeach
                    <div class="w-100"></div>
                    <div class="col-md">
                        <?php 
                            $techMinutes->setPath('minutes-tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                        ?>
                        <?php $paginator = $techMinutes; ?>
                        @include('includes.paginator')
                    </div>

                </div>
            </div>

            <div class="card-body">
                <div class="col-md"> 
                    <a href="{{ route($formRouteCreate,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah Aktifitas</a>
                    
                    <a href="{{ route($formRouteBack, $projectTask->id) }}" class="btn btn-blue-lini mt-1">Kembali</a>
                </div>
            </div>
 
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-info float-left'>{{ count($techMinutes) }}</div>
                <div class="card-header text-center">
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
                    <a href="{{ route($formRouteCreate,'project_id='.$projectTask->project_id.'&task_id='.$projectTask->id) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah Aktifitas</a>
                    
                    <a href="{{ route($formRouteBack, $projectTask->id) }}" class="btn btn-blue-lini mt-1">Kembali</a>
                </div>
            </div>
        </div>
    @endif
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
