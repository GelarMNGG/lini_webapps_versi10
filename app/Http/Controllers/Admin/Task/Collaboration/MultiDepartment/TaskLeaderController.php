<?php

namespace App\Http\Controllers\Admin\Task\Collaboration\MultiDepartment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use DB;

class TaskLeaderController extends Controller
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
    public function index()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $liniId = 1; //lini
        
        if ($userType == 'admin') {
            $data['admins'] = DB::table('admins')->get();
            $data['users'] = DB::table('users')->get();
            if ($userCompany == $liniId) {
                $data['departments'] = DB::table('department')->get();
            }else{
                $data['departments'] = DB::table('department_lintaslog')->get();
            }
            $data['usersDummy'] = DB::table('users')->where('department_id',$userDepartment)->limit(5)->get();
            $data['staffDataMulti'] = DB::table('tasks_leaders_pic')->get();
    
            //BOD
            if ($userDepartment == 11) {
                //given tasks list
                $data['countData'] = DB::table('tasks_leaders')->count();
        
                $data['tasks'] = DB::table('tasks_leaders as tl')
                    ->select([
                        'tl.*',
                        //inisiator admin
                        DB::raw('(SELECT firstname FROM admins WHERE admins.id = tl.publisher_id AND tl.publisher_type = \'admin\' AND admins.id IS NOT NULL) as admin_firstname'),
                        DB::raw('(SELECT lastname FROM admins WHERE admins.id = tl.publisher_id AND tl.publisher_type = \'admin\' AND admins.id IS NOT NULL) as admin_lastname'),
                        //inisiator co admin
                        DB::raw('(SELECT firstname FROM users WHERE users.id = tl.publisher_id AND tl.publisher_type = \'user\' AND users.id IS NOT NULL) as user_firstname'),
                        DB::raw('(SELECT lastname FROM users WHERE users.id = tl.publisher_id AND tl.publisher_type = \'user\' AND users.id IS NOT NULL) as user_lastname'),
                        //dept name
                        DB::raw('(SELECT name FROM department WHERE department.id = tl.publisher_department) as dept_name'),
                        //task level
                        DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = tl.level) as level_title'),
                        //leaders file count
                        DB::raw('(SELECT COUNT(image) FROM tasks_leaders_files WHERE tasks_leaders_files.task_id = tl.id) as file_count'),
                        //comment file count
                        DB::raw('(SELECT COUNT(image) FROM tasks_leaders_comments_files WHERE tasks_leaders_comments_files.task_id = tl.id) as comment_file_count'),
                        DB::raw('(SELECT COUNT(task_id) FROM tasks_leaders_comments WHERE tasks_leaders_comments.task_id = tl.id) as comment_count'),
                        //todo file count
                        DB::raw('(SELECT COUNT(image) FROM tasks_leaders_todos_files WHERE tasks_leaders_todos_files.task_id = tl.id) as todo_file_count'),
                        //count todo
                        DB::raw('(SELECT COUNT(status) FROM tasks_leaders_todos WHERE tasks_leaders_todos.task_id = tl.id AND tasks_leaders_todos.status = 1) as done_count'),
                        DB::raw('(SELECT COUNT(status) FROM tasks_leaders_todos WHERE tasks_leaders_todos.task_id = tl.id AND tasks_leaders_todos.status = 0) as onprogress_count'),
                    ])
                    //->where('publisher_id','!=',$userId)
                    ->orderBy('status','ASC')
                    ->orderBy('created_at','DESC')
                    ->paginate(10);
            }else{
                //given tasks list
                $data['countData'] = DB::table('tasks_leaders')->where('publisher_id',$userId)->whereOr('receiver_department','LIKE','%'.$userDepartment.'%')->count();
        
                $data['tasks'] = DB::table('tasks_leaders as tl')
                    ->select([
                        'tl.*',
                        //inisiator admin
                        DB::raw('(SELECT firstname FROM admins WHERE admins.id = tl.publisher_id AND tl.publisher_type = \'admin\' AND admins.id IS NOT NULL) as admin_firstname'),
                        DB::raw('(SELECT lastname FROM admins WHERE admins.id = tl.publisher_id AND tl.publisher_type = \'admin\' AND admins.id IS NOT NULL) as admin_lastname'),
                        //inisiator co admin
                        DB::raw('(SELECT firstname FROM users WHERE users.id = tl.publisher_id AND tl.publisher_type = \'user\' AND users.id IS NOT NULL) as user_firstname'),
                        DB::raw('(SELECT lastname FROM users WHERE users.id = tl.publisher_id AND tl.publisher_type = \'user\' AND users.id IS NOT NULL) as user_lastname'),
                        //dept name
                        DB::raw('(SELECT name FROM department WHERE department.id = tl.publisher_department) as dept_name'),
                        //task level
                        DB::raw('(SELECT tl_name FROM tasks_level WHERE tasks_level.tl_id = tl.level) as level_title'),
                        //leaders file count
                        DB::raw('(SELECT COUNT(image) FROM tasks_leaders_files WHERE tasks_leaders_files.task_id = tl.id) as file_count'),
                        //comment file count
                        DB::raw('(SELECT COUNT(image) FROM tasks_leaders_comments_files WHERE tasks_leaders_comments_files.task_id = tl.id) as comment_file_count'),
                        DB::raw('(SELECT COUNT(task_id) FROM tasks_leaders_comments WHERE tasks_leaders_comments.task_id = tl.id) as comment_count'),
                        //todo file count
                        DB::raw('(SELECT COUNT(image) FROM tasks_leaders_todos_files WHERE tasks_leaders_todos_files.task_id = tl.id) as todo_file_count'),
                        //count todo
                        DB::raw('(SELECT COUNT(status) FROM tasks_leaders_todos WHERE tasks_leaders_todos.task_id = tl.id AND tasks_leaders_todos.status = 1) as done_count'),
                        DB::raw('(SELECT COUNT(status) FROM tasks_leaders_todos WHERE tasks_leaders_todos.task_id = tl.id AND tasks_leaders_todos.status = 0) as onprogress_count'),
                    ])
                    //->where('publisher_id',$userId)
                    ->whereOr('publisher_department',$userDepartment)
                    ->whereOr('receiver_department','LIKE','%'.$userDepartment.'%')
                    ->orderBy('status','ASC')
                    ->orderBy('created_at','DESC')
                    ->paginate(10);
            }
    
            return view('admin.task.collaboration.task-leader.index', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Kolaborasi Multi Departemen.');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $userId = Auth::user()->id;
        $userType = Auth::user()->user_type;

        $coAdmin = 22; //co admin level
        $employeeStatus = 1; //active
        
        if ($userType == 'admin') {
            $data['clientDatas'] = DB::table('clients')->get();
            $data['departmentDatas'] = DB::table('department')->get();
            $data['taskPriorities'] = DB::table('tasks_level')->get();

            $data['coAdmins'] = DB::table('users')->where('user_level',$coAdmin)->where('active',$employeeStatus)->get();
    
            return view('admin.task.collaboration.task-leader.create', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Kolaborasi Multi Departemen.');
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
        $userDepartment = Auth::user()->department_id;

        if ($userType == 'admin') {
            $request->validate([
                'title' => 'required|min:5',
                'description' => 'required|min:5',
                'receiver_department' => 'required',
                'date_start' => 'required|after_or_equal:today',
                'date_end' => 'after_or_equal:task_date',
            ]);
    
            // date setting
            $data = $request->except('_token','submit');
            if ($request->receiver_department) {
                $data['receiver_department'] = serialize($request->receiver_department);
            }
            if ($request->coadmin_id) {
                $data['coadmin_id'] = serialize($request->coadmin_id);
            }
            $data['publisher_id'] = $userId;
            $data['publisher_type'] = $userType;
            $data['publisher_department'] = $userDepartment;
            $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
            //insert to database
            DB::table('tasks_leaders')->insert($data);
    
            //send notifications
                $theDepartment = $request->receiver_department;
                $taskLeader = DB::table('tasks_leaders')->select('id')->orderBy('id','DESC')->first();
                $theId = $taskLeader->id;
                $theTitle = $data['title'];
    
                $dataNotif['publisher_id'] = $userId;
                $dataNotif['publisher_type'] = $userType;
                $dataNotif['publisher_department'] = $userDepartment;
                $publisherName = Auth::user()->name;
                $publisherFirstname = Auth::user()->firstname;
                $publisherLastname = Auth::user()->lastname;
                if ($publisherFirstname !== null) {
                    $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
                }else{
                    $publisherName = ucfirst($publisherName);
                }
                ###receiver id & type
                $collaboratorDatas = DB::table('admins')->select('id','user_type','department_id')->where('id','!=',$userId)->where('department_id','!=',NULL)->get();
    
                foreach ($collaboratorDatas as $dataAlpha) {
                    if (in_array($dataAlpha->department_id,$theDepartment)) {
                        $dataNotif['receiver_id'] = $dataAlpha->id;
                        $dataNotif['receiver_type'] = $dataAlpha->user_type;
                        $dataNotif['receiver_department'] = $dataAlpha->department_id;
                        $dataNotif['level'] = 1;
    
                        ###notif message
                        $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengajak Anda berkolaborasi dalam proyek <a href='".route('task-leaders.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a>.</strong>";
                        ###insert data to notifications table
                        $notifData = DB::table('notifications')->insert($dataNotif);
                    }
                }
            //send notifications end
    
            return redirect()->route('task-leaders.index')->with('alert-success','Collaborative leader task berhasil disimpan.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Kolaborasi Multi Departemen.');
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
        $userDepartment = Auth::user()->department_id;

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
        ->where('status',1)
        ->where('active',1)
        ->orderBy('department.id','ASC')
        ->get();

        //$data['users'] = DB::table('users')->get();

        $dataCheck = DB::table('tasks_leaders as tasks')
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
        //coadmin check
        $dataCoadmin = $dataCheck->coadmin_id;
        if (isset($dataCoadmin)) {
            $data['coAdminDatas'] = unserialize($dataCoadmin);
        }else{
            $data['coAdminDatas'] = [];
        }
        $dataReceiverDepartment = $dataCheck->receiver_department;
        if (isset($dataReceiverDepartment)) {
            $data['taskDataReceiverDepartments'] = unserialize($dataReceiverDepartment);
        }else{
            $data['taskDataReceiverDepartments'] = [];
        }

        //$data['taskDataReceiverDepartments'] = unserialize($data['taskData']->receiver_department);
        $data['leadersFiles'] = DB::table('tasks_leaders_files')->where('task_id',$id)->get();
        $data['leadersTodos'] = DB::table('tasks_leaders_todos')->where('task_id',$id)->get();
        $data['leadersTodoFiles'] = DB::table('tasks_leaders_todos_files')->where('task_id',$id)->get();
        $data['leadersTodosCount'] = DB::table('tasks_leaders_todos as tlt')
            ->selectRaw('count(case when status = 1 then 1 end) as done_count')
            ->selectRaw('count(case when status = 0 then 1 end) as onprogress_count')
            ->where('task_id',$id)->first();

        //other data
        $data['dataComments'] = DB::table('tasks_leaders_comments as tc')
            ->select([
                'tc.*',
                DB::raw('(SELECT COUNT(comment_id) FROM tasks_leaders_comments_files WHERE tasks_leaders_comments_files.comment_id = tc.id) countFiles')
            ])
            ->where('task_id', $id)
            ->orderBy('id','DESC')
            ->get();

        ###comment count
        $data['countComments'] = DB::table('tasks_leaders_comments')
            ->where('task_id', $id)
            ->count();
        
        //supporting datas
        $data['commentFiles'] = DB::table('tasks_leaders_comments_files')->where('task_id',$id)->get();
        $data['picDatas'] = DB::table('tasks_leaders_pic as tlc')
        ->select([
            'tlc.*',
            DB::raw('(SELECT firstname FROM users WHERE users.id = tlc.pic_id) as pic_firstname'),
            DB::raw('(SELECT lastname FROM users WHERE users.id = tlc.pic_id) as pic_lastname'),
        ])
        ->where('task_id',$id)
        ->get();
        
        return view('admin.task.collaboration.task-leader.show', $data);
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
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //co admin level
        $employeeStatus = 1; //active

        $firstCheck = DB::table('tasks_leaders')->where('id',$id)->where('publisher_department',$userDepartment)->first();

        if (!isset($firstCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }
        
        if ($userType == 'admin') {
            $data['taskData'] = $firstCheck;
            $data['taskDataDepartment'] = unserialize($firstCheck->receiver_department);
            $data['coAdminDatas'] = unserialize($firstCheck->coadmin_id);

            $data['coAdmins'] = DB::table('users')->where('user_level',$coAdmin)->where('active',$employeeStatus)->get();

            //supporting datas
            $data['clientDatas'] = DB::table('clients')->get();
            $data['departmentDatas'] = DB::table('department')->get();
            $data['taskPriorities'] = DB::table('tasks_level')->get();
    
            return view('admin.task.collaboration.task-leader.edit', $data);
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Kolaborasi Multi Departemen.');
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
        $userDepartment = Auth::user()->department_id;

        $firstCheck = DB::table('tasks_leaders')->where('id',$id)->where('publisher_department',$userDepartment)->first();

        if (!isset($firstCheck)) {
            return redirect()->back()->with('alert-danger','Halaman yang akan Anda tuju tidak tersedia.');
        }

        if ($userType == 'admin') {
            $request->validate([
                'title' => 'required|min:5',
                'description' => 'required|min:5',
                'date_start' => 'required',
                'date_end' => 'after_or_equal:task_date',
            ]);
    
            // date setting
            $data = $request->except('_token','submit','_method');
            $newCoAdmin = $request->coadmin_id;
            $existingCoAdmin = unserialize($firstCheck->coadmin_id);
            if ($newCoAdmin != NULL && $existingCoAdmin != NULL) {
                $data['coadmin_id'] = serialize(array_merge($newCoAdmin,$existingCoAdmin));
            }elseif($newCoAdmin != NULL){
                $data['coadmin_id'] = serialize($newCoAdmin);
            }

            $newDepartment = $request->receiver_department;
            $existingDepartment = unserialize($firstCheck->receiver_department);
            if ($newDepartment != NULL && $existingDepartment != NULL) {
                $data['receiver_department'] = serialize(array_merge($newDepartment,$existingDepartment));
            }elseif($newDepartment != NULL){
                $data['receiver_department'] = serialize($newDepartment);
            }
            $data['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
    
            //insert to database
            DB::table('tasks_leaders')->where('id',$id)->update($data);
    
            //send notifications
                $theDepartment = $request->receiver_department;
                $theCoAdmin = $request->coadmin_id;
                //$taskLeader = DB::table('tasks_leaders')->select('id')->orderBy('id','DESC')->first();
                //$theId = $taskLeader->id;
                $theId = $id;
                $theTitle = $data['title'];
    
                $dataNotif['publisher_id'] = $userId;
                $dataNotif['publisher_type'] = $userType;
                $dataNotif['publisher_department'] = $userDepartment;
                $publisherName = Auth::user()->name;
                $publisherFirstname = Auth::user()->firstname;
                $publisherLastname = Auth::user()->lastname;
                if ($publisherFirstname !== null) {
                    $publisherName = ucfirst($publisherFirstname).' '.ucfirst($publisherLastname);
                }else{
                    $publisherName = ucfirst($publisherName);
                }
                ###receiver id & type
                $collaboratorDatas = DB::table('admins')->select('id','user_type','department_id')->where('id','!=',$userId)->where('department_id','!=',NULL)->get();
    
                if ($theDepartment != NULL) {
                    foreach ($collaboratorDatas as $dataAlpha) {
                        if (in_array($dataAlpha->department_id,$theDepartment)) {
                            $dataNotif['receiver_id'] = $dataAlpha->id;
                            $dataNotif['receiver_type'] = $dataAlpha->user_type;
                            $dataNotif['receiver_department'] = $dataAlpha->department_id;
                            $dataNotif['level'] = 1;
        
                            ###notif message
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> mengajak Anda berkolaborasi dalam proyek <a href='".route('task-leaders.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a>.</strong>";
                            ###insert data to notifications table
                            $notifData = DB::table('notifications')->insert($dataNotif);
                        }
                    }
                }

                $coAdmin = 22;
                $collaboratorDatas = DB::table('users')->select('id','user_type','department_id')->where('user_level',$coAdmin)->get();
                if ($theCoAdmin != NULL) {
                    foreach ($collaboratorDatas as $dataAlpha) {
                        if (in_array($dataAlpha->id,$theCoAdmin)) {
                            $dataNotif['receiver_id'] = $dataAlpha->id;
                            $dataNotif['receiver_type'] = $dataAlpha->user_type;
                            $dataNotif['receiver_department'] = $dataAlpha->department_id;
                            $dataNotif['level'] = 1;
        
                            ###notif message
                            $dataNotif['desc'] = "<strong>".$publisherName."</strong> menunjuk Anda sebagai Co Admin dalam proyek <a href='".route('user-task-leaders.show',$theId)."'><strong>".ucfirst($theTitle)."</strong></a>.</strong>";
                            ###insert data to notifications table
                            $notifData = DB::table('notifications')->insert($dataNotif);
                        }
                    }
                }

            //send notifications end
    
            return redirect()->route('task-leaders.show',$id)->with('alert-success','Data collaborative leader task berhasil diubah.');
        }

        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Kolaborasi Multi Departemen.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Task Kolaborasi Multi Departemen.');
    }
}
