<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        html{
            font-family: sans-serif;
            font-size: .9rem;
            color:#323a46;
            text-align: center;
            height:100%;
        }
        body{margin:0 !important;min-height:100%;background:#E5E2E2;-webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; }
        .logo{
            width: auto;
            height: 77px !important;
        }
        .email-confirmation{
            margin-top:.5rem;
            font-size: .7rem;
            float:right;
            color: #001A9F;
            text-transform: uppercase;
        }
        p {line-height: 1.7rem;}
        .button-box{
            margin-top:15px;
            margin-bottom: 15px;
        }
        .container-header, .container{
            width: 100%;
            max-width: 576px;
            text-align: left;
            margin-right: auto;
            margin-left: auto; 
        }
        .container {
            padding: 12px;
            background-color: #ECECEC;
            border-radius: 25px 17px;
        }
        .button{
            padding: 15px 33px;
            margin: 15px 0px;
            background-color: #71b6f9;
            border-radius:5px;
            color:#ffffff;
            text-decoration: none;
            display: inline-block;
            text-transform: uppercase;
        }
        .dark{ color:#959596; }
        .footer{
            border-top: 1px solid #323a46;
            background-color:#e6e3e3;
            padding:5px;
        }
        .blue{ color:#71b6f9; }
        .small{font-size: .9rem;}
        .text-center{text-align: center;}
        @media (max-width: 575px) {
            .container-header, .container { max-width: 337px; } 
            span.email-confirmation{
                margin-top:1rem;
                font-size: 1rem !important;
            }
        }
        @media (min-width: 576px) {
            .container-header, .container { max-width: 540px; } 
        }
        @media (min-width: 768px) {
            .container-header, .container { max-width: 720px; } 
        }
        @media (min-width: 992px) {
            .container-header, .container { max-width: 960px; } 
        }
        @media (min-width: 1200px) {
            .container-header, .container { max-width: 1140px; } 
        }
    </style>
</head>
<body>
    <div class="container-header">
        <div>
            <img class="logo" src="{{ asset('img/'.$companyInfo->logo) }}">
            <span class="email-confirmation"><br>Email confirmation</span>
        </div>
    </div>
    <div class="container">
        <p>Halo <strong>{{ ucfirst($email_data['name']) }}</strong>,</p>
        <p>
            Selamat datang di {{ ucwords($companyInfo->name) }}!
            <br>Terima kasih telah mendaftar, silahkan klik link berikut untuk mengaktifkan email kamu.
        </p>

        <p>
        @if($email_data['type'] == 'tech')
            <div class="button-box"><a href="http://ptlini.co.id/webapps/tech/{{ $email_data['link'] }}?code={{ $email_data['verification_code'] }}" class="button">Konfirmasi email</a></div>
        @else
            <div class="button-box"><a href="http://ptlini.co.id/webapps/{{ $email_data['link'] }}?code={{ $email_data['verification_code'] }}" class="button">Konfirmasi email</a></div>
        @endif
        </p>
        
        <p>Jika kamu tidak melakukan pendaftaran, abaikan email notifikasi ini.</p>

        <p>
            Terima kasih, <br>
            
            <span class="dark">{{ ucwords($companyInfo->name) }}</span>
        </p>

        <div class="footer">
            <strong class="dark">Kesulitan klik link di atas? Copy dan paste link berikut di web browser kamu:</strong><br/>
            @if($email_data['type'] == 'tech')
                <em class="small blue">http://ptlini.co.id/webapps/tech/{{ $email_data['link'] }}?code={{ $email_data['verification_code'] }}</em>
            @else
                <em class="small blue">http://ptlini.co.id/webapps/{{ $email_data['link'] }}?code={{ $email_data['verification_code'] }}</em>
            @endif
        </div>

        <div class="text-center">
            <p>
                <img class="" src="{{ asset('img/email/key-lini.png') }}">
            </p>
        </div>
    </div>
    <p class="small dark">Developed by IT Department</p><br>
</body>
</html>