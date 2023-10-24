<?php

namespace App\Http\Controllers\User\Task\Collaboration\Internal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TaskInternalController extends Controller
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
    public function index()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //userlevel
        $liniId = 1;

        $coAdminCount = DB::table('tasks_internal')->where('coadmin_id','LIKE','%'.$userId.'%')->count();
        
        $staffCount = DB::table('tasks_internal_pic')->where('pic_id',$userId)->count();

        if ($staffCount > 0 || $coAdminCount > 0) {
            $data['admins'] = DB::table('admins')->get();
            $data['users'] = DB::table('users')->get();
            if ($userCompany == $liniId) {
                $data['departments'] = DB::table('department')->get();
            }else{
                $data['departments'] = DB::table('department_lintaslog')->get();
            }
            $data['usersDummy'] = DB::table('users')->where('department_id',$userDepartment)->limit(5)->get();
            $data['staffDataInternal'] = DB::table('tasks_internal_pic')->get();
    
            //coadmin data
            $dataCoadmin = DB::table('tasks_internal')
                ->where('coadmin_id','LIKE','%'.$userId.'%')
                ->where('department_id',$userDepartment)
                ->orderBy('status','ASC')
                ->orderBy('created_at','DESC')
                ->pluck('id');
            
            //pic data
            $dataPic = DB::table('tasks_internal as ti')
                ->leftJoin('tasks_internal_pic','tasks_internal_pic.task_id','ti.id')
                ->where('tasks_internal_pic.pic_id',$userId)
                ->where('ti.department_id',$userDepartment)
                ->orderBy('ti.status','ASC')
                ->orderBy('ti.created_at','DESC')
                ->pluck('ti.id');
            
            $dataStaff1 = $dataCoadmin->toArray();
            $dataStaff2 = $dataPic->toArray();
            $staffArray = array_unique(array_merge($dataStaff1,$dataStaff2));
            
            $data['tasks'] = DB::table('tasks_internal as ti')
                ->select([
                    'ti.*',
                    //inisiator admin
                    DB::raw('(SELECT firstname FROM admins WHERE admins.id = ti.publisher_id AND ti.publisher_type = \'admin\' AND admins.id IS NOT NULL) as admin_firstname'),
                    DB::raw('(SELECT lastname FROM admins WHERE admins.id = ti.publisher_id AND ti.publisher_type = \'admin\' AND admins.id IS NOT NULL) as admin_lastname'),
                    DB::raw('(SELECT title FROM admins WHERE admins.id = ti.publisher_id AND ti.publisher_type = \'admin\' AND admins.id IS NOT NULL) as admin_title'),
                    //inisiator co admin
                    DB::raw('(SELECT firstname FROM users WHERE users.id = ti.publisher_id AND ti.publisher_type = \'user\' AND users.id IS NOT NULL) as user_firstname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = ti.publisher_id AND ti.publisher_type = \'user\' AND users.id IS NOT NULL) as user_lastname'),
                    DB::raw('(SELECT lastname FROM users WHERE users.id = ti.publisher_id AND ti.publisher_type = \'user\' AND users.id IS NOT NULL) as user_lastname'),
                    //level name
                    DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = ti.level) as level_title'),
                    //internal file count
                    DB::raw('(SELECT COUNT(image) FROM tasks_internal_files WHERE tasks_internal_files.task_id = ti.id) as file_count'),
                    //comment file count
                    DB::raw('(SELECT COUNT(image) FROM tasks_internal_comments_files WHERE tasks_internal_comments_files.task_id = ti.id) as comment_file_count'),
                    DB::raw('(SELECT COUNT(task_id) FROM tasks_internal_comments WHERE tasks_internal_comments.task_id = ti.id) as comment_count'),
                    //todo file count
                    DB::raw('(SELECT COUNT(image) FROM tasks_internal_todos_files WHERE tasks_internal_todos_files.task_id = ti.id) as todo_file_count'),
                    //count todo
                    DB::raw('(SELECT COUNT(status) FROM tasks_internal_todos WHERE tasks_internal_todos.task_id = ti.id AND tasks_internal_todos.status = 1) as done_count'),
                    DB::raw('(SELECT COUNT(status) FROM tasks_internal_todos WHERE tasks_internal_todos.task_id = ti.id AND tasks_internal_todos.status = 0) as onprogress_count'),
                ])
                ->where('publisher_id',$userId)
                ->orWhereIn('ti.id',$staffArray)
                ->orderBy('status','ASC')
                ->orderBy('created_at','DESC')
                ->paginate(10);
    
            //total data
            $data['countData'] = $data['tasks']->total();
            //coadmin check
            if ($userLevel == $coAdmin) {
                $data['coAdminCheck'] = 1;
            }else{
                $data['coAdminCheck'] = 0;
            }

            return view('user.task.collaboration.task-internal.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Internal Department Collaboration Task.');
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
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //co admin level
        $employeeStatus = 1; //active
        
        if ($userLevel == $coAdmin) {
            $data['clientDatas'] = DB::table('clients')->get();
            $data['taskPriorities'] = DB::table('tasks_level')->get();

            $data['coAdmins'] = DB::table('users')->where('department_id',$userDepartment)->where('user_level',$coAdmin)->where('active',$employeeStatus)->get();
    
            return view('user.task.collaboration.task-internal.create', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Internal Department Collaboration Task.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //coadmin

        if ($userLevel == $coAdmin) {
            $request->validate([
                'title' => 'required|min:5',
                'description' => 'required|min:5',
                'date_start' => 'required|after_or_equal:today',
                'date_end' => 'after_or_equal:task_date',
            ]);
    
            // date setting
            $data = $request->except('_token','submit');
            if (isset($request->coadmin_id)) {
                $data['coadmin_id'] = serialize($request->coadmin_id);
            }
            $data['department_id'] = $userDepartment;
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
            //insert to database
            DB::table('tasks_internal')->insert($data);
    
            return redirect()->route('user-task-internal.index')->with('alert-success','Collaborative leader task berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Internal Department Collaboration Task.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userLevel = Auth::user()->user_level;
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //userlevel

        $staffCount = DB::table('tasks_internal_pic')->where('task_id',$id)->where('pic_id',$userId)->count();
        $coAdminCount = DB::table('tasks_internal')->where('id',$id)->where('coadmin_id','LIKE','%'.$userId.'%')->count();
        $publisherCount = DB::table('tasks_internal')->where('id',$id)->where('publisher_id',$userId)->where('publisher_type',$userType)->count();

        if ($staffCount < 1 && $coAdminCount < 1 && $publisherCount < 1) {
            return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task.');
        }

        //key data
        $data['userId'] = $userId;
        $data['userType'] = $userType;
        $data['userDepartment'] = $userDepartment;

        $data['users'] = DB::table('users')->get();
        $data['admins'] = DB::table('admins')
        ->leftJoin('department','department.id','admins.department_id')
        ->select([
            'admins.*',
            DB::raw('(SELECT name FROM department WHERE department.id = admins.department_id) as department_name')
        ])
        //->where('status',1)
        ->where('active',1)
        ->orderBy('department.id','ASC')
        ->get();

        $dataCheck = DB::table('tasks_internal as tasks')
            ->select([
                'tasks.*',
                DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = tasks.level) as task_level_title'),
                DB::raw('(SELECT ts_name FROM tasks_status WHERE tasks_status.ts_id = tasks.status) as task_status_name'),
                //category name
                DB::raw('(SELECT name FROM projects_category WHERE projects_category.id = tasks.category) as category_name'),
                //inisiator admin
                DB::raw('(SELECT firstname FROM admins WHERE admins.id = tasks.publisher_id AND tasks.publisher_type = \'admin\' AND admins.id IS NOT NULL) as admin_firstname'),
                DB::raw('(SELECT lastname FROM admins WHERE admins.id = tasks.publisher_id AND tasks.publisher_type = \'admin\' AND admins.id IS NOT NULL) as admin_lastname'),
                //inisiator co admin
                DB::raw('(SELECT firstname FROM users WHERE users.id = tasks.publisher_id AND tasks.publisher_type = \'user\' AND users.id IS NOT NULL) as user_firstname'),
                DB::raw('(SELECT lastname FROM users WHERE users.id = tasks.publisher_id AND tasks.publisher_type = \'user\' AND users.id IS NOT NULL) as user_lastname'),
            ])
            ->where('id', $id)
            //->whereJsonContains('receiver_department', $userDepartment)
            ->first();

        if (isset($dataCheck)) {
            $data['taskData'] = $dataCheck;
        }else{
            return redirect()->back()->with('alert-danger','Halaman yang Anda tuju tidak tersedia.');
        }

        $dataCoadmin = $dataCheck->coadmin_id;
        if (isset($dataCoadmin)) {
            if($dataCoadmin !== 0){
                $data['coAdminDatas'] = unserialize($dataCoadmin);
            }else{
                $data['coAdminDatas'] = [];
            }
        }else{
            $data['coAdminDatas'] = [];
        }
        $data['internalFiles'] = DB::table('tasks_internal_files')->where('task_id',$id)->get();
        $data['internalTodos'] = DB::table('tasks_internal_todos')->where('task_id',$id)->get();
        $data['internalTodoFiles'] = DB::table('tasks_internal_todos_files')->where('task_id',$id)->get();
        $data['internalTodosCount'] = DB::table('tasks_internal_todos as tlt')
            ->selectRaw('count(case when status = 1 then 1 end) as done_count')
            ->selectRaw('count(case when status = 0 then 1 end) as onprogress_count')
            ->where('task_id',$id)->first();

        //other data
        $data['dataComments'] = DB::table('tasks_internal_comments as tc')
            ->select([
                'tc.*',
                DB::raw('(SELECT COUNT(comment_id) FROM tasks_internal_comments_files WHERE tasks_internal_comments_files.comment_id = tc.id) countFiles')
            ])
            ->where('task_id', $id)
            ->orderBy('id','DESC')
            ->get();

        ###comment count
        $data['countComments'] = DB::table('tasks_internal_comments')
            ->where('task_id', $id)
            ->count();
        
        //supporting datas
        $data['commentFiles'] = DB::table('tasks_internal_comments_files')->get();
        $data['picDatas'] = DB::table('tasks_internal_pic as tlc')
        ->select([
            'tlc.*',
            DB::raw('(SELECT firstname FROM users WHERE users.id = tlc.pic_id) as pic_firstname'),
            DB::raw('(SELECT lastname FROM users WHERE users.id = tlc.pic_id) as pic_lastname'),
        ])
        ->where('task_id',$id)
        ->get();

        return view('user.task.collaboration.task-internal.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //co admin level
        $employeeStatus = 1; //active
        $liniId = 1; //lini

        $firstCheck = DB::table('tasks_internal')->where('id',$id)->where('publisher_id',$userId)->first();

        if (!isset($firstCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        if ($userCompany == $liniId) {
            $data['taskData'] = $firstCheck;
            $dataCoadmin = $firstCheck->coadmin_id;
            if (isset($dataCoadmin)) {
                if($dataCoadmin !== 0){
                    $data['coAdminDatas'] = unserialize($dataCoadmin);
                }else{
                    $data['coAdminDatas'] = [];
                }
            }else{
                $data['coAdminDatas'] = [];
            }

            $data['coAdmins'] = DB::table('users')->where('department_id',$userDepartment)->where('user_level',$coAdmin)->where('active',$employeeStatus)->get();

            //supporting datas
            $data['clientDatas'] = DB::table('clients')->get();
            $data['departmentDatas'] = DB::table('department')->get();
            $data['taskPriorities'] = DB::table('tasks_level')->get();
    
            return view('user.task.collaboration.task-internal.edit', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Internal Department Collaboration Task.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Internal Department Collaboration Task.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Internal Department Collaboration Task.');
    }
}
