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
        <!-- Plugins css -->
        <link href="{{ asset('admintheme/libs/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet" />
        <link href="{{ asset('admintheme/libs/switchery/switchery.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admintheme/libs/multiselect/multi-select.css') }}"  rel="stylesheet" type="text/css" />
        <link href="{{ asset('admintheme/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admintheme/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('admintheme/libs/switchery/switchery.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('admintheme/libs/bootstrap-timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
        <link href="{{ asset('admintheme/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet">
        <link href="{{ asset('admintheme/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" rel="stylesheet">
        <link href="{{ asset('admintheme/libs/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
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

        <!-- Plugins Js -->
        <script src="{{ asset('admintheme/libs/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/switchery/switchery.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/multiselect/jquery.multi-select.js') }}"></script>
        <script src="{{ asset('admintheme/libs/jquery-quicksearch/jquery.quicksearch.min.js') }}"></script>

        <script src="{{ asset('admintheme/libs/select2/select2.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/jquery-mask-plugin/jquery.mask.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/moment/moment.js') }}"></script>
        <script src="{{ asset('admintheme/libs/bootstrap-timepicker/bootstrap-timepicker.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/bootstrap-colorpicker/bootstrap-colorpicker.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{ asset('admintheme/libs/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
        <!-- upload -->
        <script src="{{ asset('admintheme/js/pages/form-fileupload.init.js') }}"></script>
        <!-- Validation js (Parsleyjs) -->
        <script src="{{ asset('admintheme/libs/parsleyjs/parsley.min.js') }}"></script>
        <!-- validation init -->
        <script src="{{ asset('admintheme/js/pages/form-validation.init.js') }}"></script>

        <!-- Init js-->
        <script src="{{ asset('admintheme/js/pages/form-advanced.init.js') }}"></script>

        <!-- App js -->
        <script src="{{ asset('admintheme/js/app.min.js') }}"></script> 

        @yield('script')
        
    </body>
</html>