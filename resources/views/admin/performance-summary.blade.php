@extends('layouts.dashboard-chart')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Performance summary';
?>
@endsection

@section('content')
<div class="flash-message mt-2">
    @foreach (['danger','warning','success','info'] as $msg)
        @if (Session::has('alert-'.$msg))
            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        @endif
    @endforeach
    @if ($errors->any())
        <p class="alert alert-danger">
            <small class="form-text">
                <strong>{{ $errors->first() }}</strong>
            </small>
        </p>
    @endif
</div>

<div class="card mt-2">
    <div class="card-body bg-gray-lini-2">

        <div class="row">
            <div class="col-xl">
                <div class="card-box">
                    <h4 class="header-title mt-0 mb-3">Weekly report</h4>

                    <div id="simple-line-chart" class="ct-chart ct-golden-section"></div>
                </div>
            </div><!-- end col-->
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-xl">
                <div class="card-box">
                    <h4 class="header-title mt-0 mb-3">Prosentase aktifitas coding</h4>

                    <div id="simple-pie" class="ct-chart ct-golden-section simple-pie-chart-chartist"></div>
                </div>
            </div><!-- end col-->
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-xl">
                <div class="card-box">
                    <h4 class="header-title mt-0 mb-3">Layanan berdasarkan <span class="text-uppercase text-danger">kategori</span></h4>

                    <div id="pie-chart" class="ct-chart ct-golden-section"></div>
                </div>
            </div><!-- end col-->
        </div>

        <div class="row">
            <div class="col-xl">
                <div class="card-box">
                    <h4 class="header-title mt-0 mb-3">Layanan berdasarkan <span class="text-uppercase text-danger">departemen</span></h4>

                    <div id="pie-chart-2" class="ct-chart ct-golden-section"></div>
                </div>
            </div><!-- end col-->
        </div>
        <!-- end row -->
    
    </div>
</div>        
@endsection

@section ('script')
<script>
    function ucwords (str) {
        return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
            return $1.toUpperCase();
        });
    }

    // line chart datas
    new Chartist.Line("#simple-line-chart",{
        labels:[
            <?php
                foreach($minuteStatus as $minuteWeek){
                    echo "'".ucwords(date('l',strtotime($minuteWeek->date)))."',";
                }
            ?>
        ],

        series:[[<?php
                foreach($minuteStatus as $minuteOnprogress){
                    echo $minuteOnprogress->onprogress.',';
                }
            ?>],[<?php
            foreach($minuteStatus as $minuteDone){
                echo $minuteOnprogress->done.',';
            }
        ?>]]},

        {fullWidth:!0,chartPadding:{right:40},plugins:[Chartist.plugins.tooltip()]}
    );

    //pie-chart datas
    var percentageOne = [<?php
                    foreach($progCountDatas as $progCountData){
                        echo $progCountData.',';
                    }
                ?>];
    var itTeams = [<?php
                    foreach($progNameDatas as $progNameData){
                        echo "'".ucwords($progNameData)."',";
                    }
                ?>];
    var data = {
        series: [
            <?php
                foreach($progCountDatas as $progCountData){
                    echo $progCountData.',';
                }
            ?>
        ],
    };

    var sum = function(a, b) { return a + b };

    new Chartist.Pie('#simple-pie', data, {
        labelInterpolationFnc: function(value, idx) {
            var percentage = (percentageOne[idx]/<?php echo $dataProgCountDatas; ?> * 100).toFixed(0)+"%";
            return itTeams[idx] + ' (' + percentage + ')';
        }
    });

    /////minutes by its category start
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
    /////minutes by its category end

    /////minutes by its department start
        var percentageThree = [<?php
                    foreach($deptCountDatas as $deptCountData){
                        echo $deptCountData.',';
                    }
                ?>];
        var data = {
            labels: [
                <?php 
                    foreach($deptNameDatas as $deptNameData){
                        echo "'".ucwords($deptNameData)."',";
                    }
                ?>
            ],
            series: [
                <?php 
                    foreach($deptCountDatas as $deptCountData){
                        echo $deptCountData.',';
                    }
                ?>
                ]
        };

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
                    return value +" ("+ (percentageThree[idx]/<?php echo $dataDeptCountDatas; ?> * 100).toFixed(0)+"%)";
                }
            }],
            ['screen and (min-width: 1024px)', {
                labelOffset: 80,
                chartPadding: 20
            }]
        ];

        new Chartist.Pie('#pie-chart-2', data, options, responsiveOptions);
    /////minutes by its department end
</script>
@endsection