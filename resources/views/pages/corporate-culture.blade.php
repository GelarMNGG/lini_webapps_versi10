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
            <h3>Budaya perusahaan</h3>
        </div>
    </div>
@endsection

@section('content-2')
    <div class="row pt-5 pb-5 text-left" id="about" style="background-color: #1e2554; color:#ffffff;">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <img class="img-fluid" src="{{ asset('img/corporate-culture.jpg') }}">
                </div>
                <div class="col-md-8">
                    <div class="col-md pl-5 pr-5 text-box">
                        <p class="text-white mb-5">
                        Kami percaya bahwa kami bisa memberikan pelayanan terbaik terhadap pelanggan dan kerja sama yang baik dengan mitra bisnis kami untuk mewujudkan cita-cita dan tujuan bersama dalam membangun tim yang bekelanjutan dengan menerapkan nilai-nilai perusahaan.
                        </p>
                    </div>
                </div>
            </div>
        </div> 
    </div>
@endsection

@section('content-3')
    <div class="row p-5 justify-content-center text-center">
        <div class="col-md">
            <h2 class="text-blue">Kejujuran</h2>
            <p>Baik secara personal maupun perusahaan.</p>
        </div>
        <div class="col-md">
            <h2 class="text-blue">Profesionalisme</h2>
            <p>Profesional yang mengandung unsur integristasi, tanggung jawab, kompetensi, bekerja keras dan bekerja cerdas.</p>
        </div>
        <div class="col-md">
            <h2 class="text-blue">Keadilan</h2>
            <p>Memberikan rasa berkeadilan dalam mewujudkan win-win solution dengan mitra dan pelanggan.</p>
        </div>
        <div class="col-md">
            <h2 class="text-blue">Kepedulian Sosial</h2>
            <p>Menyisihkan sebagian pendapatan perusahaan untuk kegiatan sosial atau bagi orang/lembaga sosial kemasyarakatan yang membutuhkan.</p>
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