<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | {{ ucfirst($companyInfo->name) }} - {{ $companyInfo->slogan}}</title>
    <meta content="{{ ucfirst($companyInfo->brief) }}" name="description" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('admintheme/images/favicon.ico') }}">
    <!-- Bootstrap Css -->
    <link href="{{ asset('admintheme/css/bootstrap.min.css') }}" id="bootstrap-stylesheet" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('admintheme/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('admintheme/css/app.min.css') }}" id="app-stylesheet" rel="stylesheet" type="text/css" />
    <!-- style -->
    <link href="{{ asset('admintheme/css/style.css') }}" id="app-stylesheet" rel="stylesheet" type="text/css" />
</head>
<body class="bg-blue-lini">
    <div id="app" class="account-pages mb-5">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <!-- Vendor js -->
    <script src="{{ asset('admintheme/js/vendor.min.js') }}"></script>
    <!-- Validation js (Parsleyjs) -->
    <script src="{{ asset('admintheme/libs/parsleyjs/parsley.min.js') }}"></script>
    <!-- validation init -->
    <script src="{{ asset('admintheme/js/pages/form-validation.init.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('admintheme/js/app.min.js') }}"></script>
</body>
</html>
