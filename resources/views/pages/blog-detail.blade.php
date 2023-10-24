@extends('layouts.app-second-page')

@section ('data')
<?php ### custom data
    
?>
@endsection

@section('header')
    <link href="{{ asset('css/fontawesome/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/reset.css') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/favicon/favicon.ico') }}">
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
@endsection

@section('slider')
    <div class="row" id="anchor" style="background: linear-gradient(165.04deg, rgb(240, 78, 88) 0%, rgb(95, 85, 216) 100%); padding: 0px; overflow: visible; height: 251px;">
        <div class="mx-auto my-auto pt-5 text-center">
            <h1 class="text-uppercase text-white">{{ $blog->title }}</h1>
            <h3 class="text-orange">Umum</h3>
        </div>
    </div>
@endsection

@section('content-2')
    <div class="row pt-5 pb-5 text-left" id="about" style="background-color: #1e2554; color:#ffffff;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <img class="img-fluid" src="{{ asset('img/blogs/'.$blog->image) }}">
                </div>
                <div class="col-md-8">
                    <div class="col-md p-5 text-white mb-5 text-box">
                        <p class="lead">{{ ucfirst($blog->summary) }}</p>
                        <small class="text-muted">oleh: Admin</small>
                    </div>
                </div>
            </div>
        </div> 
    </div>
@endsection

@section('content-3')
    <div class="row p-5 justify-content-left text-left text-box">
        <div class="container pl-5 pr-5">
            {!! $blog->content !!}
        </div>
    </div>
    <div class="row">
        <div class="container pl-5 pr-5 mb-3">
            <a class="tombol-kembali text-uppercase" href="{{ route('blog') }}">Kembali</a>
        </div>
    </div>
@endsection

@section('footer')
    
    @include('includes.footer-second-page')
    <script>
        window.onscroll = function() {myFunction()};

        var navbar = document.getElementById("navbar");
        var sticky = navbar.offsetTop;

        function myFunction() {
            if (window.pageYOffset >= sticky) {
                navbar.classList.add("sticky")
            } else {
                navbar.classList.remove("sticky");
            }
        }
    </script>

@endsection