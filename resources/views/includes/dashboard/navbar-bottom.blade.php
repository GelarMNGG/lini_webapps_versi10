<div class="container">
    <nav class="navbar navbar-dark bg-info navbar-expand fixed-bottom d-big-none d-xl-none" style="height:59px;overflow-x:scroll; overflow-y:hidden;">
        <ul class="navbar-nav nav-justified w-100">
            @if(strtolower(Auth::user()->user_type) == 'admin')
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link icon-big no-border"> <i class="mdi mdi-view-dashboard"></i> </a>
                </li>
                <!-- IT department -->
                    <li class="nav-item">
                        <a href="{{ route('apps-dev-logs.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-laptop-code"></i> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('flash-messages.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-bullhorn"></i> </a>
                    </li>
                <!-- IT department end -->
                <!-- project department -->
                    @if(Auth::user()->department_id == 1 || Auth::user()->role == 1)
                    <li class="nav-item">
                        <a href="{{ route('admin-projects.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-project-diagram"></i> </a>
                    </li>
                    @endif
                <!-- project department -->
                <!-- general affair -->
                    @if(Auth::user()->department_id == 4 || Auth::user()->role == 1)
                    <li class="nav-item">
                        <a href="{{ route('admin-covid-test.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-hospital-user"></i> </a>
                    </li>
                    @endif
                <!-- general affair -->
                <!-- procurement department -->
                    @if(Auth::user()->department_id == 9 || Auth::user()->role == 1)
                    <li class="nav-item">
                        <a href="{{ route('admin-tech.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-users-cog"></i> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin-pr.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-file-invoice-dollar"></i> </a>
                    </li>
                    @endif
                <!-- procurement department -->
                <li class="nav-item">
                    <a href="{{ route('admin-attendance.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-calendar-check"></i> </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin-minutes.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-running"></i> </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin-collaboration.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-tasks"></i> </a>
                </li>

                @if(Auth::user()->role == 1)
                    <li class="nav-item">
                        <a href="{{ route('admin-blog.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-clipboard-list"></i> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('service.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-wrench"></i> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('department.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-cogs"></i> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('company-info.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-building"></i> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('slider.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-images"></i> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('client.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-city"></i> </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('teamuser.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-users-cog"></i> </a>
                    </li>
                @endif
                @if(Auth::user()->role == 2)
                    <li class="nav-item">
                        <a href="{{ route('teamuser.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-users-cog"></i> </a>
                    </li>
                @endif
            @endif
            @if(strtolower(Auth::user()->user_type) == 'cust')
                <li class="nav-item">
                    <a href="{{ route('cust.dashboard') }}" class="nav-link icon-big no-border"> <i class="mdi mdi-view-dashboard"></i> </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('cust-projects.dashboard') }}" class="nav-link icon-big no-border"> <i class="fas fa-project-diagram"></i> </a>
                </li>
            @endif
            @if(Auth::user()->user_type == 'tech')
                <li class="nav-item">
                    <a href="{{ route('tech.dashboard') }}" class="nav-link icon-big no-border"> <i class="mdi mdi-view-dashboard"></i> </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('project-tech.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-project-diagram"></i> </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tech-troubleshooting.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-hand-rock"></i> </a>
                </li>
                @if($dataTechCount < 1)
                    <li class="nav-item">
                        <a href="{{ route('tech-input-data-diri.index') }}" class="nav-link icon-big no-border">
                            <i class="fas fa-user-edit"></i>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('tech-input-data-diri.index') }}" class="nav-link icon-big no-border">
                            <i class="fas fa-user-edit"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('tech-test-training.index') }}" class="nav-link icon-big no-border">
                            <i class="fas fa-user-graduate"></i>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a href="{{ route('tech.paymentprocedure') }}" class="nav-link icon-big no-border"> <i class="fas fa-file-invoice"></i> </a>
                </li>
            @endif
            @if(strtolower(Auth::user()->user_type) == 'user')
                <li class="nav-item">
                    <a href="{{ route('user.index') }}" class="nav-link icon-big no-border"> <i class="mdi mdi-view-dashboard"></i> </a>
                </li>

                <!-- project department -->
                @if(Auth::user()->department_id == 1)
                    <li class="nav-item">
                        <a href="{{ route('user-projects.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-project-diagram"></i> </a>
                    </li>
                    @if(Auth::user()->user_level == 3)
                        <li class="nav-item">
                            <a href="{{ route('user-projects-template.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-cogs"></i> </a>
                        </li>
                    @endif 
                @endif
                <!-- project department end -->

                <!-- general affair -->
                @if(Auth::user()->department_id == 4)
                    @if(Auth::user()->user_level == 7)
                        <li class="nav-item">
                            <a href="{{ route('user-covid-test.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-hospital-user"></i> </a>
                        </li>
                    @endif 
                @endif
                <!-- general affair end -->
                
                <!-- IT -->
                @if(Auth::user()->department_id == 5)
                    <li class="nav-item">
                        <a href="{{ route('user-apps-dev-logs.index') }}" class="nav-link icon-big no-border"> 
                            <i class="fas fa-laptop-code"></i> 
                        </a>
                    </li>
                @endif
                <!-- IT end -->

                <li class="nav-item">
                    <a href="{{ route('attendance.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-calendar-check"></i> </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('user-minutes.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-running"></i> </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('user-collaboration.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-tasks"></i> </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('troubleshooting.index') }}" class="nav-link icon-big no-border"> <i class="fas fa-hand-rock"></i> </a>
                </li>
            @endif
        </ul>
    </nav>
</div>