@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Dashboard proyek';
    $statusBadge    = array('dark','info','success','danger','purple','pink','warning');
    //form link
    $formRouteIndex = 'cust-projects.index';
    $formRouteShow = 'cust-projects.show';
    $formRouteEdit = 'cust-projects.edit';
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

<div class="card mt-2">
    <div class="card-header text-center text-uppercase bb-orange"><strong>{{ ucfirst($pageTitle) }}</strong></div>

    <div class="card-body">
        <!-- Start Content-->
        <div class="container-fluid">

            <div class="row">
                @foreach ($projects as $data)
                    <?php
                        $total = ($onprogressCount * 2) + ($reportingCount * 3) + ($finishedCount * 4);
                        $totalFinished = ($onprogressCount + $reportingCount + $finishedCount) * 4;

                        if ($total > 0) {
                            $totalPercentage = ($total/$totalFinished) * 100;
                        }else{
                            $totalPercentage = 0;
                        }
                    ?>
                    <div class="col-md-3">
                        <div class="alert alert-warning">
                            <strong>{{ strtoupper($data->name) }}</strong> <small>({{ $totalPercentage }}%)</small>
                            <br> <span class="text-info"><small><strong>{{ $data->taskCount }} task</strong> | <a href="{{ route($formRouteShow, $data->id) }}">Detail</a></small></span>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- end row -->        

        </div> <!-- container-fluid -->
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable(
            "order":[]
        );
    } );
</script>
@endsection
