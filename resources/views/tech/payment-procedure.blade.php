@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Prosedur pembayaran';
    $statusBadge    = array('dark','info','success','danger','purple','pink','warning');
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
    <div class="card-header text-center bb-orange">
        <span class="text-uppercase"><strong>{{ ucfirst($pageTitle) }}</strong></span>
    </div>
    <div class="card-body">
        <!-- Start Content-->
        <div class="col-md text-center mt-3 mb-3">
            <!-- progressbar -->
            <ul id="progressbar" style="padding-inline-start: 0px;">
                <li class="active justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <img class="icon-login" src="{{ asset('admintheme/images/icon/tech-alat.png') }}">
                    </div>
                    <div>
                        <strong><span class="badge badge-danger badge-pill">1</span></strong>
                    </div>
                </li>
                <li class="active justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <img class="icon-login" src="{{ asset('admintheme/images/icon/tech-pengeluaran.png') }}">
                    </div>
                    <div>
                        <strong><span class="badge badge-warning badge-pill">2</span></strong>
                    </div>
                </li>
                <li class="active justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <img class="icon-login" src="{{ asset('admintheme/images/icon/tech-laporan.png') }}">
                    </div>
                    <div>
                        <strong><span class="badge badge-info badge-pill">3</span></strong>
                    </div>
                </li>
                <li class="active justify-content-center">
                    <div class="progress-icon-box mb-2">
                        <i class="fa fa-money-bill progress-icon"></i>
                    </div>
                    <div>
                        <strong><span class="badge badge-success badge-pill">4</span></strong>
                    </div>
                </li>
            </ul>
        </div>
        <hr>
        <div class="container-fluid">
            <div class="alert alert-warning">
                Pembayaran akan dilakukan setelah <strong><span class="text-danger">(1) Laporan Pengembalian Alat</span>, <span class="text-warning">(2) Laporan Pengeluaran</span>, dan <span class="text-info">(3) Laporan Pekerjaan</span></strong> divalidasi oleh pejabat terkait yang berwenang.
            </div>  

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
