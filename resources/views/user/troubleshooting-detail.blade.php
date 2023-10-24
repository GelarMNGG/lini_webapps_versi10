@extends('layouts.dashboard-form')

@section ('data')
<?php ### custom data
    $pageTitle      = 'Lihat troubleshooting';
    $formRouteIndex = 'user.index';

    //form comment
    $formCommentsStore = 'user-troubleshooting-comments.store';

    //comment files
    $formCommentFileStore = 'user-troubles-comment-file.store';
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
<div class="row pt-5 pb-5 text-left" style="background-color: #28316A; color:#ffffff;">
    <div class="container">
        <h1 class="t-orange">{{ ucwords($dataTroubleshooting->title) }}</h1>
        <div class="row mt-5">
            <div class="col-md-4">
                @if(isset($dataTroubleshooting->image) && $dataTroubleshooting->image != null)
                    <img class="w-100 img-fluid img-thumbnail" src="{{ asset('img/troubleshooting/'.$dataTroubleshooting->image) }}">
                @else
                    <img class="w-100 img-fluid img-thumbnail" src="{{ asset('img/troubleshooting/default.png') }}">
                @endif
            </div>
            <div class="col-md-8">
                <div class="col-md text-white text-box">
                    <p class="lead">{{ ucfirst($dataTroubleshooting->problem) }}</p>
                    <small class="text-muted">oleh: 
                        @if($dataTroubleshooting->publisher_type == 'user')
                            @foreach($users as $user)
                                @if($user->id == $dataTroubleshooting->publisher_id)
                                    <span>{{ ucwords($user->firstname).' '.ucwords($user->lastname) }}</span>
                                @endif
                            @endforeach
                        @elseif($dataTroubleshooting->publisher_type == 'tech')
                            @foreach($techs as $tech)
                                @if($tech->id == $dataTroubleshooting->publisher_id)
                                    <span>{{ ucwords($tech->firstname).' '.ucwords($tech->lastname) }}</span>
                                @endif
                            @endforeach
                        @else
                            @foreach($admins as $admin)
                                @if($admin->id == $dataTroubleshooting->publisher_id)
                                    <span>{{ ucwords($admin->firstname).' '.ucwords($admin->lastname) }}</span>
                                @endif
                            @endforeach
                        @endif
                    </small>
                    <br><span class="text-secondary"><small>{{ $dataTroubleshooting->view ? $dataTroubleshooting->view : '0' }} <i class="fas fa-eye"></i></small></span>
                    | <span class="text-info"><small>{{ $dataTroubleshooting->date ? date('l, d F Y',strtotime($dataTroubleshooting->date)) : '-' }}</small></span>
                </div>
            </div>
        </div>
    </div> 
</div>

<div class="row justify-content-left text-left text-box">
    <div class="container blog-box">
        {!! $dataTroubleshooting->solution !!}
    </div>
</div>
<div class="row">
    <div class="container blog-box">
        <a class="btn btn-blue-lini" href="{{ route($formRouteIndex) }}">Kembali</a>
    </div>
</div>

<!-- comments -->
    <form method="post" action="{{ route($formCommentsStore) }}" enctype="multipart/form-data" class="card-box">
        @csrf

        <span class="input-icon icon-right{{ $errors->has('comment') ? ' has-error' : '' }}">
            <textarea rows="3" name="comment" class="form-control" placeholder="Kirim komentar" required>{{ old('comment') ?? '' }}</textarea>
        </span>
        <!-- comment data -->
        <input type="hidden" name="troubles_id" value="{{ $dataTroubleshooting->id }}">
        <input type="hidden" name="receiver_id" value="{{ $dataTroubleshooting->publisher_id }}">
        <input type="hidden" name="receiver_type" value="{{ $dataTroubleshooting->publisher_type }}">
        <input type="hidden" name="receiver_department" value="{{ $dataTroubleshooting->department_id }}">
        <!-- comment data -->
        <div class="pt-1 float-right">
            <button type="submit" name="submit" class="btn btn-primary btn-sm waves-effect waves-light">Kirim komentar</button>
        </div>
        <ul class="nav nav-pills profile-pills mt-1">
            <li>
                <a href="#"><i class="far fa-smile"></i></a>
            </li>
        </ul>
    </form>
    @if($countComments > 0)
        @foreach ($dataComments as $data1)
        <div class="card-box">
            <div class='media'>
                @if($data1->publisher_type == 'user')
                    @foreach($users as $user)
                        @if($user->id == $data1->publisher_id)
                            <img src="{{ asset('admintheme/images/users/'.$user->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                            <div class='media-body'>
                                <span class="float-right text-right p-0">
                                    @if(Auth::user()->id == $data1->publisher_id)
                                        <button type="button" class="badge badge-dark" data-toggle="modal" data-target="#uploadModal{{ $data1->id }}"> Upload file</button>
                                    @endif
                                    @if($data1->countFiles > 0)
                                        <br><span class="badge badge-danger">{{ $data1->countFiles }} file</span>
                                    @endif
                                </span>

                                <h5 class='mt-0'>
                                    <a href='#' class='text-dark'>{{ ucfirst($user->firstname).' '.ucfirst($user->lastname) }}</a>
                                    <small class='ml-1 text-muted'>{{ date('l, d M Y', strtotime($data1->date)) }}</small>
                                    <small class='ml-1 text-muted'>{{ date("H:i a", strtotime($data1->date)) }}</small>
                                </h5>

                                <p>{{ ucfirst($data1->comment) }}</p>

                                @if($data1->countFiles > 0)
                                    <span class="text-info"><small>Download:</small></span>
                                    <div class='comment-footer'>
                                        <?php $ia=1; ?>
                                        @foreach($commentFiles as $commentFile)
                                            @if($commentFile->comment_id == $data1->id)
                                                <span class="text-danger ml-1">#{{ $ia }}.</span><a href="{{ asset('img/comment-file/troubleshooting/'.$commentFile->image) }}">[{{ $commentFile->image }}]</a>
                                            @endif
                                            <?php $ia++; ?>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @elseif($data1->publisher_type == 'tech')
                    @foreach($techs as $tech)
                        @if($tech->id == $data1->publisher_id)
                            <img src="{{ asset('admintheme/images/users/'.$tech->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                            <div class='media-body'>
                                <span class="float-right text-right p-0">
                                    @if(Auth::user()->id == $data1->publisher_id)
                                        <button type="button" class="badge badge-dark" data-toggle="modal" data-target="#uploadModal{{ $data1->id }}"> Upload file</button>
                                    @endif
                                    @if($data1->countFiles > 0)
                                        <br><span class="badge badge-danger">{{ $data1->countFiles }} file</span>
                                    @endif
                                </span>

                                <h5 class='mt-0'>
                                    <a href='#' class='text-dark'>{{ ucfirst($tech->firstname).' '.ucfirst($tech->lastname) }}</a>
                                    <small class='ml-1 text-muted'>{{ date('l, d M Y', strtotime($data1->date)) }}</small>
                                    <small class='ml-1 text-muted'>{{ date("H:i a", strtotime($data1->date)) }}</small>
                                </h5>

                                <p>{{ ucfirst($data1->comment) }}</p>

                                @if($data1->countFiles > 0)
                                    <span class="text-info"><small>Download:</small></span>
                                    <div class='comment-footer'>
                                        <?php $i=1; ?>
                                        @foreach($commentFiles as $commentFile)
                                            @if($commentFile->comment_id == $data1->id)
                                                <span class="text-danger ml-1">#{{ $i }}.</span><a href="{{ asset('img/comment-file/troubleshooting/'.$commentFile->image) }}">[{{ $commentFile->image }}]</a>
                                            @endif
                                            <?php $i++; ?>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @else
                    @foreach($admins as $admin)
                        @if($admin->id == $data1->publisher_id)
                            <img src="{{ asset('admintheme/images/users/'.$admin->image) }}" alt='' class='comment-avatar avatar-sm rounded mr-2'>
                            <div class='media-body'>
                                <span class="float-right text-right p-0">
                                    @if(Auth::user()->id == $data1->publisher_id)
                                        <button type="button" class="badge badge-dark" data-toggle="modal" data-target="#uploadModal{{ $data1->id }}"> Upload file</button>
                                    @endif
                                    @if($data1->countFiles > 0)
                                        <br><span class="badge badge-danger">{{ $data1->countFiles }} file</span>
                                    @endif
                                </span>

                                <h5 class='mt-0'>
                                    <a href='#' class='text-dark'>{{ ucfirst($admin->firstname).' '.ucfirst($admin->lastname) }}</a>
                                    <small class='ml-1 text-muted'>{{ date('l, d M Y', strtotime($data1->date)) }}</small>
                                    <small class='ml-1 text-muted'>{{ date("H:i a", strtotime($data1->date)) }}</small>
                                </h5>

                                <p>{{ ucfirst($data1->comment) }}</p>

                                @if($data1->countFiles > 0)
                                    <span class="text-info"><small>Download:</small></span>
                                    <div class='comment-footer'>
                                        <?php $i=1; ?>
                                        @foreach($commentFiles as $commentFile)
                                            @if($commentFile->comment_id == $data1->id)
                                                <span class="text-danger ml-1">#{{ $i }}.</span><a href="{{ asset('img/comment-file/troubleshooting/'.$commentFile->image) }}">[{{ $commentFile->image }}]</a>
                                            @endif
                                            <?php $i++; ?>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>

        <!-- modal start -->
            <div class="modal fade" id="uploadModal{{ $data1->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
                    <div class="modal-content">
                        <div class="modal-header no-bd">
                            <h5 class="modal-title">
                                <span class="fw-mediumbold text-danger"> Tutup</span> 
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span class="text-danger" aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="w-100" action="{{ route($formCommentFileStore) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- hidden data -->
                            <input type="hidden" name="comment_id" value="{{ $data1->id }}">
                            <!-- hidden data end -->
                            <div class="row">
                                <div class="col-md{{ $errors->has('image') ? ' has-error' : '' }}">
                                    <div class="card-box">
                                        <h4 class="header-title mb-3"><small>File untuk </small><br><span class="text-info">{{ ucfirst($data1->comment) }}</span></h4>
                                        <input type="file" name="image" class="dropify" data-max-file-size="1M" data-default-file="{{ asset('img/comment-file/default.png') }}" />
                                    </div>
                                </div>
                                <div class="w-100"></div>
                                <div class="col-md mt-2 mb-2">
                                    <button id="btn_upload" type="submit" class="btn btn-orange" name="submit">Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <!-- modal start end -->

        @endforeach
    @endif
<!-- comments end -->

@endsection