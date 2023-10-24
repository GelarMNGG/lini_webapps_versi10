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
            <h1 class="text-uppercase text-white">Our blogs</h1>
            <h3></h3>
        </div>
    </div>
@endsection

@section('content-2')
    <div class="row pt-5 pb-5 text-left" id="about" style="background-color: #1e2554; color:#ffffff;">
        <div class="container">
            @if(sizeof($blogs) > 0)
            <div class="row">
                <?php $ib=1; if($ib == 6){$ib = 1;} ?>
                @foreach($blogs as $blog)
                    @if($ib == 1)
                        <div class="col-md-8">
                            <a href="{{ route('blog.detail', $blog->slug) }}"><img class="img-fluid" src="{{ asset('img/blogs/'.$blog->image) }}"></a>
                            <h2 class="blog-title text-uppercase">{{ ucfirst($blog->title) }}</h2>
                        </div>
                    @endif
                    <!-- second article -->
                    @if($ib == 2)
                        <div class="col-md-4 float-left">
                            <img class="img-fluid" src="{{ asset('img/blogs/'.$blog->image) }}">
                            <h2 class="text-uppercase">{{ mb_strimwidth(ucfirst($blog->title),0,25,'...') }}</h2>
                            <p class="text-muted">{{ mb_strimwidth(ucfirst($blog->summary),0,207,'...') }}</p>
                        </div>
                    @endif
                    <!-- third article -->
                    @if($ib > 2)
                    <div class="col-md-4 float-left">
                        <img class="img-fluid" src="{{ asset('img/blogs/'.$blog->image) }}">
                        <h4 class="blog-title-2 text-uppercase">{{ mb_strimwidth(ucfirst($blog->title),0,25,'...') }}</h4>
                    </div>
                    @endif
                    <?php $ib++; ?>
                @endforeach
            </div>
            <div class="col-md justify-content-center" style="display:inline-flex">
                {{ $blogs->links() }}
            </div>
            @else
                <div class="text-muted">Belum ada data.</div>
            @endif
        </div> 
    </div>
@endsection

@section('content-3')
    <div class="row p-5 justify-content-center text-center">

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