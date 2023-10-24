<?php

namespace App\Http\Controllers\Admin\Task\Collaboration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;

class TaskCollaborationController extends Controller
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
        $userLevel = Auth::user()->user_level;
        $userCompany = Auth::user()->company_id;
        $userDepartment = Auth::user()->department_id;

        $coAdmin = 22; //userlevel
        $liniId = 1; //userlevel
        $data['countDataMulti'] = 0;
        $data['countDataInternal'] = 0;

        $data['admins'] = DB::table('admins')->get();
        $data['users'] = DB::table('users')->get();
        if ($userCompany == $liniId) {
            $data['departments'] = DB::table('department')->get();
        }else{
            $data['departments'] = DB::table('department_lintaslog')->get();
        }
        $data['usersDummy'] = DB::table('users')->where('department_id',$userDepartment)->limit(5)->get();
        
        #########multi department data#########
            $coAdminCountMulti = DB::table('tasks_leaders')->where('publisher_id',$userId)->whereOr('receiver_department','LIKE','%'.$userDepartment.'%')->count();
            $staffMulti = DB::table('tasks_leaders_pic')->get();
            //multi staff data
                $data['countDataMulti'] = $coAdminCountMulti;
                $data['staffDataMulti'] = $staffMulti;
                //$data['countData'] = DB::table('tasks_leaders')->where('receiver_department','LIKE','%'.$userDepartment.'%')->count();
        
                $data['multiTasks'] = DB::table('tasks_leaders as tl')
                        //->leftJoin('tasks_leaders_pic','tasks_leaders_pic.task_id','tl.id')
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
                        ->where('publisher_id',$userId)
                        ->whereOr('receiver_department','LIKE','%'.$userDepartment.'%')
                        ->orderBy('status','ASC')
                        ->orderBy('created_at','DESC')
                        ->limit(3)
                        ->get();
                
            //multi staff data end
        #########multi department data end#########

        #########internal department data#########
        $coAdminCountInternal = DB::table('tasks_internal')->where('publisher_id',$userId)->count();
        $staffCountInternal = DB::table('tasks_internal_pic')->where('pic_id',$userId)->count();
        $staffInternal = DB::table('tasks_internal_pic')->get();
        //internal staff data
            $data['countDataInternal'] = $coAdminCountInternal + $staffCountInternal;
            $data['staffDataInternal'] = $staffInternal;
    
            $data['internalTasks'] = DB::table('tasks_internal as ti')
                //->leftJoin('tasks_internal_pic','tasks_internal_pic.task_id','ti.id')
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
                ->where('department_id',$userDepartment)
                ->orderBy('status','ASC')
                ->orderBy('created_at','DESC')
                ->limit(3)
                ->get();
        //internal staff data end
        #########internal department data end#########

        return view('admin.task.collaboration.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task.');
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
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return redirect()->back()->with('alert-danger','Anda tidak diijinkan mengakses halaman Collaboration Task.');
    }
}
