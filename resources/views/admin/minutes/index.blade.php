@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar aktivitas harian'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $formRouteIndex = 'admin-minutes.index';
    $formRouteCreate = 'admin-minutes.create';
    $formRouteStore = 'admin-minutes.store';
    $formRouteEdit = 'admin-minutes.edit';
    $formRouteDestroy = 'admin-minutes.destroy';

    //add category
    $formCategoryIndex = 'admin-minutes-category.index';
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

    @if (sizeof($adminMinutes) > 0)
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ sizeof($adminMinutes) }}</div>
                @if($date != NULL)
                    <a href="{{ route ($formRouteIndex,'skin='.$skin.'&date='.$date)}}" class='badge badge-danger float-right'>Change skin</a>
                @else
                    <a href="{{ route ($formRouteIndex,'skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
                @endif
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="row m-0">
                    <?php $separator=1; ?>
                    @foreach($adminMinutes as $data)
                        <div class="col-sm p-2">
                            <div class="bg-card-box br-5 p-2">

                                <?php
                                    if ($data->status == 1) {
                                        $badge = 'success';
                                    }else{
                                        $badge = 'danger';
                                    }
                                ?>
                                <span class="float-right text-right">
                                    <span class="badge badge-{{ $badge }}">
                                        @if($data->status == 1)
                                            Done
                                        @else
                                            {{ $data->percentage }}<small>%</small>
                                        @endif
                                    </span>
                                    <br>
                                    @if($data->published == 1)
                                        <span class="badge badge-info">Published</span>
                                    @else
                                        <span class="badge badge-danger">Draft</span>
                                    @endif
                                    <?php
                                        if ($data->grade > 60) {
                                            $badgeBobot = 'danger';
                                        }elseif($data->grade >= 30 && $data->grade <= 60){
                                            $badgeBobot = 'info';
                                        }elseif($data->grade == 0){
                                            $badgeBobot = 'dark';
                                        }else{
                                            $badgeBobot = 'success';
                                        }
                                    ?>
                                </span>

                                <strong>{{ isset($data->name) ? ucwords($data->name) : 'Belum ada data' }}</strong>
                                <br>
                                @if(Auth::user()->company_id == 1 && Auth::user()->department_id == 5)
                                    <span class="text-danger">{{ isset($data->category_name) ? ucwords($data->category_name) : '' }}</span> | 
                                @endif

                                @if(isset($data->date))
                                    <span class="text-info"> {{ date('l, d F Y', strtotime($data->date))}}</span> |  
                                @endif 
                                <span class="text-info">{{ date('H:i A', strtotime($data->event_start)) .' - '. date('H:i A', strtotime($data->event_end)) }}</span>
                                
                                @if($data->status == 1)
                                    <br>Selesai: <span class="text-success">{{ isset($data->done_date) ? date('l, d F Y', strtotime($data->done_date)) : '-' }}</span>
                                @endif

                                <div class="mt-1">
                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#minutesModal{{ $data->id }}"><i class="fas fa-eye"></i> </button>

                                    <form action="{{ route($formRouteDestroy, $data->id) }}" method="POST" style="display:inline;">
                                        @method('DELETE')
                                        @csrf

                                        <a href="{{ route($formRouteEdit, $data->id.'&skin='.$skinBack) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i></a>

                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin akan menghapus data ini?')"><i class="fas fa-times" title='Delete'></i></button>  
                                    </form>

                                    <!-- Modal -->
                                    <div class="modal fade" id="minutesModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="projectMinutes" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                            <div class="modal-content-img">
                                                <div class="modal-body text-center">
                                                <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                    <div class="w-100" style="background-color:#d3d3d3; border-top-right-radius:5px;border-top-left-radius:5px;">
                                                        <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/minutes/user/'.$data->image) }}"  />
                                                    </div>
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
                                </div>

                            </div>
                        </div>
                        <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                        <?php $separator++; ?>
                    @endforeach
                    <div class="w-100"></div>
                    <div class="col-md">
                        <?php 
                            #$techMinutes->setPath('minutes-tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                        ?>
                        <?php $paginator = $adminMinutes; ?>
                        @include('includes.paginator')
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah aktivitas</a>
                    <a href="{{ route($formCategoryIndex) }}" class="btn btn-orange mt-1"><i class="fa fa-eye"></i> Lihat kategori</a>
                    <a href="{{ route('admin-minutes-report.index') }}" class="btn btn-orange mt-1"><i class="fa fa-eye"></i> Lihat aktivitas team</a>
                </div>
            </div>
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ sizeof($adminMinutes) }}</div>
                <strong>{{ ucfirst($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>

            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteCreate) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Tambah aktivitas</a>
                    <a href="{{ route($formCategoryIndex) }}" class="btn btn-orange mt-1"><i class="fa fa-eye"></i> Lihat kategori</a>
                    <a href="{{ route('admin-minutes-report.index') }}" class="btn btn-orange mt-1"><i class="fa fa-eye"></i> Lihat aktivitas team</a>
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
