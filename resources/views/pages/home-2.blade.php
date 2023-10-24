<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ ucwords($companyInfo->name) }}</title>

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('admintheme/images/favicon.ico') }}">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > div > a {
                color:#28316A;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
                text-align:center;
            }
            .logo-box{
                margin-bottom:27px;
            }
            .logo{
                max-width:125px;
                height:auto;
            }
            @media (max-width:576px){
                .title{
                    font-size:57px !important;
                }
                .links{
                    text-align:center;
                }
            }
            .p-4{padding:51px;}
            .border-radius{border-radius:9px;}
        </style>
        <!-- style -->
        <link href="{{ asset('admintheme/css/style.css') }}" id="app-stylesheet" rel="stylesheet" type="text/css" />
    </head>
    <body class="bg-blue-lini">
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="card bg-gray-lini border-radius">
                    <div class="card-body p-4">
                        <div class="logo-box">
                            <img class="logo" src="{{ asset('img/'.$companyInfo->logo) }}" alt="{{ ucwords($companyInfo->name) }}">
                        </div>
                        @if (Route::has('login'))
                            <div class="links">
                                @auth
                                    <a href="{{ url('/home') }}">Home</a>
                                @else
                                <div class="icon-box-login">
                                    <a href="{{ route('cust.login') }}"><img class="icon-login" src="{{ asset('admintheme/images/icon/icon-pelanggan.png') }}"></a>
                                    <a href="{{ route('cust.login') }}">Pelanggan</a>
                                </div>
                                <div class="icon-box-login">
                                    <a href="{{ route('tech.login') }}"><img class="icon-login" src="{{ asset('admintheme/images/icon/icon-teknisi.png') }}"></a>
                                    <a href="{{ route('tech.login') }}">Teknisi</a>
                                </div>
                                <div class="icon-box-login">
                                    <a href="{{ route('login') }}"><img class="icon-login" src="{{ asset('admintheme/images/icon/icon-user.png') }}"></a>
                                    <a href="{{ route('login') }}">User</a>
                                </div>
                                <div class="icon-box-login">
                                    <a href="{{ route('admin.login') }}"><img class="icon-login" src="{{ asset('admintheme/images/icon/icon-admin.png') }}"></a>
                                    <a href="{{ route('admin.login') }}">Admin</a>
                                </div>
                                <div class="icon-box-login">
                                    <a href="http://erp.limaintisinergi.com"><img class="icon-login" src="{{ asset('admintheme/images/icon/icon-erp.png') }}"></a>
                                    <a href="http://erp.limaintisinergi.com">ERP</a>
                                </div>
                                @endauth
                            </div>
                        @endif
                        <div class="title m-b-md">
                            LINI's Integrated System
                        </div>
        
                        <div class="links">
                            Developed by IT Department
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
