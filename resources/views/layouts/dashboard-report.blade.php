<!DOCTYPE html>
<html lang="en">
    <head>

        @yield ('data')

        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        @if(isset($fileTitle))
            <title>{{ $fileTitle }} - {{ $fileDate }}</title>
        @else
            <title>Dashboard | {{ ucfirst($companyInfo->name) }} - {{ $companyInfo->slogan}}</title>
        @endif

        <meta content="{{ ucfirst('$companyInfo->brief') }}" name="description" />
        <meta content="{{ ucfirst($companyInfo->name) }}" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('admintheme/images/favicon.ico') }}">
        <!-- Bootstrap Css -->
        <link href="{{ asset('admintheme/css/bootstrap.min.css') }}" id="bootstrap-stylesheet" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('admintheme/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('admintheme/css/app.min.css') }}" id="app-stylesheet" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admintheme/css/style.css') }}" id="app-stylesheet" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admintheme/css/reset.css') }}" id="app-stylesheet" rel="stylesheet" type="text/css" />
        <script>
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
            ]) !!};
        </script>
    </head>
    <body>
        <div id="preloader">
            <div id="status">
                <div class="spinner">Loading...</div>
            </div>
        </div>
        <!-- Begin page -->
        <div id="wrapper">

            @include ('includes.dashboard.topbar')

            <!-- ========== Left Sidebar Start ========== -->

            @include ('includes.dashboard.left-sidebar')

            <!-- Left Sidebar End -->
            <div class="content-reset content-page">
                
                @include('includes.dashboard.date-remaining')
                
                @yield ('content')

                    
                <!-- Footer Start -->

                @include('includes.dashboard.navbar-bottom')

                <footer class="footer navbar-fixed-bottom">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                {{ $companyInfo->year }} - <?php echo date('Y'); ?> &copy; {{ ucwords($companyInfo->name) }}
                            </div>
                            <div class="col-md-6">
                                <div class="text-md-right footer-links d-none d-sm-block">
                                    <a href="javascript:void(0);">FAQ</a>
                                    <a href="javascript:void(0);">Bantuan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- end Footer -->
            </div>
        </div>
        <!-- END wrapper -->
        <!-- Vendor js -->
        <script src="{{ asset('admintheme/js/vendor.min.js') }}"></script>

        <!-- App js -->
        <script src="{{ asset('admintheme/js/app.min.js') }}"></script> 

        @yield('script')
        
    </body>
</html>