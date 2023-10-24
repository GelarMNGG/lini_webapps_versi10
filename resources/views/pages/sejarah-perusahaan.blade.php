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
            <h1 class="text-uppercase text-white">About us</h1>
            <h3>Sejarah LINI</h3>
        </div>
    </div>
@endsection

@section('content-2')
    <div class="row pt-5 pb-5 text-left" id="about" style="background-color: #1e2554; color:#ffffff;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <img class="img-fluid" src="{{ asset('img/perjalanan-lini.jpg') }}">
                </div>
                <div class="col-md-8">
                    <div class="col-md pl-5 pr-5 text-white mb-5 text-box">
                        <p> PT Lima Inti Sinergi (LINI) didirikan pada tahun 2013 dan memulai usahanya sebagai penyedia layanan jasa di bidang infrastruktur telekomunikasi.LINI memulai bisnisnya dengan layanan pertama kami yaitu instalasi BTS. Dengan mendapatkan izin berdasarkan akta Notaris No. 12 dan telah disahkan oleh Menteri Hukum dan Hak Asasi Manusia Republik Indonesia pada tahun 2013.</p>

                        <p>Pada tahun 2016, LINI memberanikan diri untuk mengembangkan bisnisnya setelah dipercaya untuk mengerjakan project Delivery pertamanya dari PT Dayamitra Telekomunikasi. Tak hanya itu, di tahun yang samapun kami menambah lingkup pekerjaan lainnya, yaitu: CME, IMB, dan Power PLN.</p>

                        <p>Sejak saat itu, atas izin dan rida Allah Subhanahu wa ta’ala, perkembangan LINI semakin cepat dan terus beranjak hingga sampai saat ini kami memiliki 12 lingkup pekerjaan dalam industri infrastruktur telekomunikasi.</p>

                        <p>Dalam industri infrastruktur telekomunikasi yang sangat kompetitif saat ini, LINI berkomitmen untuk memberikan pengalaman terbaik bagi para customer melalui peningkatan kualitas layanan dan inovasi berkelanjutan.</p>
                    </div>
                </div>
            </div>
        </div> 
    </div>
@endsection

@section('content-3')
    <div class="row p-5 justify-content-center text-center text-box">
        <img class="img-fluid" src="{{ asset('img/milestone-pt-lima-inti.jpg') }}" title="milestone">
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