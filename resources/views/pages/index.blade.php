@extends('layouts.app')

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
    <div class="carousel-inner w-100" role="listbox">
        <ol class="carousel-indicators carousel-indicators-1">
            @for($i = 1; $i <= $slidersCount; $i++)
                <li data-target="#carousel-slider" data-slide-to="{{ $i }}" class="{{ $i == 1 ? 'active' : '' }}"> 0{{$i}}  &nbsp.</li>
            @endfor
        </ol>
        <?php $is=1; ?>
        @foreach($sliders as $slider)
            <div class="carousel-item{{ $is == 1 ? ' active' : '' }}">
                <div>
                    <img class="img-fluid w-100" src="{{ asset('img/sliders/'.$slider->image) }}">
                </div>
                <div class="carousel-caption d-md-block text-center">
                    <div><img src="{{ asset('img/'.$companyInfo->logo) }}"></div>
                    <h5 class="slider-title text-yellow">{{ $slider->title }}</h5>
                    <p class="slider-text">
                        <a href="#">{{ $slider->description }}</a>
                    </p>
                </div>
            </div>
            <a class="carousel-control-prev" href="#carousel-slider" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carousel-slider" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
            <?php $is++; ?>
        @endforeach
    </div>
@endsection

@section('content-2')
    <div class="row pt-5 pb-5 text-left" id="about" style="background-color: #1e2554; color:#ffffff;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <img class="img-fluid" src="{{ asset('img/bg-about.png') }}">
                </div>
                <div class="col-md-8">
                    <div class="col-md pl-5 pr-5 mb-5">
                        <h2 class="text-uppercase">We... as your partner,</h2>
                        <h5>kami menyediakan solusi untuk masalah teknologi komunikasi Anda</h5>
                    </div>
                    <div class="w-100"></div>
                    <div class="col-md pl-5 pr-5 about-box">
                        <p class="text-white mb-5">
                            <span class="text-uppercase text-muted"><strong>Latar belakang</strong></span>
                            <br>Kami bangga bahwa team kami memiliki komitmen tinggi terhadap setiap proyek yang dikerjakan, serta menjunjung tinggi perbaikan dalam aspek pekerjaan, kejujuran, dan integritas yang merupakan nilai dari perusahaan kami. Karakter inilah yang membuat kami konsisten fokus kepada customer, berusaha memahami kebutuhan customer, dan mampu memberikan solusi sesuai dengan kebutuhan mereka.
                        </p>
                    </div>
                </div>
                <div class="col-md">
                    <p class="mb-5">
                        <span class="text-uppercase text-muted"><strong>Visi Kami</strong></span>
                        <br>Menjadi perusahaan penyedia layanan telekomunikasi bertaraf nasional yang terpercaya dan tanggap perubahan demi kesejahteraan berbangsa dan bernegara di Indonesia.
                    </p>
                    <p>
                        <span class="text-uppercase text-muted"><strong>Misi Kami</strong></span>
                        <ul class="text-white">
                            <li>Menjadikan perusahaan sebagai sarana mendapat ridho Allah Subhanahu Wata’ala dengan senantiasa, melakukan perbaikan sesuai syariat islam, sehingga membawa kebaikan bagi karyawan dan banyak orang.
                            </li>
                            <li> Menumbuhkan nilai transparasi dan nilai percaya yang berkesinambungan antara perusahaan dengan karyawan, customer, mitra, dan stakeholder lainnya.
                            </li>
                            <li> Meningkatkan kemampuan Sumber Daya Manusia dan infrastruktur perusahaan secara berkesinambungan demi menunjang pelayanan yang terbaik pada seluruh pelanggan.</li>
                            <li> Membawa standard baru <em>Customer Experience</em> dengan solusi yang saling menguntungkan melalui layanan luar biasa dan konsisten.</li>
                        </ul>
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <img class="img-fluid mb-2 rounded" src="{{ asset('img/tower-bts.jpg') }}">
                    <img class="img-fluid rounded" src="{{ asset('img/tower-dlihat-dari-dalam.jpg') }}">
                </div>
            </div>
        </div> 
    </div>
@endsection

@section('content-3')
    <div id="our-services" class="row pt-5 pb-5 text-center" style="background: linear-gradient(124.41deg, #F04E58 0%, #5F55D8 100%);">
        <div class="container pl-5 pt-5 our-services-box">
            <div class="row">
                <div class="col-md pl-5 pr-5 mb-5">
                    <h2 class="text-uppercase">Our services</h2>
                </div>
                <div class="w-100"></div>
                <div class="col-md pl-5 pr-5">

                    @foreach($services as $service)
                    <a class="icon-service-box" href="{{ route('our-services', $service->slug) }}">
                        <img class="icon-service" src="{{ asset('img/services/icon/'.$service->icon) }}" title="">
                        <br><span class="icon-title">{{ $service->name }}</span>
                    </a>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
    <div class="row p-5 justify-content-center text-center">
        <div class="col-md">
            <h2 class="text-uppercase">Our Achievements</h2>
        </div>
        <div class="w-100"></div>
        <div class="col-md">
            <img class="img-certificate" src="{{ asset('img/certificates/zte-best-partner.jpg') }}" title="ZTE Best Partner">
            <img class="img-certificate" src="{{ asset('img/certificates/best-new-build-project-vendor-3.jpg') }}" title="Best New Build Project Vendor 3">
            <img class="img-certificate" src="{{ asset('img/certificates/tbgg-best-new-build-project-vendor-3.jpg') }}" title="Best New Build Project Vendor 3">
            <img class="img-certificate" src="{{ asset('img/certificates/achievement-mitratel-project-bts-perbatasan.jpg') }}" title="Mitratel Mitra Pelaksana Project BTS Perbatasan tahun 2017">
        </div>
    </div>
    <hr>
    <div class="row text-center pt-5">
        <div class="col-md pl-5 pr-5 mb-5">
            <h2 class="text-uppercase">Clients & Partners</h2>
            <p class="text-muted">Lima Inti Sinergi bersyukur dan terus meningkatkan kualitas baik pelayanan maupun produk dan jasa yang diberikan. 
                <br>Kami terus melakukan improvement dan berkolaborasi dengan klien dan partner seperti: 
                @foreach($clients as $client)
                    {{ ucwords($client->name).', ' }}
                @endforeach
                dan lainnya.
            </p>
        </div>
        <div class="w-100"></div>
        <div class="container">
            <div class="row pl-5 pr-5 justify-content-center">
                @foreach($clients as $client)
                    <div class="logo-clients-box">
                        <img class="logo-clients" src="{{ asset('img/clients/'.$client->logo) }}" alt="{{ $client->name}}">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('footer')
    
    @include('includes.footer')
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