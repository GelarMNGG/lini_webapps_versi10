@extends('layouts.dashboard-form')

@section ('data')
<?php ### custom data
    $pageTitle      = 'Lihat troubleshooting';
    $formRouteIndex = 'tech-troubleshooting.index';
?>
@endsection

@section('content')
    <div class="row pt-5 pb-5 text-left" style="background-color: #28316A; color:#ffffff;">
        <div class="container">
            <h1 class="t-orange">{{ ucwords($troubleshootingData->title) }}</h1>
            <div class="row mt-5">
                <div class="col-md-4">
                    <img class="img-fluid" src="{{ asset('img/blogs/'.$troubleshootingData->image) }}">
                </div>
                <div class="col-md-8">
                    <div class="col-md text-white text-box">
                        <p class="lead">{{ ucfirst($troubleshootingData->problem) }}</p>
                        <small class="text-muted">oleh: 
                            @if($troubleshootingData->publisher_type == 'tech')
                                @foreach($techDatas as $data3)
                                    @if($data3->id == $troubleshootingData->publisher_id)
                                        <span>{{ ucwords($data3->firstname).' '.ucwords($data3->lastname) }}</span>
                                    @endif
                                @endforeach
                            @else
                                @foreach($userDatas as $user)
                                    @if($user->id == $troubleshootingData->publisher_id)
                                        <span>{{ ucwords($user->firstname).' '.ucwords($user->lastname) }}</span>
                                    @endif
                                @endforeach
                            @endif
                        </small>
                        <br><span class="text-secondary"><small>{{ $troubleshootingData->view ? $troubleshootingData->view : '0' }} <i class="fas fa-eye"></i></small></span>
                        | <span class="text-info"><small>{{ $troubleshootingData->date ? date('l, d F Y',strtotime($troubleshootingData->date)) : '-' }}</small></span>
                    </div>
                </div>
            </div>
        </div> 
    </div>

    <div class="row justify-content-left text-left text-box">
        <div class="container blog-box">
            {!! $troubleshootingData->solution !!}
        </div>
    </div>
    <div class="row">
        <div class="container blog-box">
            <a class="btn btn-blue-lini" href="{{ route($formRouteIndex) }}">Kembali</a>
        </div>
    </div>
@endsection