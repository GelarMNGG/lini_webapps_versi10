@extends('layouts.dashboard-datatables')

@section ('data')
<?php ### customized data
    $pageTitle      = 'Notifikasi';
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
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <!-- Start Content-->
        <div class="container-fluid">
            <div class="row">
                @if ($notifDataCount > 0)

                    @if ($notifById != null)
                        <div class='col-md media mb-3 alert alert-warning'>

                            <?php 
                                if (isset($notifById->publisher_user_image)) {
                                    $publisher_image = $notifById->publisher_user_image;
                                }elseif(isset($notifById->publisher_admin_image)){
                                    $publisher_image = $notifById->publisher_admin_image;
                                }else{
                                    $publisher_image = 'default.png';
                                }
                            ?>

                            <img src="{{ asset('admintheme/images/users/'.$publisher_image) }}" class='comment-avatar avatar-sm rounded mr-2' />

                            <div class='media-body'>
                                @if ($notifById->status == 0)
                                    <form action="{{ route($formNotifUpdateAction,$notifById->id) }}" id="updatestatus-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input class="form-control" type="number" name="status" value="1" hidden>
                                        <span class='float-right text-muted'><small>
                                        <button class="submit submit_alert" type="submit">Bersihkan</button> </small> </span>
                                    </form>
                                @endif
                                
                                <h5 class='mt-0'><a href='#' class='text-dark'> {{ ucwords($notifById->level_name) }} </a><small class='ml-1 text-muted'>{{ date("l, d F Y",strtotime($notifById->date)) }}</small></h5>
                                <p>{!! ucfirst($notifById->desc) !!}</p>
                            </div>
                                
                        </div>
                        <div class="w-100"></div>
                    @endif

                    @foreach ($notifAlls as $notifAll)
                        @if ($notifAll->status == 0) 
                            <?php $statusCSS  = "warning"; ?>
                        @else
                            <?php $statusCSS = "secondary"; ?>
                        @endif
                        <?php
                            if ($notifAll->level == 3) {
                                $icon2 = 'icon-penting.png';
                            }elseif($notifAll->level == 2){
                                $icon2 = 'icon-sedang.png';
                            }else{
                                $icon2 = 'icon-normal.png';
                            }
                        ?>
                        <div class='col-md media mb-3 alert alert-{{ $statusCSS }}'>
                            <?php 
                                if (isset($notifAll->publisher_user_image)) {
                                    $publisher_image = $notifAll->publisher_user_image;
                                }else{
                                    $publisher_image = $notifAll->publisher_admin_image;
                                }
                            ?>

                            <img src="{{ asset('admintheme/images/users/'.$publisher_image) }}" class='comment-avatar avatar-sm rounded mr-2' />

                            <div class='media-body'>
                                @if ($notifAll->status == 0)
                                    <form action="{{ route($formNotifUpdateAction,$notifAll->id) }}" id="updatestatus-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input class="form-control" type="number" name="status" value="1" hidden>
                                        <span class='float-right text-muted'><small>
                                        <button class="submit submit_alert" type="submit">Bersihkan</button> </small> </span>
                                    </form>
                                @endif
                                
                                <h5 class='mt-0'><a href='#' class='text-dark'> {{ ucwords($notifAll->level_name) }} </a><small class='ml-1 text-muted'>{{ date("l, d F Y",strtotime($notifAll->date)) }}</small></h5>
                                <p>{!! ucfirst($notifAll->desc) !!}</p>
                            </div>
                        </div>
                        <div class="w-100"></div>
                    @endforeach
                @else
                    <div class="col-md alert alert-warning">Tidak ada data yang tersedia.</div>
                @endif
            </div>
            <!-- end row -->        
        </div> <!-- container-fluid -->
    </div>
</div> <!-- container-fluid -->
@endsection

@section ('script')
<script>
    $(document).ready(function() {
        $('table.display').DataTable();
    } );
</script>
@endsection
