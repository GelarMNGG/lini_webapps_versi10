@extends('layouts.dashboard-form')

@section ('data')
<?php ### custom data
    $pageTitle      = 'Lihat Artikel';
    $formRouteIndex = 'admin-blog.index';
?>
@endsection

@section('content')
    <div class="row pt-5 pb-5 text-left" style="background-color: #28316A; color:#ffffff;">
        <div class="container">
            <h1 class="t-orange">{{ ucwords($blog->title) }}</h1>
            <div class="row mt-5">
                <div class="col-md-4">
                    <img class="img-fluid" src="{{ asset('img/blogs/'.$blog->image) }}">
                </div>
                <div class="col-md-8">
                    <div class="col-md text-white text-box">
                        <p class="lead">{{ ucfirst($blog->summary) }}</p>
                        <small class="text-muted">oleh: 
                            @if($blog->author_type == 'admin')
                                @foreach($admins as $admin)
                                    @if($admin->id == $blog->author_id)
                                        <span>{{ ucwords($admin->firstname).' '.ucwords($admin->lastname) }}</span>
                                    @endif
                                @endforeach
                            @else
                                @foreach($users as $user)
                                    @if($user->id == $blog->author_id)
                                        <span>{{ ucwords($user->firstname).' '.ucwords($user->lastname) }}</span>
                                    @endif
                                @endforeach
                            @endif
                        </small>
                        <br><span class="text-secondary"><small>{{ $blog->views ? $blog->views : '0' }} <i class="fas fa-eye"></i></small></span>
                        | <span class="text-info"><small>{{ $blog->created_at ? date('l, d F Y',strtotime($blog->created_at)) : '-' }}</small></span>
                    </div>
                </div>
            </div>
        </div> 
    </div>

    <div class="row justify-content-left text-left text-box">
        <div class="container blog-box">
            {!! $blog->content !!}
        </div>
    </div>
    <div class="row">
        <div class="container blog-box">
            <a class="btn btn-blue-lini" href="{{ route($formRouteIndex) }}">Kembali</a>
        </div>
    </div>
@endsection