<!DOCTYPE html>
<html lang="en">
    <head>

        @yield ('data')

        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard | {{ ucfirst($companyInfo->name) }} - {{ $companyInfo->slogan}}</title>
        <meta content="{{ ucfirst('$companyInfo->brief') }}" name="description" />
        <meta content="{{ ucfirst($companyInfo->name) }}" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('admintheme/images/favicon.ico') }}">

        <!-- Chartist Chart CSS -->
        <link rel="stylesheet" href="{{ asset('admintheme/libs/chartist/chartist.min.css') }}">

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

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-reset">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        @include('includes.dashboard.date-remaining')
                        
                        @yield ('content')
                        
                    </div> <!-- container-fluid -->

                </div> <!-- content -->

                <!-- Footer Start -->
                @include('includes.dashboard.navbar-bottom')

                <footer class="footer">
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

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->


        </div>
        <!-- END wrapper -->

        <!-- Vendor js -->
        <script src="{{ asset('admintheme/js/vendor.min.js') }}"></script>

        <!--Chartist Chart-->
        <script src="{{ asset('admintheme/libs/chartist/chartist.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/chartist/chartist.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/chartist/chartist-plugin-tooltip.min.js') }}"></script>

        <!-- Init js -->
        <!-- <script src="{{ asset('admintheme/js/pages/chartist.init.js') }}"></script> -->

        <!-- App js -->
        <script src="{{ asset('admintheme/js/app.min.js') }}"></script>

        @yield('script')

    </body>
</html>