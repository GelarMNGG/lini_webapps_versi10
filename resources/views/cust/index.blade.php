@extends('layouts.dashboard')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Dashboard';
    $statusBadge    = array('dark','success','danger','purple','pink','warning');
?>
@endsection

@section('content')
<div class="flash-message mt-2">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
</div>

<div class="card text-center mt-2">
    <div class="card-header text-uppercase bb-orange"><h4>Statistics</h4></div>

    <div class="card-body">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <div class="col-xl">
            <div class="card-box">
      
                <div  class="col-md text-center">
                    <div id="bar-chart" ></div>
                </div>

                <style>
                    #area-chart, #line-chart, #bar-chart, #stacked, #pie-chart{
                        min-height: 350px;
                    }
                </style>
            </div>
        </div><!-- end col -->

    </div>
</div> <!-- container-fluid -->
    <div class="card">
        <div class="card-box">

            <h4 class="header-title mt-0 mb-3 text-left">Latest Projects</h4>

            <div class="table-responsive text-left">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Project Name</th>
                        <th>Start Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Project manager</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(sizeof($dataProjects) > 0)
                        <?php $ip=1; ?>
                        @foreach($dataProjects as $dataProject)

                        <tr>
                            <td>{{ $ip }}</td>
                            <td>{{ ucfirst($dataProject->name) }}</td>
                            <td>{{ $dataProject->date != null ? date('l, d F Y',strtotime($dataProject->date)) : '-'}}</td>
                            <td>-</td>
                            <td>
                                @foreach($projectStatus as $dataStatus)
                                    @if($dataStatus->id == $dataProject->status)
                                        <span class="badge badge-{{ $statusBadge[$dataStatus->id] }}">{{ ucwords($dataStatus->name) }}</span>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                {{ isset($dataProject->pm_firstname) ? ucwords($dataProject->pm_firstname).' '.ucwords($dataProject->pm_lastname) : '-' }}
                            </td>
                        </tr>
                        <?php $ip++; ?>
                        @endforeach
                    @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div><!-- end col -->
@endsection

@section('script')
<!--Morris Chart-->
    <script src="{{ asset('admintheme/libs/morris-js/morris.min.js') }}"></script>
    <script src="{{ asset('admintheme/libs/raphael/raphael.min.js') }}"></script>

    <!-- Init js -->
    <script src="{{ asset('admintheme/js/pages/morris.init.js') }}"></script>
    <script>
        var data = [
            //{ y: '2014', a: 50, b: 90},
                { y: 'JAN', a: 65},
                { y: 'PEB', a: 50},
                { y: 'MAR', a: 75},
                { y: 'APR', a: 80},
                { y: 'MEI', a: 90},
                { y: 'JUN', a: 100},
                { y: 'JUL', a: 115},
                { y: 'AGU', a: 120},
                { y: 'SEP', a: 145},
                { y: 'NOP', a: 160},
                { y: 'DES', a: 190}
            ],
            config = {
            data: data,
            xkey: 'y',
            ykeys: ['a'],
            labels: ['Total Site'],
            //ykeys: ['a', 'b'],
            //labels: ['Total Income', 'Total Outcome'],
            fillOpacity: 0.6,
            hideHover: 'auto',
            behaveLikeLine: true,
            resize: true,
            pointFillColors:['#ffffff'],
            pointStrokeColors: ['black'],
            lineColors:['gray','red']
        };
        // config.element = 'area-chart';
        // Morris.Area(config);
        // config.element = 'line-chart';
        // Morris.Line(config);
        config.element = 'bar-chart';
        Morris.Bar(config);
        // config.element = 'stacked';
        // config.stacked = true;
        // Morris.Bar(config);
        Morris.Donut({
        element: 'pie-chart',
        data: [
            {label: "Friends", value: 30},
            {label: "Allies", value: 15},
            {label: "Enemies", value: 45},
            {label: "Neutral", value: 10}
        ]
    });
    </script>
@endsection
