@extends('layouts.dashboard-report')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daily report';
    //form route
    $formRouteIndex = 'admin-minutes-report.index';
    $formRouteCustomReport = 'admin-minutes-report.customreport';
    $formRouteUpdate= 'admin-minutes-report.update';
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
                                <span><small>{{ strtoupper($adminProfile->department_name) }} department</small></span>
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
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>Kegiatan</th>
                                                    <th>Bobot</th>
                                                    <th>Status</th>
                                                    <th>Tanggal selesai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $i = 1; ?>
                                                @foreach($minutesDatas as $data)
                                                    <tr>
                                                        <td><small>{{ $i }}</small></td>
                                                        <td><small>{{ ucwords($data->firstname).' '.ucwords($data->lastname) }}</small></td>
                                                        <td><strong><small>{{ ucfirst(strtolower($data->name)) }}</small></strong></td>
                                                        <td><strong><small>{{ $data->grade }}%</small></strong></td>
                                                        <td>
                                                            @if($data->status == 1) 
                                                                <span class="text-info"><small>Done</small></span> 
                                                            @else 
                                                                <span class="text-danger"><small>{{ $data->percentage }}%</small></span> 
                                                            @endif
                                                        </td>
                                                        <td><span class="text-success"><small>{{ isset($data->done_date) ? date('l, d F Y', strtotime($data->done_date)) : '-'}}</small></span></td>
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

                                    <button type="button" class="btn btn-orange" data-toggle="collapse" data-target="#custom_sortt" aria-expanded="false" aria-controls="custom_sortt"><i class="fas fa-plus"></i> Pilih user</button>

                                    <a href="{{ route($formRouteIndex) }}" type="button" class="btn btn-secondary">Kembali</a>

                                </div>

                                <?php
                                    #$minutesDatas->setPath('customreport?date='.$date.'&pid='.$publisherId);
                                ?>

                                @if(!isset($date))
                                    <?php /*{{ $minutesDatas->links() }} */?>
                                    <?php $paginator = $minutesDatas; ?>
                                    @include('includes.paginator')
                                @endif

                                <div class="clearfix"></div>
                            </div>
                        </div>


                        <div class="collapse" id="custom_sortt">
                            <form action="{{ route($formRouteCustomReport) }}" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                @csrf
                                
                                <div class="row bg-gray-lini-2">
                                    <div class="col-md mt-2 form-group{{ $errors->has('publisher_id') ? ' has-error' : '' }}">
                                        <label for="publisher_id">Nama <small class="c-red">*</small></label>
                                        <select id="publisher_id" name="publisher_id" class="form-control select2">
                                        <?php 
                                            //qct_id
                                            if(old('publisher_id') != null) {
                                                $publisher_id = old('publisher_id');
                                            }else{
                                                $publisher_id = null;
                                            }
                                        ?>
                                            @if ($publisher_id != null)
                                                @foreach($userProfiles as $userProfile)
                                                    @if($userProfile->id == $publisher_id)
                                                        <option value="{{ $userProfile->id }}">{{ ucwords($userProfile->firstname).' '.ucwords($userProfile->lastname) }}</option>
                                                    @endif
                                                @endforeach
                                                @foreach($userProfiles as $userProfile)
                                                    @if($userProfile->id != $publisher_id)
                                                        <option value="{{ $userProfile->id }}">{{ ucwords($userProfile->firstname).' '.ucwords($userProfile->lastname) }}</option>
                                                    @endif
                                                @endforeach
                                            @else
                                                <option value="0">Pilih staff</option>
                                                @foreach($userProfiles as $userProfile)
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
