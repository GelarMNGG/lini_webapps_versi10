@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Daftar pengajuan tes covid'; 
    $statusBadge    = array('','success','info','danger','purple','pink','warning','dark');
    //form route
    $formRouteIndex = 'admin-covid-test.index';
    $formRouteShow = 'admin-covid-test.show';
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

    @if ($covidDatas != null)

        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <strong><span class="text-danger">{{ ucfirst($pageTitle) }}</span></strong>
            </div>

            <div class="card-body bg-gray-lini-2">
                <div class="row m-0">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <?php $separator=1; ?>
                    @foreach($covidDatas as $data)
                        <div class="col-sm p-2">
                            <div class="bg-card-box br-5 p-2">

                                <div class="img-ca-box">

                                    @if(isset($data->image))

                                        <button type="button" class="btn badge-pill text-dark" data-toggle="modal" style="position:absolute;" data-target="#covidModal{{ $data->id }}"><i class="fas fa-eye"></i> </button>

                                        <img class="img-ca" src="{{ asset('img/covid-test/'.$data->image) }}">
                                    @else
                                        <div class="p-5 text-center"><img class="img-ca-2" src="{{ asset('img/covid-test/default.png') }}"></div>
                                    @endif
                                </div>

                                <!-- Modal -->
                                <div class="modal fade" id="covidModal{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="covidImage" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                                        <div class="modal-content-img">
                                            <div class="modal-body text-center">
                                            <button type="button" class="close close-img" data-dismiss="modal" aria-label="Close">
                                                <img name="image" class="img-fluid" style="margin-bottom:-2px;" src="{{ asset('/img/covid-test/'.$data->image) }}"  />
                                                <div class="alert alert-warning" id="covidImage">
                                                    <h5>
                                                        Bukti tes: <span class="text-muted">{{ ucfirst($data->name) }}</span>
                                                    </h5>
                                                </div>
                                            </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <br><strong>{{ isset($data->name) ? ucwords($data->name) : 'Belum ada data' }}</strong>
                                | {{ $data->nik }}

                                <?php
                                    if ($data->status == 3) {
                                        $cssBadge = 'success';
                                    }elseif($data->status == 2){
                                        $cssBadge = 'info';
                                    }else{
                                        $cssBadge = 'danger';
                                    }
                                ?>
                                <span class="badge badge-{{ $cssBadge }} float-right">{{ ucfirst($data->status_name) }}</span>

                                <br>Alamat: {!! ucfirst($data->address) !!}

                                <br>Pemohon: 
                                    @if(isset($data->requester_name_by_id) || isset($data->requester_name))
                                        <span>{{ isset($data->requester_name_by_id) ? ucwords($data->requester_name_by_id) : ucwords($data->requester_name) }}</span>
                                    @else
                                        <span>-</span>
                                    @endif

                                <br>Proyek: <span>{{ isset($data->project_name) ? ucwords($data->project_name) : '-' }}</span>
                                <br>Tujuan/site: <span>{{ isset($data->destination) ? ucfirst($data->destination) : '-' }}</span>

                                <br>Tanggal: <span class="text-info">{{ $data->date !== null ? date('l, d F Y',strtotime($data->date)) : 'Belum ada data' }}</span>

                                <div class="mt-1">
                                    
                                    <a href="{{ route($formRouteShow, $data->id) }}" class="btn btn-warning"><i class="fas fa-print"></i></a>
                                    
                                </div>

                            </div>
                        </div>
                        <?php if($separator % 2 == 0){echo "<div class='w-100'></div>";} ?>
                        <?php $separator++; ?>
                    @endforeach
                    <div class="w-100"></div>
                    <div class="col-md">
                        <?php 
                            //$covidDatas->setPath('user-covid-test');
                        ?>
                        <?php $paginator = $covidDatas; ?>
                        @include('includes.paginator')
                    </div>

                </div>
            </div>
            
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($dashboardLink) }}" class="btn btn-blue-lini">Kembali</a>
                </div>
            </div>
        </div>

    @else
        <div class="card mt-2">
            <div class="card-header text-center bb-orange">
                <div style="display:inline-block">
                    <strong><span class="text-info text-uppercase">{{ $pageTitle }}</span></strong>
                </div>
            </div>

            <div class="bg-gray-lini-2">
                <div class="card-body">
                    <div class="alert alert-warning">Belum ada data.</div>
                </div>
            </div>
            
            <div class="card-body">
                <div class="col-md">
                    <a href="{{ route($dashboardLink) }}" class="btn btn-blue-lini">Kembali</a>
                </div>
            </div>
        </div> <!-- container-fluid -->
    @endif
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
