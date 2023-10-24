@extends('layouts.dashboard-report')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Apps development report';
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    $statusAppsBadge    = array('','info','purple','danger','success');
    //form route
    $formRouteIndex = 'apps-dev-logs-report.index';
    $formRouteCustomReport = 'apps-dev-logs-report.customreport';
    $formRouteUpdate= 'apps-dev-logs-report.update';
?>
@endsection

@section('content')

    <div class="content mt-2">
        <!-- Start Content-->
        <div class="container-fluid">
            <div class="flash-message d-print-none">
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

            <div class="row">
                <div class="col-md-12">
                    <div class="card-box">
                        <div class="clearfix">
                            <span class="logo float-left">
                                <img src="{{ asset('img/'.$companyInfo->logo) }}" alt="logo {{ $companyInfo->name }}" height="57">
                            </span>
                            <div class="panel-heading text-center text-uppercase">
                                <h3>{{ $pageTitle }}</h3>
                                <span><small>
                                    @if(isset($departmentId))
                                        {{ strtoupper($departmentId) }} department
                                    @else
                                        All department
                                    @endif
                                </small></span>
                                <br><span><small>
                                    @if(isset($date))
                                        {{ date('d F Y', strtotime($date)) }}
                                    @else
                                        {{ date('d F Y') }}
                                    @endif
                                </small></span>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table minute-report">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nama task</th>
                                                    <th>Tanggal mulai</th>
                                                    <th>Tanggal selesai</th>
                                                    @if(Auth::user()->department_id == 5)
                                                        <th>Departement</th>
                                                    @endif
                                                    <th>Programmer</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach ($appsDevLogsDatas as $dataLog)
                                <?php $ia = 1; ?>
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td><strong>{{ ucfirst($dataLog->name) }}</strong></td>
                                        <td>
                                            @if($dataLog->date != null)
                                                <span class="text-info">{{ date('l, d F Y', strtotime($dataLog->date)) }}</span>
                                                <br> <small>{{ $dataLog->event_end ? date('H:i a', strtotime($dataLog->event_end)) : 'Jam tidak tersedia' }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($dataLog->done_date != null)
                                                <span class="text-success">{{ date('l, d F Y', strtotime($dataLog->done_date)) }}</span>
                                                <br> <small>{{ $dataLog->event_end ? date('H:i a', strtotime($dataLog->event_end)) : 'Jam tidak tersedia' }}</small>
                                            @else
                                                <span class="text-danger">-</span>
                                            @endif
                                        </td>
                                        @if(Auth::user()->department_id == 5)
                                            <td>
                                                @if (isset($dataLog->department_id))
                                                    @foreach ($departmensDatas as $dataOne)
                                                        @if ($dataOne->id == $dataLog->department_id)
                                                            <span class="text-danger"><small>[{{ ucwords(strtolower($dataOne->name)) }}]</small></span>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            @if (isset($dataLog->programmer_id))
                                                @foreach ($programmersDatas as $dataThree)
                                                    @if ($dataThree->id == $dataLog->programmer_id)
                                                        <small><span class="text-info">{{ ucwords(strtolower($dataThree->firstname)).' '.ucwords(strtolower($dataThree->lastname)) }}</span></small>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            <?php if($ia == 4){ $ia = 1;} ?>
                                            @if ($dataLog->status > 0)
                                                @foreach ($appsStatusDatas as $dataTwo)
                                                    @if ($dataTwo->id == $dataLog->status)
                                                        <span class="badge badge-{{ $statusAppsBadge[$dataLog->status] }}">
                                                            {{ ucwords(strtolower($dataTwo->name)) }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @endif
                                            <?php
                                                if ($dataLog->percentage <= 10) {
                                                    $badge = 'danger';
                                                }elseif($dataLog->percentage <= 35){
                                                    $badge = 'warning';
                                                }elseif($dataLog->percentage <= 85){
                                                    $badge = 'info';
                                                }else{
                                                    $badge = 'success';
                                                }
                                            ?>
                                            @if ($dataLog->status == 4)
                                                <span class="badge badge-success">
                                                    100<small>%</small>
                                                </span>
                                            @else
                                                <span class="badge badge-{{ $badge }}">
                                                    {{ $dataLog->percentage }}<small>%</small>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-xl-6 col-6">
                                    
                                </div>
                                <div class="col-xl-3 col-6 offset-xl-3 pr-3">
                                    <p class="text-right"><b></b> <strong></strong></p>
                                </div>
                            </div>
                            <div class="row m-0 mb-5">
                                <div class="col-md">
                                    <div class="clearfix">
                                        <h5 class="small text-dark text-uppercase mb-1">prepared by</h5>
                                        <small>
                                            {{ ucwords($adminProfile->firstname).' '.ucwords($adminProfile->lastname) }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-print-none">
                                <div class="float-right">
                                    <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>

                                    <button type="button" class="btn btn-orange disabled" data-toggle="collapse" data-target="#custom_sortt" aria-expanded="false" aria-controls="custom_sortt" disabled><i class="fas fa-plus"></i> Pilih user</button>

                                    <a href="{{ route($formRouteIndex) }}" type="button" class="btn btn-secondary">Kembali</a>

                                </div>

                                <?php
                                    #$appsDevLogsDatas->setPath('customreport?date='.$date.'&pid='.$publisherId);
                                ?>

                                @if(!isset($date))
                                    {{ $appsDevLogsDatas->links() }}
                                @endif

                                <div class="clearfix"></div>
                            </div>
                        </div>


                        <div class="collapse" id="custom_sortt">
                            <form action="{{ route($formRouteCustomReport) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                @csrf
                                
                                <div class="row bg-gray-lini-2">
                                    <div class="col-md mt-2 form-group{{ $errors->has('programmer_id') ? ' has-error' : '' }}">
                                        <label for="programmer_id">Programmer <small class="c-red">*</small></label>
                                        <select id="programmer_id" name="programmer_id" class="form-control select2">
                                        <?php 
                                            //qct_id
                                            if(old('programmer_id') != null) {
                                                $programmer_id = old('programmer_id');
                                            }else{
                                                $programmer_id = null;
                                            }
                                        ?>
                                            @if ($programmer_id != null)
                                                @foreach($programmersDatas as $userProfile)
                                                    @if($userProfile->id == $programmer_id)
                                                        <option value="{{ $userProfile->id }}">{{ ucwords($userProfile->firstname).' '.ucwords($userProfile->lastname) }}</option>
                                                    @endif
                                                @endforeach
                                                @foreach($programmersDatas as $userProfile)
                                                    @if($userProfile->id != $programmer_id)
                                                        <option value="{{ $userProfile->id }}">{{ ucwords($userProfile->firstname).' '.ucwords($userProfile->lastname) }}</option>
                                                    @endif
                                                @endforeach
                                            @else
                                                <option value="0">Pilih staff</option>
                                                @foreach($programmersDatas as $userProfile)
                                                    <option value="{{ $userProfile->id }}">{{ ucwords($userProfile->firstname).' '.ucwords($userProfile->lastname) }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="w-100"></div>
                                    <div class="col-md mt-2 form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                                        <label for="date">Tanggal <small class="c-red">*</small></label>
                                        <input type="date" class="form-control" name="date" value="{{ old('date') }}">
                                        @if ($errors->has('date'))
                                            <small class="form-text text-muted">
                                                <strong>{{ $errors->first('date') }}</strong>
                                            </small>
                                        @endif
                                    </div>
                                    <div class="w-100"></div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for=""></label>
                                            <input type="submit" class="btn btn-orange" name="submit" value="Pilih">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>


                    </div>
                </div>
            </div>
            <!-- end row -->        
            
        </div> <!-- container-fluid -->
    </div> <!-- content -->

@endsection

@section ('script')

@endsection
