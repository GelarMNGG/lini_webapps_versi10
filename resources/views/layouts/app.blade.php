<!DOCTYPE html>
<html lang="en">
<head>

    @yield ('data')

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <title>{{ ucfirst(strtolower($companyInfo->name)) }}</title>
    <meta name="author" content="{{ ucfirst($companyInfo->url) }}">
    <meta name='keywords' content="{{ $companyInfo->keywords }}">
    <meta name='description' content="{{ substr($companyInfo->brief,0,200) }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('admintheme/images/favicon.ico') }}">
        
    <meta property='og:title' content="{{ ucfirst(strtolower($companyInfo->name)) }}"/>
    <meta property='og:url' content="{{ $companyInfo->url }}"/>
    <meta property='og:description' content="{{ substr($companyInfo->brief,0,200) }}"/>
    <meta property='og:image' content="{{ asset('img/'.$companyInfo->logo) }}"/>
    <meta property='og:type' content='website' />
    <!-- twitter card -->
    <meta name='twitter:card' content='summary' />
    <meta name='twitter:title' content="{{ ucfirst(strtolower($companyInfo->name)) }}"/>
    <meta name='twitter:url' content="{{ $companyInfo->url }}" />
    <meta name='twitter:description' content="{{ substr($companyInfo->brief,0,200) }}"/>
    <meta name='twitter:image' content="{{ asset('img/'.$companyInfo->logo) }}"/>
    <!-- Additional CSS Files -->
    
    @yield('header')

</head>
<body>

    @include('includes.nav')

    <div class="row" id="anchor" style="background: linear-gradient(165.04deg, rgb(240, 78, 88) 0%, rgb(95, 85, 216) 100%); padding: 0px; overflow: visible; height: 868px;">
        <div class="mx-auto my-auto">
            <div id="carousel-slider" class="carousel slide w-100" data-ride="carousel" data-wrap="false">
                
                @yield('slider')

            </div>
        </div>
    </div>
    
    @yield('content')
    
    @yield('content-2')
    
    @yield('content-3')
    
    @yield('footer')
    <script>
        // add padding top to show content behind navbar
        //$('body').css('padding-top', $('.navbar').outerHeight() + 'px')

        // detect scroll top or down
        if ($('.smart-scroll').length > 0) { // check if element exists
            var last_scroll_top = 0;
            $(window).on('scroll', function() {
                scroll_top = $(this).scrollTop();
                if(scroll_top < last_scroll_top) {
                    $('.smart-scroll').removeClass('scrolled-down').addClass('scrolled-up bg-orange');
                }
                else {
                    $('.smart-scroll').removeClass('scrolled-up bg-orange').addClass('scrolled-down');
                }
                last_scroll_top = scroll_top;
            });
        }
    </script>

</body>
</html>