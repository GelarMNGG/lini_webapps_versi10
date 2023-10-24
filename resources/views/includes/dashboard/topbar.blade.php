<!-- Topbar Start -->
<div class="navbar-custom bg-orange">
    <ul class="list-unstyled topnav-menu float-right mb-0">
        <li class="d-none d-sm-block">
            <form class="app-search">
                <div class="app-search-box">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search...">
                        <div class="input-group-append">
                            <button class="btn" type="submit">
                                <i class="fe-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </li>
        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle  waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="fe-bell noti-icon t-white"></i>
                @if ($notifCount > 0 && $notifCount < 10)
                    <span class='badge badge-danger rounded-circle noti-icon-badge'>{{ $notifCount }}</span>
                @elseif ($notifCount >= 10 && $notifCount <= 20 )
                    <span class='badge badge-danger rounded-circle noti-icon-badge' style="line-height: 1.5em; top: 9px;">{{ $notifCount }}</span>
                @elseif ($notifCount >= 21)
                    <span class='badge badge-danger rounded-circle noti-icon-badge' style="line-height: 2.4em; top: 0; right: 0px;">>21</span>
                @else
                    <span class='badge badge-danger rounded-circle noti-icon-badge' style="display:none"></span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-lg">
                <!-- item-->
                @if ($notifCount > 0)
                    <div class="dropdown-item noti-title">
                        <h5 class="m-0">
                            <span class="float-right">
                                <form action="{{ route($linkClearNotif,'all=1') }}" id="updatestatus-form" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input class="form-control" type="number" name="all" value="1" hidden>
                                    <span class='float-right'><small>
                                    <button class="submit submit_alert text-danger" type="submit">Bersihkan semua</button> </small> </span>
                                </form>
                            </span>
                            <span class="left-title">Notifikasi</span>
                        </h5>
                    </div>
                    <div class="slimscroll noti-scroll">
                        @foreach ($notifDatas as $notifData)
                            <div class='dropdown-item notify-item active'>
                                <!-- user akun -->
                                @if(strtolower($notifData->publisher_type) == 'user')
                                    @foreach ($userDatas as $userData)
                                        @if ($userData->id == $notifData->publisher_id)
                                            <div class='notify-icon'>
                                                <img src="{{ asset('admintheme/images/users/'.$userData->image) }}" class='img-fluid rounded-circle' alt="{{ $userData->firstname }}" />
                                            </div>
                                            <form action="{{ route( $formNotifUpdateAction,$notifData->id) }}" id="updatestatus-form" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input class="form-control" type="number" name="status" value="1" hidden>
                                                <span class='float-right'><small>
                                                <button class="submit submit_alert text-danger" type="submit">Bersihkan</button> </small> </span>
                                            </form>
                                            <p class='notify-details'> {{ ucwords($userData->firstname) .' '. ucwords($userData->lastname) }} 
                                            </p>
                                        @endif
                                    @endforeach
                                @endif
                                <!-- user akun -->
                                <!-- tech akun -->
                                @if(strtolower($notifData->publisher_type) == 'tech')
                                    @foreach ($techDatas as $techData)
                                        @if ($techData->id == $notifData->publisher_id)
                                            <div class='notify-icon'>
                                                <img src="{{ asset('admintheme/images/users/'.$techData->image) }}" class='img-fluid rounded-circle' alt="{{ $techData->firstname }}" />
                                            </div>
                                            <form action="{{ route( $formNotifUpdateAction,$notifData->id) }}" id="updatestatus-form" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input class="form-control" type="number" name="status" value="1" hidden>
                                                <span class='float-right'><small>
                                                <button class="submit submit_alert text-danger" type="submit">Bersihkan</button> </small> </span>
                                            </form>
                                            <p class='notify-details'> {{ ucwords($techData->firstname) .' '. ucwords($techData->lastname) }}
                                            </p>
                                        @endif
                                    @endforeach
                                @endif
                                <!-- tech akun -->
                                <!-- admin akun -->
                                @if(strtolower($notifData->publisher_type) == 'admin')
                                    @foreach ($adminDatas as $userData)
                                        @if ($userData->id == $notifData->publisher_id)
                                            <div class='notify-icon'>
                                                <img src="{{ asset('admintheme/images/users/'.$userData->image) }}" class='img-fluid rounded-circle' alt="{{ $userData->firstname }}" />
                                            </div>
                                            <form action="{{ route( $formNotifUpdateAction,$notifData->id) }}" id="updatestatus-form" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input class="form-control" type="number" name="status" value="1" hidden>
                                                <span class='float-right'><small>
                                                <button class="submit submit_alert text-danger" type="submit">Bersihkan</button> </small> </span>
                                            </form>
                                            <p class='notify-details'> {{ ucwords($userData->firstname) .' '. ucwords($userData->lastname) }} 
                                            </p>
                                        @endif
                                    @endforeach
                                @endif
                                <!-- admin akun -->
                                <p class='text-muted mb-0 user-msg'>
                                    <span class="notif-desc small">{!! ucfirst($notifData->desc) !!}</span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                    <!-- All-->
                    @if (Auth::user()->user_type == 'admin')
                        <a href="{{ route('notifikasi-admin.index') }}" class="dropdown-item text-center text-primary notify-item notify-all">
                    @elseif(Auth::user()->user_type == 'tech')
                        <a href="{{ route('notifikasi-tech.index') }}" class="dropdown-item text-center text-primary notify-item notify-all">
                    @else
                        <a href="{{ route('notifikasi-user.index') }}" class="dropdown-item text-center text-primary notify-item notify-all">
                    @endif
                        <span class="small">View all</span>
                        <i class="fi-arrow-right"></i>
                    </a>
                @else
                    <div class="dropdown-item noti-title">
                        <h5 class="m-0">Notifikasi</h5>
                    </div>
                    <div class="">
                        <a href='javascript:void(0);' class='dropdown-item notify-item active' style="margin-left:0px">
                            <p><small>Belum ada notifikasi.</small></p>
                        </a>
                    </div>
                @endif
            </div>
        </li>
        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <img src="{{ asset('admintheme/images/users/'.Auth::user()->image.'') }}" alt="user-image" class="rounded-circle">
                <span class="pro-user-name ml-1 t-white"> {{ Auth::user()->firstname ? ucfirst(Auth::user()->firstname) : ucfirst(Auth::user()->name) }} <i class="mdi mdi-chevron-down t-white"></i> </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                <!-- item-->
                <div class="dropdown-header noti-title">
                    <h6 class="text-overflow m-0">Selamat datang!</h6>
                </div>
                <!-- item-->
                <a href="{{ route($profileLink) }}" class="dropdown-item notify-item">
                    <i class="fe-user"></i>
                    <span>My Account</span>
                </a>
                <!-- item-->
                <a href="{{ route($changePasswordLink) }}" class="dropdown-item notify-item">
                    <i class="fe-settings"></i>
                    <span>Ganti Password</span>
                </a>
                <div class="dropdown-divider"></div>
                <!-- item-->
                @if (strtolower(Auth::user()->user_type) == 'admin')
                <a href="{{ route('admin.logout') }}" class="dropdown-item notify-item">
                @elseif (strtolower(Auth::user()->user_type) == 'tech')
                <a href="{{ route('tech.logout') }}" class="dropdown-item notify-item">
                @elseif (strtolower(Auth::user()->user_type) == 'cust')
                <a href="{{ route('cust.logout') }}" class="dropdown-item notify-item">
                @else
                <a href="{{ route('user.logout') }}" class="dropdown-item notify-item">
                @endif
                    <i class="fe-log-out"></i>
                    <span>Logout</span>
                </a>
            </div>
        </li>
    </ul>
    <!-- LOGO -->
    <div class="logo-box bg-reset">
        <a href="{{ route( $dashboardLink ) }}" class="logo logo-dark text-center">
            <span class="logo-lg">
                @if(Auth::user()->department_id == 10)
                    <img src="{{ asset('img/logo-translog.jpeg') }}" alt="logo Lintas Log" height="33">
                @else
                    <img src="{{ asset('img/'.$companyInfo->logo) }}" alt="logo {{ $companyInfo->name }}" height="33">
                @endif
            </span>
        </a>
    </div>
    <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
        <?php /*
        <li>
            <button class="button-menu-mobile disable-btn waves-effect">
                <i class="fe-menu"></i>
            </button>
        </li>
        */ ?>
        <li>
            <span class="page-title-main t-white"><strong>{{ ucfirst($pageTitle) }}</strong></span>
        </li>
    </ul>
</div>
<!-- end Topbar -->