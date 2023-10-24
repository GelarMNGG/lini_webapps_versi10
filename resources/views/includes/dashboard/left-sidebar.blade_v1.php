<div class="left-side-menu bg-reset">
    <div class="slimscroll-menu">
        <!-- User box -->
        <div class="user-box text-center">
            <img src="{{ asset('admintheme/images/users/'.Auth::user()->image.'') }}" alt="foto-{{ Auth::user()->name }}" title="{{ Auth::user()->name }}" class="rounded-circle img-thumbnail avatar-xl">
            <div class="dropdown">
                <div href="#" class="user-name h5 mt-2 mb-1 d-block t-white">{{ ucfirst(Auth::user()->firstname) }} {{ ucfirst(Auth::user()->lastname) }}</div>
            </div>
            <p class="text-muted pl-2 pr-2">
                @foreach($department as $departmentData)
                    @if($departmentData->id == Auth::user()->department_id)
                        <strong>{{ ucwords($departmentData->name) }} </strong> department
                    @endif
                @endforeach
                @if(Auth::user()->user_type == 'tech')
                    <strong>{{ ucwords(Auth::user()->title) }} </strong>
                @endif
            </p>
        </div>
        <hr>
        <!--- Sidemenu -->
        @if(strtolower(Auth::user()->user_type) == 'admin')
            <div id="sidebar-menu">
                <ul class="metismenu" id="side-menu">
                    <li class="menu-title">Navigation</li>
                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span> Dashboard </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-attendance.index') }}">
                            <i class="fas fa-calendar-check"></i>
                            <span> Attendance </span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-running"></i>
                            <span> Activity </span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{ route('admin-minutes.create') }}">Tambah aktivitas</a></li>
                            <li><a href="{{ route('admin-minutes.index') }}">Lihat semua aktivitas</a></li>
                            <li><a href="{{ route('admin-minutes-report.index') }}">Lihat aktivitas tim</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('client.index') }}">
                            <i class="fas fa-city"></i>
                            <span> Clients </span>
                        </a>
                    </li>
                    <!-- project department -->
                        @if(Auth::user()->company_id == 1 && Auth::user()->department_id == 1 || Auth::user()->role == 1)
                            <li>
                                <a href="javascript: void(0);">
                                    <i class="fas fa-project-diagram"></i>
                                    <span> Projects </span>
                                </a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="{{ route('admin-projects.create') }}">Tambah project</a></li>
                                    <li><a href="{{ route('admin-projects.index') }}">Lihat semua project</a></li>
                                    <li><a href="{{ route('admin-projects-category.index') }}">Lihat template report</a></li>
                                </ul>
                            </li>
                        @endif
                    <!-- project department -->
                    <!-- general affair -->
                        @if(Auth::user()->company_id == 1 && Auth::user()->department_id == 4 || Auth::user()->role == 1)
                            <li>
                                <a href="{{ route('admin-covid-test.index') }}">
                                    <i class="fas fa-first-aid"></i>
                                    <span> Test Covid </span>
                                </a>
                            </li>
                        @endif
                    <!-- general affair -->
                    <!-- procurement department -->
                        @if(Auth::user()->company_id == 1 && Auth::user()->department_id == 9 || Auth::user()->role == 1)
                            <li>
                                <a href="javascript: void(0);">
                                    <i class="fas fa-user-graduate"></i>
                                    <span> Talent Pool </span>
                                </a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="{{ route('admin-tech.index') }}">Daftar Teknisi</a></li>
                                    <li><a href="{{ route('admin-proc-assesment-question.index') }}">Pertanyaan Training</a></li>
                                    <li><a href="{{ route('admin-proc-test-psychology.index') }}">Pertanyaan Psikologi</a></li>
                                    <li><a href="{{ route('admin-proc-competency-test.index') }}">Pertanyaan Kompetensi</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="{{ route('admin-pr.index') }}">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <span> PR Request </span>
                                </a>
                            </li>
                        @endif
                    <!-- procurement department -->
                    <!-- IT department -->
                    @if(Auth::user()->company_id == 1 && Auth::user()->department_id == 5 || Auth::user()->role == 1)
                        <li>
                            <a href="{{ route('admin-user-acceptance-test.index') }}">
                                <i class="mdi mdi-order-bool-descending-variant"></i>
                                <span> User Acceptance Test </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('performance-summary.index') }}">
                                <i class="fas fa-chart-line"></i>
                                <span> Performance Summary </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('flash-messages.index') }}">
                                <i class="fas fa-bullhorn"></i>
                                <span> Send Flash Message </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin-cities.index') }}">
                                <i class="fas fa-globe-asia"></i>
                                <span> Provinces & Cities </span>
                            </a>
                        </li>
                    @endif
                    <!-- IT department end -->
                    <!-- Lintaslog -->
                    @if(Auth::user()->company_id == 2 && Auth::user()->department_id == 10 || Auth::user()->role == 1)
                        <li>
                            <a href="{{ route('department-lintaslog.index') }}">
                                <i class="fas fa-cogs"></i>
                                <span> Department </span>
                            </a>
                        </li>
                    @endif
                    <!-- Lintaslog -->
                    @if(Auth::user()->company_id == 1 && Auth::user()->department_id == 5 || Auth::user()->role == 1)
                        <li>
                            <a href="javascript: void(0);">
                                <i class="fas fa-laptop-code"></i>
                                <span> Apps Development </span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="{{ route('apps-dev-logs.index') }}">Logs</a></li>
                                <li><a href="{{ route('apps-update.index') }}">Update</a></li>
                            </ul>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('apps-dev-logs.index') }}">
                                <i class="fas fa-laptop-code"></i>
                                <span> Apps Dev Logs </span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('admin-wfh-to-wfo.index') }}">
                            <i class="fas fa-people-arrows"></i>
                            <span> WFH to WFO </span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-tasks"></i>
                            @if( $taskCount > 25)
                                <span class="badge badge-danger float-right">> 25</span>
                            @else
                                <span class="badge badge-warning float-right">{{ $taskCount }}</span>
                            @endif
                            <span> Taskboard </span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{ route('task.create') }}">Buat tugas</a></li>
                            <li><a href="{{ route('admin-collaboration.index') }}">Kolaborasi</a></li>
                            <li><a href="{{ route('task.index') }}">Lihat semua tugas</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-graduation-cap"></i>
                            <span class="badge badge-danger float-right">0</span>
                            <span> Knowledge base </span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="#">Troubleshooting</a></li>
                            <li><a href="#">Tutorial</a></li>
                        </ul>
                    </li>
                    @if(Auth::user()->role == 1)
                        <li>
                            <a href="{{ route('admin-blog.index') }}">
                                <i class="fas fa-clipboard-list"></i>
                                <span> Article </span>
                            </a>
                        </li>
                        <li class="menu-title">PENGATURAN</li>
                        <li>
                            <a href="{{ route('service.index') }}">
                                <i class="fas fa-wrench"></i>
                                <span> Services </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('department.index') }}">
                                <i class="fas fa-cogs"></i>
                                <span> Department </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('company-info.index') }}">
                                <i class="fas fa-building"></i>
                                <span> Company info </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('slider.index') }}">
                                <i class="fas fa-images"></i>
                                <span> Sliders </span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript: void(0);">
                                <i class="fas fa-users-cog"></i>
                                <span> Akun internal </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                @if(Auth::user()->role == 1)
                                    <li><a href="{{ route('team.index') }}">Akun admin</a></li>
                                @endif
                                <li><a href="{{ route('teamuser.index') }}">Akun user</a></li>
                            </ul>
                        </li>
                    @endif
                    @if(Auth::user()->role == 2)
                    <li class="menu-title">PENGATURAN</li>
                        <li>
                            <a href="javascript: void(0);">
                                <i class="fas fa-users-cog"></i>
                                <span> Akun internal </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="{{ route('teamuser.index') }}">Akun user</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        @endif
        <!-- cust -->
        @if(strtolower(Auth::user()->user_type) == 'cust')
            <div id="sidebar-menu">
                <ul class="metismenu" id="side-menu">
                    <li class="menu-title">Navigation</li>
                    <li>
                        <a href="{{ route('cust.dashboard') }}">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span> Dashboard </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cust-projects.dashboard') }}">
                            <i class="fas fa-project-diagram"></i>
                            <span> Projects </span>
                        </a>
                    </li>
                </ul>
            </div>
        @endif
        <!-- tech -->
        @if(strtolower(Auth::user()->user_type) == 'tech')
            <div id="sidebar-menu">
                <ul class="metismenu" id="side-menu">
                    <li class="menu-title">Navigation</li>
                    <li>
                        <a href="{{ route('tech.dashboard') }}">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span> Dashboard </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('project-tech.index') }}">
                            <i class="fas fa-project-diagram"></i>
                            <span> Projects </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tech-troubleshooting.index') }}">
                            <i class="fas fa-hand-rock"></i>
                            <span> Troubleshooting </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tech-bug-report.index') }}">
                            <i class="mdi mdi-book-search-outline"></i>
                            <span> Bug Report </span>
                        </a>
                    </li>
                    @if($dataTechCount < 1)
                        <li>
                            <a href="{{ route('tech-input-data-diri.index') }}">
                                <i class="fas fa-user-edit"></i>
                                <span> Input Data Diri </span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('tech-input-data-diri.index') }}">
                                <i class="fas fa-user-edit"></i>
                                <span> Edit Data Diri </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tech-test-training.index') }}">
                                <i class="fas fa-user-graduate"></i>
                                <span> Test & Training </span>
                            </a>
                        </li>
                    @endif
                    <?php /** hide for a while */
                    /*
                    <li>
                        <a href="#">
                            <i class="fas fa-wrench"></i>
                            <span> Tools </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('expenses-tech.index') }}">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span> Expenses </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('report-tech.index') }}">
                            <i class="fas fa-file-signature"></i>
                            <span> Laporan </span>
                        </a>
                    </li>
                    */?>
                    <li>
                        <a href="{{ route('tech.paymentprocedure') }}">
                            <i class="fas fa-file-invoice"></i>
                            <span> Prosedur pembayaran </span>
                        </a>
                    </li>
                </ul>
            </div>
        @endif
        <!-- user sidebar -->
        @if(strtolower(Auth::user()->user_type) == 'user')
            <div id="sidebar-menu">
                <ul class="metismenu" id="side-menu">
                    <li class="menu-title">Navigation</li>
                    <li>
                        <a href="{{ route('user.index') }}">
                            <i class="mdi mdi-view-dashboard"></i>
                            <span> Dashboard </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user-bug-report.index') }}">
                            <i class="mdi mdi-book-search-outline"></i>
                            <span> Bug Report </span>
                        </a>
                    </li>
                    @if(Auth::user()->company_id == 1 && Auth::user()->user_level == 22)
                    <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-running"></i>
                            <span> Activity </span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{ route('user-minutes.create') }}">Tambah aktivitas</a></li>
                            <li><a href="{{ route('user-minutes.index') }}">Lihat semua aktivitas</a></li>
                            <li><a href="{{ route('user-minutes-report.index') }}">Buat laporan aktivitas</a></li>
                        </ul>
                    </li>
                    @else
                    <li>
                        <a href="{{ route('user-minutes.index') }}">
                            <i class="fas fa-running"></i>
                            <span> Activity </span>
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('attendance.index') }}">
                            <i class="fas fa-calendar-check"></i>
                            <span> Attendance </span>
                        </a>
                    </li>
                    <!-- project department -->
                    @if(Auth::user()->company_id == 1 && Auth::user()->department_id == 1)
                        <!-- co admin -->
                        @if(Auth::user()->user_level == 22)
                        <li>
                            <a href="javascript: void(0);">
                                <i class="fas fa-project-diagram"></i>
                                <span> Projects </span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="{{ route('user-projects.create') }}">Tambah project</a></li>
                                <li><a href="{{ route('user-projects.index') }}">Lihat semua project</a></li>
                                <li><a href="{{ route('user-projects-category.index') }}">Lihat template report</a></li>
                            </ul>
                        </li>
                        @else
                            <li>
                                <a href="{{ route('user-projects.index') }}">
                                    <i class="fas fa-project-diagram"></i>
                                    <span> Projects </span>
                                </a>
                            </li>
                        @endif
                        <!-- project manager -->
                        @if(Auth::user()->user_level == 3)
                            <li>
                                <a href="{{ route('user-projects-template.index') }}">
                                    <i class="fas fa-cogs"></i>
                                    <span> Template </span>
                                </a>
                            </li>
                            <?php /*
                            <li>
                                <a href="{{ route('user-pr.index') }}">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <span> PR </span>
                                </a>
                            </li>
                            */ ?>
                        @endif
                    @endif
                    <!-- project department end -->
                    <!-- general affair -->
                    @if(Auth::user()->company_id == 1 && Auth::user()->department_id == 4)
                        @if(Auth::user()->user_level == 7)
                        <li>
                            <a href="{{ route('user-covid-test.index') }}">
                                <i class="fas fa-first-aid"></i>
                                <span> Test Covid </span>
                            </a>
                        </li>
                        @endif 
                    @endif
                    <!-- general affair end -->
                    <!-- Procurement Departement -->
                    @if(Auth::user()->company_id == 1 && Auth::user()->department_id == 9)
                        <li>
                            <a href="javascript: void(0);">
                                <i class="fas fa-user-graduate"></i>
                                <span> Talent Pool </span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                @if(Auth::user()->user_level == 22) <li><a href="{{ route('user-tech.index') }}">Daftar Teknisi</a> </li>@endif
                                <li><a href="{{ route('user-proc-assesment-question.index') }}">Pertanyaan Training</a></li>
                                <li><a href="{{ route('user-proc-test-psychology.index') }}">Pertanyaan Psikologi</a></li>
                                <li><a href="{{ route('user-proc-competency-test.index') }}">Pertanyaan Kompetensi</a></li>
                            </ul>
                        </li>
                    @endif
                    <!-- Procurement Departement end -->
                    <!-- IT -->
                    @if(Auth::user()->company_id == 1 && Auth::user()->department_id == 5)
                        <li>
                            <a href="{{ route('user-apps-dev-logs.index') }}">
                                <i class="fas fa-laptop-code"></i>
                                <span> Apps Dev Logs </span>
                            </a>
                        </li>
                    @endif
                    <!-- IT end -->
                    <li>
                        <a href="{{ route('user-wfh-to-wfo.index') }}">
                            <i class="fas fa-people-arrows"></i>
                            <span> WFH to WFO </span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('troubleshooting.index') }}">
                            <i class="fas fa-hand-rock"></i>
                            <span> Troubleshooting </span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-tasks"></i>
                            @if( $taskCount > 25)
                                <span class="badge badge-danger float-right">> 25</span>
                            @else
                                <span class="badge badge-warning float-right">{{ $taskCount }}</span>
                            @endif
                            <span> Taskboard </span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="{{ route('task-user.create') }}">Buat tugas</a></li>
                            <li><a href="{{ route('user-collaboration.index') }}">Kolaborasi</a></li>
                            <li><a href="{{ route('task-user.index') }}">Lihat semua tugas</a></li>
                        </ul>
                    </li>
                    <?php /*
                    <li>
                        <a href="javascript: void(0);">
                            <i class="fas fa-graduation-cap"></i>
                            <span class="badge badge-danger float-right">100</span>
                            <span> Knowledge base </span>
                        </a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="#">Troubleshooting</a></li>
                            <li><a href="#">Tutorial</a></li>
                        </ul>
                    </li>
                    */ ?>
                    @if(Auth::user()->company_id == 1 && Auth::user()->user_level == 22)
                    <li class="menu-title">PENGATURAN</li>
                        <li>
                            <a href="javascript: void(0);">
                                <i class="fas fa-users-cog"></i>
                                <span> Akun internal </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul class="nav-second-level" aria-expanded="false">
                                <li><a href="{{ route('user-teamuser.index') }}">Akun user</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        @endif
        <!-- End Sidebar -->
        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -left -->
</div>