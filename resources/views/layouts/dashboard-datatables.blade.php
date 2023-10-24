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
        <!-- third party css -->
        <link href="{{ asset('admintheme/libs/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admintheme/libs/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admintheme/libs/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('admintheme/libs/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
        <!-- third party css end -->
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
        </div>
        <!-- END wrapper -->
        <!-- Vendor js -->
        <script src="{{ asset('admintheme/js/vendor.min.js') }}"></script>
        <!-- third party js -->
        <script src="{{ asset('admintheme/libs/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/datatables/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('admintheme/libs/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/datatables/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/datatables/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/pdfmake/pdfmake.min.js') }}"></script>
        <script src="{{ asset('admintheme/libs/pdfmake/vfs_fonts.js') }}"></script>
        <!-- third party js ends -->
        <!-- Datatables init -->
        <script src="{{ asset('admintheme/js/pages/datatables.init.js') }}"></script>
        <!-- App js -->
        <script src="{{ asset('admintheme/js/app.min.js') }}"></script>

        @yield('script')
        
    </body>
</html>