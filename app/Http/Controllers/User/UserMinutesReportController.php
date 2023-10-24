<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class UserMinutesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth' => 'verified']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        
        $published = 1;
        $staffType = 'user';
        $skin = $request->skin;
        $coAdmin = 22;

        if ($userLevel == $coAdmin) {
            if ($skin == 1) {
                $data['skin'] = 0;
                $data['skinBack'] = 1;
    
                $data['userMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                    //task
                    #DB::raw('(SELECT task_title FROM tasks WHERE tasks.receiver_department = '.$userDepartment.') as task_name')
                ])
                ->where('publisher_type',$staffType)
                ->where('publisher_department',$userDepartment)
                ->where('published',$published)
                ->orderBy('status','ASC')
                ->orderBy(DB::raw('YEAR(date), MONTH(date), HOUR(event_start)'),'DESC')
                ->get();
        
                return view('user.minutes-report.index-table',$data);
            }else{
                $data['skin'] = 1;
                $data['skinBack'] = 0;
                $data['userLevels'] = DB::table('users_level')->get();
                
                $data['userMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                    //level_id
                    DB::raw('(SELECT user_level FROM users WHERE users.id = min.publisher_id) as user_level'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                    //task
                    #DB::raw('(SELECT task_title FROM tasks WHERE tasks.receiver_department = '.$userDepartment.') as task_name')
                ])
                ->where('publisher_type',$staffType)
                ->where('publisher_department',$userDepartment)
                ->where('published',$published)
                ->orderBy('status','ASC')
                ->orderBy(DB::raw('YEAR(date), MONTH(date), HOUR(event_start)'),'DESC')
                ->paginate(10);
                
        
                return view('user.minutes-report.index',$data);
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activity Report.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $staffType = 'user';
        $published = 1;
        $coAdmin = 22;

        if ($userLevel == $coAdmin) {
            $data['minutesDatas'] = DB::table('minutes as min')
            ->select([
                'min.*',
                DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
            ])
            ->where('publisher_type',$staffType)
            ->where('publisher_department',$userDepartment)
            ->orderBy('status','ASC')
            ->orderBy('publisher_id','ASC')
            ->where('published',$published)
            ->paginate(35);

            if (!isset($data['minutesDatas'])) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }

            $data['adminProfile'] = DB::table('users')
            ->select([
                'users.*',
                DB::raw('(SELECT name FROM department WHERE department.id = users.department_id) as department_name')
            ])
            ->where('id',$userId)->where('department_id',$userDepartment)->first();

            $data['userProfiles'] = DB::table('users')->where('department_id',$userDepartment)->whereNull('deleted_at')->get();

            return view('user.minutes-report.report',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activity Report.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activity Report.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activity Report.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activity Report.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activity Report.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activity Report.');
    }

    public function customReport(Request $request)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;
        $published = 1;
        
        $date = $request->date;
        $publisherId = $request->publisher_id;

        $minuteDone = 1; //done
        $taskDone = 3; //done
        $coAdmin = 22;

        if ($userLevel == $coAdmin) {
            if ($date != null) {
                if ($publisherId != 0) {
                    $minutesDatas = DB::table('minutes as min')
                        ->select([
                            'min.*',
                            DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                        ])
                        ->whereRaw( "(date >= ? AND date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                        ->where('publisher_id',$publisherId)
                        ->where('publisher_department',$userDepartment)
                        ->where('published',$published)
                        ->where('status',$minuteDone)
                        ->orderBy('status','DESC')
                        ->get();
                    //pending
                    $pendingMinutesDatas = DB::table('minutes as min')
                        ->select([
                            'min.*',
                            DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                        ])
                        ->whereRaw("(date <= ?)", [$date." 23:59:59"])
                        ->where('publisher_id',$publisherId)
                        ->where('publisher_department',$userDepartment)
                        ->where('published',$published)
                        ->where('status','<',$minuteDone)
                        ->orderBy('status','DESC')
                        ->get();
    
                    //tasks data
                    $tasksDatas = DB::table('tasks')
                        ->select([
                            'tasks.*',
                            DB::raw('(SELECT firstname FROM users WHERE users.id = tasks.task_receiver_id) as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = tasks.task_receiver_id) as lastname'),
                        ])
                        ->whereRaw( "(task_date >= ? AND task_date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                        ->where('task_receiver_id',$publisherId)
                        ->where('receiver_department',$userDepartment)
                        ->orderBy('task_status','DESC')
                        ->get();
                    //pending
                    $pendingTasksDatas = DB::table('tasks')
                        ->select([
                            'tasks.*',
                            DB::raw('(SELECT firstname FROM users WHERE users.id = tasks.task_receiver_id) as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = tasks.task_receiver_id) as lastname'),
                        ])
                        ->whereRaw("(task_date <= ?)", [$date." 23:59:59"])
                        ->where('task_receiver_id',$publisherId)
                        ->where('receiver_department',$userDepartment)
                        ->where('task_status','<',$taskDone)
                        ->orderBy('task_status','DESC')
                        ->get();
                        //->paginate(35);
                }else{
                    $minutesDatas = DB::table('minutes as min')
                    ->select([
                        'min.*',
                        DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                        DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                    ])
                    ->whereRaw( "(date >= ? AND date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                    ->where('publisher_department',$userDepartment)
                    ->where('published',$published)
                    ->orderBy('status','DESC')
                    ->get();
                    //pending
                    $pendingMinutesDatas = DB::table('minutes as min')
                    ->select([
                        'min.*',
                        DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                        DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                    ])
                    ->whereRaw("(date <= ?)", [$date." 23:59:59"])
                    ->where('publisher_department',$userDepartment)
                    ->where('published',$published)
                    ->where('status','<',$minuteDone)
                    ->orderBy('status','DESC')
                    ->get();
    
                    //tasks data
                    $tasksDatas = DB::table('tasks')
                    ->select([
                        'tasks.*',
                        DB::raw('(SELECT firstname FROM users WHERE users.id = tasks.task_receiver_id) as firstname'),
                        DB::raw('(SELECT lastname FROM users WHERE users.id = tasks.task_receiver_id) as lastname'),
                    ])
                    ->whereRaw( "(task_date >= ? AND task_date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                    ->where('task_receiver_id',$publisherId)
                    ->where('receiver_department',$userDepartment)
                    ->orderBy('task_status','DESC')
                    ->get();
                    //pending
                    $pendingTasksDatas = DB::table('tasks')
                    ->select([
                        'tasks.*',
                        DB::raw('(SELECT firstname FROM users WHERE users.id = tasks.task_receiver_id) as firstname'),
                        DB::raw('(SELECT lastname FROM users WHERE users.id = tasks.task_receiver_id) as lastname'),
                    ])
                    ->whereRaw("(task_date <= ?)", [$date." 23:59:59"])
                    ->where('task_receiver_id',$publisherId)
                    ->where('receiver_department',$userDepartment)
                    ->where('task_status','<',$taskDone)
                    ->orderBy('task_status','DESC')
                    ->get();
                    //->paginate(35);
                }
            }else{
                if ($publisherId != 0) {
                    $minutesDatas = DB::table('minutes as min')
                        ->select([
                            'min.*',
                            DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                        ])
                        ->where('publisher_id',$publisherId)
                        ->where('publisher_department',$userDepartment)
                        ->where('published',$published)
                        ->orderBy('status','DESC')
                        ->get();
                    //pending
                    $pendingMinutesDatas = [];
                    
                    //tasks data
                    $tasksDatas = DB::table('tasks')
                        ->select([
                            'tasks.*',
                            DB::raw('(SELECT firstname FROM users WHERE users.id = tasks.task_receiver_id) as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = tasks.task_receiver_id) as lastname'),
                        ])
                        ->where('task_receiver_id',$publisherId)
                        ->where('receiver_department',$userDepartment)
                        ->orderBy('task_status','DESC')
                        ->get();
                        //->paginate(35);
                    //pending
                    $pendingTasksDatas = [];
    
                }else{
                    return redirect()->route('user-minutes-report.create')->with('alert-danger','Anda belum memilih nama staff atau tanggal.');
                }
            }
    
            $data['minutesDatas'] = $minutesDatas;
            $data['pendingMinutesDatas'] = $pendingMinutesDatas;
            $data['tasksDatas'] = $tasksDatas;
            $data['pendingTasksDatas'] = $pendingTasksDatas;
            $data['publisherId'] = $publisherId;
            $data['date'] = $date;
            
            if (!isset($minutesDatas)) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
    
            $data['staffData'] = DB::table('users')->where('id',$publisherId)->where('department_id',$userDepartment)->whereNull('deleted_at')->first();
    
            $data['adminProfile'] = DB::table('users')
            ->select([
                'users.*',
                DB::raw('(SELECT name FROM department WHERE department.id = users.department_id) as department_name')
            ])
            ->where('id',$userId)->where('department_id',$userDepartment)->first();
    
            $data['userProfiles'] = DB::table('users')->where('department_id',$userDepartment)->whereNull('deleted_at')->get();
        }

        return view('user.minutes-report.report-custom',$data);
    }

    public function customReportLintaslog(Request $request)
    {
        $userId = Auth::user()->id;
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;
        $published = 1;
        
        $date = $request->date;
        $publisherId = $request->publisher_id;

        $minuteDone = 1; //done
        $taskDone = 3; //done
        $coAdmin = 22;

        if ($userLevel == $coAdmin) {
            if ($date != null) {
                if ($publisherId != 0) {
                    $minutesDatas = DB::table('minutes as min')
                        ->select([
                            'min.*',
                            DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                        ])
                        ->whereRaw( "(date >= ? AND date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                        ->where('publisher_id',$publisherId)
                        ->where('publisher_company',$userCompany)
                        ->where('published',$published)
                        ->where('status',$minuteDone)
                        ->orderBy('status','DESC')
                        ->get();
                    //pending
                    $pendingMinutesDatas = DB::table('minutes as min')
                        ->select([
                            'min.*',
                            DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                        ])
                        ->whereRaw("(date <= ?)", [$date." 23:59:59"])
                        ->where('publisher_id',$publisherId)
                        ->where('publisher_company',$userCompany)
                        ->where('published',$published)
                        ->where('status','<',$minuteDone)
                        ->orderBy('status','DESC')
                        ->get();
    
                    //tasks data
                    $tasksDatas = DB::table('tasks')
                        ->select([
                            'tasks.*',
                            DB::raw('(SELECT firstname FROM users WHERE users.id = tasks.task_receiver_id) as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = tasks.task_receiver_id) as lastname'),
                        ])
                        ->whereRaw( "(task_date >= ? AND task_date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                        ->where('task_receiver_id',$publisherId)
                        //->where('receiver_department',$userDepartment)
                        ->orderBy('task_status','DESC')
                        ->get();
                    //pending
                    $pendingTasksDatas = DB::table('tasks')
                        ->select([
                            'tasks.*',
                            DB::raw('(SELECT firstname FROM users WHERE users.id = tasks.task_receiver_id) as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = tasks.task_receiver_id) as lastname'),
                        ])
                        ->whereRaw("(task_date <= ?)", [$date." 23:59:59"])
                        ->where('task_receiver_id',$publisherId)
                        //->where('receiver_department',$userDepartment)
                        ->where('task_status','<',$taskDone)
                        ->orderBy('task_status','DESC')
                        ->get();
                        //->paginate(35);
                }else{
                    $minutesDatas = DB::table('minutes as min')
                    ->select([
                        'min.*',
                        DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                        DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                    ])
                    ->whereRaw( "(date >= ? AND date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                    ->where('publisher_company',$userCompany)
                    ->where('published',$published)
                    ->orderBy('status','DESC')
                    ->get();
                    //pending
                    $pendingMinutesDatas = DB::table('minutes as min')
                    ->select([
                        'min.*',
                        DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                        DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                    ])
                    ->whereRaw("(date <= ?)", [$date." 23:59:59"])
                    ->where('publisher_company',$userCompany)
                    ->where('published',$published)
                    ->where('status','<',$minuteDone)
                    ->orderBy('status','DESC')
                    ->get();
    
                    //tasks data
                    $tasksDatas = DB::table('tasks')
                    ->select([
                        'tasks.*',
                        DB::raw('(SELECT firstname FROM users WHERE users.id = tasks.task_receiver_id) as firstname'),
                        DB::raw('(SELECT lastname FROM users WHERE users.id = tasks.task_receiver_id) as lastname'),
                    ])
                    ->whereRaw( "(task_date >= ? AND task_date <= ?)", [$date." 00:00:00", $date." 23:59:59"])
                    ->where('task_receiver_id',$publisherId)
                    //->where('receiver_department',$userDepartment)
                    ->orderBy('task_status','DESC')
                    ->get();
                    //pending
                    $pendingTasksDatas = DB::table('tasks')
                    ->select([
                        'tasks.*',
                        DB::raw('(SELECT firstname FROM users WHERE users.id = tasks.task_receiver_id) as firstname'),
                        DB::raw('(SELECT lastname FROM users WHERE users.id = tasks.task_receiver_id) as lastname'),
                    ])
                    ->whereRaw("(task_date <= ?)", [$date." 23:59:59"])
                    ->where('task_receiver_id',$publisherId)
                    //->where('receiver_department',$userDepartment)
                    ->where('task_status','<',$taskDone)
                    ->orderBy('task_status','DESC')
                    ->get();
                    //->paginate(35);
                }
            }else{
                if ($publisherId != 0) {
                    $minutesDatas = DB::table('minutes as min')
                        ->select([
                            'min.*',
                            DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                        ])
                        ->where('publisher_id',$publisherId)
                        ->where('publisher_company',$userCompany)
                        ->where('published',$published)
                        ->orderBy('status','DESC')
                        ->get();
                    //pending
                    $pendingMinutesDatas = [];
                    
                    //tasks data
                    $tasksDatas = DB::table('tasks')
                        ->select([
                            'tasks.*',
                            DB::raw('(SELECT firstname FROM users WHERE users.id = tasks.task_receiver_id) as firstname'),
                            DB::raw('(SELECT lastname FROM users WHERE users.id = tasks.task_receiver_id) as lastname'),
                        ])
                        ->where('task_receiver_id',$publisherId)
                        //->where('receiver_department',$userDepartment)
                        ->orderBy('task_status','DESC')
                        ->get();
                        //->paginate(35);
                    //pending
                    $pendingTasksDatas = [];
    
                }else{
                    return redirect()->route('user-minutes-report.create')->with('alert-danger','Anda belum memilih nama staff atau tanggal.');
                }
            }
    
            $data['minutesDatas'] = $minutesDatas;
            $data['pendingMinutesDatas'] = $pendingMinutesDatas;
            $data['tasksDatas'] = $tasksDatas;
            $data['pendingTasksDatas'] = $pendingTasksDatas;
            $data['publisherId'] = $publisherId;
            $data['date'] = $date;
            
            if (!isset($minutesDatas)) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
    
            $data['staffData'] = DB::table('users')->select([
                'users.*',
                DB::raw('(SELECT name FROM department_lintalog WHERE department_lintalog.id = users.department_id) as department_name')
            ])
            ->where('id',$publisherId)->where('department_id',$userDepartment)->whereNull('deleted_at')->first();
    
            $data['adminProfile'] = DB::table('users')
            ->select([
                'users.*',
                DB::raw('(SELECT name FROM department WHERE department.id = users.department_id) as department_name')
            ])
            ->where('id',$userId)->where('department_id',$userDepartment)->first();
    
            $data['userProfiles'] = DB::table('users')->where('department_id',$userDepartment)->whereNull('deleted_at')->get();
        }

        return view('user.minutes-report.report-custom',$data);
    }
}
