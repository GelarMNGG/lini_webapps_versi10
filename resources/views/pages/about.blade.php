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
            <h3>Tentang perusahaan</h3>
        </div>
    </div>
@endsection

@section('content-2')
    <div class="row pt-5 pb-5 text-left bg-image-1" id="about" style="background-color: #1e2554; color:#ffffff; min-height:350px;">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <img class="img-fluid" src="">
                </div>
                <div class="col-md-8">
                    <div class="col-md pl-5 pr-5">
                        <p class="text-white mb-5">
                        <h2>Bekerjalah
                        Untuk Akhiratmu</h2>
                        seolah-olah kamu akan mati esok hari,
                        dan bekerjalah untuk kehidupan duniamu
                        seolah-olah kamu akan hidup selamanya.
                        <br><span class="text-muted">Ali Bin Abi Thalip</span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md mt-5 pt-5 pb-5 bg-blue text-orange text-center justify-content-center">
                <h2>Tentang LINI</h2>
                <hr class="hr-orange-75">
                <div class="w-75 mx-auto">
                    <p>
                    PT Lima Inti Sinergi atau yang dikenal sebagai LINI merupakan perusahaan yang bergerak di bidang Layanan Jasa Telekomunikasi dan Teknologi Informasi. Berkontribusi sejak tahun 2013, dengan membangun infrastruktur telekomunikasi untuk memenuhi kebutuhan telekomunikasi masyarakat di area pedalaman Indonesia.</p>
        
                    <p>Dengan pengalaman yang panjang dan reputasi yang baik di sektor infrastruktur telekomunikasi, kami telah bekerja sama dengan perusahaan telekomunikasi terkemuka di Indonesia.</p>
        
                    <p>Kami bangga bahwa team kami memiliki komitmen tinggi terhadap setiap proyek yang dikerjakan, serta menjunjung tinggi perbaikan dalam aspek pekerjaan, kejujuran, dan integritas yang merupakan nilai dari perusahaan kami. Karakter inilah yang membuat kami konsisten fokus kepada customer, berusaha memahami kebutuhan customer, dan mampu memberikan solusi sesuai dengan kebutuhan mereka.</p>
        
                    <p>Dengan demikian, citra kami dibangun dari kemampuan kami dalam memberikan layanan jasa telekomunikasi yang berkualitas, keahlian yang luas, dan hubungan jangka panjang dengan customer.
                    </p>
                </div>
            </div>
        </div> 
    </div>
@endsection

@section('content-3')
    <div class="row bg-blue justify-content-center text-orange pt-5 pb-5">
        <div>
            <h2>Pendiri LINI</h2>
        </div>
    </div>
    <div class="row pt-5 pb-5 text-box-parallax justify-content-center img-pendiri-box">
        <div class="bg-blue mt-5 mb-5 m-2 p-3 text-center img-pendiri-box-top">
            <img class="img-fluid img-pendiri rounded" src="{{ asset('img/pak-bambang.png') }}" title="logo lima inti sinergi">
        </div>
        <div class="bg-blue mt-5 mb-5 m-2 p-3 text-center img-pendiri-box-bottom">
            <img class="img-fluid img-pendiri rounded" src="{{ asset('img/pak-koko.png') }}" title="logo lima inti sinergi">
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