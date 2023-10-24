@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar aktivitas team'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $formRouteIndex = 'admin-minutes-report.index';
    $formRouteEdit = 'admin-minutes-report.edit';

    $formRouteCreate = 'admin-minutes-report.create';
    //add category
    $formCategoryIndex = 'admin-minutes-category.index';
    //back
    $formRouteBack = 'admin-minutes.index';
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

    @if (count($userMinutes) > 0)
        <div class="card mt-2">
            <div class="card-header text-center text-uppercase bb-orange">
                <div class='badge badge-info float-left'>{{ $userMinutes->total() }}</div>
                <a href="{{ route ($formRouteIndex,'skin='.$skin)}}" class='badge badge-danger float-right'>Change skin</a>
                <strong>{{ strtoupper($pageTitle) }}</strong>
            </div>

            <div class="bg-gray-lini-2">
                <div class="card-body">
                    <div class="row m-0">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        
                        <?php $separator=1; ?>
                        @foreach ($userMinutes as $data)
                            <div class="col-sm p-2">
                                <div class="bg-card-box br-5 p-2">
                                    <?php
                                        if ($data->status == 1) {
                                            $cssTitle = '';
                                            $badge = 'success';
                                        }else{
                                            $cssTitle = 'text-danger';
                                            $badge = 'danger';
                                        }
                                    ?>
                                    <div class="float-right text-right">
                                        <span class="badge badge-{{ $badge }}">
                                            @if($data->status == 1)
                                                Done
                                            @else
                                                {{ $data->percentage }}<small>%</small>
                                            @endif
                                        </span>
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
                                        <br><span class="badge badge-{{ $badgeBobot }}"><small>Bobot: </small>{{ $data->grade }}%</span>
                                    </div>

                                    <span class="text-success">{{ ucwords($data->firstname).' '.ucwords($data->lastname) }} 

                                        @foreach($userLevels as $userLevel)
                                            @if($userLevel->role != null && $userLevel->role == $data->user_level)
                                                | <small>{{ ucwords($userLevel->name) }}</small>
                                            @elseif($userLevel->id == $data->user_level)
                                                | <small>{{ ucwords($userLevel->name) }}</small>
                                            @endif
                                        @endforeach
                                        
                                    </span>
                                    <br><span class="{{ $cssTitle }}"><strong>{{ isset($data->name) ? ucwords($data->name) : 'Belum ada data' }}</strong></span>

                                    <br>
                                    @if(isset($data->category_name))
                                        <span class="text-danger">{{ ucwords($data->category_name) }} | </span>
                                    @endif

                                    @if(isset($data->date))
                                        <span class="text-info"> {{ date('l, d F Y', strtotime($data->date))}} </span> | 
                                    @endif
                                    
                                    <span class="text-info">{{ date('H:i A', strtotime($data->event_start)) .' - '. date('H:i A', strtotime($data->event_end)) }}</span>
                                    
                                    @if($data->status == 1)
                                        <br>Selesai: <span class="text-success">{{ date('l, d F Y', strtotime($data->done_date)) }}</span>
                                    @endif

                                    <br>
                                    @if(isset($data->department_name))
                                        <span class="text-info text-uppercase">{{ $data->department_name }} Departement</span>
                                    @endif

                                    <div class="mt-1">
                                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#minutesModal{{ $data->id }}"><i class="fas fa-eye"></i> </button>

                                        <a href="{{ route($formRouteEdit, $data->id.'&skin='.$skinBack) }}" class='btn btn-icon waves-effect waves-light btn-info t-white'> <i class='fas fa-edit' title='Edit'></i></a>

                                        <!-- Modal -->
                                        <div class="modal fade" id="minutesModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="projectMinutes" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                                <div class="modal-content-img">
                                                    <div class="modal-body text-center">
                                                    <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                        <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/minutes/user/'.$data->image) }}"  />
                                                        <div class="alert alert-warning" id="projectMinutes">
                                                            <h5>
                                                                Aktifitas: <span class="text-muted">{{ ucfirst($data->name) }}</span>
                                                                <br><span class="text-info"><small>Staff: {{ ucwords($data->firstname).' '.ucwords($data->lastname) }}</small></span>
                                                                <span class="text-info"><small> | @if(isset($data->date)) {{ date('l, d F Y', strtotime($data->date))}} @endif</small></span>
                                                            </h5>
                                                            <small>Keterangan:</small>
                                                            <span class="text-muted"><small>{!! ucfirst($data->description) !!}</small></span>
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
                        <div class="col-sm">
                            <?php 
                                #$userMinutes->setPath('minutes-tech?project_id='.$projectTask->project_id.'&task_id='.$projectTask->id);
                            ?>
                            <?php $paginator = $userMinutes; ?>
                            @include('includes.paginator')
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="col-md"> 
                    <a href="{{ route($formRouteCreate,'skin='.$skinBack) }}" class="btn btn-orange mt-1"><i class="fa fa-plus"></i> Create daily report</a>
                    
                    @if(Auth::user()->department_id != 1)
                        <a href="{{ route($formCategoryIndex) }}" class="btn btn-orange mt-1"><i class="fa fa-eye"></i> Lihat kategori</a>
                    @endif
                </div>
            </div>
        </div> <!-- card -->
    @else
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div class='badge badge-info float-left'>0</div>
                <strong>{{ strtoupper($pageTitle) }}</strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="alert alert-warning">Belum ada data.</div>
            </div>
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($formRouteBack) }}" class="btn btn-blue-lini mt-1">Kembali</a>  
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
