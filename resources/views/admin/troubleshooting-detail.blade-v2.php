@extends('layouts.dashboard-form')

@section ('data')
<?php ### custom data
    $pageTitle      = 'Lihat troubleshooting';
    $formRouteIndex = 'admin.index';
?>
@endsection

@section('content')
    <div class="row pt-5 pb-5 text-left" style="background-color: #28316A; color:#ffffff;">
        <div class="container">
            <h1 class="t-orange">{{ ucwords($dataTroubleshooting->title) }}</h1>
            <div class="row mt-5">
                <div class="col-md-4">
                    @if(isset($dataTroubleshooting->image) && $dataTroubleshooting->image != null)
                        <img class="w-100 img-fluid img-thumbnail" src="{{ asset('img/troubleshooting/'.$dataTroubleshooting->image) }}">
                    @else
                        <img class="w-100 img-fluid img-thumbnail" src="{{ asset('img/troubleshooting/default.png') }}">
                    @endif
                </div>
                <div class="col-md-8">
                    <div class="col-md text-white text-box">
                        <p class="lead">{{ ucfirst($dataTroubleshooting->problem) }}</p>
                        <small class="text-muted">oleh: 
                            @if($dataTroubleshooting->publisher_type == 'user')
                                @foreach($users as $user)
                                    @if($user->id == $dataTroubleshooting->publisher_id)
                                        <span>{{ ucwords($user->firstname).' '.ucwords($user->lastname) }}</span>
                                    @endif
                                @endforeach
                            @elseif($dataTroubleshooting->publisher_type == 'tech')
                                @foreach($techs as $tech)
                                    @if($tech->id == $dataTroubleshooting->publisher_id)
                                        <span>{{ ucwords($tech->firstname).' '.ucwords($tech->lastname) }}</span>
                                    @endif
                                @endforeach
                            @else
                                @foreach($admins as $admin)
                                    @if($admin->id == $dataTroubleshooting->publisher_id)
                                        <span>{{ ucwords($admin->firstname).' '.ucwords($admin->lastname) }}</span>
                                    @endif
                                @endforeach
                            @endif
                        </small>
                        <br><span class="text-secondary"><small>{{ $dataTroubleshooting->view ? $dataTroubleshooting->view : '0' }} <i class="fas fa-eye"></i></small></span>
                        | <span class="text-info"><small>{{ $dataTroubleshooting->date ? date('l, d F Y',strtotime($dataTroubleshooting->date)) : '-' }}</small></span>
                    </div>
                </div>
            </div>
        </div> 
    </div>

    <div class="row justify-content-left text-left text-box">
        <div class="container blog-box">
            {!! $dataTroubleshooting->solution !!}
        </div>
    </div>
    <div class="row">
        <div class="container blog-box">
            <a class="btn btn-blue-lini" href="{{ route($formRouteIndex) }}">Kembali</a>
        </div>
    </div>
@endsection