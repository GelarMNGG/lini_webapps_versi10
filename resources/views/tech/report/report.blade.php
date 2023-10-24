@extends('layouts.dashboard-form')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Laporan pekerjaan';
    $formRouteIndex = 'report-tech.index';
    $formRouteUpdate= 'report-tech.update';
?>
@endsection

@section('content')
    <div class="content">
        <!-- Start Content-->
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">
                    <div class="card-box">
                        <span class="logo float-left">
                            <img src="{{ asset('img/'.$companyInfo->logo) }}" alt="logo {{ $companyInfo->name }}" height="57">
                        </span>
                        <div class="panel-heading text-center text-uppercase">
                            <h3>{{ $pageTitle }}</h3>
                        </div>
                        <hr>
                        <div class="panel-body">
                            <div class="clearfix">
                                <div class="float-left">
                                    <span>Nama teknisi: {{ ucwords($userProfile->firstname).' '.ucwords($userProfile->lastname) }}</span>
                                    <br><span>Project: USO 125 Site</span>
                                </div>
                                <div class="float-right">
                                    <h4>No WO #<strong>WO-P0009-2021</strong> </h4>
                                    <span>Tanggal: {{ date('d F Y') }}</span>
                                </div>
                            </div>
                            <hr>

                            <div class="flash-message">
                                @foreach (['danger','warning','success','info'] as $msg)
                                    @if (Session::has('alert-'.$msg))
                                        <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                                    @endif
                                @endforeach

                                <p class="alert alert-danger">Anda belum melengkapi gambar yang diperlukan.<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>

                            </div>
                            <div class="row m-0 alert alert-warning">
                                <div class="float-left"> <a href="#" class="btn btn-info"><i class="fa fa-plus"></i> Tambah gambar</a></div>
                            </div>

                            <!-- end row -->
                            <div class="row mt-4">
                                <div class="col-md-12 text-center">
                                    <h4 class="text-uppercase">Base band unit</h4>
                                </div>
                                <div class="row m-0">
                                    <div class="col-md">
                                        <img class="img-fluid" src="{{ asset('/img/projects/bts/bts-2.jpg') }}" alt="">
                                        <br><span>Tampak depan</span>
                                    </div>
                                    <div class="col-md">
                                        <img class="img-fluid" src="{{ asset('/img/projects/bts/bts-17.jpg') }}" alt="">
                                        <br><span>Tampak samping</span>
                                    </div>
                                    <div class="col-md">
                                        <img class="img-fluid" src="{{ asset('/img/projects/bts/bts-18.jpg') }}" alt="">
                                        <br><span>Tampak depan</span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row mt-4">
                                <div class="col-md-12 text-center">
                                    <h4 class="text-uppercase">RRU Instalation</h4>
                                </div>
                                <div class="row m-0">
                                    <div class="col-md">
                                        <img class="img-fluid" src="{{ asset('/img/projects/bts/bts-14.jpg') }}" alt="">
                                        <br><span>Tampak depan</span>
                                    </div>
                                    <div class="col-md">
                                        <img class="img-fluid" src="{{ asset('/img/projects/bts/bts-15.jpg') }}" alt="">
                                        <br><span>Tampak samping</span>
                                    </div>
                                    <div class="col-md">
                                        <img class="img-fluid" src="{{ asset('/img/projects/bts/bts-14.jpg') }}" alt="">
                                        <br><span>Tampak depan</span>
                                    </div>
                                    <div class="col-md alert alert-danger">
                                        <br><span>Tampak belakang</span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row mt-4">
                                <div class="col-md-12 text-center">
                                    <h4 class="text-uppercase">Antenna Instalation</h4>
                                </div>
                                <div class="row m-0">
                                    <div class="col-md">
                                        <img class="img-fluid" src="{{ asset('/img/projects/bts/bts-16.jpg') }}" alt="">
                                        <br><span>Tampak depan</span>
                                    </div>
                                    <div class="col-md">
                                        <img class="img-fluid" src="{{ asset('/img/projects/bts/bts-16.jpg') }}" alt="">
                                        <br><span>Tampak samping</span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row m-0 mb-5">
                                <div class="col-xl-6 col-6">
                                    <div class="clearfix">
                                        <h5 class="small text-dark text-uppercase mb-5">dibuat</h5>
                                        <small>
                                            {{ ucwords($userProfile->firstname).' '.ucwords($userProfile->lastname) }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-6">
                                    <h5 class="small text-dark text-uppercase mb-5">Disetujui, Project coordinator</h5>
                                    <small>
                                        Darmo Sentono
                                    </small>
                                </div>
                            </div>
                            <hr>
                            <div class="d-print-none">
                                <div class="float-right">
                                    <a href="javascript:window.print()" class="btn btn-dark waves-effect waves-light"><i class="fa fa-print"></i></a>

                                    <form action="#" style="display:inline-block" method="post" enctype="multipart/form-data" data-parsley-validate novalidate>
                                        @csrf
                                        @method('PUT')
                                        <input class="form-control" type="number" name="status" value="2" hidden>
                                        <button type="submit" class="btn btn-icon waves-effect waves-light btn-danger" name="submit"><i class='fas fa-paper-plane' title='done'> </i> Kirim laporan</button>
                                    </form>

                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <!-- end row -->        
            
        </div> <!-- container-fluid -->

    </div> <!-- content -->
@endsection

@section ('script')
<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace( 'description' );
</script>
@endsection
