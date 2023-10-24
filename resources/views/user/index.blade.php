@extends('layouts.dashboard-chart')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Dashboard'; 
    $dashboardLink  = 'user.index';
    $department = Auth::user()->department_id;
    $statusBadge    = array('','dark','info','success','danger','purple','pink','warning');
    $formTroubleshootingShow = 'user.troubleshootingdetail';
    $allMembersPage = 'user.allmembersdepartment';
?>
@endsection

@section('content')
    <div class="flash-message mt-2">
        <!-- announcement -->
        @if(isset($basicRulesofConduct))
            <p class="alert alert-warning"><strong>Aturan dasar no.#{{$basicRulesofConduct->id}}</strong>:
                <br>{{ ucfirst($basicRulesofConduct->name) }} 
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
        <!-- announcement -->
        @if(isset($flashMessageData))
            <p class="alert alert-{{ $flashMessageData->level }}">{{ ucfirst($flashMessageData->message) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
        <!-- session -->
        @foreach (['danger','warning','success','info'] as $msg)
            @if (Session::has('alert-'.$msg))
                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            @endif
        @endforeach
    </div>

    <!-- Slider Satgas -->
    <div class="card text-center mt-2">
        <div class="row" id="anchor" style=" padding: 0px;">
            <div class="mx-auto my-auto">
                <div id="carousel-slider" class="carousel slide w-100" data-ride="carousel" data-wrap="false">
                    <div class="container">
                        <div class="carousel-inner w-100" role="listbox">
                            
                            <?php $is=1; ?>
                                @foreach($sliders as $slider)
                                <ol class="carousel-indicators carousel-indicators-1">
                                    @for($i = 1; $i <= $slider->slidersCount; $i++)
                                        <li data-target="#carousel-slider" data-slide-to="{{ $i }}" class="{{ $i == 1 ? 'active' : '' }}"> 0{{$i}}  &nbsp.</li>
                                    @endfor
                                </ol>
                                    @if($slider->status == 1)
                                    <div class="carousel-item{{ $is == 1 ? ' active' : '' }}">
                                        <div>
                                            <img class="img-fluid w-100" src="{{ asset('img/sliders/'.$slider->image) }}">
                                        </div>
                                    </div>
                                    @endif
                                <a class="carousel-control-prev" href="#carousel-slider" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carousel-slider" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                                <?php $is++; ?>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Slider Satgas -->

    <div class="card text-center mt-2">
        <div class="card-header text-uppercase bb-orange"><strong>{{ ucfirst($pageTitle) }}</strong></div>

        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="col-md">
                <div>
                    <a href="{{ route('attendance.index') }}" class="btn icon-box">
                        <i class="far fa-calendar-check rounded-circle icon-xl mb-2 badge-pink"></i>
                        <br><span class="icon-title">Absensi</span>
                    </a>
                    <a href="{{ route('troubleshooting.create') }}" class="btn icon-box">
                        <i class="fas fa-hand-rock rounded-circle icon-xl mb-2 badge-warning"></i>
                        <br><span class="icon-title">Troubleshooting</span>
                    </a>
                    <a href="{{ route('user-minutes.index') }}" class="btn icon-box">
                        <i class="fas fa-running rounded-circle icon-xl mb-2 badge-info"></i>
                        <br><span class="icon-title">Tambah Aktifitas</span>
                    </a>
                    @if(Auth::user()->department_id == 4 && Auth::user()->user_type == 'user' && Auth::user()->user_level == 7)
                        <a href="{{ route('user-covid-test.create') }}" class="btn icon-box">
                            <i class="fas fa-hospital-user rounded-circle icon-xl mb-2 badge-danger"></i>
                            <br><span class="icon-title">Ajukan test Covid</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div> <!-- container-fluid -->

    <!-- chart -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">Grafik aktifitas harian <small class="text-muted">(pribadi vs departemen)</small></h4>

                <div id="smil-animations" class="ct-chart ct-golden-section"></div>
                <small>Ket: <span class="text-purple">-- total done</span>, <span class="text-warning">-- total inprogress</span>, <span class="text-pink">-- pribadi done</span>, <span class="text-info">-- pribadi inprogress</span>. </small>
                <br><small>Data diambil 7 hari dari tanggal saat ini.</small>
            </div>
        </div><!-- end col-->
        <div class="col-xl-6">
            <div class="card-box">
                <h4 class="header-title mt-0 mb-3">Aktifitas berdasarkan <small class="text-danger">(kategori)</small></h4>

                @if($datacatCountDatas > 0)
                    <div id="pie-chart" class="ct-chart ct-golden-section"></div>
                @else
                    <div id="pie-chart-2" class="ct-chart ct-golden-section"></div>
                    <small class="text-danger">Belum ada data. Grafik menggunakan dummy data.</small>
                @endif

            </div>
        </div><!-- end col-->
    </div>
    <!-- chart -->

    <!-- teams -->
    <div class="card">
        <div class="card-header">
            <span class="badge badge-danger float-left mr-1">{{ count($dataTeams) <= 100 ? count($dataTeams) + 1 : '>100' }}</span> 
            <strong>{{ strtoupper($dataAdmin->dept_name) }}</strong> team members
            <span class="float-right"><a href="{{ route($allMembersPage) }}">Browse All</a></span>
        </div>
        <div class="card-body bg-gray-lini-2">
            <div class="row">
                @if(isset($dataAdmin))
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="media">
                                <div class="avatar-lg mr-3">
                                    <img src="{{ asset('admintheme/images/users/'.$dataAdmin->image) }}" class="img-fluid rounded-circle" alt="{{ ucwords($dataAdmin->firstname).' '.ucwords($dataAdmin->lastname) }}">
                                </div>
                                <div class="media-body overflow-hidden">
                                    <h5 class="mt-0 mb-1">{{ ucwords($dataAdmin->firstname).' '.ucwords($dataAdmin->lastname) }}</h5>
                                    <p class="text-muted mb-2 font-13 text-truncate">{{ strtolower($dataAdmin->email) }}</p>
                                    <small class="text-danger"><b>{{ ucwords($dataAdmin->title) }}</b></small>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col -->
                @endif
                <?php $idt = 1; ?>
                @foreach($dataTeams as $dataTeam)
                <?php if($idt == 8){$idt=1;} ?>
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="media">
                                <div class="avatar-lg mr-3">
                                    <img src="{{ asset('admintheme/images/users/'.$dataTeam->image) }}" class="img-fluid rounded-circle" alt="{{ ucwords($dataTeam->firstname).' '.ucwords($dataTeam->lastname) }}">
                                </div>
                                <div class="media-body overflow-hidden">
                                    <h5 class="mt-0 mb-1">{{ ucwords($dataTeam->firstname).' '.ucwords($dataTeam->lastname) }}</h5>
                                    <p class="text-muted mb-2 font-13 text-truncate">{{ strtolower($dataTeam->email) }}</p>
                                    <small class="text-{{ $statusBadge[$idt] }}"><b>{{ isset($dataTeam->user_title) ? ucwords($dataTeam->user_title) : 'Staff' }}</b></small>
                                </div>
                            </div>
                        </div>
                    </div><!-- end col -->
                    <?php $idt++; ?>
                @endforeach
            </div>
        </div>
    </div>
    <!-- teams end -->

    <!-- user project -->
    @if(Auth::user()->department_id == 1)
        @if(sizeof($dataProjects) > 0)
        <div class="card">
            <div class="card-header"><span class="badge badge-danger float-left mr-1">{{ sizeof($dataProjects) <= 5 ? sizeof($dataProjects) : '>5' }}</span> Project terbaru</div>
            <div class="card-body">
                @foreach($dataProjects as $dataProject)
                    <div class="alert alert-warning">{{ ucfirst($dataProject->name) }} | {{ date('l, d F Y',strtotime($dataProject->date))}} 
                        @if($dataProject->status == 0)
                            <span class="badge badge-pink float-right">New</span>
                        @else
                            @foreach($projectStatus as $dataStatus)
                                @if($dataStatus->id == $dataProject->status)
                                    <span class="badge badge-{{ $statusBadge[$dataStatus->id] }} float-right">{{ ucwords($dataStatus->name) }}</span>
                                @endif
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    @endif
    <!-- user project -->
    @if(count($appsUpdateDatas) > 0)
        <div class="card">
            <div class="card-header"><span class="badge badge-info float-left mr-1">{{ sizeof($appsUpdateDatas) <= 5 ? sizeof($appsUpdateDatas) : '>5' }}</span> Informasi pembaruan aplikasi</div>
            <div class="card-body small">
                @foreach($appsUpdateDatas as $dataUpdateApp)
                    <div class="alert alert-warning">
                        <strong>{{ ucfirst($dataUpdateApp->title) }}</strong>
                        <span class="float-right text-info hide-in-small-screen">{{ date('l, d F Y',strtotime($dataUpdateApp->created_at)).' | '.date('H:i A',strtotime($dataUpdateApp->created_at)) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    <!-- troubleshooting -->
    @if(isset($dataTroubleshootings) && count($dataTroubleshootings) > 0)
        <div class="card">
            <div class="card-header bb-orange">Troubleshooting
                @if(count($dataTroubleshootings) > 4)
                    <span class="float-right"><a href="#">Lihat semua</a></span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    @if(isset($dataTroubleshootings))
                        <?php $id = 1; ?>
                        @foreach($dataTroubleshootings as $dataTroubleshooting)
                            <?php
                                if ($id % 2 == 0) {
                                    $manualCSS = '';
                                } else{
                                    $manualCSS = ' mb-2';
                                }
                            ?>
                            <div class="col-md-6{{ $manualCSS }}">
                                @if(isset($dataTroubleshooting->image) && $dataTroubleshooting->image != null)
                                    <img class="w-100 img-fluid img-thumbnail" src="{{ asset('img/troubleshooting/'.$dataTroubleshooting->image) }}">
                                @else
                                    <img class="w-100 img-fluid img-thumbnail" src="{{ asset('img/troubleshooting/default.png') }}">
                                @endif
                                <div class="dashboard-article-box bg-blue-lini-2">
                                    <div class="col-md"><span class="text-uppercase">{{ $dataTroubleshooting->title }}</span></div>
                                    <div class="col-md mt-2" style="display:inline-block">
                                        <div class="float-left">
                                            <span class="text-secondary"><small>{{ $dataTroubleshooting->view ? $dataTroubleshooting->view : '0' }} <i class="fas fa-eye"></i></small></span>
                                        </div>
                                        <div class="float-right">
                                            <a href="{{ route($formTroubleshootingShow, $dataTroubleshooting->id) }}">Baca selengkapnya</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php $id++; ?>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif
    <!-- troubleshooting end -->
@endsection

@section('script')
    <!-- js -->
    <script>
        ////total activity
            var chart = new Chartist.Line('#smil-animations', {
                labels:
                    <?php 
                        echo "[";
                        foreach ($minuteAllDone as $minuteStatu) {
                            echo "'".date('l d',strtotime($minuteStatu->date))."',";
                        }
                        echo "],";
                    ?>
                series: [
                    <?php 
                        //all
                        echo "[";
                        foreach ($minuteAllDone as $minute1) {
                            echo $minute1->done.',';
                        }
                        echo "],";
                        //inprogress
                        echo "[";
                        foreach ($minuteAllInProgress as $minute1) {
                            echo $minute1->inprogress.',';
                        }
                        if ($totalAllInProgress < $totalAllDone) {
                            $diff = $totalAllDone - $totalAllInProgress;
                            for ($i=0; $i < $diff; $i++) { 
                                echo "0,";
                            }
                        }
                        echo "],";
                    ?>
                    <?php 
                        //user done
                        echo "[";
                        foreach ($minuteDone as $minute2) {
                            echo $minute2->done.',';
                        }
                        if ($totalDone < $totalAllDone) {
                            $diff = $totalAllDone - $totalDone;
                            for ($i=0; $i < $diff; $i++) { 
                                echo "0,";
                            }
                        }
                        echo "],";
                        //inprogress
                        echo "[";
                        foreach ($minuteInProgress as $minute2) {
                            echo $minute2->inprogress.',';
                        }
                        if ($totalInProgress < $totalAllDone) {
                            $diff = $totalAllDone - $totalInProgress;
                            for ($i=0; $i < $diff; $i++) { 
                                echo "0,";
                            }
                        }
                        echo "]";
                    ?>
                ]
            }, {
                low: 0
            });

            // Let's put a sequence number aside so we can use it in the event callbacks
            var seq = 0,
            delays = 80,
            durations = 500;

            // Once the chart is fully created we reset the sequence
            chart.on('created', function() {
                seq = 0;
            });

            // On each drawn element by Chartist we use the Chartist.Svg API to trigger SMIL animations
            chart.on('draw', function(data) {
                seq++;

                if(data.type === 'line') {
                    // If the drawn element is a line we do a simple opacity fade in. This could also be achieved using CSS3 animations.
                    data.element.animate({
                    opacity: {
                        // The delay when we like to start the animation
                        begin: seq * delays + 1000,
                        // Duration of the animation
                        dur: durations,
                        // The value where the animation should start
                        from: 0,
                        // The value where it should end
                        to: 1
                    }
                    });
                } else if(data.type === 'label' && data.axis === 'x') {
                    data.element.animate({
                    y: {
                        begin: seq * delays,
                        dur: durations,
                        from: data.y + 100,
                        to: data.y,
                        // We can specify an easing function from Chartist.Svg.Easing
                        easing: 'easeOutQuart'
                    }
                    });
                } else if(data.type === 'label' && data.axis === 'y') {
                    data.element.animate({
                    x: {
                        begin: seq * delays,
                        dur: durations,
                        from: data.x - 100,
                        to: data.x,
                        easing: 'easeOutQuart'
                    }
                    });
                } else if(data.type === 'point') {
                    data.element.animate({
                    x1: {
                        begin: seq * delays,
                        dur: durations,
                        from: data.x - 10,
                        to: data.x,
                        easing: 'easeOutQuart'
                    },
                    x2: {
                        begin: seq * delays,
                        dur: durations,
                        from: data.x - 10,
                        to: data.x,
                        easing: 'easeOutQuart'
                    },
                    opacity: {
                        begin: seq * delays,
                        dur: durations,
                        from: 0,
                        to: 1,
                        easing: 'easeOutQuart'
                    }
                    });
                } else if(data.type === 'grid') {
                    // Using data.axis we get x or y which we can use to construct our animation definition objects
                    var pos1Animation = {
                    begin: seq * delays,
                    dur: durations,
                    from: data[data.axis.units.pos + '1'] - 30,
                    to: data[data.axis.units.pos + '1'],
                    easing: 'easeOutQuart'
                    };

                    var pos2Animation = {
                    begin: seq * delays,
                    dur: durations,
                    from: data[data.axis.units.pos + '2'] - 100,
                    to: data[data.axis.units.pos + '2'],
                    easing: 'easeOutQuart'
                    };

                    var animations = {};
                    animations[data.axis.units.pos + '1'] = pos1Animation;
                    animations[data.axis.units.pos + '2'] = pos2Animation;
                    animations['opacity'] = {
                    begin: seq * delays,
                    dur: durations,
                    from: 0,
                    to: 1,
                    easing: 'easeOutQuart'
                    };

                    data.element.animate(animations);
                }
            });

            // For the sake of the example we update the chart every time it's created with a delay of 10 seconds
            chart.on('created', function() {
                if(window.__exampleAnimateTimeout) {
                    clearTimeout(window.__exampleAnimateTimeout);
                    window.__exampleAnimateTimeout = null;
                }
                window.__exampleAnimateTimeout = setTimeout(chart.update.bind(chart), 12000);
            });
        ////end
        /////minutes by its category start
        <?php if ($datacatCountDatas > 0): ?>
            var percentageTwo = [<?php
                        foreach($catCountDatas as $catCountData){
                            echo $catCountData.',';
                        }
                    ?>];
            var data = {
                labels: [
                    <?php 
                        foreach($catNameDatas as $catNameData){
                            echo "'".ucwords($catNameData)."',";
                        }
                    ?>
                ],
                series: [
                    <?php 
                        foreach($catCountDatas as $catCountData){
                            echo $catCountData.',';
                        }
                    ?>
                ]
            };
        <?php else: ?>
            var percentageTwo = [3,1,7];
            var data = {
                labels: ['administratif','jaringan','komunikasi'],
                series: [5,7,8],
            };
            <?php $datacatCountDatas = 11; ?>
        <?php endif; ?>

        var options = {
            labelInterpolationFnc: function(value) {
                return value[0]
            }
        };

        var responsiveOptions = [
            ['screen and (min-width: 640px)', {
                chartPadding: 30,
                labelOffset: 100,
                labelDirection: 'explode',
                labelInterpolationFnc: function(value,idx) {
                    return value +" ("+ (percentageTwo[idx]/<?php echo $datacatCountDatas; ?> * 100).toFixed(0)+"%)";
                }
            }],
            ['screen and (min-width: 1024px)', {
                labelOffset: 80,
                chartPadding: 20
            }]
        ];

        new Chartist.Pie('#pie-chart', data, options, responsiveOptions);
        
        new Chartist.Pie('#pie-chart-2', data, options, responsiveOptions);
    /////minutes by its category end
    </script>
@endsection
