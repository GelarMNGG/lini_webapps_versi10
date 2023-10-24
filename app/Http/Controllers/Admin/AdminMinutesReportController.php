<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class AdminMinutesReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;
        
        $published = 1;
        $staffType = 'user';
        $skin = $request->skin;

        if ($skin == 1) {
            $data['skin'] = 0;
            $data['skinBack'] = 1;

            if ($userCompany == 2) {
                $data['userMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                    //department name
                    DB::raw('(SELECT name FROM department_lintaslog WHERE department_lintaslog.id = min.publisher_department) as department_name'),
                    //task
                    #DB::raw('(SELECT task_title FROM tasks WHERE tasks.receiver_department = '.$userDepartment.') as task_name')
                ])
                ->where('publisher_company',$userCompany)
                ->where('publisher_type',$staffType)
                ->where('published',$published)
                ->orderBy('status','ASC')
                ->orderBy('date','DESC')
                ->orderBy(DB::raw('YEAR(date), MONTH(date), HOUR(event_start)'),'DESC')
                ->get();
    
                return view('admin.minutes-report.index-table',$data);
            }elseif($userCompany == 1){
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
                ->orderBy('date','DESC')
                ->orderBy(DB::raw('YEAR(date), MONTH(date), HOUR(event_start)'),'DESC')
                ->get();
    
                return view('admin.minutes-report.index-table',$data);
            }else{
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activities Report.');
            }
        }else{
            $data['skin'] = 1;
            $data['skinBack'] = 0;
            $data['userLevels'] = DB::table('users_level')->get();

            if ($userCompany == 2) {
                $data['userMinutes'] = DB::table('minutes as min')
                ->select([
                    'min.*',
                    DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                    //level_id
                    DB::raw('(SELECT user_level FROM users WHERE users.id = min.publisher_id) as user_level'),
                    //category
                    DB::raw('(SELECT name FROM minutes_category WHERE minutes_category.id = min.minute_cat AND minutes_category.department_id = '.$userDepartment.') as category_name'),
                    //department name
                    DB::raw('(SELECT name FROM department_lintaslog WHERE department_lintaslog.id = min.publisher_department) as department_name'),
                    //task
                    #DB::raw('(SELECT task_title FROM tasks WHERE tasks.receiver_department = '.$userDepartment.') as task_name')
                ])
                ->where('publisher_company',$userCompany)
                ->where('publisher_type',$staffType)
                ->where('published',$published)
                ->orderBy('status','ASC')
                ->orderBy('date','DESC')
                ->orderBy(DB::raw('YEAR(date), MONTH(date), HOUR(event_start)'),'DESC')
                ->paginate(10);
                
                return view('admin.minutes-report.index',$data);
            }elseif($userCompany == 1){
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
                ->orderBy('date','DESC')
                ->orderBy(DB::raw('YEAR(date), MONTH(date), HOUR(event_start)'),'DESC')
                ->paginate(10);
                
                return view('admin.minutes-report.index',$data);
            }else{
                return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activities Report.');
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activities Report.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userId = Auth::user()->id;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $staffType = 'user';
        $published = 1;

        if ($userCompany == 2) {
            //department datas
            $data["lintaslogDepartments"] = DB::table("department_lintaslog as dl")
            ->select([
                "dl.*",
                DB::raw("(SELECT COUNT(*) FROM minutes WHERE minutes.publisher_company = $userCompany AND minutes.publisher_department = dl.id) as dept_count")
            ])
            ->orderBy('id','ASC')->get();

            //minutes datas
            $data['minutesDatas'] = DB::table('minutes as min')
            ->select([
                'min.*',
                DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
                DB::raw('(SELECT department_id FROM users WHERE users.id = min.publisher_id) as dept_id'),
                DB::raw('COUNT(min.publisher_id) as pub_count')
            ])
            ->where('publisher_type',$staffType)
            ->where('publisher_company',$userCompany)
            ->where('published',$published)
            ->orderBy('status','ASC')
            ->orderBy('publisher_id','ASC')
            ->orderBy('publisher_department','ASC')
            //->limit(25)
            ->groupBy('publisher_id')
            ->get();
            
    
            if (!isset($data['lintaslogDepartments'])) {
                return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
            }
    
            $data['adminProfile'] = DB::table('admins')
            ->select([
                'admins.*',
                DB::raw('(SELECT name FROM department WHERE department.id = admins.department_id) as department_name')
            ])
            ->where('id',$userId)->where('department_id',$userDepartment)->first();
    
            $data['userProfiles'] = DB::table('users')->where('company_id',$userCompany)->whereNull('deleted_at')->get();
    
            return view('admin.minutes-report.report-lin',$data);

        }elseif($userCompany == 1){
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
    
            $data['adminProfile'] = DB::table('admins')
            ->select([
                'admins.*',
                DB::raw('(SELECT name FROM department WHERE department.id = admins.department_id) as department_name')
            ])
            ->where('id',$userId)->where('department_id',$userDepartment)->first();
    
            $data['userProfiles'] = DB::table('users')->where('department_id',$userDepartment)->whereNull('deleted_at')->get();
    
            return view('admin.minutes-report.report',$data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activities Report.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activities Report.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activities Report.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        //custom param
        $data['skin'] = substr($id,-1);
        $id = explode('&', $id)[0];

        if ($userCompany == 1) {
            $data['departmentDatas'] = DB::table('department')->get();
            $data['minutesCats'] = DB::table('minutes_category')->where('department_id',$userDepartment)->get();
    
            $data['userMinute'] = DB::table('minutes as min')
            ->select([
                'min.*',
                DB::raw('(SELECT firstname FROM users WHERE users.id = min.publisher_id) as firstname'),
                DB::raw('(SELECT lastname FROM users WHERE users.id = min.publisher_id) as lastname'),
            ])
            ->where('id',$id)->where('publisher_department',$userDepartment)->first();
            
            if (isset($data['userMinute'])) {
                return view('admin.minutes-report.edit', $data);
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activities Report.');
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
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $data['skin'] = substr($id,-1);
        $id = explode('&', $id)[0];
        $staffType = 'user';
        $skin = $data['skin'];

        if ($userCompany == 1) {
            $request->validate([
                'grade' => 'required',
                'name' => 'required',
                'event_start' => 'required',
                'event_end' => 'required|after:event_start',
            ]);
    
            //file handler
            $fileName = null;
            $destinationPath = public_path().'/img/minutes/user/';
            
            // Retrieving An Uploaded File
            $file = $request->file('image');
            if (!empty($file)) {
                $extension = $file->getClientOriginalExtension();
                $fileName = time().'_'.$file->getClientOriginalName();
        
                // Moving An Uploaded File
                $request->file('image')->move($destinationPath, $fileName);
    
                //delete previous image
                $dataImage = DB::table('minutes')->select('image as image')->where('id', $id)->first();
                $oldImage = $dataImage->image;
    
                if($oldImage !== 'default.png'){
                    $image_path = $destinationPath.$oldImage;
                    if(File::exists($image_path)) {
                        File::delete($image_path);
                    }
                }
            }
    
            //custom setting to support file upload
            $data = $request->except(['_token','_method','submit']);
            
            $data['event_start'] = date('H:i:s', strtotime($request->event_start));
            $data['event_end'] = date('H:i:s', strtotime($request->event_end));
    
            if (!empty($fileName)) {
                $data['image'] = $fileName;
            }
    
            $checkData = DB::table('minutes')->where('id',$id)->where('publisher_type',$staffType)->where('publisher_department',$userDepartment)->count();
    
            if ($checkData > 0) {
                DB::table('minutes')->where('id',$id)->update($data);
                
                return redirect()->route('admin-minutes-report.index','?skin='.$skin)->with('alert-success','Data berhasil diubah.');
            }
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activities Report.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Activities Report.');
    }

    public function customReport(Request $request)
    {
        $userId = Auth::user()->id;
        $userDepartment = Auth::user()->department_id;
        $published = 1;
        
        $date = $request->date;
        $publisherId = $request->publisher_id;

        $minuteDone = 1; //done
        $taskDone = 3; //done

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
                    ->whereRaw("(date < ?)", [$date." 00:00:01"])
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
                    ->whereRaw("(task_date < ?)", [$date." 00:00:01"])
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
                ->whereRaw("(date < ?)", [$date." 00:00:01"])
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
                ->whereRaw("(task_date < ?)", [$date." 00:00:01"])
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
                return redirect()->route('admin-minutes-report.create')->with('alert-danger','Anda belum memilih nama staff atau tanggal.');
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

        $data['adminProfile'] = DB::table('admins')
        ->select([
            'admins.*',
            DB::raw('(SELECT name FROM department WHERE department.id = admins.department_id) as department_name')
        ])
        ->where('id',$userId)->where('department_id',$userDepartment)->first();

        $data['userProfiles'] = DB::table('users')->where('department_id',$userDepartment)->whereNull('deleted_at')->get();

        return view('admin.minutes-report.report-custom',$data);
    }
    public function customReportLintaslog(Request $request)
    {
        $userId = Auth::user()->id;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;
        $published = 1;
        
        $date = $request->date;
        $publisherId = $request->publisher_id;

        $minuteDone = 1; //done
        $taskDone = 3; //done

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
                    ->whereRaw("(date < ?)", [$date." 00:00:01"])
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
                    ->whereRaw("(task_date < ?)", [$date." 00:00:01"])
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
                ->whereRaw("(date < ?)", [$date." 00:00:01"])
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
                ->whereRaw("(task_date < ?)", [$date." 00:00:01"])
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
                return redirect()->route('admin-minutes-report.create')->with('alert-danger','Anda belum memilih nama staff atau tanggal.');
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

        $data['staffData'] = DB::table('users')
        ->select([
            'users.*',
            DB::raw('(SELECT name FROM department_lintaslog WHERE department_lintaslog.id = users.department_id) as department_name')
        ])
        ->where('id',$publisherId)->where('company_id',$userCompany)->whereNull('deleted_at')->first();

        $data['adminProfile'] = DB::table('admins')
        ->select([
            'admins.*',
            DB::raw('(SELECT name FROM department WHERE department.id = admins.department_id) as department_name')
        ])->where('id',$userId)->where('department_id',$userDepartment)->first();

        $data['userProfiles'] = DB::table('users')->where('company_id',$userCompany)->whereNull('deleted_at')->get();

        return view('admin.minutes-report.report-custom',$data);
    }
}
